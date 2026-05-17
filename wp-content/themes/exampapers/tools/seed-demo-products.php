<?php
/**
 * One-time demo product seeder for Exampapers.
 *
 * Run once, then delete this file.
 *
 * Browser:
 *   1. Log in as an administrator.
 *   2. Open /wp-content/themes/exampapers/tools/seed-demo-products.php?run=1
 *
 * CLI:
 *   php wp-content/themes/exampapers/tools/seed-demo-products.php --run
 *
 * @package Exampapers
 */

define( 'SHORTINIT', false );

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';

if ( ! file_exists( $wp_load ) ) {
	exit( 'Could not find wp-load.php.' . PHP_EOL );
}

require_once $wp_load;

$is_cli   = 'cli' === PHP_SAPI;
$is_purge = $is_cli
	? in_array( '--purge', $argv, true )
	: ( ! empty( $_GET['purge'] ) && '1' === (string) $_GET['purge'] );
$is_run   = $is_cli
	? in_array( '--run', $argv, true )
	: ( ! empty( $_GET['run'] ) && '1' === (string) $_GET['run'] );

if ( ! $is_cli ) {
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'You must be logged in as an administrator with WooCommerce management access.', 'exampapers' ) );
	}

	if ( ! $is_purge && ! $is_run ) {
		wp_die( esc_html__( 'Add ?run=1 to seed demo products, or ?purge=1 to delete all 13+, GCSE, Pre-11+ and SATs products.', 'exampapers' ) );
	}
} elseif ( ! $is_purge && ! $is_run ) {
	exit( 'Add --run to seed demo products, or --purge to delete 13+, GCSE, Pre-11+ and SATs products.' . PHP_EOL );
}

if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Product_Simple' ) ) {
	exit( 'WooCommerce is not active or not loaded.' . PHP_EOL );
}

/**
 * Print one line for CLI or browser.
 *
 * @param string $message Message.
 */
function exampapers_seed_line( $message ) {
	if ( 'cli' === PHP_SAPI ) {
		echo $message . PHP_EOL;
		return;
	}

	echo '<li>' . esc_html( $message ) . '</li>';
}

/**
 * Find a product by exact title.
 *
 * @param string $title Product title.
 * @return int
 */
function exampapers_seed_get_product_id_by_title( $title ) {
	global $wpdb;

	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_title = %s LIMIT 1",
			$title
		)
	);
}

/**
 * Ensure the sample PDF exists.
 *
 * @return string Download URL.
 */
function exampapers_seed_pdf() {
	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'demo-papers';
	$file    = trailingslashit( $dir ) . 'sample-11-plus-paper.pdf';

	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	if ( ! file_exists( $file ) ) {
		$pdf = "%PDF-1.4\n";
		$pdf .= "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
		$pdf .= "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n";
		$pdf .= "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n";
		$pdf .= "4 0 obj << /Length 96 >> stream\n";
		$pdf .= "BT /F1 24 Tf 72 700 Td (Exampapers Demo PDF) Tj 0 -36 Td /F1 14 Tf (Sample 11 plus paper placeholder.) Tj ET\n";
		$pdf .= "endstream endobj\n";
		$pdf .= "5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n";
		$pdf .= "xref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000241 00000 n \n0000000388 00000 n \n";
		$pdf .= "trailer << /Size 6 /Root 1 0 R >>\nstartxref\n458\n%%EOF\n";

		file_put_contents( $file, $pdf );
	}

	return trailingslashit( $uploads['baseurl'] ) . 'demo-papers/sample-11-plus-paper.pdf';
}

/**
 * Ensure the demo product placeholder image is registered in the Media Library.
 *
 * @return int Attachment ID, or 0 if the file is missing.
 */
function exampapers_seed_product_placeholder_image() {
	$uploads       = wp_upload_dir();
	$relative_file = 'demo-products/product-placeholder.webp';
	$file          = trailingslashit( $uploads['basedir'] ) . $relative_file;

	if ( ! file_exists( $file ) ) {
		return 0;
	}

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'   => '_wp_attached_file',
					'value' => $relative_file,
				),
			),
		)
	);

	if ( ! empty( $existing ) ) {
		return (int) $existing[0];
	}

	$filetype = wp_check_filetype( basename( $file ), null );

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => $filetype['type'] ? $filetype['type'] : 'image/webp',
			'post_title'     => 'Demo product placeholder',
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$file
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return 0;
	}

	update_post_meta( $attachment_id, '_wp_attached_file', $relative_file );

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$metadata = wp_generate_attachment_metadata( $attachment_id, $file );
	wp_update_attachment_metadata( $attachment_id, $metadata );

	return (int) $attachment_id;
}

/**
 * Ensure a product category exists.
 *
 * @param string $name Category name.
 * @return int
 */
function exampapers_seed_category( $name ) {
	$term = term_exists( $name, 'product_cat' );

	if ( 0 === $term || null === $term ) {
		$term = wp_insert_term( $name, 'product_cat' );
	}

	return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
}

/**
 * Ensure a WooCommerce global attribute and terms exist.
 *
 * @param string $label Attribute label.
 * @param string $slug Attribute slug without pa_.
 * @param array  $terms Term names.
 * @return string Taxonomy name.
 */
function exampapers_seed_attribute( $label, $slug, array $terms ) {
	$taxonomy = wc_attribute_taxonomy_name( $slug );

	if ( ! wc_attribute_taxonomy_id_by_name( $taxonomy ) ) {
		wc_create_attribute(
			array(
				'name'         => $label,
				'slug'         => $slug,
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => true,
			)
		);

		delete_transient( 'wc_attribute_taxonomies' );
	}

	if ( ! taxonomy_exists( $taxonomy ) ) {
		register_taxonomy(
			$taxonomy,
			array( 'product' ),
			array(
				'hierarchical' => false,
				'label'        => $label,
				'public'       => true,
				'rewrite'      => array( 'slug' => $taxonomy ),
			)
		);
	}

	foreach ( $terms as $term_name ) {
		if ( ! term_exists( $term_name, $taxonomy ) ) {
			wp_insert_term( $term_name, $taxonomy );
		}
	}

	return $taxonomy;
}

/**
 * Build a product attribute object.
 *
 * @param string $taxonomy Attribute taxonomy.
 * @param array  $term_names Term names.
 * @return WC_Product_Attribute|null
 */
function exampapers_seed_product_attribute( $taxonomy, array $term_names ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return null;
	}

	$term_ids = array();

	foreach ( $term_names as $term_name ) {
		$term = term_exists( $term_name, $taxonomy );

		if ( $term && ! is_wp_error( $term ) ) {
			$term_ids[] = (int) $term['term_id'];
		}
	}

	if ( empty( $term_ids ) ) {
		return null;
	}

	$attribute = new WC_Product_Attribute();
	$attribute->set_id( wc_attribute_taxonomy_id_by_name( $taxonomy ) );
	$attribute->set_name( $taxonomy );
	$attribute->set_options( $term_ids );
	$attribute->set_position( 0 );
	$attribute->set_visible( true );
	$attribute->set_variation( false );

	return $attribute;
}

/**
 * Return IDs of all products assigned to a given pa_exam-level term.
 *
 * @param string $term_name Term name (e.g. '13+').
 * @return int[]
 */
function exampapers_seed_products_by_exam_level( $term_name ) {
	return get_posts(
		array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => 'pa_exam-level',
					'field'    => 'name',
					'terms'    => $term_name,
				),
			),
		)
	);
}

// ── Purge mode: delete all 13+, GCSE, Pre-11+, SATs products ────────────────
if ( $is_purge ) {
	$purge_levels = array( '13+', 'GCSE', 'Pre-11+', 'SATs' );
	$deleted      = array();

	if ( ! $is_cli ) {
		header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
		echo '<!doctype html><html><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '"><title>Exampapers purge</title></head><body>';
		echo '<h1>Deleting 13+, GCSE, Pre-11+ and SATs products</h1><ul>';
	}

	foreach ( $purge_levels as $level ) {
		$ids = exampapers_seed_products_by_exam_level( $level );

		foreach ( $ids as $product_id ) {
			$title    = get_the_title( (int) $product_id );
			wp_delete_post( (int) $product_id, true );
			$deleted[] = '[' . $level . '] ' . $title;
			exampapers_seed_line( 'Deleted: [' . $level . '] ' . $title );
		}
	}

	exampapers_seed_line( 'Done. Deleted ' . count( $deleted ) . ' product(s).' );

	if ( ! $is_cli ) {
		echo '</ul></body></html>';
	}

	exit;
}

$category_names = array(
	'11+ Practice Papers',
	'CSSE 11+',
	'GL Style 11+',
	'Sutton SET',
	'Kent Test',
	'Pre-11+ Practice',
	'English',
	'Maths',
	'Vocabulary',
	'Mock Exams',
	'Bundles',
	'13+ Practice',
	'SATs Practice',
	'GCSE Practice',
	'CEM Style',
	'Bexley',
	'Tiffin',
	'Buckinghamshire',
	'Creative Writing',
	'Verbal Reasoning',
	'Non-Verbal Reasoning',
	'Online Tests',
);

$category_ids = array();

foreach ( $category_names as $category_name ) {
	$category_ids[ $category_name ] = exampapers_seed_category( $category_name );
}

$attribute_taxonomies = array(
	'exam_level' => exampapers_seed_attribute( 'Exam Level', 'exam-level', array( 'Pre-11+', '11+', '13+', 'SATs', 'GCSE' ) ),
	'exam_area'  => exampapers_seed_attribute( 'Exam Area', 'exam-area', array( 'CSSE', 'GL Style', 'CEM Style', 'Sutton SET', 'Kent Test', 'Bexley', 'Tiffin', 'Buckinghamshire', 'General 11+' ) ),
	'subject'    => exampapers_seed_attribute( 'Subject', 'subject', array( 'English', 'Maths', 'Vocabulary', 'Verbal Reasoning', 'Non-Verbal Reasoning', 'Creative Writing' ) ),
	'format'     => exampapers_seed_attribute( 'Format', 'format', array( 'Printable PDF', 'Mock Exam', 'Practice Questions', 'Bundle', 'Online Test' ) ),
	'difficulty' => exampapers_seed_attribute( 'Difficulty', 'difficulty', array( 'Foundation', 'Standard', 'Advanced', 'Challenge' ) ),
	'school'     => exampapers_seed_attribute( 'School', 'school', array( 'Tiffin School', 'Colchester Royal Grammar School', 'King Edward VI Grammar School', 'Sutton Grammar School', 'Dartford Grammar School', 'The Latymer School' ) ),
);

$pdf_url = exampapers_seed_pdf();
$placeholder_image_id = exampapers_seed_product_placeholder_image();

$products = array(
	array(
		'title'      => 'CSSE 11+ Full Mock Exam Pack 1',
		'sku'        => 'DEMO-CSSE-11-FULL-MOCK-1',
		'price'      => '14.99',
		'categories' => array( '11+ Practice Papers', 'CSSE 11+', 'Mock Exams', 'Bundles' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CSSE' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam', 'Bundle' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'CSSE 11+ English Practice Pack',
		'sku'        => 'DEMO-CSSE-11-ENGLISH',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers', 'CSSE 11+', 'English' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CSSE' ),
			'subject'    => array( 'English' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'CSSE 11+ Maths Practice Pack',
		'sku'        => 'DEMO-CSSE-11-MATHS',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers', 'CSSE 11+', 'Maths' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CSSE' ),
			'subject'    => array( 'Maths' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'GL Style 11+ Maths Practice Pack',
		'sku'        => 'DEMO-GL-11-MATHS',
		'price'      => '9.99',
		'categories' => array( '11+ Practice Papers', 'GL Style 11+', 'Maths' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'GL Style' ),
			'subject'    => array( 'Maths', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => 'GL Style 11+ English Practice Pack',
		'sku'        => 'DEMO-GL-11-ENGLISH',
		'price'      => '9.99',
		'categories' => array( '11+ Practice Papers', 'GL Style 11+', 'English' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'GL Style' ),
			'subject'    => array( 'English', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => 'Sutton SET 11+ Practice Pack',
		'sku'        => 'DEMO-SUTTON-SET-11',
		'price'      => '12.99',
		'categories' => array( '11+ Practice Papers', 'Sutton SET', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Sutton SET' ),
			'subject'    => array( 'English', 'Maths' ),
			'format'     => array( 'Printable PDF', 'Mock Exam' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => 'Kent Test 11+ Full Mock Pack',
		'sku'        => 'DEMO-KENT-11-FULL-MOCK',
		'price'      => '14.99',
		'categories' => array( '11+ Practice Papers', 'Kent Test', 'Mock Exams', 'Bundles' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Kent Test' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam', 'Bundle' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'Pre-11+ Vocabulary Foundation Pack',
		'sku'        => 'DEMO-PRE-11-VOCAB-FOUNDATION',
		'price'      => '6.99',
		'categories' => array( 'Pre-11+ Practice', 'Vocabulary' ),
		'attributes' => array(
			'exam_level' => array( 'Pre-11+' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'Vocabulary', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => 'CSSE 11+ Verbal Reasoning Standard Pack',
		'sku'        => 'DEMO-CSSE-11-VR-STANDARD',
		'price'      => '7.99',
		'categories' => array( '11+ Practice Papers', 'CSSE 11+', 'Verbal Reasoning' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CSSE' ),
			'subject'    => array( 'Verbal Reasoning', 'Vocabulary' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
			'school'     => array( 'Colchester Royal Grammar School' ),
		),
	),
	array(
		'title'      => 'CSSE 11+ Challenge Bundle Pack',
		'sku'        => 'DEMO-CSSE-11-CHALLENGE-BUNDLE',
		'price'      => '18.99',
		'categories' => array( '11+ Practice Papers', 'CSSE 11+', 'Bundles', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CSSE' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Bundle', 'Mock Exam' ),
			'difficulty' => array( 'Challenge' ),
			'school'     => array( 'Colchester Royal Grammar School' ),
		),
	),
	array(
		'title'      => 'GL Style 11+ Non-Verbal Reasoning Pack',
		'sku'        => 'DEMO-GL-11-NVR',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers', 'GL Style 11+', 'Non-Verbal Reasoning' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'GL Style' ),
			'subject'    => array( 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'GL Style 11+ Full Mock Exam Pack 2',
		'sku'        => 'DEMO-GL-11-FULL-MOCK-2',
		'price'      => '14.99',
		'categories' => array( '11+ Practice Papers', 'GL Style 11+', 'Mock Exams', 'Bundles' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'GL Style' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam', 'Bundle' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => 'CEM Style 11+ Mixed Skills Pack',
		'sku'        => 'DEMO-CEM-11-MIXED-SKILLS',
		'price'      => '11.99',
		'categories' => array( '11+ Practice Papers', 'CEM Style', 'Bundles' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CEM Style' ),
			'subject'    => array( 'English', 'Maths', 'Vocabulary', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions', 'Bundle' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'CEM Style 11+ Timed Online Test Demo',
		'sku'        => 'DEMO-CEM-11-ONLINE-TEST',
		'price'      => '5.99',
		'categories' => array( '11+ Practice Papers', 'CEM Style', 'Online Tests' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CEM Style' ),
			'subject'    => array( 'English', 'Maths' ),
			'format'     => array( 'Online Test' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'Sutton SET 11+ Maths Speed Pack',
		'sku'        => 'DEMO-SUTTON-SET-MATHS-SPEED',
		'price'      => '8.49',
		'categories' => array( '11+ Practice Papers', 'Sutton SET', 'Maths' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Sutton SET' ),
			'subject'    => array( 'Maths' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
			'school'     => array( 'Sutton Grammar School' ),
		),
	),
	array(
		'title'      => 'Sutton SET 11+ English Speed Pack',
		'sku'        => 'DEMO-SUTTON-SET-ENGLISH-SPEED',
		'price'      => '8.49',
		'categories' => array( '11+ Practice Papers', 'Sutton SET', 'English' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Sutton SET' ),
			'subject'    => array( 'English' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
			'school'     => array( 'Sutton Grammar School' ),
		),
	),
	array(
		'title'      => 'Kent Test 11+ Reasoning Practice Pack',
		'sku'        => 'DEMO-KENT-11-REASONING',
		'price'      => '9.49',
		'categories' => array( '11+ Practice Papers', 'Kent Test', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Kent Test' ),
			'subject'    => array( 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
			'school'     => array( 'Dartford Grammar School' ),
		),
	),
	array(
		'title'      => 'Kent Test 11+ Advanced Mock Pack',
		'sku'        => 'DEMO-KENT-11-ADVANCED-MOCK',
		'price'      => '15.99',
		'categories' => array( '11+ Practice Papers', 'Kent Test', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Kent Test' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam' ),
			'difficulty' => array( 'Advanced' ),
			'school'     => array( 'Dartford Grammar School' ),
		),
	),
	array(
		'title'      => 'Bexley 11+ Full Mock Exam Pack',
		'sku'        => 'DEMO-BEXLEY-11-FULL-MOCK',
		'price'      => '14.49',
		'categories' => array( '11+ Practice Papers', 'Bexley', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Bexley' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'Bexley 11+ Foundation Skills Pack',
		'sku'        => 'DEMO-BEXLEY-11-FOUNDATION',
		'price'      => '7.49',
		'categories' => array( '11+ Practice Papers', 'Bexley', 'English', 'Maths' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Bexley' ),
			'subject'    => array( 'English', 'Maths' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => 'Tiffin 11+ Stage 1 Practice Pack',
		'sku'        => 'DEMO-TIFFIN-11-STAGE-1',
		'price'      => '12.99',
		'categories' => array( '11+ Practice Papers', 'Tiffin', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Tiffin' ),
			'subject'    => array( 'English', 'Maths' ),
			'format'     => array( 'Printable PDF', 'Mock Exam' ),
			'difficulty' => array( 'Advanced' ),
			'school'     => array( 'Tiffin School' ),
		),
	),
	array(
		'title'      => 'Tiffin 11+ Vocabulary Challenge Pack',
		'sku'        => 'DEMO-TIFFIN-11-VOCAB-CHALLENGE',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers', 'Tiffin', 'Vocabulary' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Tiffin' ),
			'subject'    => array( 'Vocabulary', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Challenge' ),
			'school'     => array( 'Tiffin School' ),
		),
	),
	array(
		'title'      => 'Buckinghamshire 11+ GL Mock Pack',
		'sku'        => 'DEMO-BUCKS-11-GL-MOCK',
		'price'      => '13.99',
		'categories' => array( '11+ Practice Papers', 'Buckinghamshire', 'GL Style 11+', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Buckinghamshire', 'GL Style' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'Buckinghamshire 11+ Non-Verbal Reasoning Pack',
		'sku'        => 'DEMO-BUCKS-11-NVR',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers', 'Buckinghamshire', 'Non-Verbal Reasoning' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Buckinghamshire' ),
			'subject'    => array( 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'General 11+ Creative Writing Pack',
		'sku'        => 'DEMO-GENERAL-11-CREATIVE-WRITING',
		'price'      => '7.99',
		'categories' => array( '11+ Practice Papers', 'Creative Writing', 'English' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'Creative Writing', 'English' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'General 11+ Mixed Practice Bundle',
		'sku'        => 'DEMO-GENERAL-11-MIXED-BUNDLE',
		'price'      => '16.99',
		'categories' => array( '11+ Practice Papers', 'Bundles' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'English', 'Maths', 'Vocabulary', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Bundle' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'Pre-11+ Maths Foundation Pack',
		'sku'        => 'DEMO-PRE-11-MATHS-FOUNDATION',
		'price'      => '6.99',
		'categories' => array( 'Pre-11+ Practice', 'Maths' ),
		'attributes' => array(
			'exam_level' => array( 'Pre-11+' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'Maths' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => 'Pre-11+ English Foundation Pack',
		'sku'        => 'DEMO-PRE-11-ENGLISH-FOUNDATION',
		'price'      => '6.99',
		'categories' => array( 'Pre-11+ Practice', 'English' ),
		'attributes' => array(
			'exam_level' => array( 'Pre-11+' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'English' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => 'Pre-11+ Reasoning Starter Pack',
		'sku'        => 'DEMO-PRE-11-REASONING-STARTER',
		'price'      => '7.49',
		'categories' => array( 'Pre-11+ Practice', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
		'attributes' => array(
			'exam_level' => array( 'Pre-11+' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => '13+ English Entrance Practice Pack',
		'sku'        => 'DEMO-13-ENGLISH-ENTRANCE',
		'price'      => '10.99',
		'categories' => array( '13+ Practice', 'English' ),
		'attributes' => array(
			'exam_level' => array( '13+' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'English', 'Creative Writing' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => '13+ Maths Entrance Practice Pack',
		'sku'        => 'DEMO-13-MATHS-ENTRANCE',
		'price'      => '10.99',
		'categories' => array( '13+ Practice', 'Maths' ),
		'attributes' => array(
			'exam_level' => array( '13+' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'Maths' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => 'SATs Year 6 Maths Arithmetic Pack',
		'sku'        => 'DEMO-SATS-Y6-MATHS-ARITHMETIC',
		'price'      => '5.99',
		'categories' => array( 'SATs Practice', 'Maths' ),
		'attributes' => array(
			'exam_level' => array( 'SATs' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'Maths' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'SATs Year 6 Reading Practice Pack',
		'sku'        => 'DEMO-SATS-Y6-READING',
		'price'      => '5.99',
		'categories' => array( 'SATs Practice', 'English' ),
		'attributes' => array(
			'exam_level' => array( 'SATs' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'English' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'GCSE Maths Foundation Practice Pack',
		'sku'        => 'DEMO-GCSE-MATHS-FOUNDATION',
		'price'      => '9.99',
		'categories' => array( 'GCSE Practice', 'Maths' ),
		'attributes' => array(
			'exam_level' => array( 'GCSE' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'Maths' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => 'GCSE English Language Practice Pack',
		'sku'        => 'DEMO-GCSE-ENGLISH-LANGUAGE',
		'price'      => '9.99',
		'categories' => array( 'GCSE Practice', 'English' ),
		'attributes' => array(
			'exam_level' => array( 'GCSE' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'English', 'Creative Writing' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => '11+ Vocabulary Challenge Online Test',
		'sku'        => 'DEMO-11-VOCAB-CHALLENGE-ONLINE',
		'price'      => '4.99',
		'categories' => array( '11+ Practice Papers', 'Vocabulary', 'Online Tests' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'General 11+' ),
			'subject'    => array( 'Vocabulary' ),
			'format'     => array( 'Online Test' ),
			'difficulty' => array( 'Challenge' ),
		),
	),
	array(
		'title'      => 'Latymer Style 11+ English and Maths Pack',
		'sku'        => 'DEMO-LATYMER-11-EN-MATHS',
		'price'      => '13.49',
		'categories' => array( '11+ Practice Papers', 'English', 'Maths', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'GL Style' ),
			'subject'    => array( 'English', 'Maths' ),
			'format'     => array( 'Printable PDF', 'Mock Exam' ),
			'difficulty' => array( 'Advanced' ),
			'school'     => array( 'The Latymer School' ),
		),
	),
	array(
		'title'      => 'King Edward VI 11+ Practice Bundle',
		'sku'        => 'DEMO-KEVI-11-PRACTICE-BUNDLE',
		'price'      => '17.49',
		'categories' => array( '11+ Practice Papers', 'Bundles', 'CEM Style' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CEM Style' ),
			'subject'    => array( 'English', 'Maths', 'Vocabulary', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Bundle' ),
			'difficulty' => array( 'Challenge' ),
			'school'     => array( 'King Edward VI Grammar School' ),
		),
	),
);

$created = array();
$skipped = array();
$updated_images = array();

foreach ( $products as $product_data ) {
	$existing_id = wc_get_product_id_by_sku( $product_data['sku'] );

	if ( ! $existing_id ) {
		$existing_id = exampapers_seed_get_product_id_by_title( $product_data['title'] );
	}

	if ( $existing_id ) {
		if ( 0 === strpos( $product_data['sku'], 'DEMO-' ) && $placeholder_image_id && ! has_post_thumbnail( $existing_id ) ) {
			set_post_thumbnail( $existing_id, $placeholder_image_id );
			$updated_images[] = $product_data['title'];
		}

		$skipped[] = $product_data['title'];
		continue;
	}

	$product = new WC_Product_Simple();
	$product->set_name( $product_data['title'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_sku( $product_data['sku'] );
	$product->set_regular_price( $product_data['price'] );
	$product->set_virtual( true );
	$product->set_downloadable( true );
	$product->set_download_limit( -1 );
	$product->set_download_expiry( -1 );
	$product->set_short_description( 'Demo downloadable PDF pack for testing 11+ exam paper layouts, filters and checkout flow.' );
	$product->set_description(
		'<p>This is a fake demo product for testing the Exampapers WooCommerce theme.</p>' .
		'<p>It includes a placeholder downloadable PDF, sample attributes, categories and product copy so the shop, product page and filters can be reviewed safely.</p>' .
		'<p>Do not use this content as a real exam resource.</p>'
	);

	$download = new WC_Product_Download();
	$download->set_id( wp_generate_uuid4() );
	$download->set_name( 'Sample 11 plus paper PDF' );
	$download->set_file( $pdf_url );
	$product->set_downloads( array( $download ) );

	if ( $placeholder_image_id ) {
		$product->set_image_id( $placeholder_image_id );
	}

	$product_category_ids = array();

	foreach ( $product_data['categories'] as $category_name ) {
		if ( ! empty( $category_ids[ $category_name ] ) ) {
			$product_category_ids[] = $category_ids[ $category_name ];
		}
	}

	$product->set_category_ids( $product_category_ids );

	$product_attributes = array();

	foreach ( $product_data['attributes'] as $attribute_key => $term_names ) {
		if ( empty( $attribute_taxonomies[ $attribute_key ] ) ) {
			continue;
		}

		$attribute = exampapers_seed_product_attribute( $attribute_taxonomies[ $attribute_key ], $term_names );

		if ( $attribute instanceof WC_Product_Attribute ) {
			$product_attributes[] = $attribute;
		}
	}

	$product->set_attributes( $product_attributes );

	$product_id = $product->save();

	if ( $product_id ) {
		$created[] = $product_data['title'];
	}
}

if ( ! $is_cli ) {
	header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
	echo '<!doctype html><html><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '"><title>Exampapers demo seeder</title></head><body>';
	echo '<h1>Exampapers demo product seeder</h1><ul>';
}

exampapers_seed_line( 'PDF ready: wp-content/uploads/demo-papers/sample-11-plus-paper.pdf' );

if ( $placeholder_image_id ) {
	exampapers_seed_line( 'Product image ready: wp-content/uploads/demo-products/product-placeholder.webp' );
} else {
	exampapers_seed_line( 'Product image missing: wp-content/uploads/demo-products/product-placeholder.webp' );
}

foreach ( $created as $title ) {
	exampapers_seed_line( 'Created: ' . $title );
}

foreach ( $skipped as $title ) {
	exampapers_seed_line( 'Skipped existing product: ' . $title );
}

foreach ( $updated_images as $title ) {
	exampapers_seed_line( 'Added missing image to existing demo product: ' . $title );
}

exampapers_seed_line( 'Done. Created ' . count( $created ) . ' product(s), skipped ' . count( $skipped ) . ' existing product(s), updated ' . count( $updated_images ) . ' demo image(s).' );

if ( ! $is_cli ) {
	echo '</ul><p>Delete this script after confirming the demo products.</p></body></html>';
}
