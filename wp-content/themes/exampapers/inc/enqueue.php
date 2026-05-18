<?php
/**
 * Frontend assets.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return a file modification version when possible.
 *
 * @param string $relative_path Path relative to the child theme directory.
 * @return string
 */
function exampapers_asset_version( $relative_path ) {
	$path = get_stylesheet_directory() . '/' . ltrim( $relative_path, '/' );

	return file_exists( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' );
}

/**
 * Enqueue parent, child and conditional WooCommerce assets.
 */
function exampapers_enqueue_styles() {
	$parent_theme = wp_get_theme( get_template() );

	wp_enqueue_style(
		'exampapers-poppins',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap',
		array(),
		null
	);

	if ( is_front_page() || ( function_exists( 'is_account_page' ) && is_account_page() ) ) {
		wp_enqueue_style( 'dashicons' );
	}

	wp_enqueue_style(
		'twentytwentyfive-style',
		get_template_directory_uri() . '/style.css',
		array(),
		$parent_theme->get( 'Version' )
	);

	wp_enqueue_style(
		'exampapers-theme',
		get_stylesheet_directory_uri() . '/assets/css/theme.css',
		array( 'twentytwentyfive-style' ),
		exampapers_asset_version( 'assets/css/theme.css' )
	);

	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() || is_front_page() ) ) {
		wp_enqueue_style(
			'exampapers-woocommerce',
			get_stylesheet_directory_uri() . '/assets/css/woocommerce.css',
			array( 'exampapers-theme' ),
			exampapers_asset_version( 'assets/css/woocommerce.css' )
		);
	}

	wp_enqueue_script(
		'exampapers-theme',
		get_stylesheet_directory_uri() . '/assets/js/theme.js',
		array(),
		exampapers_asset_version( 'assets/js/theme.js' ),
		true
	);

	if ( function_exists( 'is_woocommerce' ) && ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) ) ) {
		wp_enqueue_script(
			'exampapers-filters',
			get_stylesheet_directory_uri() . '/assets/js/filters.js',
			array(),
			exampapers_asset_version( 'assets/js/filters.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'exampapers_enqueue_styles' );
