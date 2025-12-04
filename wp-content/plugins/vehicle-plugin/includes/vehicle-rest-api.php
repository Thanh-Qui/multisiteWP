<?php

// REST-API
function vehicle_search_api(WP_REST_Request $request) {

    // get keyword to params
    $keyword = sanitize_text_field($request->get_param('search'));

    // select post type
    $args = [
        'post_type' => 'vehicle',
        'posts_per_page' => -1,
        's' => $keyword
    ];

    // init query
    $query = new WP_Query($args);

    $results = [];
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            // return data
            $results[] = [
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'content' => get_the_excerpt(),
            ];
        };
        wp_reset_postdata();
    }

    $response = rest_ensure_response([
        'search' => $keyword,
        'total' => count($results),
        'data' => $results,

    ]);

    $response->set_status(200);
    return $response;
}

add_action('rest_api_init', function() {
    register_rest_route('vehicle/v1', '/search', [
        'methods' => 'GET',
        'callback' => 'vehicle_search_api',
        'permission_callback' => '__return_true',
    ]);
});