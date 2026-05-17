<?php
/**
 * Title: Homepage free samples
 * Slug: exampapers/homepage-free-samples
 * Categories: featured, products
 *
 * @package Exampapers
 */
?>

<section class="exampapers-page-section">
	<div class="exampapers-section-inner">
		<h2><?php esc_html_e( 'Free samples', 'exampapers' ); ?></h2>
		<p><?php esc_html_e( 'Let parents try the style before buying a full pack.', 'exampapers' ); ?></p>
		<?php echo do_shortcode( '[products limit="4" columns="4" category="free-samples"]' ); ?>
	</div>
</section>
