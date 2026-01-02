<?php

class CoreRecaptcha
{
    private $site_key = '';
    private $secret_key = '';
    private $display_recaptcha = false;

    public function __construct() {
        $this->site_key = get_option('core_recaptcha_site_key', null);
        $this->secret_key = get_option('core_recaptcha_secret_key', null);
        $this->display_recaptcha = get_option('core_recaptcha_display', false);
    
    
        // Check if on login page
        $script_filename = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
        $is_login_page = (strpos($script_filename, 'wp-login.php') !== false);
        
        // only login
        if ($is_login_page && $this->is_active_recaptcha()) {            
            add_action('login_enqueue_scripts', array($this, 'add_script_google_recaptcha_api'));
            add_action('init', array($this, 'init'));
        }
    }

    public function is_active_recaptcha()
    {
        return !empty($this->site_key) && !empty($this->secret_key);
    }

    public function verify_recaptcha_api_callback($request)
    {
        $params = $request->get_params();

        $response = $this->is_form_verified($params);

        if (!empty($response) && $response['status']) {
            return new WP_REST_Response(array('verified' => $response['data']->success, 'data' => $response['data']), 200);
        } else {
            return new WP_REST_Response(array('verified' => false, 'error' => 'reCAPTCHA verification failed'), 403);
        }
    }

    public function add_script_google_recaptcha_api()
    {
        if (!$this->is_active_recaptcha()) {
            return;
        }

        if (!wp_script_is('jquery', 'enqueued')) {
            wp_enqueue_script('jquery');
        }

        wp_enqueue_script('google-recaptcha-api', 'https://www.google.com/recaptcha/api.js?render=' . $this->site_key, array(), null, true);
        add_filter('script_loader_tag', [$this, 'add_async_attribute'], 10, 2);

        $recaptcha_settings = array(
            'site_key' => $this->site_key,
            'is_active_recaptcha' => $this->is_active_recaptcha(),
            'display_recaptcha' => $this->display_recaptcha,
        );
        wp_localize_script('google-recaptcha-api', 'recaptchaSettings', $recaptcha_settings);
    }

    function add_async_attribute($tag, $handle)
    {
        if ('google-recaptcha-api' !== $handle) {
            return $tag;
        }
        return str_replace(' src', ' async src', $tag);
    }

    public function add_recaptcha_script_processing()
    {
        wp_enqueue_script(
            'custom-recaptcha',
            CORE_RECAPTCHA_PLUGIN_URL . 'assets/js/custom-recaptcha.min.js',
            array(),
            null,
            array(
                'strategy' => 'async'
            )
        );

        if (!$this->display_recaptcha) {
            echo '<style>.grecaptcha-badge{visibility:hidden}</style>';
        }

?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {

                jQuery("#loginform").submit(function(event) {
                    event.preventDefault();

                    grecaptcha.ready(function() {
                        grecaptcha.execute("<?php echo esc_js($this->site_key); ?>", {
                            action: "login"
                        }).then(function(token) {
                            jQuery("#ss-recaptcha-token").val(token);
                            jQuery("#loginform").unbind("submit").submit();
                        });
                    });
                });

                jQuery("#lostpasswordform").submit(function(event) {
                    event.preventDefault();

                    grecaptcha.ready(function() {
                        grecaptcha.execute("<?php echo esc_js($this->site_key); ?>", {
                            action: "lostpassword"
                        }).then(function(token) {
                            jQuery("#ss-recaptcha-token").val(token);
                            jQuery("#lostpasswordform").unbind("submit").submit();
                        });
                    });
                });
            });
        </script>
        <?php
    }

    public function init()
    {
        
        if (!$this->is_active_recaptcha()) {
            return;
        }
        
        add_action('login_footer', array($this, 'add_recaptcha_script_processing'));
        add_action('login_form', array($this, 'add_recaptcha_input_field'));
        
        add_filter('authenticate', array($this, 'verify_recaptcha_auth'), 30, 3);
    
        add_action('login_enqueue_scripts', function () {
            ?>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    let form = document.querySelector("form#lostpasswordform");
                    if (form) {
                        let input = document.createElement("input");
                        input.type = "hidden";
                        input.name = "ss-recaptcha-token";
                        input.id = "ss-recaptcha-token";
                        form.appendChild(input);
                    }
                });
            </script>
            <?php
        });
        
        add_action('lostpassword_post', array($this, 'verify_lost_password_recaptcha'), 10, 1);
    }

    public function verify_lost_password_recaptcha($errors) {
        
        if (!isset($_POST['ss-recaptcha-token']) || empty($_POST['ss-recaptcha-token'])) {
            $errors->add('recaptcha_error', __('reCAPTCHA verification failed. Please try again.'));
            return;
        }
        
        $recaptcha_token = sanitize_text_field($_POST['ss-recaptcha-token']);
        
        $verification_response = $this->verify_recaptcha_with_google($recaptcha_token);
        
        if (!$verification_response || !$verification_response->success) {
            $errors->add('recaptcha_error', __('reCAPTCHA verification failed. Please try again.'));
        }
    }

    public function add_recaptcha_input_field()
    {
        echo '<input type="hidden" name="ss-recaptcha-token" id="ss-recaptcha-token" />';
    }

    /**
     * Verify reCAPTCHA during authentication
     */
    public function verify_recaptcha_auth($user, $username, $password)
    {
       
        // Skip verification if user is already authenticated or if it's a password reset
        if (is_wp_error($user) || empty($username) || empty($password)) {
            return $user;
        }

        // Check if reCAPTCHA token is present
        if (!isset($_POST['ss-recaptcha-token']) || empty($_POST['ss-recaptcha-token'])) {
            return new WP_Error('recaptcha_error', __('reCAPTCHA verification failed. Please try again.'));
        }

        $recaptcha_token = sanitize_text_field($_POST['ss-recaptcha-token']);

        $verification_response = $this->verify_recaptcha_with_google($recaptcha_token);

        if (!$verification_response || !$verification_response->success) {
            return new WP_Error('recaptcha_error', __('reCAPTCHA verification failed. Please try again.'));
        }

        return $user;
    }

    private function is_form_verified($params)
    {
        if (isset($params['recaptchaToken']) && !empty($params['recaptchaToken'])) {
            $recaptcha_token = sanitize_text_field($params['recaptchaToken']);

            $verification_response = $this->verify_recaptcha_with_google($recaptcha_token);

            return ['status' => true, 'data' => $verification_response];
        }

        return ['status' => false, 'data' => []];
    }

    private function verify_recaptcha_with_google($token)
    {
        $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret={$this->secret_key}&response={$token}");
        
        if (is_wp_error($response)) {
            error_log('reCAPTCHA verification error: ' . $response->get_error_message());
            return null;
        }
        
        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body);

        error_log('reCAPTCHA verification response: ' . print_r($result, true));

        return $result;
    }
}
