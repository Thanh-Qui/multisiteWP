<?php
namespace CustomVehiclePlugin;

// vehicle custom hooks
class Vehicle_Custom_Hooks implements plugin_vehicle_module {

    // init the custom hooks
    public function init() {
        add_action('display_discount', array($this, 'display_discount_banner'));
        add_action('custom_hook', array($this, 'test_custom_hook'));
    }

    // display discount banner
    public function display_discount_banner() {
        echo '<div>' . esc_html__('Black Friday (Sale 50%)', 'bluehost-blueprint') . '</div>';
    }

    // test custom hook
    public function test_custom_hook() {
        echo esc_html__('Hello', 'bluehost-blueprint');
    }
}

// vehicle admin menu
class Vehicle_Admin_Menu implements plugin_vehicle_module {

    // init the admin menu
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    // add admin menu
    public function add_admin_menu() {
        add_menu_page(
            'WPOrg',
            'WPOrg Options',
            'manage_options',
            'wporg',
            array($this, 'render_admin_page'),
            plugin_dir_url(__FILE__) . 'images/icon_wporg.png',
            20
        );
    }

    // render admin page
    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                // Output security fields for the registered setting "wporg_options"
                settings_fields('wporg_options');
                // Output setting sections and their fields
                do_settings_sections('wporg');
                // Output save settings button
                submit_button(__('Save Settings', 'textdomain'));
                ?>
            </form>
        </div>
        <?php
    }
}