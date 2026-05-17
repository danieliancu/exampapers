<?php
/**
 * Product attribute helpers.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attribute taxonomy map used by the theme.
 *
 * @return array<string,string>
 */
function exampapers_product_attribute_map() {
	return array(
		'exam_level' => 'pa_exam-level',
		'exam_area'  => 'pa_exam-area',
		'subject'    => 'pa_subject',
		'format'     => 'pa_format',
		'difficulty' => 'pa_difficulty',
		'school'     => 'pa_school',
	);
}

/**
 * Get product attribute labels.
 *
 * @param WC_Product|false $product Product instance.
 * @param string           $key Attribute key from exampapers_product_attribute_map().
 * @return array<int,string>
 */
function exampapers_get_product_attribute_terms( $product, $key ) {
	if ( ! $product instanceof WC_Product ) {
		return array();
	}

	$map = exampapers_product_attribute_map();

	if ( empty( $map[ $key ] ) || ! taxonomy_exists( $map[ $key ] ) ) {
		return array();
	}

	$terms = wc_get_product_terms(
		$product->get_id(),
		$map[ $key ],
		array(
			'fields' => 'names',
		)
	);

	return is_wp_error( $terms ) ? array() : array_values( array_filter( $terms ) );
}

/**
 * Print compact product attributes for cards.
 *
 * @param WC_Product|false $product Product instance.
 */
function exampapers_product_card_attributes( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$items = array();

	foreach ( array( 'exam_level', 'exam_area', 'subject', 'difficulty' ) as $key ) {
		$terms = exampapers_get_product_attribute_terms( $product, $key );

		if ( ! empty( $terms ) ) {
			$items[] = $terms[0];
		}
	}

	if ( empty( $items ) ) {
		return;
	}

	echo '<ul class="exampapers-product-meta-list">';
	foreach ( array_slice( $items, 0, 4 ) as $item ) {
		echo '<li>' . esc_html( $item ) . '</li>';
	}
	echo '</ul>';
}

/**
 * Print standard downloadable-product badges.
 *
 * @param WC_Product|false $product Product instance.
 */
function exampapers_product_badges( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	echo '<div class="exampapers-product-badges">';

	if ( $product->is_downloadable() || $product->is_virtual() ) {
		exampapers_badge( __( 'PDF Download', 'exampapers' ) );
		exampapers_badge( __( 'Instant Access', 'exampapers' ), 'exampapers-badge--green' );
	}

	exampapers_badge( __( 'Answers Included', 'exampapers' ), 'exampapers-badge--cyan' );

	echo '</div>';
}

/**
 * Print product detail sections from existing product content and attributes.
 *
 * @param WC_Product|false $product Product instance.
 */
function exampapers_product_info_sections( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$subjects = exampapers_get_product_attribute_terms( $product, 'subject' );
	$levels   = exampapers_get_product_attribute_terms( $product, 'exam_level' );
	$areas    = exampapers_get_product_attribute_terms( $product, 'exam_area' );
	$formats  = exampapers_get_product_attribute_terms( $product, 'format' );

	echo '<section class="exampapers-product-sections" aria-label="' . esc_attr__( 'Product details', 'exampapers' ) . '">';

	echo '<article class="exampapers-card"><h2>' . esc_html__( 'What is included', 'exampapers' ) . '</h2>';
	echo '<p>' . esc_html__( 'A downloadable practice paper pack designed for focused exam preparation. Product downloads, answers and supporting files are managed from WooCommerce product data.', 'exampapers' ) . '</p></article>';

	echo '<article class="exampapers-card"><h2>' . esc_html__( 'Who this is for', 'exampapers' ) . '</h2><p>';
	echo esc_html__( 'Suitable for pupils preparing for', 'exampapers' ) . ' ';
	echo esc_html( implode( ', ', array_filter( array_merge( $levels, $areas ) ) ) ?: __( 'school entrance exams', 'exampapers' ) );
	echo '.</p></article>';

	echo '<article class="exampapers-card"><h2>' . esc_html__( 'Skills covered', 'exampapers' ) . '</h2>';
	if ( ! empty( $subjects ) ) {
		echo '<ul>';
		foreach ( $subjects as $subject ) {
			echo '<li>' . esc_html( $subject ) . '</li>';
		}
		echo '</ul>';
	} else {
		echo '<p>' . esc_html__( 'Skills vary by paper and are shown in the product description and attributes.', 'exampapers' ) . '</p>';
	}
	echo '</article>';

	echo '<article class="exampapers-card"><h2>' . esc_html__( 'How it works', 'exampapers' ) . '</h2><ol>';
	echo '<li>' . esc_html__( 'Add the paper pack to your cart.', 'exampapers' ) . '</li>';
	echo '<li>' . esc_html__( 'Complete checkout securely.', 'exampapers' ) . '</li>';
	echo '<li>' . esc_html__( 'Download the PDF files instantly from your account and order email.', 'exampapers' ) . '</li>';
	echo '</ol>';
	if ( ! empty( $formats ) ) {
		echo '<p class="exampapers-muted">' . esc_html__( 'Format:', 'exampapers' ) . ' ' . esc_html( implode( ', ', $formats ) ) . '</p>';
	}
	echo '</article>';

	echo '<article class="exampapers-card exampapers-product-faq-card"><h2>' . esc_html__( 'FAQ', 'exampapers' ) . '</h2>';
	echo '<details><summary>' . esc_html__( 'Is this a physical product?', 'exampapers' ) . '</summary><p>' . esc_html__( 'No. Products are intended to be virtual, downloadable PDF resources unless stated otherwise.', 'exampapers' ) . '</p></details>';
	echo '<details><summary>' . esc_html__( 'Are answers included?', 'exampapers' ) . '</summary><p>' . esc_html__( 'Answer files should be included with each paid pack where noted in the product data.', 'exampapers' ) . '</p></details>';
	echo '</article>';

	echo '<aside class="exampapers-disclaimer">' . esc_html__( 'Disclaimer: 11+ exam formats and requirements differ by area, school and admission year. Always check the latest guidance from the relevant school, consortium or local authority.', 'exampapers' ) . '</aside>';

	echo '</section>';
}
