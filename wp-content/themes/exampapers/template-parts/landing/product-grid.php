<?php
/**
 * Landing page product grid.
 *
 * @package Exampapers
 */

defined( 'ABSPATH' ) || exit;

$args       = isset( $args ) && is_array( $args ) ? $args : array();
$query_args = ! empty( $args['query_args'] ) && is_array( $args['query_args'] ) ? $args['query_args'] : array();

if ( empty( $query_args ) ) {
	return;
}

if ( ! function_exists( 'woocommerce_product_loop_start' ) || ! function_exists( 'wc_get_template_part' ) ) {
	echo '<p class="exampapers-muted">' . esc_html__( 'Product listings are unavailable right now.', 'exampapers' ) . '</p>';
	return;
}

$products = new WP_Query( $query_args );

if ( ! $products->have_posts() ) {
	echo '<p class="exampapers-muted">' . esc_html__( 'No matching papers found yet.', 'exampapers' ) . '</p>';
	return;
}

$previous_loop = wc_get_loop_prop( 'name' );
wc_set_loop_prop( 'name', 'exampapers-landing-products' );
wc_set_loop_prop( 'columns', 4 );
wc_set_loop_prop( 'total', (int) $products->post_count );

woocommerce_product_loop_start();

while ( $products->have_posts() ) {
	$products->the_post();

	do_action( 'woocommerce_shop_loop' );
	wc_get_template_part( 'content', 'product' );
}

woocommerce_product_loop_end();

wc_set_loop_prop( 'name', $previous_loop );
wc_set_loop_prop( 'columns', '' );
wc_set_loop_prop( 'total', 0 );
wp_reset_postdata();
