<?php

/**
 * Plugin Name: Cron Job Custom
 * Description: A custom plugin for cron job.
 * Version: 1.0.0
 * Author: Thanh-Qui
 * Author URI: http://multisitewp.test:8080/
 * License: GPL2
 */

 if (! defined('ABSPATH')) {
	die;
}


require_once plugin_dir_path(__FILE__) . 'includes/cron-job-send-email.php';
require_once plugin_dir_path(__FILE__) . 'includes/cron-job-email-product.php';