<?php
/**
 * Theme setup.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and editor assets.
 */
function exampapers_theme_setup() {
	load_child_theme_textdomain( 'exampapers', get_stylesheet_directory() . '/languages' );

	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/css/theme.css', 'assets/css/editor.css' ) );

	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'exampapers_theme_setup' );
