<?php
/**
 * SEO-related theme helpers.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print archive SEO support content after product grids.
 */
function exampapers_archive_seo_content() {
	if ( ! function_exists( 'is_product_taxonomy' ) || ! ( is_shop() || is_product_taxonomy() ) ) {
		return;
	}

	echo '<section class="exampapers-archive-seo">';

	if ( is_product_taxonomy() ) {
		$term = get_queried_object();

		if ( $term instanceof WP_Term ) {
			printf(
				'<h2>%1$s</h2><p>%2$s</p>',
				esc_html( sprintf( __( 'About %s exam papers', 'exampapers' ), $term->name ) ),
				esc_html__( 'Use this landing page to compare relevant downloadable practice papers, formats, subjects and difficulty levels.', 'exampapers' )
			);
		}
	} else {
		echo '<h2>' . esc_html__( 'Find the right exam papers', 'exampapers' ) . '</h2>';
		echo '<p>' . esc_html__( 'Browse printable PDF mock papers and practice packs by exam level, exam area, subject, format and difficulty.', 'exampapers' ) . '</p>';
	}

	echo '<div class="exampapers-faq-list">';
	echo '<details><summary>' . esc_html__( 'Which 11+ paper should I choose?', 'exampapers' ) . '</summary><p>' . esc_html__( "Start with your exam area or school guidance, then choose subject and difficulty based on your child's current preparation stage.", 'exampapers' ) . '</p></details>';
	echo '<details><summary>' . esc_html__( 'Are the papers downloadable?', 'exampapers' ) . '</summary><p>' . esc_html__( 'Products are prepared for virtual downloadable delivery through WooCommerce.', 'exampapers' ) . '</p></details>';
	echo '</div>';

	exampapers_term_links( 'product_cat' );

	echo '</section>';
}
