<?php
/**
 * Legal page seeder for Exampapers.
 *
 * Browser:
 *   1. Log in as an administrator.
 *   2. Open /wp-content/themes/exampapers/tools/seed-legal-pages.php?run=1
 *
 * CLI:
 *   php wp-content/themes/exampapers/tools/seed-legal-pages.php --run
 *
 * @package Exampapers
 */

define( 'SHORTINIT', false );

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';

if ( ! file_exists( $wp_load ) ) {
	exit( 'Could not find wp-load.php.' . PHP_EOL );
}

require_once $wp_load;

$is_cli = 'cli' === PHP_SAPI;
$is_run = $is_cli
	? in_array( '--run', $argv, true )
	: ( ! empty( $_GET['run'] ) && '1' === (string) $_GET['run'] );

if ( ! $is_cli ) {
	if ( ! is_user_logged_in() || ! current_user_can( 'edit_pages' ) ) {
		wp_die( esc_html__( 'You must be logged in as an administrator with page editing access.', 'exampapers' ) );
	}

	if ( ! $is_run ) {
		wp_die( esc_html__( 'Add ?run=1 to seed legal pages.', 'exampapers' ) );
	}
} elseif ( ! $is_run ) {
	exit( 'Add --run to seed legal pages.' . PHP_EOL );
}

/**
 * Print one line for CLI or browser.
 *
 * @param string $message Message.
 */
function exampapers_seed_legal_line( $message ) {
	if ( 'cli' === PHP_SAPI ) {
		echo $message . PHP_EOL;
		return;
	}

	echo '<li>' . esc_html( $message ) . '</li>';
}

$updated_date = gmdate( 'j F Y' );

$legal_pages = array(
	array(
		'title'   => 'Terms & Conditions',
		'slug'    => 'terms-and-conditions',
		'content' => '<!-- wp:paragraph --><p>Last updated: ' . esc_html( $updated_date ) . '</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>These Terms & Conditions apply to the Exampapers Ltd website, products and downloadable digital resources. By browsing the website, creating an account, placing an order or downloading a file, you agree to these terms.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>About Exampapers Ltd</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Exampapers Ltd provides downloadable educational practice materials for pupils and families preparing for UK school entrance and related exams.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>Exampapers Ltd is independent. Our products are not official exam papers and we are not affiliated with, endorsed by, sponsored by or approved by any exam board, school, grammar school consortium, local authority, admissions body or publisher unless this is expressly stated in writing.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Digital products and licence</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Products sold on this website are normally digital downloads, such as PDF practice papers, answer files or supporting materials. When you buy a product, you receive a personal, non-exclusive, non-transferable licence to use the files for private home, pupil or family study.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>You must not resell, share publicly, upload, publish, copy for commercial use, distribute through a school, tutoring centre or online group, or remove any copyright notices from the materials without written permission from Exampapers Ltd.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Orders, account access and downloads</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>After payment, download access is normally provided through your order confirmation email and your website account. You are responsible for entering the correct email address and keeping your account login secure.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>If a technical issue prevents access to a purchased file, contact us with your order number so we can investigate and, where appropriate, restore access or provide a replacement file.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Prices, taxes and payment</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Prices are shown on the product and checkout pages. We may change prices, product availability, discounts or product descriptions from time to time. Secure payments are processed through Stripe, PayPal and WooCommerce checkout integrations. Exampapers Ltd does not store full card details on the website.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Educational use and results</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Our materials are designed for practice and revision. They do not guarantee admission, exam success, a particular score or suitability for every school, exam board or admissions route. Always check current guidance from the relevant school, exam area, local authority or admissions body.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Website availability and errors</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>We aim to keep the website and downloads available, accurate and secure, but access may occasionally be interrupted for maintenance, hosting issues or events outside our control. We may correct errors, update files or withdraw products where needed.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Limitation of liability</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Nothing in these terms excludes liability that cannot legally be excluded. To the fullest extent permitted by law, Exampapers Ltd is not responsible for indirect loss, loss of opportunity, loss of data, loss of profit or decisions made based on educational materials purchased from the website.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Changes to these terms</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>We may update these terms when our products, services, payment options or legal requirements change. The updated version will apply from the date shown on this page.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'Privacy Policy',
		'slug'    => 'privacy-policy',
		'content' => '<!-- wp:paragraph --><p>Last updated: ' . esc_html( $updated_date ) . '</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>This Privacy Policy explains how Exampapers Ltd collects, uses and protects personal data when you use our website, place an order, create an account, contact us or download digital products.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Who we are</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Exampapers Ltd provides downloadable educational practice resources. Exampapers Ltd is independent and is not official or affiliated with any exam board, school, local authority or admissions body.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Information we collect</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>We may collect your name, email address, billing details, order details, account login details, download history, customer support messages, IP address, browser information, device information and website usage information.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>When you pay by card or PayPal, payment information is handled by Stripe, PayPal and WooCommerce checkout integrations. Exampapers Ltd does not store full card numbers on the website.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>How we use personal data</h2><!-- /wp:heading -->
<!-- wp:list --><ul><li>To process orders and provide access to digital downloads.</li><li>To create and manage customer accounts.</li><li>To send order confirmations, receipts and service messages.</li><li>To respond to customer support requests.</li><li>To prevent fraud, abuse and unauthorised access.</li><li>To maintain website security and improve website performance.</li><li>To meet tax, accounting and legal obligations.</li></ul><!-- /wp:list -->
<!-- wp:heading --><h2>Legal bases</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>We process personal data where it is needed to perform a contract with you, where we have a legal obligation, where we have a legitimate interest in operating and protecting the website, or where you have given consent.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Sharing data</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>We may share relevant data with service providers that help run the website and fulfil orders, including hosting providers, WooCommerce, Stripe, PayPal, email delivery tools, analytics tools, fraud-prevention tools, accountants and professional advisers.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>We do not sell personal data.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>How long we keep data</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Order and account records are kept for as long as needed to provide downloads, support customers, meet accounting requirements and resolve disputes. Support messages and technical logs are kept only for as long as reasonably needed for the purpose collected.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Your rights</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Depending on your location and the circumstances, you may have rights to access, correct, delete, restrict or object to use of your personal data, and to request a copy of your data. You can also complain to the relevant data protection authority.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Security</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>We use reasonable technical and organisational measures to protect personal data. No website or payment system can be guaranteed to be completely secure, but secure payment processing is provided through Stripe, PayPal and WooCommerce checkout integrations.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Children</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Our products are intended to be purchased by parents, guardians, tutors or other adults. Children should not create accounts or place orders without adult supervision.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Updates</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>We may update this Privacy Policy when our website, services, suppliers or legal requirements change.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'Refund / Digital Download Policy',
		'slug'    => 'refund-digital-download-policy',
		'content' => '<!-- wp:paragraph --><p>Last updated: ' . esc_html( $updated_date ) . '</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>This policy explains how refunds and download access work for digital products sold by Exampapers Ltd.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Digital download access</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Most Exampapers Ltd products are downloadable digital files. After successful payment, files are normally made available through your account and order confirmation email. Because access can be provided immediately, digital purchases are treated differently from physical goods.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Refund eligibility</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>We will review refund requests fairly. A refund or replacement may be offered where a file is unavailable due to a technical issue we cannot resolve, the download is corrupt, the wrong file was supplied, a duplicate order was placed by mistake, or the product was materially different from its description.</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>We normally cannot provide a refund after a digital file has been accessed or downloaded simply because you changed your mind, selected the wrong product, no longer need the resource, or expected official exam-board materials. Exampapers Ltd is independent, not official and not affiliated with any exam board, school, local authority or admissions body.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Before requesting a refund</h2><!-- /wp:heading -->
<!-- wp:list --><ul><li>Check your order confirmation email and account downloads area.</li><li>Try downloading the file again using a stable internet connection.</li><li>Confirm that your device has software capable of opening PDF files.</li><li>Contact us with your order number and a clear description of the problem.</li></ul><!-- /wp:list -->
<!-- wp:heading --><h2>How refunds are processed</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Approved refunds are returned to the original payment method where possible. Payment timing depends on Stripe, PayPal, WooCommerce checkout integrations, banks and card providers.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Download limits and misuse</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Download access is for the purchaser or household/private study use only. We may suspend or limit download access if we reasonably believe files are being shared, resold, uploaded publicly or used outside the licence terms.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Product descriptions</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Please read product pages carefully before buying. Product pages explain the type of pack, subject, exam area, format and any included answer or support files where applicable.</p><!-- /wp:paragraph -->',
	),
	array(
		'title'   => 'Cookie notice',
		'slug'    => 'cookie-notice',
		'content' => '<!-- wp:paragraph --><p>Last updated: ' . esc_html( $updated_date ) . '</p><!-- /wp:paragraph -->
<!-- wp:paragraph --><p>This Cookie notice explains how Exampapers Ltd uses cookies and similar technologies on this website.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>What cookies are</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Cookies are small files stored on your device when you visit a website. They help the website remember actions such as cart contents, account login status, checkout progress and preferences.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Cookies we may use</h2><!-- /wp:heading -->
<!-- wp:list --><ul><li>Essential cookies required for WordPress, WooCommerce, account login, cart, checkout, fraud prevention and website security.</li><li>Payment and checkout cookies used by Stripe, PayPal and WooCommerce checkout integrations to process orders securely.</li><li>Preference cookies that remember choices you make on the website.</li><li>Analytics or performance cookies, if enabled, to understand how visitors use the site and improve performance.</li></ul><!-- /wp:list -->
<!-- wp:heading --><h2>WooCommerce and payments</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>WooCommerce may use cookies to keep items in your cart, manage checkout sessions and recognise logged-in customers. Stripe and PayPal may also set cookies or use similar technologies to process payments, prevent fraud and keep transactions secure.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Managing cookies</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>You can block or delete cookies using your browser settings. If you block essential cookies, parts of the site may not work properly, including account login, cart, checkout and digital download access.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Third-party services</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>Some cookies may be set by third-party providers used to operate the website, process secure payments, protect against fraud, measure performance or deliver website features. Those providers may process data according to their own policies.</p><!-- /wp:paragraph -->
<!-- wp:heading --><h2>Updates</h2><!-- /wp:heading -->
<!-- wp:paragraph --><p>We may update this Cookie notice when website features, payment integrations or cookie usage changes.</p><!-- /wp:paragraph -->',
	),
);

if ( ! $is_cli ) {
	echo '<!doctype html><html><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '"><title>Exampapers legal page seeder</title></head><body>';
	echo '<h1>Exampapers legal page seeder</h1><ul>';
}

exampapers_seed_legal_line( 'Exampapers legal page seeder' );

foreach ( $legal_pages as $page_data ) {
	$existing = get_page_by_path( $page_data['slug'], OBJECT, 'page' );
	$postarr  = array(
		'post_title'   => $page_data['title'],
		'post_name'    => $page_data['slug'],
		'post_content' => $page_data['content'],
		'post_status'  => 'publish',
		'post_type'    => 'page',
	);

	if ( $existing instanceof WP_Post ) {
		$postarr['ID'] = $existing->ID;
		$page_id       = wp_update_post( $postarr, true );
	} else {
		$page_id = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		exampapers_seed_legal_line( 'Failed: ' . $page_data['title'] );
		continue;
	}

	if ( 'privacy-policy' === $page_data['slug'] ) {
		update_option( 'wp_page_for_privacy_policy', (int) $page_id );
	}

	exampapers_seed_legal_line( ( $existing instanceof WP_Post ? 'Updated: ' : 'Created: ' ) . $page_data['title'] . ' (/' . $page_data['slug'] . '/)' );
}

if ( ! $is_cli ) {
	echo '</ul></body></html>';
}
