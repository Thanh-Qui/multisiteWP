<?php
namespace CustomVehiclePlugin;

// vehicle meta box
class Vehicle_Meta_Box implements plugin_vehicle_module {

//    Define meta box fields
    private $fields = [
        'vehicle_model' => '_vehicle_model',
        'vehicle_year' => '_vehicle_year',
        'vehicle_badge' => '_vehicle_badge',
        'vehicle_color' => '_vehicle_color',
        'vehicle_odometer' => '_vehicle_odometer',
        'vehicle_price' => '_vehicle_price',
    ];

//   init the meta box
    public function init() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('post_edit_form_tag', array($this, 'add_form_enctype'));
        add_action('save_post', array($this, 'save_meta'));
    }

    public function add_meta_box() {
        add_meta_box(
            'vehicle_info',           // Meta box ID
            'Vehicle Info',           // Title
            array($this, 'render_meta_box'), // Callback
            'vehicle'                 // Post type
        );
    }

    public function render_meta_box($post) {
        // Retrieve existing meta values
        $model = get_post_meta($post->ID, '_vehicle_model', true);
        $year = get_post_meta($post->ID, '_vehicle_year', true);
        $badge = get_post_meta($post->ID, '_vehicle_badge', true);
        $color = get_post_meta($post->ID, '_vehicle_color', true);
        $odometer = get_post_meta($post->ID, '_vehicle_odometer', true);
        $price = get_post_meta($post->ID, '_vehicle_price', true);
        $image_id = get_post_meta($post->ID, '_vehicle_image', true);
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';

        // Add nonce for security
        wp_nonce_field('vehicle_save_meta', 'vehicle_meta_nonce');
        ?>
        <div>
            <p>
                <label for="vehicle_model">Model:</label><br>
                <input type="text" id="vehicle_model" name="vehicle_model" value="<?php echo esc_attr($model); ?>" style="width:100%;">
            </p>
            <p>
                <label for="vehicle_year">Year:</label><br>
                <input type="text" id="vehicle_year" name="vehicle_year" value="<?php echo esc_attr($year); ?>" style="width:100%;">
            </p>
            <p>
                <label for="vehicle_badge">Badge:</label><br>
                <input type="text" id="vehicle_badge" name="vehicle_badge" value="<?php echo esc_attr($badge); ?>" style="width:100%;">
            </p>
            <p>
                <label for="vehicle_color">Color:</label><br>
                <input type="color" id="vehicle_color" name="vehicle_color" value="<?php echo esc_attr($color); ?>">
            </p>
            <p>
                <label for="vehicle_odometer">Odometer:</label><br>
                <input type="number" id="vehicle_odometer" name="vehicle_odometer" value="<?php echo esc_attr($odometer); ?>" style="width:100%;">
            </p>
            <p>
                <label for="vehicle_price">Price:</label><br>
                <input type="number" id="vehicle_price" name="vehicle_price" value="<?php echo esc_attr($price); ?>" style="width:100%;">
            </p>
            <p>
                <label for="vehicle_image">Image:</label><br>
                <?php if ($image_url): ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="Vehicle Image" style="max-width:150px; display:block; margin-bottom:5px;">
                <?php endif; ?>
                <input type="file" id="vehicle_image" name="vehicle_image" accept="image/*">
            </p>
        </div>
        <?php
    }

    public function add_form_enctype() {
        echo ' enctype="multipart/form-data"';
    }

    public function save_meta($post_id) {
        // Verify nonce for security
        if (!isset($_POST['vehicle_meta_nonce']) || !wp_verify_nonce($_POST['vehicle_meta_nonce'], 'vehicle_save_meta')) {
            return;
        }

        // Check if user has permission to edit
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Avoid autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Save text fields
        foreach ($this->fields as $field_name => $meta_key) {
            if (isset($_POST[$field_name])) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field_name]));
            }
        }

        // Handle image upload
        if (isset($_FILES['vehicle_image']) && !empty($_FILES['vehicle_image']['name'])) {
            // Ensure media upload functions are available
            if (!function_exists('media_handle_upload')) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');
            }

            $attachment_id = media_handle_upload('vehicle_image', $post_id);
            if (!is_wp_error($attachment_id)) {
                update_post_meta($post_id, '_vehicle_image', $attachment_id);
            }
        }
    }
}
