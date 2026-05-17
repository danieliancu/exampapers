<?php
/**
 * Title: Homepage hero search
 * Slug: exampapers/homepage-hero-search
 * Categories: featured, call-to-action
 *
 * @package Exampapers
 */

$search_suggestions = array();
$suggestion_sources = array( 'product_cat', 'pa_exam-area', 'pa_subject', 'pa_school' );

foreach ( $suggestion_sources as $taxonomy ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		continue;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'number'     => 30,
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		continue;
	}

	foreach ( $terms as $term ) {
		$search_suggestions[] = $term->name;
	}
}

$search_suggestions = array_values( array_unique( array_filter( $search_suggestions ) ) );
sort( $search_suggestions, SORT_NATURAL | SORT_FLAG_CASE );
?>

<section class="exampapers-hero">
	<div class="exampapers-section-inner">
		<h1><?php esc_html_e( 'Downloadable 11+ mock papers for focused UK exam preparation.', 'exampapers' ); ?></h1>
		<p class="exampapers-lede"><?php esc_html_e( 'Find printable PDF practice papers by exam level, area, subject, format and difficulty.', 'exampapers' ); ?></p>

		<form class="exampapers-search-panel" role="search" method="get" action="<?php echo esc_url( function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/shop/' ) ); ?>" autocomplete="off">
			<label for="exampapers-home-search"><?php esc_html_e( 'Search by school, exam area or subject', 'exampapers' ); ?></label>
			<div class="exampapers-search-panel__row">
				<div class="exampapers-search-autocomplete" data-exampapers-autocomplete>
					<input id="exampapers-home-search" type="search" name="s" autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false" placeholder="<?php esc_attr_e( 'e.g. CSSE, Kent Test, English, Tiffin', 'exampapers' ); ?>" aria-autocomplete="list" aria-controls="exampapers-home-search-suggestions" aria-expanded="false">
					<?php if ( ! empty( $search_suggestions ) ) : ?>
						<ul id="exampapers-home-search-suggestions" class="exampapers-search-suggestions" hidden>
							<?php foreach ( $search_suggestions as $suggestion ) : ?>
								<li><button type="button" data-suggestion="<?php echo esc_attr( $suggestion ); ?>"><?php echo esc_html( $suggestion ); ?></button></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
				<input type="hidden" name="post_type" value="product">
				<button type="submit"><?php esc_html_e( 'Search papers', 'exampapers' ); ?></button>
			</div>
		</form>

		<div class="exampapers-actions">
			<a class="wp-element-button" href="/product-category/11-plus/"><?php esc_html_e( 'Browse 11+ Papers', 'exampapers' ); ?></a>
			<a class="wp-element-button is-style-outline" href="/shop/"><?php esc_html_e( 'Find My Exam Area', 'exampapers' ); ?></a>
			<a class="wp-element-button is-style-outline" href="/product-category/free-samples/"><?php esc_html_e( 'Try Free Sample', 'exampapers' ); ?></a>
		</div>
	</div>
</section>
