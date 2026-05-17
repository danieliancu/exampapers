<?php
/**
 * Reusable product card markup.
 *
 * @package Exampapers
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
	return;
}

?>

<li <?php wc_product_class( 'exampapers-product-card', $product ); ?>>
	<a class="exampapers-product-card-link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'exampapers' ), get_the_title() ) ); ?>">
		<span class="exampapers-product-card-media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php echo woocommerce_get_product_thumbnail(); ?>
			<?php else : ?>
				<span class="exampapers-product-placeholder" aria-hidden="true">PDF</span>
			<?php endif; ?>
		</span>

		<?php exampapers_product_badges( $product ); ?>

		<h2 class="woocommerce-loop-product__title exampapers-product-card-title"><?php the_title(); ?></h2>

		<p class="exampapers-product-card-excerpt">
			<?php
			echo wp_kses_post(
				wp_trim_words(
					$product->get_short_description() ? $product->get_short_description() : $product->get_description(),
					24
				)
			);
			?>
		</p>

		<?php exampapers_product_card_attributes( $product ); ?>
	</a>

	<div class="exampapers-product-card-actions">
		<?php woocommerce_template_loop_price(); ?>
		<?php woocommerce_template_loop_add_to_cart(); ?>
	</div>
</li>
