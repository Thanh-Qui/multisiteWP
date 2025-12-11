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

interface plugin_vehicle_module
{

    //init method
    public function init();
}

// Main Plugin Class

class Vehicle_Plugin
{
    private static $instance = null;
    private $module = [];

    /**

     * Private constructor to prevent direct initialization

     */
    private function __construct(array $module = [])
    {
        $this->module = $module;
    }

    /**

     * Singleton instance loader

     */
    public static function instance(array $module = [])
    {

        if (self::$instance === null) {

            self::$instance = new self($module);
        }

        return self::$instance;
    }

    /**

     * Main init method

     */
    public function init()
    {

        // Enqueue styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_styles']);

        // Init modules
        foreach ($this->module as $component) {

            if ($component instanceof plugin_vehicle_module) {
                $component->init();
            }
        }
    }



    public function enqueue_styles()
    {
        wp_enqueue_style('vehicle-plugin-style', plugin_dir_url(__FILE__) . 'assets/scss/style.css', [], null, 'all');
    }
}

// Instantiate module list
$modules = [

    new Vehicle_CPT(),

    new Vehicle_Taxonomy(),

    new Vehicle_Meta_Box(),

    new Vehicle_Shortcode(),

    new Vehicle_REST_API(),

    new Vehicle_Custom_Hooks(),

    new Vehicle_Admin_Menu(),

];

// Load plugin using Singleton + WordPress hook
add_action('plugins_loaded', function () use ($modules) {
    Vehicle_Plugin::instance($modules)->init();
});
