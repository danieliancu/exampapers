<?php
/**
 * My Account Dashboard
 *
 * @package Exampapers
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$dashboard_links = array(
	array(
		'label' => __( 'Dashboard', 'exampapers' ),
		'url'   => wc_get_page_permalink( 'myaccount' ),
		'icon'  => 'dashicons-dashboard',
	),
	array(
		'label' => __( 'Orders', 'exampapers' ),
		'url'   => wc_get_endpoint_url( 'orders' ),
		'icon'  => 'dashicons-clipboard',
	),
	array(
		'label' => __( 'Downloads', 'exampapers' ),
		'url'   => wc_get_endpoint_url( 'downloads' ),
		'icon'  => 'dashicons-download',
	),
	array(
		'label' => __( 'Addresses', 'exampapers' ),
		'url'   => wc_get_endpoint_url( 'edit-address' ),
		'icon'  => 'dashicons-location',
	),
	array(
		'label' => __( 'Account details', 'exampapers' ),
		'url'   => wc_get_endpoint_url( 'edit-account' ),
		'icon'  => 'dashicons-admin-users',
	),
	array(
		'label' => __( 'Log out', 'exampapers' ),
		'url'   => wc_logout_url(),
		'icon'  => 'dashicons-migrate',
	),
);
?>

<nav class="exampapers-account-dashboard-links" aria-label="<?php esc_attr_e( 'Account dashboard links', 'exampapers' ); ?>">
	<?php foreach ( $dashboard_links as $dashboard_link ) : ?>
		<a href="<?php echo esc_url( $dashboard_link['url'] ); ?>">
			<span class="dashicons <?php echo esc_attr( $dashboard_link['icon'] ); ?>" aria-hidden="true"></span>
			<span><?php echo esc_html( $dashboard_link['label'] ); ?></span>
		</a>
	<?php endforeach; ?>
</nav>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action( 'woocommerce_account_dashboard' );

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_before_my_account' );

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action( 'woocommerce_after_my_account' );
