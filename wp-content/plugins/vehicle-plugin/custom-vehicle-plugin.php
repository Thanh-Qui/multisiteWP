<?php
namespace CustomVehiclePlugin;

/**
 * Plugin Name: Custom Vehicle Plugin
 * Description: A custom plugin for managing vehicle listings with OOP architecture.
 * Version: 1.0.0
 * Author: Thanh-Qui
 * Author URI: http://multisiteWP.test:8080/
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    die;
}

// Include component classes
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-tax.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-meta-box.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-rest-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-ext.php';

// Component Interface
interface plugin_vehicle_module {
    // init method
    public function init();
}

// Main Plugin Class
class Vehicle_Plugin {
    private $module = [];

    // constructor with dependency injection
    public function __construct(array $module = []) {
        $this->module = $module;
    }

    // init the plugin
    public function init() {
        // Enqueue styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));

        // Init all module
        foreach ($this->module as $component) {
            if ($component instanceof plugin_vehicle_module) {
                $component->init();
            }
        }
    }

    // enqueue styles
    public function enqueue_styles() {
        wp_enqueue_style('vehicle-plugin-style', plugin_dir_url(__FILE__) . 'assets/scss/style.css', array(), '', 'all');
    }
}

// Instantiate module with dependency injection
$vehicle_cpt = new Vehicle_CPT();
$vehicle_taxonomy = new Vehicle_Taxonomy();
$vehicle_meta_box = new Vehicle_Meta_Box();
$vehicle_shortcode = new Vehicle_Shortcode();
$vehicle_rest_api = new Vehicle_REST_API();
$vehicle_custom_hooks = new Vehicle_Custom_Hooks();
$vehicle_admin_menu = new Vehicle_Admin_Menu();

// Create plugin instance with injected module
$vehicle_plugin = new Vehicle_Plugin([
    $vehicle_cpt,
    $vehicle_taxonomy,
    $vehicle_meta_box,
    $vehicle_shortcode,
    $vehicle_rest_api,
    $vehicle_custom_hooks,
    $vehicle_admin_menu,
]);

// Initialize the plugin
$vehicle_plugin->init();
