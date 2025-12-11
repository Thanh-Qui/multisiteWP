<?php
namespace CustomVehiclePlugin;

// vehicle taxonomies
class Vehicle_Taxonomy implements plugin_vehicle_module {

//   init the taxonomies
    public function init() {
        add_action('init', array($this, 'register_taxonomies'));
    }

    // register the taxonomies
    public function register_taxonomies() {
        // Taxonomy configurations
        $taxonomies = [
            'vehicle_brand' => [
                'labels' => [
                    'name'          => __('Brands', 'bluehost-blueprint'), 'singular_name' => __('Brand', 'bluehost-blueprint'),
                ],
                'args' => [
                    'hierarchical'      => true,
                    'rewrite'           => ['slug' => 'brand'],
                    'show_admin_column' => true,
                ],
            ],
            'vehicle_series' => [
                'labels' => [
                    'name'          => __('Series', 'bluehost-blueprint'),
                    'singular_name' => __('Series', 'bluehost-blueprint'),
                ],
                'args' => [
                    'hierarchical'      => true,
                    'rewrite'           => ['slug' => 'series'],
                    'show_admin_column' => true,
                ],
            ],
        ];

        foreach ($taxonomies as $taxonomy_slug => $config) {
            register_taxonomy(
                $taxonomy_slug,// Taxonomy slug
                'vehicle',// Associated post type
                array_merge($config['args'], ['labels' => $config['labels']])
            );
        }
    }
}