<?php
/**
 * Title: Homepage best sellers
 * Slug: exampapers/homepage-best-sellers
 * Categories: featured, products
 *
 * @package Exampapers
 */
?>

<section class="exampapers-page-section exampapers-section-white">
	<div class="exampapers-section-inner">
		<h2><?php esc_html_e( 'Best-selling packs', 'exampapers' ); ?></h2>
		<?php echo do_shortcode( '[products limit="4" columns="4" orderby="popularity"]' ); ?>
	</div>
</section>
