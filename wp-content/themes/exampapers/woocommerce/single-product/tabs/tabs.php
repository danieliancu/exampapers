<?php
/**
 * Single Product tabs as accessible details panels.
 *
 * @package Exampapers
 * @version 9.8.0
 */

defined( 'ABSPATH' ) || exit;

$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );

if ( empty( $product_tabs ) ) {
	return;
}
?>

<section class="woocommerce-tabs wc-tabs-wrapper exampapers-tabs" aria-label="<?php esc_attr_e( 'Product information', 'exampapers' ); ?>">
	<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
		<details class="exampapers-tab exampapers-tab--<?php echo esc_attr( $key ); ?>" <?php echo 'description' === $key ? 'open' : ''; ?>>
			<summary>
				<?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $product_tab['title'], $key ) ); ?>
			</summary>
			<div class="woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?> panel entry-content wc-tab">
				<?php
				if ( isset( $product_tab['callback'] ) ) {
					call_user_func( $product_tab['callback'], $key, $product_tab );
				}
				?>
			</div>
		</details>
	<?php endforeach; ?>

	<?php do_action( 'woocommerce_product_after_tabs' ); ?>
</section>
