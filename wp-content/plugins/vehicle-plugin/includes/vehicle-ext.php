<?php

// Add file css
function add_my_css() {
    wp_enqueue_style(
        'vehicle-plugin-style',
        plugin_dir_url(__FILE__) . '../assets/css/style.css',
        array(),
        null,
        'all'
    );
}
// add_action('wp_enqueue_scripts', 'add_my_css');

// custom hook
function display_banner_discount() {
    echo "<div>Black Friday (Sale 50%)</div>";
}
add_action('display_discount', 'display_banner_discount');

function test_custom_hook() {
    echo "Hello";
}
add_action('custom_hook', 'test_custom_hook');

// add top level menu
add_action( 'admin_menu', 'wporg_options_page' );
function wporg_options_page() {
    add_menu_page(
        'WPOrg',
        'WPOrg Options',
        'manage_options',
        'wporg',
        'wporg_options_page_html',
        plugin_dir_url(__FILE__) . 'images/icon_wporg.png',
        20
    );
}

function wporg_options_page_html() {
    ?>
    <div class="wrap">
      <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
      <form action="options.php" method="post">
        <?php
        // output security fields for the registered setting "wporg_options"
        settings_fields( 'wporg_options' );
        // output setting sections and their fields
        // (sections are registered for "wporg", each field is registered to a specific section)
        do_settings_sections( 'wporg' );
        // output save settings button
        submit_button( __( 'Save Settings', 'textdomain' ) );
        ?>
      </form>
    </div>
    <?php
}