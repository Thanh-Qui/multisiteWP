<?php
namespace CustomVehiclePlugin;

use WP_Query;
use WP_REST_Request;

class Vehicle_REST_API implements plugin_vehicle_module {

    // init the REST API routes
    public function init() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route('vehicle/v1', '/search', [
            'methods'             => 'GET',
            'callback'            => array($this, 'search_vehicles'),
            'permission_callback' => '__return_true',
            'args'                => [
                'search' => [
                    'required'          => false,
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return is_string($param);
                    },
                ],
            ],
        ]);
    }

    public function search_vehicles(WP_REST_Request $request) {
        // Sanitize the search keyword
        $keyword = sanitize_text_field($request->get_param('search'));

        // Query arguments for vehicle search
        $args = [
            'post_type'      => 'vehicle',
            'posts_per_page' => -1,
            's'              => $keyword,
        ];

        // Execute the query
        $query = new WP_Query($args);

        $results = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                // Build result array with vehicle data
                $results[] = [
                    'id'      => get_the_ID(),
                    'title'   => get_the_title(),
                    'content' => get_the_excerpt(),
                ];
            }
            wp_reset_postdata();
        }

        // Prepare and return the response
        $response_data = [
            'search' => $keyword,
            'total'  => count($results),
            'data'   => $results,
        ];

        $response = rest_ensure_response($response_data);
        $response->set_status(200);

        return $response;
    }
}