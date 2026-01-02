<?php
/**
 * Plugin Name: Custom Core Recaptcha
 * Description: A custom plugin for core recaptcha.
 * Version: 1.0.0
 * Author: Thanh-Qui
 * Author URI: http://multisitewp.test:8080/
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if(!defined('CORE_RECAPTCHA_PLUGIN_PATH')){
    define('CORE_RECAPTCHA_PLUGIN_PATH', plugin_dir_path(__FILE__) . DIRECTORY_SEPARATOR);
}

if(!defined('CORE_RECAPTCHA_PLUGIN_URL')){
    define('CORE_RECAPTCHA_PLUGIN_URL', plugin_dir_url(__FILE__));
}

require_once CORE_RECAPTCHA_PLUGIN_PATH . 'includes/class-core-recaptcha.php';

new CoreRecaptcha();