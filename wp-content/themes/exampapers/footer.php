<?php
/**
 * Classic footer wrapper for WooCommerce PHP templates.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<footer class="exampapers-footer">
		<div class="exampapers-footer__inner">
			<section>
				<h3><?php bloginfo( 'name' ); ?></h3>
				<p><?php esc_html_e( 'Downloadable exam practice papers for UK school entrance preparation.', 'exampapers' ); ?></p>
			</section>

			<section>
				<h3><?php esc_html_e( 'Shop', 'exampapers' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'All papers', 'exampapers' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/product-category/free-samples/' ) ); ?>"><?php esc_html_e( 'Free samples', 'exampapers' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/cart/' ) ); ?>"><?php esc_html_e( 'Cart', 'exampapers' ); ?></a></li>
				</ul>
			</section>

			<section>
				<h3><?php esc_html_e( 'Guidance', 'exampapers' ); ?></h3>
				<p><?php esc_html_e( 'Always check current admission guidance from your exam area, school or local authority before preparing.', 'exampapers' ); ?></p>
			</section>
		</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>
