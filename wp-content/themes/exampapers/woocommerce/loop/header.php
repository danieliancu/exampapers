<?php
/**
 * Product taxonomy archive header.
 *
 * @package Exampapers
 * @version 8.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="woocommerce-products-header">
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
		<h6 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h6>
	<?php endif; ?>

	<?php do_action( 'woocommerce_archive_description' ); ?>
</header>
