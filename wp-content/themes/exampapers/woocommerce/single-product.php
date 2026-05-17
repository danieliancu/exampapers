<?php
/**
 * Single product template.
 *
 * @package Exampapers
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>

<main id="primary" class="exampapers-product-shell">
	<?php
	woocommerce_breadcrumb();

	do_action( 'woocommerce_before_single_product' );

	while ( have_posts() ) :
		the_post();
		wc_get_template_part( 'content', 'single-product' );
	endwhile;

	do_action( 'woocommerce_after_single_product' );
	?>
</main>

<?php
get_footer( 'shop' );
