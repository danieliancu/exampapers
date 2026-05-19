<?php
/**
 * Title: Homepage popular areas
 * Slug: exampapers/homepage-popular-areas
 * Categories: featured
 *
 * @package Exampapers
 */

$area_terms = array();

if ( taxonomy_exists( 'product_cat' ) ) {
	$parent_category = get_term_by( 'name', '11+ Practice Papers', 'product_cat' );

	if ( $parent_category instanceof WP_Term ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'parent'     => (int) $parent_category->term_id,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'number'     => 8,
			)
		);

		$area_terms = is_wp_error( $terms ) ? array() : $terms;
	}
}
?>

<section class="exampapers-page-section exampapers-section-white">
	<div class="exampapers-section-inner">
		<h2><?php esc_html_e( 'Choose your 11+ area', 'exampapers' ); ?></h2>
		<p><?php esc_html_e( 'Go straight to papers for the exam area your child is preparing for.', 'exampapers' ); ?></p>
		<nav class="exampapers-feature-grid exampapers-area-grid" aria-label="<?php esc_attr_e( 'Popular 11+ areas', 'exampapers' ); ?>">
			<?php foreach ( $area_terms as $term ) : ?>
				<?php
				if ( ! $term instanceof WP_Term ) {
					continue;
				}

				$link = add_query_arg( 'exam_area', $term->slug, function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) );
				?>
				<a class="exampapers-card exampapers-nav-card" href="<?php echo esc_url( $link ); ?>">
					<span><?php echo esc_html( $term->name ); ?></span>
				</a>
			<?php endforeach; ?>
		</nav>
		<div class="exampapers-actions exampapers-actions--center">
			<a class="wp-element-button is-style-outline" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'See more', 'exampapers' ); ?></a>
		</div>
	</div>
</section>
