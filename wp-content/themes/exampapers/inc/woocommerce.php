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
 * Get product filter dropdown data for homepage and shop forms.
 *
 * @return array<string,mixed>
 */
function exampapers_get_product_filter_dropdown_data() {
	$exam_level_terms = array();
	$exam_area_terms  = array();
	$subject_terms    = array();
	$filter_matches   = array();
	$area_schools     = array();

	if ( taxonomy_exists( 'pa_exam-level' ) ) {
		$include = array_filter(
			array_map(
				static function ( $term_name ) {
					$term = get_term_by( 'name', $term_name, 'pa_exam-level' );
					return $term instanceof WP_Term ? (int) $term->term_id : 0;
				},
				array( '11+', 'SATs', 'GCSE' )
			)
		);

		$terms = get_terms(
			array(
				'taxonomy'   => 'pa_exam-level',
				'hide_empty' => true,
				'orderby'    => 'include',
				'include'    => $include,
			)
		);

		$exam_level_terms = is_wp_error( $terms ) ? array() : $terms;
	}

	if ( taxonomy_exists( 'pa_exam-area' ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'pa_exam-area',
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		$exam_area_terms = is_wp_error( $terms ) ? array() : $terms;

		foreach ( $exam_area_terms as $area_term ) {
			if ( ! $area_term instanceof WP_Term || ! function_exists( 'exampapers_get_exam_area_schools' ) ) {
				continue;
			}

			$school_data = exampapers_get_exam_area_schools( $area_term->name );

			if ( empty( $school_data['schools'] ) || ! is_array( $school_data['schools'] ) ) {
				continue;
			}

			$area_schools[ $area_term->slug ] = array_values(
				array_filter(
					array_map(
						static function ( $school ) {
							if ( ! $school instanceof WP_Term ) {
								return null;
							}

							$url = function_exists( 'exampapers_school_shop_filter_url' ) ? exampapers_school_shop_filter_url( $school ) : get_term_link( $school );

							return array(
								'name' => $school->name,
								'url'  => is_wp_error( $url ) ? '' : $url,
							);
						},
						$school_data['schools']
					)
				)
			);
		}
	}

	if ( taxonomy_exists( 'pa_subject' ) ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'pa_subject',
				'hide_empty' => true,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		$subject_terms = is_wp_error( $terms ) ? array() : $terms;
	}

	if ( taxonomy_exists( 'pa_exam-level' ) && taxonomy_exists( 'pa_exam-area' ) && taxonomy_exists( 'pa_subject' ) ) {
		$product_ids = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		foreach ( $product_ids as $product_id ) {
			$levels   = wp_get_post_terms( (int) $product_id, 'pa_exam-level', array( 'fields' => 'slugs' ) );
			$areas    = wp_get_post_terms( (int) $product_id, 'pa_exam-area', array( 'fields' => 'slugs' ) );
			$subjects = wp_get_post_terms( (int) $product_id, 'pa_subject', array( 'fields' => 'slugs' ) );

			if ( is_wp_error( $levels ) || is_wp_error( $areas ) || is_wp_error( $subjects ) ) {
				continue;
			}

			$filter_matches[] = array(
				'exam_level' => array_values( array_filter( $levels ) ),
				'exam_area'  => array_values( array_filter( $areas ) ),
				'subject'    => array_values( array_filter( $subjects ) ),
			);
		}
	}

	$eleven_plus = taxonomy_exists( 'pa_exam-level' ) ? get_term_by( 'name', '11+', 'pa_exam-level' ) : false;

	return array(
		'exam_levels'              => $exam_level_terms,
		'exam_areas'               => $exam_area_terms,
		'subjects'                 => $subject_terms,
		'matches'                  => $filter_matches,
		'area_schools'             => $area_schools,
		'area_required_level_slug' => $eleven_plus instanceof WP_Term ? $eleven_plus->slug : '11',
	);
}

/**
 * Render product filter dropdowns used by the homepage and shop.
 *
 * @param string $id_prefix Field ID prefix.
 * @param array  $selected Selected slugs.
 * @param bool   $show_area_schools Whether to render the area schools placeholder.
 */
function exampapers_render_product_filter_dropdowns( $id_prefix, array $selected = array(), $show_area_schools = false ) {
	$data = exampapers_get_product_filter_dropdown_data();

	$selected = wp_parse_args(
		$selected,
		array(
			'exam_level' => '',
			'exam_area'  => '',
			'subject'    => '',
		)
	);

	echo '<script type="application/json" data-exampapers-filter-matches>' . wp_json_encode( $data['matches'] ) . '</script>';
	if ( $show_area_schools ) {
		echo '<script type="application/json" data-exampapers-area-schools>' . wp_json_encode( $data['area_schools'] ) . '</script>';
	}

	echo '<div class="exampapers-hero-filters" data-exampapers-area-required-level="' . esc_attr( $data['area_required_level_slug'] ) . '">';

	echo '<div><label for="' . esc_attr( $id_prefix ) . '-exam-level">' . esc_html__( 'Exam Level', 'exampapers' ) . '</label>';
	echo '<select id="' . esc_attr( $id_prefix ) . '-exam-level" name="exam_level">';
	echo '<option value="">' . esc_html__( 'Any level', 'exampapers' ) . '</option>';
	foreach ( $data['exam_levels'] as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		echo '<option value="' . esc_attr( $term->slug ) . '"' . selected( $selected['exam_level'], $term->slug, false ) . '>' . esc_html( $term->name ) . '</option>';
	}
	echo '</select></div>';

	echo '<div><label for="' . esc_attr( $id_prefix ) . '-exam-area">' . esc_html__( 'Exam Area', 'exampapers' ) . '</label>';
	echo '<select id="' . esc_attr( $id_prefix ) . '-exam-area" name="exam_area">';
	echo '<option value="">' . esc_html__( 'Any area', 'exampapers' ) . '</option>';
	foreach ( $data['exam_areas'] as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		echo '<option value="' . esc_attr( $term->slug ) . '"' . selected( $selected['exam_area'], $term->slug, false ) . '>' . esc_html( $term->name ) . '</option>';
	}
	echo '</select>';
	if ( $show_area_schools ) {
		echo '<div class="exampapers-area-schools" data-exampapers-area-schools-output hidden></div>';
	}
	echo '</div>';

	echo '<div><label for="' . esc_attr( $id_prefix ) . '-subject">' . esc_html__( 'Subject', 'exampapers' ) . '</label>';
	echo '<select id="' . esc_attr( $id_prefix ) . '-subject" name="subject">';
	echo '<option value="">' . esc_html__( 'Any subject', 'exampapers' ) . '</option>';
	foreach ( $data['subjects'] as $term ) {
		if ( ! $term instanceof WP_Term ) {
			continue;
		}
		echo '<option value="' . esc_attr( $term->slug ) . '"' . selected( $selected['subject'], $term->slug, false ) . '>' . esc_html( $term->name ) . '</option>';
	}
	echo '</select></div>';

	echo '</div>';
}

/**
 * Apply homepage dropdown filters to the main shop product query.
 *
 * @param WC_Query $query WooCommerce product query.
 */
function exampapers_apply_shop_dropdown_filters( $query ) {
	if ( is_admin() || ! function_exists( 'is_shop' ) || ! is_shop() ) {
		return;
	}

	$filter_map = array(
		'exam_level' => 'pa_exam-level',
		'exam_area'  => 'pa_exam-area',
		'subject'    => 'pa_subject',
		'school'     => 'pa_school',
	);

	$tax_query = (array) $query->get( 'tax_query' );

	foreach ( $filter_map as $param => $taxonomy ) {
		if ( empty( $_GET[ $param ] ) || ! taxonomy_exists( $taxonomy ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			continue;
		}

		$term_slug = sanitize_title( wp_unslash( $_GET[ $param ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		if ( '' === $term_slug || ! get_term_by( 'slug', $term_slug, $taxonomy ) ) {
			continue;
		}

		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => array( $term_slug ),
			'operator' => 'IN',
		);
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	if ( ! empty( $tax_query ) ) {
		$query->set( 'tax_query', $tax_query );
	}
}
add_action( 'woocommerce_product_query', 'exampapers_apply_shop_dropdown_filters' );

/**
 * Render archive filters from WooCommerce attribute archives.
 */
function exampapers_archive_filters() {
	if ( ! function_exists( 'is_shop' ) || ! ( is_shop() || is_product_taxonomy() || ( is_search() && 'product' === get_query_var( 'post_type' ) ) ) ) {
		return;
	}

	echo '<button class="exampapers-filter-toggle" type="button" aria-expanded="false" aria-controls="exampapers-shop-filters">' . esc_html__( 'Filters', 'exampapers' ) . '</button>';
	echo '<aside id="exampapers-shop-filters" class="exampapers-shop-filters" aria-label="' . esc_attr__( 'Product filters', 'exampapers' ) . '">';

	$selected = array(
		'exam_level' => isset( $_GET['exam_level'] ) ? sanitize_title( wp_unslash( $_GET['exam_level'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		'exam_area'  => isset( $_GET['exam_area'] ) ? sanitize_title( wp_unslash( $_GET['exam_area'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		'subject'    => isset( $_GET['subject'] ) ? sanitize_title( wp_unslash( $_GET['subject'] ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	);

	echo '<form role="search" method="get" action="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '" class="exampapers-product-search" data-exampapers-dependent-filters>';
	echo '<label>' . esc_html__( 'Search papers', 'exampapers' ) . '</label>';

	exampapers_render_product_filter_dropdowns( 'exampapers-shop', $selected );

	echo '<button type="submit">' . esc_html__( 'Search', 'exampapers' ) . '</button>';
	echo '</form>';

	echo '</aside>';
}
