<?php
/**
 * Plugin Name:       product-list
 * Description:       Example block scaffolded with Create Block tool.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       multiple-block
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function create_block_multiple_block_block_init() {
	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
require_once plugin_dir_path( __FILE__ ) . 'common/js/api/product-list.php';
new ProductList();

add_action( 'init', 'create_block_multiple_block_block_init' );

add_action('enqueue_block_assets', 'enqueue_multiple_block_scripts');

function enqueue_multiple_block_scripts() {
	$handle = 'create-block-multiple-block-editor-script';

	wp_localize_script(
		$handle,
		'productListData',
		[
			'enableQuickView' => defined('FEATURE_QUICK_VIEW') ? FEATURE_QUICK_VIEW : false,
		]
	);
}

add_action('enqueue_block_assets', 'add_to_cart_with_nonce');
function add_to_cart_with_nonce() {
	$handle = 'create-block-multiple-block-editor-script';

	wp_localize_script(
		$handle,
		'addToCart',
		[
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce'   => wp_create_nonce('add-to-cart-nonce'),
		]
	);
}

add_action( 'wp_ajax_test_nonce_process', 'handle_react_add_to_cart' );
add_action( 'wp_ajax_nopriv_test_nonce_process', 'handle_react_add_to_cart' );

function handle_react_add_to_cart() {
    if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'add-to-cart-nonce' ) ) {
        wp_send_json_error( 'Nonce không hợp lệ!' );
    }

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    wp_send_json_success( 'Nonce chuẩn! Đã nhận ID sản phẩm: ' . $product_id );
}