<?php
/**
 * Title: Homepage hero search
 * Slug: exampapers/homepage-hero-search
 * Categories: featured, call-to-action
 *
 * @package Exampapers
 */

?>

<section class="exampapers-hero">
	<div class="exampapers-section-inner">
		<h1><?php esc_html_e( '11+ Practice Papers & Mock Exams', 'exampapers' ); ?></h1>
		<p class="exampapers-lede"><?php esc_html_e( 'Download printable PDF packs by exam area, subject and level.', 'exampapers' ); ?></p>

		<form class="exampapers-search-panel" role="search" method="get" action="<?php echo esc_url( function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' ) ); ?>" data-exampapers-dependent-filters>
			<label><?php esc_html_e( 'Search by school, exam area or subject', 'exampapers' ); ?></label>
			<div class="exampapers-search-panel__row">
				<?php
				if ( function_exists( 'exampapers_render_product_filter_dropdowns' ) ) {
					exampapers_render_product_filter_dropdowns( 'exampapers-home' );
				}
				?>
				<button type="submit"><?php esc_html_e( 'Search papers', 'exampapers' ); ?></button>
			</div>
		</form>

		<div class="exampapers-actions">
			<a class="wp-element-button" href="/11-plus-practice-papers/"><?php esc_html_e( 'Browse 11+ Papers', 'exampapers' ); ?></a>
			<a class="wp-element-button is-style-outline" href="/csse-essex-11-plus-practice-papers/"><?php esc_html_e( 'Find My Exam Area', 'exampapers' ); ?></a>
			<a class="wp-element-button is-style-outline" href="/product-category/free-samples/"><?php esc_html_e( 'Try Free Sample', 'exampapers' ); ?></a>
		</div>
	</div>
</section>
