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

$cron_job_send_email = new Cron_Job_Send_Email();
$cron_job_email_product = new Cron_Job_Email_Product();
$cron_job_schedule = new Cron_Job_Schedule();

$cron_plugin = new Cron_Job_Custom(array(
	$cron_job_send_email,
	$cron_job_email_product,
	$cron_job_schedule,
));

$cron_plugin->init();
