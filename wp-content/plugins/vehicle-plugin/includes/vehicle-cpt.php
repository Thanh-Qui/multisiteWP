<?php

/* 
*   custom post type
*/
function register_vehicle_cpt()
{
    // label containing display information
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

    $args = array(
        'label'              => __('Vehicles', 'bluehost-blueprint'),
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        // Enable front-end queries using WP_query
        'publicly_queryable' => true,
        'menu_position'      => 4,
        'menu_icon'          => 'dashicons-car',
        'supports'           => array('title', 'editor', 'thumbnail'),
        // URL routing is slug (vehicle)
        'rewrite'            => array('slug' => 'vehicle'),
    );

    register_post_type('vehicle', $args);
}

// hook init register custom post type
add_action('init', 'register_vehicle_cpt');
