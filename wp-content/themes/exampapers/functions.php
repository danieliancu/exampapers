<?php
/**
 * Exampapers child theme bootstrap.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$exampapers_includes = array(
	'inc/setup.php',
	'inc/template-tags.php',
	'inc/product-meta.php',
	'inc/enqueue.php',
	'inc/seo.php',
	'inc/woocommerce.php',
);

foreach ( $exampapers_includes as $exampapers_file ) {
	$exampapers_path = get_stylesheet_directory() . '/' . $exampapers_file;

	if ( file_exists( $exampapers_path ) ) {
		require_once $exampapers_path;
	}
}
