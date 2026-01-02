<?php

class Feature_WooCommerce_Search_WooCommerce implements plugin_feature_woocommerce_module
{

    public function init()
    {
        // Initialization code for the search functionality
        add_filter('posts_search', array($this, 'search_products_by_sku'), 10, 2);
    }

    public function search_products_by_sku($search, $query)
    {
        global $wpdb;

        if (is_a($query, 'WP_Query') && $query->is_search() && $query->get('post_type') === 'product') {
            $search_term = $query->get('s');
            error_log('Searching for term: ' . $query->get('s'));

            if (!empty($search_term)) {
                $search_term_like = '%' . $wpdb->esc_like($search_term) . '%';

                // add SKU search
                $search = $search . $wpdb->prepare(
                    " OR {$wpdb->posts}.ID IN (
                        SELECT post_id FROM {$wpdb->postmeta}
                        WHERE meta_key = '_sku' AND meta_value LIKE %s
                    )",
                    $search_term_like
                );
            }
        }

        return $search;
    }
}
