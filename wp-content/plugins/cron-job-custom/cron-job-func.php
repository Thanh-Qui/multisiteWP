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
require_once plugin_dir_path(__FILE__) . 'includes/cron-job-schedule.php';
require_once plugin_dir_path(__FILE__) . 'includes/cron-job-factory.php';

interface plugin_cronjob_module
{
	public function init();
}

class Cron_Job_Custom
{
	private $module = [];

	public function __construct(array $module = [])
	{
		$this->module = $module;
	}

	public function init()
	{
		foreach ($this->module as $component) {
			if ($component instanceof plugin_cronjob_module) {
				$component->init();
			}
		}
	}
}

// use factory to create cron job instances
$cron_list = [
	Cron_job_factory::create('send_email'),
	Cron_job_factory::create('email_product'),
	Cron_job_factory::create('schedule'),
];

$cron_plugin = new Cron_Job_Custom($cron_list);
$cron_plugin->init();
