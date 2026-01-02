<?php

/**
 * Plugin Name: Feature Commerce
 * Description: Add new features for WooCommerce using OOP architecture.
 * Version: 1.0.0
 * Author: Thanh-Qui
 * Author URI: http://multisitewp.test:8080/
 * License: GPL2
 */

if (! defined('ABSPATH')) {
	die;
}

require_once plugin_dir_path(__FILE__) . 'includes/factory-woocommerce.php';
require_once plugin_dir_path(__FILE__) . 'includes/search-woocomerce.php';

interface plugin_feature_woocommerce_module
{
	public function init();
}

class Feature_WooCommerce
{
    private $module = [];

    public function __construct(array $module = [])
    {
        $this->module = $module;
    }

    public function init()
    {
        foreach ($this->module as $component) {
            if ($component instanceof plugin_feature_woocommerce_module) {
                $component->init();
            }
        }
    }
}

// use factory to create cron job instances
$feature_list = [
    Feature_WooCommerce_Factory::create('search_woocommerce'),
];

$feature_plugin = new Feature_WooCommerce($feature_list);
$feature_plugin->init();