<?php
/**
 * WooCommerce integration.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set product archive density.
 *
 * @return int
 */
function exampapers_loop_shop_per_page() {
	return 12;
}
add_filter( 'loop_shop_per_page', 'exampapers_loop_shop_per_page', 20 );

/**
 * Set product columns.
 *
 * @return int
 */
function exampapers_loop_shop_columns() {
	return 3;
}
add_filter( 'loop_shop_columns', 'exampapers_loop_shop_columns' );

/**
 * Set related product layout.
 *
 * @param array $args Related product args.
 * @return array
 */
function exampapers_related_products_args( $args ) {
	$args['posts_per_page'] = 3;
	$args['columns']        = 3;

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'exampapers_related_products_args' );

/**
 * Sell virtual/downloadable products individually.
 *
 * @param bool       $sold_individually Current value.
 * @param WC_Product $product Product instance.
 * @return bool
 */
function exampapers_digital_products_sold_individually( $sold_individually, $product ) {
	if ( $product instanceof WC_Product && ( $product->is_virtual() || $product->is_downloadable() ) ) {
		return true;
	}

	return $sold_individually;
}
add_filter( 'woocommerce_is_sold_individually', 'exampapers_digital_products_sold_individually', 10, 2 );

/**
 * Replace cart quantity controls for digital products with a fixed value.
 *
 * @param string $product_quantity Quantity HTML.
 * @param string $cart_item_key Cart item key.
 * @param array  $cart_item Cart item data.
 * @return string
 */
function exampapers_digital_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
	if ( empty( $cart_item['data'] ) || ! $cart_item['data'] instanceof WC_Product ) {
		return $product_quantity;
	}

	if ( $cart_item['data']->is_virtual() || $cart_item['data']->is_downloadable() ) {
		return '<span class="exampapers-fixed-quantity">1</span>';
	}

	return $product_quantity;
}
add_filter( 'woocommerce_cart_item_quantity', 'exampapers_digital_cart_item_quantity', 10, 3 );

/**
 * Check whether a product is already in the cart.
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function exampapers_product_is_in_cart( $product_id ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return false;
	}

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( (int) $cart_item['product_id'] === (int) $product_id ) {
			return true;
		}
	}

	return false;
}

/**
 * Keep loop add-to-cart native while adding the theme's in-cart state.
 *
 * @param string     $html Button HTML.
 * @param WC_Product $product Product instance.
 * @param array      $args Button args.
 * @return string
 */
function exampapers_loop_add_to_cart_link( $html, $product, $args = array() ) {
	if ( ! $product instanceof WC_Product || ! $product->is_type( 'simple' ) || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return $html;
	}

	$classes = isset( $args['class'] ) ? $args['class'] : 'button';

	if ( false === strpos( $classes, 'exampapers-add-to-cart' ) ) {
		$classes .= ' exampapers-add-to-cart';
	}

	$attributes = isset( $args['attributes'] ) && is_array( $args['attributes'] ) ? $args['attributes'] : array();
	$in_cart    = ( $product->is_virtual() || $product->is_downloadable() ) && exampapers_product_is_in_cart( $product->get_id() );
	$text       = $in_cart ? __( 'Added to cart', 'exampapers' ) : $product->add_to_cart_text();
	$url        = $in_cart ? '#' : $product->add_to_cart_url();

	if ( $in_cart ) {
		$classes                       .= ' exampapers-in-cart disabled';
		$attributes['aria-disabled']    = 'true';
		$attributes['data-in-cart']     = 'true';
		$attributes['tabindex']         = '-1';
	} else {
		$attributes['aria-label'] = $product->add_to_cart_description();
	}

	$attributes['data-product_id']  = $product->get_id();
	$attributes['data-product_sku'] = $product->get_sku();

	return sprintf(
		'<a href="%1$s" data-quantity="%2$s" class="%3$s" %4$s>%5$s</a>',
		esc_url( $url ),
		esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
		esc_attr( trim( $classes ) ),
		wc_implode_html_attributes( $attributes ),
		esc_html( $text )
	);
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'exampapers_loop_add_to_cart_link', 10, 3 );

/**
 * Add the theme's in-cart state to WooCommerce product grid block buttons.
 *
 * @param array      $attributes Button attributes.
 * @param WC_Product $product Product instance.
 * @return array
 */
function exampapers_product_grid_add_to_cart_attributes( $attributes, $product ) {
	if ( empty( $attributes['class'] ) ) {
		return $attributes;
	}

	if ( false === strpos( $attributes['class'], 'exampapers-add-to-cart' ) ) {
		$attributes['class'] .= ' exampapers-add-to-cart';
	}

	if ( $product instanceof WC_Product ) {
		$attributes['data-product_id']  = $product->get_id();
		$attributes['data-product_sku'] = $product->get_sku();
	}

	if ( $product instanceof WC_Product && ( $product->is_virtual() || $product->is_downloadable() ) && exampapers_product_is_in_cart( $product->get_id() ) ) {
		$attributes['class']          .= ' exampapers-in-cart disabled';
		$attributes['aria-disabled']   = 'true';
		$attributes['aria-label']      = __( 'Added to cart', 'exampapers' );
		$attributes['data-in-cart']    = 'true';
		$attributes['tabindex']        = '-1';
	}

	return $attributes;
}
add_filter( 'woocommerce_blocks_product_grid_add_to_cart_attributes', 'exampapers_product_grid_add_to_cart_attributes', 10, 2 );

/**
 * Change the single product button text when the product is already in the cart.
 *
 * @param string $text Button text.
 * @return string
 */
function exampapers_single_add_to_cart_text( $text ) {
	global $product;

	if ( $product instanceof WC_Product && ( $product->is_virtual() || $product->is_downloadable() ) && exampapers_product_is_in_cart( $product->get_id() ) ) {
		return __( 'Added to cart', 'exampapers' );
	}

	return $text;
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'exampapers_single_add_to_cart_text' );

/**
 * Reorder single product summary blocks:
 * move Add to Cart above the excerpt so it appears next to the image at the top,
 * restore product meta (SKU + category links) below the button,
 * and remove the data tabs which are replaced by custom sections.
 */
function exampapers_remove_single_product_meta_sections() {
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

	// Move Add to Cart from priority 30 to 12 (right after price at 10).
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 12 );

	// Remove meta from summary — rendered in the media column via template.
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
}
add_action( 'wp', 'exampapers_remove_single_product_meta_sections' );

/**
 * Add product badges inside single product summary.
 */
function exampapers_single_product_badges() {
	global $product;

	exampapers_product_badges( $product );
}
add_action( 'woocommerce_single_product_summary', 'exampapers_single_product_badges', 6 );

/**
 * Add structured product sections after the standard summary.
 */
function exampapers_single_product_sections() {
	global $product;

	exampapers_product_info_sections( $product );
}
add_action( 'woocommerce_after_single_product_summary', 'exampapers_single_product_sections', 8 );

/**
 * Add SEO content below archive product loops — after pagination (priority 10).
 */
add_action( 'woocommerce_after_shop_loop', 'exampapers_archive_seo_content', 100 );
add_action( 'woocommerce_no_products_found', 'exampapers_archive_seo_content', 20 );

/**
 * Force PHP templates for all WooCommerce product archives so every product
 * card renders via template-parts/product-card.php instead of block markup.
 */
add_filter( 'woocommerce_has_block_template', '__return_false', 100 );

/**
 * Block product searches shorter than 3 characters by redirecting to shop.
 */
function exampapers_enforce_min_search_length() {
	if ( ! is_search() || get_query_var( 'post_type' ) !== 'product' ) {
		return;
	}

	$query = get_search_query();

	if ( '' !== $query && mb_strlen( trim( $query ) ) < 3 ) {
		wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
		exit;
	}
}
add_action( 'template_redirect', 'exampapers_enforce_min_search_length' );

/**
 * Render archive filters from WooCommerce attribute archives.
 */
function exampapers_archive_filters() {
	if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) ) ) {
		return;
	}

	$filters = array(
		'pa_exam-level' => __( 'Exam Level', 'exampapers' ),
		'pa_exam-area'  => __( 'Exam Area', 'exampapers' ),
		'pa_subject'    => __( 'Subject', 'exampapers' ),
		'pa_format'     => __( 'Format', 'exampapers' ),
		'pa_difficulty' => __( 'Difficulty', 'exampapers' ),
		'pa_school'     => __( 'School', 'exampapers' ),
	);

	echo '<button class="exampapers-filter-toggle" type="button" aria-expanded="false" aria-controls="exampapers-shop-filters">' . esc_html__( 'Filters', 'exampapers' ) . '</button>';
	echo '<aside id="exampapers-shop-filters" class="exampapers-shop-filters" aria-label="' . esc_attr__( 'Product filters', 'exampapers' ) . '">';
	echo '<form role="search" method="get" action="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" class="exampapers-product-search">';
	echo '<label for="exampapers-product-search">' . esc_html__( 'Search papers', 'exampapers' ) . '</label>';
	echo '<input id="exampapers-product-search" type="search" name="s" minlength="3" value="' . esc_attr( get_search_query() ) . '" placeholder="' . esc_attr__( 'School, area or subject', 'exampapers' ) . '" aria-describedby="exampapers-search-hint">';
	echo '<span id="exampapers-search-hint" class="screen-reader-text">' . esc_html__( 'Enter at least 3 characters', 'exampapers' ) . '</span>';
	echo '<input type="hidden" name="post_type" value="product">';
	echo '<button type="submit">' . esc_html__( 'Search', 'exampapers' ) . '</button>';
	echo '</form>';

	foreach ( $filters as $taxonomy => $label ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => true,
				'number'     => 12,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			continue;
		}

		echo '<section class="exampapers-filter-group"><h2>' . esc_html( $label ) . '</h2>';

		foreach ( $terms as $term ) {
			$link = get_term_link( $term );

			if ( is_wp_error( $link ) ) {
				continue;
			}

			$is_current = is_tax( $term->taxonomy, $term->term_id );
			printf(
				'<a href="%1$s"%2$s>%3$s</a>',
				esc_url( $link ),
				$is_current ? ' aria-current="page"' : '',
				esc_html( $term->name )
			);
		}

		echo '</section>';
	}

	echo '</aside>';
}
