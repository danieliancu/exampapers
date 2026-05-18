<?php
/**
 * SEO landing page helpers.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post meta keys used by editable landing pages.
 *
 * @return array<string,string>
 */
function exampapers_landing_meta_keys() {
	return array(
		'is_landing_page'  => '_exampapers_is_landing_page',
		'meta_title'       => '_exampapers_meta_title',
		'meta_description' => '_exampapers_meta_description',
		'intro'            => '_exampapers_intro',
		'what_for'         => '_exampapers_what_for',
		'recommended'      => '_exampapers_recommended',
		'cta_label'        => '_exampapers_cta_label',
		'cta_url'          => '_exampapers_cta_url',
		'disclaimer'       => '_exampapers_disclaimer',
		'faqs'             => '_exampapers_faqs',
		'internal_links'   => '_exampapers_internal_links',
		'exam_level'       => '_exampapers_target_exam_level',
		'exam_area'        => '_exampapers_target_exam_area',
		'exam_style'       => '_exampapers_target_exam_style',
		'subject'          => '_exampapers_target_subject',
		'format'           => '_exampapers_target_format',
		'difficulty'       => '_exampapers_target_difficulty',
		'school'           => '_exampapers_target_school',
		'product_limit'    => '_exampapers_product_limit',
	);
}

/**
 * Term meta keys used for Exam Area to School relationships.
 *
 * @return array<string,string>
 */
function exampapers_area_school_meta_keys() {
	return array(
		'school_terms' => '_exampapers_area_school_terms',
		'sources'      => '_exampapers_area_school_sources',
	);
}

/**
 * Parse a comma-separated admin/meta value.
 *
 * @param mixed $value Value.
 * @return string|string[]
 */
function exampapers_landing_parse_list_value( $value ) {
	if ( is_array( $value ) ) {
		$items = array_values( array_filter( array_map( 'trim', $value ) ) );
		return 1 === count( $items ) ? $items[0] : $items;
	}

	$value = trim( (string) $value );

	if ( '' === $value || false === strpos( $value, ',' ) ) {
		return $value;
	}

	$items = array_values( array_filter( array_map( 'trim', explode( ',', $value ) ) ) );

	return 1 === count( $items ) ? $items[0] : $items;
}

/**
 * Convert FAQ rows from textarea input to structured meta.
 *
 * @param string $value Textarea value.
 * @return array<int,array<string,string>>
 */
function exampapers_landing_parse_faq_rows( $value ) {
	$rows = array();

	foreach ( preg_split( '/\r\n|\r|\n/', (string) $value ) as $line ) {
		$line = trim( $line );

		if ( '' === $line || false === strpos( $line, '|' ) ) {
			continue;
		}

		list( $question, $answer ) = array_map( 'trim', explode( '|', $line, 2 ) );

		if ( '' !== $question && '' !== $answer ) {
			$rows[] = array(
				'question' => sanitize_text_field( $question ),
				'answer'   => sanitize_textarea_field( $answer ),
			);
		}
	}

	return $rows;
}

/**
 * Convert internal-link rows from textarea input to structured meta.
 *
 * @param string $value Textarea value.
 * @return array<int,array<string,string>>
 */
function exampapers_landing_parse_link_rows( $value ) {
	$rows = array();

	foreach ( preg_split( '/\r\n|\r|\n/', (string) $value ) as $line ) {
		$line = trim( $line );

		if ( '' === $line || false === strpos( $line, '|' ) ) {
			continue;
		}

		list( $label, $url ) = array_map( 'trim', explode( '|', $line, 2 ) );

		if ( '' !== $label && '' !== $url ) {
			$rows[] = array(
				'label' => sanitize_text_field( $label ),
				'url'   => esc_url_raw( $url ),
			);
		}
	}

	return $rows;
}

/**
 * Format FAQ rows for textarea editing.
 *
 * @param mixed $rows Rows.
 * @return string
 */
function exampapers_landing_format_faq_rows( $rows ) {
	if ( ! is_array( $rows ) ) {
		return '';
	}

	$lines = array();

	foreach ( $rows as $row ) {
		if ( empty( $row['question'] ) || empty( $row['answer'] ) ) {
			continue;
		}

		$lines[] = $row['question'] . ' | ' . $row['answer'];
	}

	return implode( "\n", $lines );
}

/**
 * Format link rows for textarea editing.
 *
 * @param mixed $rows Rows.
 * @return string
 */
function exampapers_landing_format_link_rows( $rows ) {
	if ( ! is_array( $rows ) ) {
		return '';
	}

	$lines = array();

	foreach ( $rows as $row ) {
		if ( empty( $row['label'] ) || empty( $row['url'] ) ) {
			continue;
		}

		$lines[] = $row['label'] . ' | ' . $row['url'];
	}

	return implode( "\n", $lines );
}

/**
 * Get all landing meta for a page.
 *
 * @param int $post_id Post ID.
 * @return array<string,mixed>
 */
function exampapers_get_landing_page_meta( $post_id ) {
	$keys = exampapers_landing_meta_keys();
	$data = array();

	foreach ( $keys as $name => $key ) {
		$data[ $name ] = get_post_meta( $post_id, $key, true );
	}

	$data['product_limit'] = $data['product_limit'] ? max( 1, (int) $data['product_limit'] ) : 8;

	return $data;
}

/**
 * Get landing data for the current published landing page.
 *
 * @return array<string,mixed>|null
 */
function exampapers_get_current_landing_page_config() {
	if ( ! is_page() ) {
		return null;
	}

	$page = get_queried_object();

	if ( ! $page instanceof WP_Post || 'publish' !== $page->post_status ) {
		return null;
	}

	$keys = exampapers_landing_meta_keys();

	if ( '1' !== (string) get_post_meta( $page->ID, $keys['is_landing_page'], true ) ) {
		return null;
	}

	$meta = exampapers_get_landing_page_meta( $page->ID );

	$meta['post_id']      = $page->ID;
	$meta['slug']         = $page->post_name;
	$meta['h1']           = get_the_title( $page );
	$meta['post_content'] = $page->post_content;
	$meta['query']        = array(
		'exam_level' => $meta['exam_level'],
		'exam_area'  => $meta['exam_area'],
		'exam_style' => $meta['exam_style'],
		'subject'    => $meta['subject'],
		'format'     => $meta['format'],
		'difficulty' => $meta['difficulty'],
		'school'     => $meta['school'],
		'limit'      => $meta['product_limit'],
	);

	return $meta;
}

/**
 * Get schools associated with an exam area term.
 *
 * @param string|string[] $exam_area Exam area name or names.
 * @return array{schools:array<int,WP_Term>,sources:array<int,array<string,string>>}
 */
function exampapers_get_exam_area_schools( $exam_area ) {
	if ( empty( $exam_area ) || ! taxonomy_exists( 'pa_exam-area' ) || ! taxonomy_exists( 'pa_school' ) ) {
		return array(
			'schools' => array(),
			'sources' => array(),
		);
	}

	$areas = is_array( $exam_area ) ? $exam_area : array( $exam_area );
	$keys  = exampapers_area_school_meta_keys();

	$school_ids = array();
	$sources    = array();

	foreach ( $areas as $area_name ) {
		$area_name = trim( (string) $area_name );

		if ( '' === $area_name ) {
			continue;
		}

		$area = get_term_by( 'name', $area_name, 'pa_exam-area' );

		if ( ! $area instanceof WP_Term ) {
			continue;
		}

		$area_school_ids = get_term_meta( $area->term_id, $keys['school_terms'], true );
		$area_sources    = get_term_meta( $area->term_id, $keys['sources'], true );

		if ( is_array( $area_school_ids ) ) {
			$school_ids = array_merge( $school_ids, array_map( 'absint', $area_school_ids ) );
		}

		if ( is_array( $area_sources ) ) {
			$sources = array_merge( $sources, $area_sources );
		}
	}

	$school_ids = array_values( array_unique( array_filter( $school_ids ) ) );

	if ( empty( $school_ids ) ) {
		return array(
			'schools' => array(),
			'sources' => $sources,
		);
	}

	$schools = get_terms(
		array(
			'taxonomy'   => 'pa_school',
			'include'    => $school_ids,
			'hide_empty' => false,
			'orderby'    => 'include',
		)
	);

	return array(
		'schools' => is_wp_error( $schools ) ? array() : $schools,
		'sources' => $sources,
	);
}

/**
 * Build a shop URL filtered by school.
 *
 * @param WP_Term $school School term.
 * @return string
 */
function exampapers_school_shop_filter_url( WP_Term $school ) {
	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

	return add_query_arg(
		array(
			'school' => $school->slug,
		),
		$shop_url
	);
}

/**
 * Normalize landing page URLs for output.
 *
 * @param string $url URL.
 * @return string
 */
function exampapers_landing_url( $url ) {
	$url = trim( (string) $url );

	if ( '' === $url ) {
		return '';
	}

	if ( preg_match( '#^https?://#i', $url ) ) {
		return $url;
	}

	return home_url( $url );
}

/**
 * Use the landing page template for landing Pages.
 *
 * @param string $template Template path.
 * @return string
 */
function exampapers_landing_page_template( $template ) {
	if ( ! exampapers_get_current_landing_page_config() ) {
		return $template;
	}

	$landing_template = locate_template( 'template-parts/landing/landing-page.php' );

	return $landing_template ? $landing_template : $template;
}
add_filter( 'template_include', 'exampapers_landing_page_template' );

/**
 * Set document title for landing pages.
 *
 * @param array<string,string> $parts Title parts.
 * @return array<string,string>
 */
function exampapers_landing_page_title_parts( $parts ) {
	$config = exampapers_get_current_landing_page_config();

	if ( ! $config || empty( $config['meta_title'] ) ) {
		return $parts;
	}

	$parts['title'] = $config['meta_title'];
	unset( $parts['site'] );

	return $parts;
}
add_filter( 'document_title_parts', 'exampapers_landing_page_title_parts' );

/**
 * Print meta description for landing pages.
 */
function exampapers_landing_page_meta_description() {
	$config = exampapers_get_current_landing_page_config();

	if ( ! $config || empty( $config['meta_description'] ) ) {
		return;
	}

	echo '<meta name="description" content="' . esc_attr( $config['meta_description'] ) . '">' . "\n";
}
add_action( 'wp_head', 'exampapers_landing_page_meta_description', 1 );

/**
 * Convert product grid filters to a WooCommerce product query.
 *
 * @param array<string,mixed> $args Product grid arguments.
 * @return array<string,mixed>
 */
function exampapers_product_grid_query_args( array $args ) {
	$taxonomy_map = array(
		'exam_level' => 'pa_exam-level',
		'exam_area'  => 'pa_exam-area',
		'exam_style' => 'pa_exam-style',
		'subject'    => 'pa_subject',
		'format'     => 'pa_format',
		'difficulty' => 'pa_difficulty',
		'school'     => 'pa_school',
		'category'   => 'product_cat',
	);

	$tax_query = array();

	foreach ( $taxonomy_map as $key => $taxonomy ) {
		if ( empty( $args[ $key ] ) || ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		$terms = is_array( $args[ $key ] ) ? $args[ $key ] : array( $args[ $key ] );
		$terms = array_values( array_filter( array_map( 'trim', $terms ) ) );

		if ( empty( $terms ) ) {
			continue;
		}

		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'name',
			'terms'    => $terms,
			'operator' => 'IN',
		);
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	$query_args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => ! empty( $args['limit'] ) ? max( 1, (int) $args['limit'] ) : 8,
		'orderby'             => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	if ( ! empty( $tax_query ) ) {
		$query_args['tax_query'] = $tax_query;
	}

	return $query_args;
}

/**
 * Render a WooCommerce product grid using the existing product card template.
 *
 * @param array<string,mixed> $args Product grid arguments.
 */
function exampapers_render_product_grid( array $args = array() ) {
	get_template_part(
		'template-parts/landing/product-grid',
		null,
		array(
			'query_args' => exampapers_product_grid_query_args( $args ),
		)
	);
}

/**
 * Shortcode wrapper for reusable product grids.
 *
 * @param array<string,string> $atts Shortcode attributes.
 * @return string
 */
function exampapers_products_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'exam_level' => '',
			'exam_area'  => '',
			'exam_style' => '',
			'subject'    => '',
			'format'     => '',
			'difficulty' => '',
			'school'     => '',
			'category'   => '',
			'limit'      => 8,
		),
		$atts,
		'exampapers_products'
	);

	$args = array(
		'limit' => (int) $atts['limit'],
	);

	foreach ( array( 'exam_level', 'exam_area', 'exam_style', 'subject', 'format', 'difficulty', 'school', 'category' ) as $key ) {
		if ( '' !== $atts[ $key ] ) {
			$args[ $key ] = exampapers_landing_parse_list_value( $atts[ $key ] );
		}
	}

	ob_start();
	exampapers_render_product_grid( $args );
	return ob_get_clean();
}
add_shortcode( 'exampapers_products', 'exampapers_products_shortcode' );

/**
 * Add landing page settings meta box.
 */
function exampapers_landing_page_meta_box() {
	add_meta_box(
		'exampapers-landing-page-settings',
		__( 'Exampapers Landing Page', 'exampapers' ),
		'exampapers_landing_page_meta_box_callback',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'exampapers_landing_page_meta_box' );

/**
 * Render a text input for landing meta.
 *
 * @param string $name Field name.
 * @param string $label Field label.
 * @param mixed  $value Field value.
 */
function exampapers_landing_meta_text_input( $name, $label, $value ) {
	if ( is_array( $value ) ) {
		$value = implode( ', ', $value );
	}

	echo '<p><label for="exampapers_' . esc_attr( $name ) . '"><strong>' . esc_html( $label ) . '</strong></label>';
	echo '<input class="widefat" id="exampapers_' . esc_attr( $name ) . '" name="exampapers_landing[' . esc_attr( $name ) . ']" type="text" value="' . esc_attr( (string) $value ) . '"></p>';
}

/**
 * Render a textarea for landing meta.
 *
 * @param string $name Field name.
 * @param string $label Field label.
 * @param mixed  $value Field value.
 * @param string $help Help text.
 */
function exampapers_landing_meta_textarea( $name, $label, $value, $help = '' ) {
	echo '<p><label for="exampapers_' . esc_attr( $name ) . '"><strong>' . esc_html( $label ) . '</strong></label>';
	echo '<textarea class="widefat" id="exampapers_' . esc_attr( $name ) . '" name="exampapers_landing[' . esc_attr( $name ) . ']" rows="4">' . esc_textarea( (string) $value ) . '</textarea>';

	if ( $help ) {
		echo '<span class="description">' . esc_html( $help ) . '</span>';
	}

	echo '</p>';
}

/**
 * Render landing page meta box.
 *
 * @param WP_Post $post Post.
 */
function exampapers_landing_page_meta_box_callback( $post ) {
	$data = exampapers_get_landing_page_meta( $post->ID );

	wp_nonce_field( 'exampapers_landing_page_meta', 'exampapers_landing_page_meta_nonce' );

	echo '<p><label><input type="checkbox" name="exampapers_landing[is_landing_page]" value="1" ' . checked( '1', (string) $data['is_landing_page'], false ) . '> ' . esc_html__( 'Use Exampapers landing page template', 'exampapers' ) . '</label></p>';

	exampapers_landing_meta_text_input( 'meta_title', __( 'Meta title', 'exampapers' ), $data['meta_title'] );
	exampapers_landing_meta_textarea( 'meta_description', __( 'Meta description', 'exampapers' ), $data['meta_description'] );
	exampapers_landing_meta_textarea( 'intro', __( 'Intro text', 'exampapers' ), $data['intro'] );
	exampapers_landing_meta_textarea( 'what_for', __( 'What this page is for', 'exampapers' ), $data['what_for'] );
	exampapers_landing_meta_textarea( 'recommended', __( 'Popular packs / recommended papers', 'exampapers' ), $data['recommended'] );
	exampapers_landing_meta_text_input( 'cta_label', __( 'CTA label', 'exampapers' ), $data['cta_label'] );
	exampapers_landing_meta_text_input( 'cta_url', __( 'CTA URL', 'exampapers' ), $data['cta_url'] );
	exampapers_landing_meta_textarea( 'disclaimer', __( 'Disclaimer', 'exampapers' ), $data['disclaimer'] );
	exampapers_landing_meta_textarea( 'faqs', __( 'FAQ items', 'exampapers' ), exampapers_landing_format_faq_rows( $data['faqs'] ), __( 'One per line: Question | Answer', 'exampapers' ) );
	exampapers_landing_meta_textarea( 'internal_links', __( 'Internal links', 'exampapers' ), exampapers_landing_format_link_rows( $data['internal_links'] ), __( 'One per line: Label | URL', 'exampapers' ) );

	echo '<hr>';
	echo '<h3>' . esc_html__( 'Product grid filters', 'exampapers' ) . '</h3>';
	exampapers_landing_meta_text_input( 'exam_level', __( 'Exam Level', 'exampapers' ), $data['exam_level'] );
	exampapers_landing_meta_text_input( 'exam_area', __( 'Exam Area', 'exampapers' ), $data['exam_area'] );
	exampapers_landing_meta_text_input( 'exam_style', __( 'Exam Style', 'exampapers' ), $data['exam_style'] );
	exampapers_landing_meta_text_input( 'subject', __( 'Subject', 'exampapers' ), $data['subject'] );
	exampapers_landing_meta_text_input( 'format', __( 'Format', 'exampapers' ), $data['format'] );
	exampapers_landing_meta_text_input( 'difficulty', __( 'Difficulty', 'exampapers' ), $data['difficulty'] );
	exampapers_landing_meta_text_input( 'school', __( 'School', 'exampapers' ), $data['school'] );
	exampapers_landing_meta_text_input( 'product_limit', __( 'Product limit', 'exampapers' ), $data['product_limit'] );
}

/**
 * Save landing page meta.
 *
 * @param int $post_id Post ID.
 */
function exampapers_save_landing_page_meta( $post_id ) {
	if ( ! isset( $_POST['exampapers_landing_page_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['exampapers_landing_page_meta_nonce'] ) ), 'exampapers_landing_page_meta' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}

	$input = isset( $_POST['exampapers_landing'] ) && is_array( $_POST['exampapers_landing'] )
		? wp_unslash( $_POST['exampapers_landing'] )
		: array();
	$keys  = exampapers_landing_meta_keys();

	update_post_meta( $post_id, $keys['is_landing_page'], ! empty( $input['is_landing_page'] ) ? '1' : '0' );

	foreach ( array( 'meta_title', 'meta_description', 'intro', 'what_for', 'recommended', 'cta_label', 'cta_url', 'disclaimer' ) as $name ) {
		$value = isset( $input[ $name ] ) ? (string) $input[ $name ] : '';
		$value = 'cta_url' === $name ? esc_url_raw( $value ) : sanitize_textarea_field( $value );
		update_post_meta( $post_id, $keys[ $name ], $value );
	}

	update_post_meta( $post_id, $keys['faqs'], exampapers_landing_parse_faq_rows( isset( $input['faqs'] ) ? (string) $input['faqs'] : '' ) );
	update_post_meta( $post_id, $keys['internal_links'], exampapers_landing_parse_link_rows( isset( $input['internal_links'] ) ? (string) $input['internal_links'] : '' ) );

	foreach ( array( 'exam_level', 'exam_area', 'exam_style', 'subject', 'format', 'difficulty', 'school' ) as $name ) {
		update_post_meta( $post_id, $keys[ $name ], exampapers_landing_parse_list_value( isset( $input[ $name ] ) ? (string) $input[ $name ] : '' ) );
	}

	update_post_meta( $post_id, $keys['product_limit'], isset( $input['product_limit'] ) ? max( 1, (int) $input['product_limit'] ) : 8 );
}
add_action( 'save_post_page', 'exampapers_save_landing_page_meta' );
