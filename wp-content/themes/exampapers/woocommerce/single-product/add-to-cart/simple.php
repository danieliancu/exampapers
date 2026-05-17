<?php
/**
 * Simple product add to cart.
 *
 * @package Exampapers
 * @version 10.2.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

echo wc_get_stock_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

if ( $product->is_in_stock() ) :
	$in_cart        = ( $product->is_virtual() || $product->is_downloadable() ) && function_exists( 'exampapers_product_is_in_cart' ) && exampapers_product_is_in_cart( $product->get_id() );
	$button_classes = 'single_add_to_cart_button button alt';
	$button_class   = wc_wp_theme_get_element_class_name( 'button' );

	if ( $button_class ) {
		$button_classes .= ' ' . $button_class;
	}

	if ( $in_cart ) {
		$button_classes .= ' exampapers-in-cart disabled';
	}
	?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );

		woocommerce_quantity_input(
			array(
				'min_value'   => $product->get_min_purchase_quantity(),
				'max_value'   => $product->get_max_purchase_quantity(),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // phpcs:ignore WordPress.Security.NonceVerification.Missing
			)
		);

		do_action( 'woocommerce_after_add_to_cart_quantity' );
		?>

		<button
			type="submit"
			name="add-to-cart"
			value="<?php echo esc_attr( $product->get_id() ); ?>"
			class="<?php echo esc_attr( $button_classes ); ?>"
			data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
			<?php echo $in_cart ? 'data-in-cart="true" disabled aria-disabled="true"' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
