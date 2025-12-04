<?php

/**
 * Plugin Name: Custome vehicle plugin
 * Description: A custom plugin for managing vehicle listings.
 * Version: 1.0.0
 * Author: Thanh-Qui
 * Author URI: http://project1.test:8080/
 * License: GPL2
 */

if (! defined('ABSPATH')) {
	die;
}

function add_file_scss()
{
	wp_enqueue_style('vehicle-plugin-style', plugin_dir_url(__FILE__) . 'assets/scss/style.css', array(), '', 'all');
}
add_action('wp_enqueue_scripts', 'add_file_scss');

require_once plugin_dir_path(__FILE__) . 'includes/vehicle-cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-ext.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-meta-box.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-rest-api.php';
require_once plugin_dir_path(__FILE__) . 'includes/vehicle-tax.php';
