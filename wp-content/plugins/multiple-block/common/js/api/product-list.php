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
        $cache_group = 'product_api';
        $cache_key = 'all_product';
        $cache_ttl = 300;

        // if exist cache return from cache
        $cached_data = wp_cache_get($cache_key, $cache_group);
        if ($cached_data !== false) {
            return [
                'to' => 'redis_cache',
                'total'  => count($cached_data),
                'data'   => $cached_data,
            ];
        }

        // if not exist cache fetch from database
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
                    'image_url' => get_post_meta(get_the_ID(), '_thumbnail_id', true) ? 
                    wp_get_attachment_url(get_post_meta(get_the_ID(), '_thumbnail_id', true)) : '',
                ];
            }
            wp_reset_postdata();
        }

        // save cache when query db
        wp_cache_set($cache_key, $results, $cache_group, $cache_ttl);
        $response_data = [
            'to' => 'database',
            'total' => count($results),
            'data'  => $results,
        ];

        return $response_data;
    }

    public function get_all_categories()
    {
        $cache_group = 'product_api';
        $cache_key = 'all_categories';
        $cache_ttl = 300;

        // if exist cache return from cache
        $cached_data = wp_cache_get($cache_key, $cache_group);
        if ($cached_data !== false) {
            return [
                'to' => 'redis_cache',
                'total'  => count($cached_data),
                'data'   => $cached_data,
            ];
        }

        // if not exist cache fetch from database
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

        // save cache when query db
        wp_cache_set($cache_key, $categories, $cache_group, $cache_ttl);

        $response_data = [
            'to' => 'database',
            'total' => count($categories),
            'data'  => $categories,
        ];

        return $response_data;
    }
}
