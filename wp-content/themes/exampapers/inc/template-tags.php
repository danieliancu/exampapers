<?php
/**
 * Small template helpers.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print a badge.
 *
 * @param string $label Badge text.
 * @param string $class Optional modifier class.
 */
function exampapers_badge( $label, $class = '' ) {
	if ( '' === trim( (string) $label ) ) {
		return;
	}

	printf(
		'<span class="exampapers-badge %1$s">%2$s</span>',
		esc_attr( $class ),
		esc_html( $label )
	);
}

/**
 * Print a list of internal links when terms exist.
 *
 * @param string $taxonomy Taxonomy name.
 * @param int    $limit Number of links.
 */
function exampapers_term_links( $taxonomy, $limit = 8 ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'number'     => absint( $limit ),
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return;
	}

	echo '<nav class="exampapers-link-list" aria-label="' . esc_attr__( 'Related exam links', 'exampapers' ) . '">';

	foreach ( $terms as $term ) {
		$link = get_term_link( $term );

		if ( is_wp_error( $link ) ) {
			continue;
		}

		printf( '<a href="%1$s">%2$s</a>', esc_url( $link ), esc_html( $term->name ) );
	}

	echo '</nav>';
}

/**
 * Return the cart item count.
 *
 * @return int
 */
function exampapers_cart_count() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return 0;
	}

	return count( WC()->cart->get_cart() );
}

/**
 * Render the account icon link.
 */
function exampapers_account_link() {
	?>
	<div class="wp-block-woocommerce-customer-account exampapers-account-link">
		<a class="wc-block-customer-account__link" href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>" aria-label="<?php esc_attr_e( 'My Account', 'exampapers' ); ?>">
			<svg class="wc-block-customer-account__account-icon" viewBox="1 1 29 29" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
				<circle cx="16" cy="10.5" r="3.5" stroke="currentColor" stroke-width="2" fill="none"></circle>
				<path fill-rule="evenodd" clip-rule="evenodd" d="M11.5 18.5H20.5C21.8807 18.5 23 19.6193 23 21V25.5H25V21C25 18.5147 22.9853 16.5 20.5 16.5H11.5C9.01472 16.5 7 18.5147 7 21V25.5H9V21C9 19.6193 10.1193 18.5 11.5 18.5Z" fill="currentColor"></path>
			</svg>
		</a>
	</div>
	<?php
}

/**
 * Render the cart icon link.
 */
function exampapers_cart_link() {
	$count = exampapers_cart_count();
	?>
	<div class="wc-block-mini-cart wp-block-woocommerce-mini-cart exampapers-mini-cart">
		<a class="wc-block-mini-cart__button" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Number of items in the cart: %d', 'exampapers' ), $count ) ); ?>">
			<span class="wc-block-mini-cart__quantity-badge">
				<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="wc-block-mini-cart__icon" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
					<circle cx="12.667" cy="24.667" r="2"></circle>
					<circle cx="23.333" cy="24.667" r="2"></circle>
					<path fill-rule="evenodd" d="M9.285 10.036a1 1 0 0 1 .776-.37h15.272a1 1 0 0 1 .99 1.142l-1.333 9.333A1 1 0 0 1 24 21H12a1 1 0 0 1-.98-.797L9.083 10.87a1 1 0 0 1 .203-.834m2.005 1.63L12.814 19h10.319l1.047-7.333z" clip-rule="evenodd"></path>
					<path fill-rule="evenodd" d="M5.667 6.667a1 1 0 0 1 1-1h2.666a1 1 0 0 1 .984.82l.727 4a1 1 0 1 1-1.967.359l-.578-3.18H6.667a1 1 0 0 1-1-1" clip-rule="evenodd"></path>
				</svg>

				<?php if ( $count > 0 ) : ?>
					<span class="wc-block-mini-cart__badge"><?php echo esc_html( (string) $count ); ?></span>
				<?php endif; ?>
			</span>
		</a>
	</div>
	<?php
}

/**
 * Render a header navigation link with an active state.
 *
 * @param string $url Link URL.
 * @param string $label Link label.
 * @param bool   $is_current Whether the link points to the current view.
 */
function exampapers_header_nav_link( $url, $label, $is_current = false ) {
	printf(
		'<a href="%1$s"%2$s>%3$s</a>',
		esc_url( $url ),
		$is_current ? ' aria-current="page"' : '',
		esc_html( $label )
	);
}

/**
 * Check whether the current product category is a slug or one of its children.
 *
 * @param string $slug Product category slug.
 * @return bool
 */
function exampapers_is_product_category_or_child( $slug ) {
	if ( ! is_tax( 'product_cat' ) ) {
		return false;
	}

	$term = get_queried_object();

	if ( ! $term instanceof WP_Term ) {
		return false;
	}

	if ( $slug === $term->slug ) {
		return true;
	}

	$parent = get_term_by( 'slug', $slug, 'product_cat' );

	return $parent instanceof WP_Term && in_array( $parent->term_id, get_ancestors( $term->term_id, 'product_cat' ), true );
}

/**
 * Render the complete site header.
 */
function exampapers_site_header() {
	?>
	<header class="exampapers-header">
		<div class="exampapers-header__inner">
			<div class="exampapers-brand">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php bloginfo( 'name' ); ?>
				</a>
			</div>

			<button class="exampapers-nav-toggle" aria-expanded="false" aria-controls="exampapers-main-nav" aria-label="<?php esc_attr_e( 'Open menu', 'exampapers' ); ?>">
				<span></span><span></span><span></span>
			</button>

			<nav id="exampapers-main-nav" class="exampapers-main-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'exampapers' ); ?>">
				<?php
				exampapers_header_nav_link( home_url( '/shop/' ), __( 'Shop', 'exampapers' ), function_exists( 'is_shop' ) && is_shop() );
				exampapers_header_nav_link( home_url( '/product-category/11-plus/' ), __( '11+ Areas', 'exampapers' ), exampapers_is_product_category_or_child( '11-plus' ) );
				exampapers_header_nav_link( home_url( '/product-category/free-samples/' ), __( 'Free Samples', 'exampapers' ), exampapers_is_product_category_or_child( 'free-samples' ) );
				exampapers_header_nav_link( home_url( '/blog/' ), __( 'Parent Guide', 'exampapers' ), ! is_front_page() && ( is_home() || is_singular( 'post' ) || is_category() || is_tag() ) );
				?>
			</nav>

			<div class="exampapers-header-actions">
				<?php exampapers_account_link(); ?>
				<?php exampapers_cart_link(); ?>
			</div>
		</div>
	</header>
	<?php
}

/**
 * Render the complete site footer.
 */
function exampapers_site_footer() {
	?>
	<footer class="exampapers-footer">
		<div class="exampapers-footer__inner">
			<section>
				<h3><?php esc_html_e( 'Exampapers', 'exampapers' ); ?></h3>
				<p><?php esc_html_e( 'Downloadable exam practice papers for UK school entrance preparation. Exampapers Ltd is independent and is not official or affiliated with any exam board, school, local authority or admissions body.', 'exampapers' ); ?></p>
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
				<h3><?php esc_html_e( 'Legal', 'exampapers' ); ?></h3>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/terms-and-conditions/' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'exampapers' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'exampapers' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/refund-digital-download-policy/' ) ); ?>"><?php esc_html_e( 'Refund / Digital Download Policy', 'exampapers' ); ?></a></li>
					<li><a href="<?php echo esc_url( home_url( '/cookie-notice/' ) ); ?>"><?php esc_html_e( 'Cookie notice', 'exampapers' ); ?></a></li>
				</ul>
			</section>

			<section>
				<h3><?php esc_html_e( 'Guidance', 'exampapers' ); ?></h3>
				<p><?php esc_html_e( 'Always check current admission guidance from your exam area, school or local authority before preparing.', 'exampapers' ); ?></p>
				<p><?php esc_html_e( 'Secure payments are handled through Stripe, PayPal and WooCommerce checkout integrations.', 'exampapers' ); ?></p>
			</section>
		</div>
	</footer>
	<?php
}

/**
 * Replace inherited block-theme header template parts with the PHP header.
 *
 * @param string $block_content Rendered block content.
 * @param array  $block Parsed block.
 * @return string
 */
function exampapers_replace_header_template_part( $block_content, $block ) {
	if ( empty( $block['attrs']['slug'] ) || 'header' !== $block['attrs']['slug'] ) {
		return $block_content;
	}

	ob_start();
	exampapers_site_header();

	return ob_get_clean();
}
add_filter( 'render_block_core/template-part', 'exampapers_replace_header_template_part', 10, 2 );

/**
 * Replace inherited block-theme footer template parts with the PHP footer.
 *
 * @param string $block_content Rendered block content.
 * @param array  $block Parsed block.
 * @return string
 */
function exampapers_replace_footer_template_part( $block_content, $block ) {
	if ( empty( $block['attrs']['slug'] ) || 'footer' !== $block['attrs']['slug'] ) {
		return $block_content;
	}

	ob_start();
	exampapers_site_footer();

	return ob_get_clean();
}
add_filter( 'render_block_core/template-part', 'exampapers_replace_footer_template_part', 10, 2 );
