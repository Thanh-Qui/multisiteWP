<?php

class Feature_WooCommerce_Factory
{
    public static function create($type)
    {
        switch ($type) {
            case 'search_woocommerce':
                return new Feature_WooCommerce_Search_WooCommerce();
            default:
                throw new Exception("Unknown WooCommerce feature type: " . $type);
        }
    }
}