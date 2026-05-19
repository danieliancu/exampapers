<?php
/**
 * Title: Homepage exam levels
 * Slug: exampapers/homepage-exam-levels
 * Categories: featured
 *
 * @package Exampapers
 */

$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );

$exam_level_url = static function ( $category_name ) use ( $shop_url ) {
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		return $shop_url;
	}

	$term = get_term_by( 'name', $category_name, 'product_cat' );

	if ( ! $term instanceof WP_Term ) {
		return $shop_url;
	}

	return add_query_arg( 'exam_level', $term->slug, $shop_url );
};
?>

<section class="exampapers-page-section">
	<div class="exampapers-section-inner">
		<h2><?php esc_html_e( 'Choose your exam', 'exampapers' ); ?></h2>
		<p><?php esc_html_e( 'Start with the exam level your child is preparing for.', 'exampapers' ); ?></p>
		<div class="exampapers-feature-grid">
			<a class="exampapers-card exampapers-nav-card exampapers-nav-card--featured" href="<?php echo esc_url( $exam_level_url( '11+ Practice Papers' ) ); ?>"><span><?php esc_html_e( '11+', 'exampapers' ); ?></span><small><?php esc_html_e( 'For grammar school entrance preparation, usually taken in Year 6 for Year 7 entry.', 'exampapers' ); ?></small></a>
			<div class="exampapers-card exampapers-nav-card exampapers-nav-card--disabled" aria-disabled="true"><span><?php esc_html_e( 'SATs', 'exampapers' ); ?> <em class="exampapers-coming-soon-pill"><?php esc_html_e( 'Coming soon', 'exampapers' ); ?></em></span><small><?php esc_html_e( 'For Year 6 end-of-primary-school practice in reading, maths and related skills.', 'exampapers' ); ?></small></div>
			<div class="exampapers-card exampapers-nav-card exampapers-nav-card--disabled" aria-disabled="true"><span><?php esc_html_e( 'GCSE', 'exampapers' ); ?> <em class="exampapers-coming-soon-pill"><?php esc_html_e( 'Coming soon', 'exampapers' ); ?></em></span><small><?php esc_html_e( 'For secondary school revision and exam practice, commonly taken in Year 11.', 'exampapers' ); ?></small></div>
		</div>
	</div>
</section>
