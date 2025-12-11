<?php
namespace CustomVehiclePlugin;

// vehicle custom post type
class Vehicle_CPT implements plugin_vehicle_module {

    // Init the Vehicle CPT
    public function init() {
        add_action('init', array($this, 'register'));
    }

    // register the Vehicle CPT
    public function register() {
        // Labels containing display information for the CPT
        $labels = array(
            'name'               => __('Vehicles', 'bluehost-blueprint'),
            'singular_name'      => __('Vehicle', 'bluehost-blueprint'),
            'menu_name'          => __('Vehicles', 'bluehost-blueprint'),
            'all_items'          => __('All Vehicles', 'bluehost-blueprint'),
            'add_new'            => __('Add New', 'bluehost-blueprint'),
            'add_new_item'       => __('Add New Vehicle', 'bluehost-blueprint'),
            'edit_item'          => __('Edit Vehicle', 'bluehost-blueprint'),
            'new_item'           => __('New Vehicle', 'bluehost-blueprint'),
        );

        // Arguments for registering the post type
        $args = array(
            'label'              => __('Vehicles', 'bluehost-blueprint'),
            'labels'             => $labels,
            'public'             => true,
            'show_in_rest'       => true,
            // Enable front-end queries using WP_Query
            'publicly_queryable' => true,
            'menu_position'      => 4,
            'menu_icon'          => 'dashicons-car',
            'supports'           => array('title', 'editor', 'thumbnail'),
            // URL routing uses 'vehicle' slug
            'rewrite'            => array('slug' => 'vehicle'),
        );

        register_post_type('vehicle', $args);
    }
}
