<?php

class ProductList
{

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes()
    {
        register_rest_route('product/v1', '/get-all-products', [
            'methods'             => 'GET',
            'callback'            => array($this, 'get_all_product'),
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('product/v1', '/get-all-categories', [
            'methods'             => 'GET',
            'callback'            => array($this, 'get_all_categories'),
            'permission_callback' => '__return_true'
        ]);
    }

    public function get_all_product()
    {
        $args = [
            'post_type'      => 'product',
            'posts_per_page' => -1,
        ];

        $query = new WP_Query($args);
        $results = [];
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = [
                    'id'    => get_the_ID(),
                    'title' => get_the_title(),
                    'content' => wp_strip_all_tags(get_the_content()),
                    'price' => get_post_meta(get_the_ID(), '_price', true),
                    'category_id' => wp_get_post_terms(get_the_ID(), 'product_cat', ['fields' => 'ids']),
                    'image_url' => get_post_meta(get_the_ID(), '_thumbnail_id', true) ? wp_get_attachment_url(get_post_meta(get_the_ID(), '_thumbnail_id', true)) : '',
                ];
            }
            wp_reset_postdata();
        }

        $response_data = [
            'total' => count($results),
            'data'  => $results,
        ];

        return $response_data;
    }

    public function get_all_categories()
    {
        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        ]);

        if (is_wp_error($terms)) {
            return [
                'total' => 0,
                'data'  => [],
            ];
        }

        $categories = [];

        foreach ($terms as $term) {
            $categories[] = [
                'id'    => $term->term_id,
                'name'  => $term->name,
                'slug'  => $term->slug,
                'parent' => $term->parent,
                'count' => $term->count,
            ];
        }

        return [
            'total' => count($categories),
            'data'  => $categories,
        ];
    }
}
