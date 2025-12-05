<?php

/* 
*   taxonomy
*/

// register 2 taxonomy for cpt of vehicle is brand and series
function register_vehicle_tax()
{

    // create brand
    $brand_labels = array(
        // dipslay name menu
        'name'          => __('Brands', 'bluehost-blueprint'),
        'singular_name' => __('Brand', 'bluehost-blueprint'),
    );

    register_taxonomy(
        // slug private of taxonomy
        'vehicle_brand',
        // use for vehicle cpt
        'vehicle',
        array(
            'labels'        => $brand_labels,
            // 
            'hierarchical'  => true,
            'rewrite'       => array('slug' => 'brand'),
            // add column brand at list vehicle
            'show_admin_column' => true,
        )
    );

    // create series
    $series_labels = array(
        'name'          => __('Series', 'bluehost-blueprint'),
        'singular_name' => __('Series', 'bluehost-blueprint'),
    );

    register_taxonomy(
        'vehicle_series',
        'vehicle',
        array(
            'labels'        => $series_labels,
            'hierarchical'  => true,
            'rewrite'       => array('slug' => 'series'),
            'show_admin_column' => true,
        )
    );
}
add_action('init', 'register_vehicle_tax');