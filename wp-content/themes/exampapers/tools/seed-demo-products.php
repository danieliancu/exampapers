<?php
/**
 * Demo product seeder for Exampapers.
 *
 * Browser:
 *   1. Log in as an administrator.
 *   2. Open /wp-content/themes/exampapers/tools/seed-demo-products.php?purge=1
 *   3. Open /wp-content/themes/exampapers/tools/seed-demo-products.php?run=1
 *   4. Open /wp-content/themes/exampapers/tools/seed-demo-products.php?clean_categories=1
 *
 * CLI:
 *   php wp-content/themes/exampapers/tools/seed-demo-products.php --purge
 *   php wp-content/themes/exampapers/tools/seed-demo-products.php --run
 *   php wp-content/themes/exampapers/tools/seed-demo-products.php --clean-categories
 *
 * @package Exampapers
 */

define( 'SHORTINIT', false );

$wp_load = dirname( __DIR__, 4 ) . '/wp-load.php';

if ( ! file_exists( $wp_load ) ) {
	exit( 'Could not find wp-load.php.' . PHP_EOL );
}

require_once $wp_load;

$is_cli              = 'cli' === PHP_SAPI;
$is_purge            = $is_cli
	? in_array( '--purge', $argv, true )
	: ( ! empty( $_GET['purge'] ) && '1' === (string) $_GET['purge'] );
$is_run              = $is_cli
	? in_array( '--run', $argv, true )
	: ( ! empty( $_GET['run'] ) && '1' === (string) $_GET['run'] );
$is_clean_categories = $is_cli
	? in_array( '--clean-categories', $argv, true )
	: ( ! empty( $_GET['clean_categories'] ) && '1' === (string) $_GET['clean_categories'] );

if ( ! $is_cli ) {
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'You must be logged in as an administrator with WooCommerce management access.', 'exampapers' ) );
	}

	if ( ! $is_purge && ! $is_run && ! $is_clean_categories ) {
		wp_die( esc_html__( 'Add ?run=1 to seed demo products, ?purge=1 to delete all WooCommerce products, or ?clean_categories=1 to clean product categories.', 'exampapers' ) );
	}
} elseif ( ! $is_purge && ! $is_run && ! $is_clean_categories ) {
	exit( 'Add --run to seed demo products, --purge to delete all WooCommerce products, or --clean-categories to clean product categories.' . PHP_EOL );
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
 * Open browser output.
 *
 * @param string $title Page title.
 */
function exampapers_seed_open_output( $title ) {
	if ( 'cli' === PHP_SAPI ) {
		return;
	}

	header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
	echo '<!doctype html><html><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '"><title>' . esc_html( $title ) . '</title></head><body>';
	echo '<h1>' . esc_html( $title ) . '</h1><ul>';
}

/**
 * Close browser output.
 */
function exampapers_seed_close_output() {
	if ( 'cli' === PHP_SAPI ) {
		return;
	}

	echo '</ul><p>Delete this script after confirming the demo products.</p></body></html>';
}

/**
 * Ensure the sample PDF exists.
 *
 * @return string Download URL.
 */
function exampapers_seed_pdf() {
	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'demo-papers';
	$file    = trailingslashit( $dir ) . 'sample-exampapers-demo.pdf';

	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	if ( ! file_exists( $file ) ) {
		$pdf = "%PDF-1.4\n";
		$pdf .= "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
		$pdf .= "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n";
		$pdf .= "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n";
		$pdf .= "4 0 obj << /Length 120 >> stream\n";
		$pdf .= "BT /F1 24 Tf 72 700 Td (Exampapers Demo PDF) Tj 0 -36 Td /F1 14 Tf (Placeholder downloadable exam practice paper.) Tj ET\n";
		$pdf .= "endstream endobj\n";
		$pdf .= "5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n";
		$pdf .= "xref\n0 6\n0000000000 65535 f \n0000000009 00000 n \n0000000058 00000 n \n0000000115 00000 n \n0000000241 00000 n \n0000000412 00000 n \n";
		$pdf .= "trailer << /Size 6 /Root 1 0 R >>\nstartxref\n482\n%%EOF\n";

		file_put_contents( $file, $pdf ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}

	return trailingslashit( $uploads['baseurl'] ) . 'demo-papers/sample-exampapers-demo.pdf';
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
 * Final top-level product category names.
 *
 * @return string[]
 */
function exampapers_seed_top_level_categories() {
	return array(
		'11+ Practice Papers',
		'Pre-11+ Practice',
		'13+ Practice Papers',
		'SATs Practice Papers',
		'GCSE Practice Papers',
		'Mock Exams',
		'Bundles',
		'Free Samples',
	);
}

/**
 * Final 11+ child product category names.
 *
 * @return string[]
 */
function exampapers_seed_eleven_plus_child_categories() {
	return array(
		'CSSE Essex 11+',
		'Kent Test',
		'Sutton SET',
		'Bexley 11+',
		'Buckinghamshire 11+',
		'Medway Test',
		'West Midlands Grammar Schools 11+',
		'Trafford 11+',
		'Gloucestershire 11+',
		'Redbridge 11+',
		'Tiffin 11+',
		'Slough Consortium 11+',
		'SEAG Northern Ireland',
	);
}

/**
 * Ensure a product category exists.
 *
 * @param string $name Category name.
 * @param int    $parent_id Parent category ID.
 * @return int
 */
function exampapers_seed_category( $name, $parent_id = 0 ) {
	$term = term_exists( $name, 'product_cat' );

	if ( 0 === $term || null === $term ) {
		$term = wp_insert_term(
			$name,
			'product_cat',
			array(
				'parent' => $parent_id,
			)
		);
	} else {
		$term_id       = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
		$current_term  = get_term( $term_id, 'product_cat' );
		$current_parent = $current_term && ! is_wp_error( $current_term ) ? (int) $current_term->parent : 0;

		if ( $current_parent !== $parent_id ) {
			wp_update_term( $term_id, 'product_cat', array( 'parent' => $parent_id ) );
		}
	}

	return is_wp_error( $term ) ? 0 : (int) $term['term_id'];
}

/**
 * Ensure the final product category hierarchy exists.
 *
 * @return array<string,int>
 */
function exampapers_seed_ensure_final_categories() {
	$category_ids = array();

	foreach ( exampapers_seed_top_level_categories() as $category_name ) {
		$category_ids[ $category_name ] = exampapers_seed_category( $category_name );
	}

	foreach ( exampapers_seed_eleven_plus_child_categories() as $category_name ) {
		$category_ids[ $category_name ] = exampapers_seed_category( $category_name, $category_ids['11+ Practice Papers'] );
	}

	return $category_ids;
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
 * @param int    $position Attribute position.
 * @return WC_Product_Attribute|null
 */
function exampapers_seed_product_attribute( $taxonomy, array $term_names, $position ) {
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
	$attribute->set_position( $position );
	$attribute->set_visible( true );
	$attribute->set_variation( false );

	return $attribute;
}

/**
 * Delete all WooCommerce product posts and product variations.
 *
 * @return int Deleted post count.
 */
function exampapers_seed_purge_products() {
	$product_ids = get_posts(
		array(
			'post_type'      => array( 'product', 'product_variation' ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	$deleted = 0;

	foreach ( $product_ids as $product_id ) {
		$post = get_post( (int) $product_id );

		if ( ! $post || ! in_array( $post->post_type, array( 'product', 'product_variation' ), true ) ) {
			continue;
		}

		$title = $post->post_title ? $post->post_title : '#' . $post->ID;
		$type  = $post->post_type;

		if ( wp_delete_post( (int) $product_id, true ) ) {
			$deleted++;
			exampapers_seed_line( 'Deleted ' . $type . ': ' . $title );
		}
	}

	return $deleted;
}

/**
 * Category names to delete and their preferred final category destinations.
 *
 * @return array<string,string>
 */
function exampapers_seed_unwanted_category_map() {
	return array(
		'English'                => '',
		'Maths'                  => '',
		'Vocabulary'             => '',
		'Creative Writing'       => '',
		'Verbal Reasoning'       => '',
		'Non-Verbal Reasoning'   => '',
		'Online Tests'           => '',
		'CSSE 11+'               => 'CSSE Essex 11+',
		'Bexley'                 => 'Bexley 11+',
		'Buckinghamshire'        => 'Buckinghamshire 11+',
		'Tiffin'                 => 'Tiffin 11+',
		'GL Style 11+'           => '11+ Practice Papers',
		'CEM Style'              => '11+ Practice Papers',
		'SATs Practice'          => 'SATs Practice Papers',
		'GCSE Practice'          => 'GCSE Practice Papers',
		'13+ Practice'           => '13+ Practice Papers',
	);
}

/**
 * Find product category terms by exact name or expected slug.
 *
 * @param string $name Category name.
 * @return WP_Term[]
 */
function exampapers_seed_find_category_terms( $name ) {
	$expected_slug = sanitize_title( $name );
	$terms         = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'name'       => $name,
		)
	);

	if ( is_wp_error( $terms ) ) {
		$terms = array();
	}

	$slug_term = get_term_by( 'slug', $expected_slug, 'product_cat' );

	if ( $slug_term instanceof WP_Term ) {
		$terms[] = $slug_term;
	}

	$unique_terms = array();

	foreach ( $terms as $term ) {
		if ( $term instanceof WP_Term && ( $term->name === $name || $term->slug === $expected_slug ) ) {
			$unique_terms[ $term->term_id ] = $term;
		}
	}

	return array_values( $unique_terms );
}

/**
 * Pick a final fallback category from a product's Exam Level attribute.
 *
 * @param int   $product_id Product ID.
 * @param array $category_ids Final category IDs by name.
 * @return int
 */
function exampapers_seed_fallback_category_id_for_product( $product_id, array $category_ids ) {
	$levels = taxonomy_exists( 'pa_exam-level' )
		? wp_get_post_terms( $product_id, 'pa_exam-level', array( 'fields' => 'names' ) )
		: array();

	if ( is_wp_error( $levels ) ) {
		$levels = array();
	}

	$fallbacks = array(
		'Pre-11+'       => 'Pre-11+ Practice',
		'13+'           => '13+ Practice Papers',
		'SATs'          => 'SATs Practice Papers',
		'GCSE'          => 'GCSE Practice Papers',
		'11+'           => '11+ Practice Papers',
		'SEAG'          => '11+ Practice Papers',
		'ISEB Pre-Test' => '11+ Practice Papers',
	);

	foreach ( $levels as $level ) {
		if ( ! empty( $fallbacks[ $level ] ) && ! empty( $category_ids[ $fallbacks[ $level ] ] ) ) {
			return (int) $category_ids[ $fallbacks[ $level ] ];
		}
	}

	return (int) $category_ids['11+ Practice Papers'];
}

/**
 * Remove Uncategorized from demo products that already have a final category.
 *
 * @param int[] $final_category_ids Final category IDs.
 * @return int Number of products updated.
 */
function exampapers_seed_remove_uncategorized_from_demo_products( array $final_category_ids ) {
	$uncategorized = get_term_by( 'slug', 'uncategorized', 'product_cat' );

	if ( ! $uncategorized instanceof WP_Term ) {
		return 0;
	}

	$product_ids = get_posts(
		array(
			'post_type'      => 'product',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => '_sku',
					'value'   => 'DEMO-',
					'compare' => 'LIKE',
				),
			),
		)
	);

	$updated = 0;

	foreach ( $product_ids as $product_id ) {
		$current_ids = wp_get_object_terms( (int) $product_id, 'product_cat', array( 'fields' => 'ids' ) );

		if ( is_wp_error( $current_ids ) || ! in_array( (int) $uncategorized->term_id, array_map( 'intval', $current_ids ), true ) ) {
			continue;
		}

		$new_ids = array_values( array_diff( array_map( 'intval', $current_ids ), array( (int) $uncategorized->term_id ) ) );

		if ( empty( array_intersect( $new_ids, $final_category_ids ) ) ) {
			continue;
		}

		wp_set_object_terms( (int) $product_id, $new_ids, 'product_cat' );
		$updated++;
	}

	return $updated;
}

/**
 * Clean old product categories while preserving products and attributes.
 *
 * @return array<string,int>
 */
function exampapers_seed_clean_product_categories() {
	$category_ids       = exampapers_seed_ensure_final_categories();
	$final_category_ids = array_values( array_map( 'intval', $category_ids ) );
	$deleted           = 0;
	$moved_products    = 0;
	$skipped           = 0;

	exampapers_seed_line( 'Final category hierarchy ensured.' );

	foreach ( exampapers_seed_unwanted_category_map() as $old_category_name => $target_category_name ) {
		$terms = exampapers_seed_find_category_terms( $old_category_name );

		if ( empty( $terms ) ) {
			exampapers_seed_line( 'Not found: ' . $old_category_name );
			continue;
		}

		foreach ( $terms as $term ) {
			$target_category_id = ! empty( $target_category_name ) && ! empty( $category_ids[ $target_category_name ] )
				? (int) $category_ids[ $target_category_name ]
				: 0;
			$object_ids         = get_objects_in_term( (int) $term->term_id, 'product_cat' );

			if ( is_wp_error( $object_ids ) ) {
				$skipped++;
				exampapers_seed_line( 'Skipped: ' . $term->name . ' (could not read assigned products)' );
				continue;
			}

			foreach ( $object_ids as $object_id ) {
				if ( 'product' !== get_post_type( (int) $object_id ) ) {
					continue;
				}

				$current_ids = wp_get_object_terms( (int) $object_id, 'product_cat', array( 'fields' => 'ids' ) );

				if ( is_wp_error( $current_ids ) ) {
					continue;
				}

				$new_ids = array_values( array_diff( array_map( 'intval', $current_ids ), array( (int) $term->term_id ) ) );

				if ( $target_category_id ) {
					$new_ids[] = $target_category_id;
				}

				if ( empty( array_intersect( $new_ids, $final_category_ids ) ) ) {
					$new_ids[] = exampapers_seed_fallback_category_id_for_product( (int) $object_id, $category_ids );
				}

				$new_ids = array_values( array_unique( array_filter( array_map( 'intval', $new_ids ) ) ) );
				wp_set_object_terms( (int) $object_id, $new_ids, 'product_cat' );
				$moved_products++;
			}

			$deleted_term = wp_delete_term( (int) $term->term_id, 'product_cat' );

			if ( is_wp_error( $deleted_term ) || ! $deleted_term ) {
				$skipped++;
				exampapers_seed_line( 'Skipped delete: ' . $term->name );
				continue;
			}

			$deleted++;
			exampapers_seed_line( 'Deleted category: ' . $term->name );
		}
	}

	$uncategorized_updates = exampapers_seed_remove_uncategorized_from_demo_products( $final_category_ids );

	exampapers_seed_line( 'Remaining Product Categories:' );

	$remaining_terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( ! is_wp_error( $remaining_terms ) ) {
		foreach ( $remaining_terms as $term ) {
			$parent = $term->parent ? get_term( (int) $term->parent, 'product_cat' ) : null;
			$label  = $parent instanceof WP_Term ? $parent->name . ' > ' . $term->name : $term->name;
			exampapers_seed_line( 'Kept: ' . $label );
		}
	}

	return array(
		'deleted'                 => $deleted,
		'moved_products'          => $moved_products,
		'skipped'                 => $skipped,
		'uncategorized_products'  => $uncategorized_updates,
	);
}

if ( $is_purge ) {
	exampapers_seed_open_output( 'Exampapers product purge' );
	$deleted = exampapers_seed_purge_products();
	exampapers_seed_line( 'Done. Deleted ' . $deleted . ' WooCommerce product post(s).' );
	exampapers_seed_close_output();
	exit;
}

if ( $is_clean_categories ) {
	exampapers_seed_open_output( 'Exampapers product category cleanup' );
	$cleanup = exampapers_seed_clean_product_categories();
	exampapers_seed_line( 'Done. Deleted ' . $cleanup['deleted'] . ' old categor' . ( 1 === $cleanup['deleted'] ? 'y' : 'ies' ) . ', updated ' . $cleanup['moved_products'] . ' product assignment(s), removed Uncategorized from ' . $cleanup['uncategorized_products'] . ' demo product(s), skipped ' . $cleanup['skipped'] . ' item(s).' );
	exampapers_seed_close_output();
	exit;
}

$category_ids = exampapers_seed_ensure_final_categories();

$attribute_taxonomies = array(
	'exam_level' => exampapers_seed_attribute( 'Exam Level', 'exam-level', array( 'Pre-11+', '11+', '13+', 'SATs', 'GCSE', 'SEAG', 'ISEB Pre-Test' ) ),
	'exam_area'  => exampapers_seed_attribute(
		'Exam Area',
		'exam-area',
		array(
			'Bexley 11+',
			'Buckinghamshire 11+',
			'Kent Test',
			'Medway Test',
			'Lincolnshire 11+',
			'CSSE Essex 11+',
			'Southend 11+',
			'Slough Consortium 11+',
			'Torbay & Devon 11+',
			'Trafford 11+',
			'West Midlands Grammar Schools 11+',
			'Gloucestershire 11+',
			'Reading & Kendrick 11+',
			'Redbridge 11+',
			'Sutton SET',
			'Tiffin 11+',
			'Bromley 11+',
			'Barnet 11+',
			'Latymer / Enfield 11+',
			'South West Hertfordshire 11+',
			'Bournemouth & Poole 11+',
			'Plymouth 11+',
			'Wiltshire 11+',
			'Lancashire 11+',
			'Cumbria 11+',
			'North Yorkshire 11+',
			'Calderdale 11+',
			'Kirklees 11+',
			'Wirral 11+',
			'Liverpool 11+',
			'Shropshire / The Wrekin 11+',
			'SEAG Northern Ireland',
			'General',
		)
	),
	'exam_style' => exampapers_seed_attribute( 'Exam Style', 'exam-style', array( 'GL Style', 'CEM Style', 'CSSE Style', 'SET Style', 'SEAG Style', 'School-specific', 'Consortium-specific', 'General Practice' ) ),
	'subject'    => exampapers_seed_attribute( 'Subject', 'subject', array( 'English', 'Maths', 'Vocabulary', 'Verbal Reasoning', 'Non-Verbal Reasoning', 'Creative Writing', 'Comprehension', 'Grammar & Punctuation', 'Problem Solving' ) ),
	'format'     => exampapers_seed_attribute( 'Format', 'format', array( 'Printable PDF', 'Online Test', 'Mock Exam', 'Practice Questions', 'Bundle', 'Answer Pack', 'Revision Pack', 'Free Sample' ) ),
	'difficulty' => exampapers_seed_attribute( 'Difficulty', 'difficulty', array( 'Foundation', 'Standard', 'Advanced', 'Challenge' ) ),
	'school'     => exampapers_seed_attribute(
		'School',
		'school',
		array(
			'King Edward VI Grammar School',
			'Colchester Royal Grammar School',
			'Colchester County High School for Girls',
			'Southend High School for Boys',
			'Southend High School for Girls',
			'Westcliff High School for Boys',
			'Westcliff High School for Girls',
			'Sutton Grammar School',
			"Wilson's School",
			'Tiffin School',
			"The Tiffin Girls' School",
			'The Latymer School',
			'Dartford Grammar School',
		)
	),
);

$pdf_url              = exampapers_seed_pdf();
$placeholder_image_id = exampapers_seed_product_placeholder_image();

$products = array(
	array(
		'title'      => 'CSSE Essex 11+ Full Mock Exam Pack 1',
		'sku'        => 'DEMO-CSSE-ESSEX-11-FULL-MOCK-1',
		'price'      => '14.99',
		'categories' => array( '11+ Practice Papers', 'CSSE Essex 11+', 'Mock Exams', 'Bundles' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CSSE Essex 11+' ),
			'exam_style' => array( 'CSSE Style' ),
			'subject'    => array( 'English', 'Maths', 'Vocabulary', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam', 'Bundle' ),
			'difficulty' => array( 'Standard' ),
			'school'     => array( 'Colchester Royal Grammar School', 'Colchester County High School for Girls' ),
		),
	),
	array(
		'title'      => 'CSSE Essex 11+ English Practice Pack',
		'sku'        => 'DEMO-CSSE-ESSEX-11-ENGLISH',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers', 'CSSE Essex 11+' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CSSE Essex 11+' ),
			'exam_style' => array( 'CSSE Style' ),
			'subject'    => array( 'English', 'Comprehension', 'Grammar & Punctuation' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'CSSE Essex 11+ Maths Practice Pack',
		'sku'        => 'DEMO-CSSE-ESSEX-11-MATHS',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers', 'CSSE Essex 11+' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CSSE Essex 11+' ),
			'exam_style' => array( 'CSSE Style' ),
			'subject'    => array( 'Maths', 'Problem Solving' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'CSSE Essex 11+ Vocabulary & Verbal Reasoning Pack',
		'sku'        => 'DEMO-CSSE-ESSEX-11-VOCAB-VR',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers', 'CSSE Essex 11+' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'CSSE Essex 11+' ),
			'exam_style' => array( 'CSSE Style' ),
			'subject'    => array( 'Vocabulary', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'GL Style 11+ Full Mock Exam Pack 1',
		'sku'        => 'DEMO-GL-STYLE-11-FULL-MOCK-1',
		'price'      => '14.99',
		'categories' => array( '11+ Practice Papers', 'Mock Exams', 'Bundles' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam', 'Bundle' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'GL Style 11+ English Practice Pack',
		'sku'        => 'DEMO-GL-STYLE-11-ENGLISH',
		'price'      => '9.99',
		'categories' => array( '11+ Practice Papers' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'English', 'Comprehension' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'GL Style 11+ Maths Practice Pack',
		'sku'        => 'DEMO-GL-STYLE-11-MATHS',
		'price'      => '9.99',
		'categories' => array( '11+ Practice Papers' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'Maths', 'Problem Solving' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'GL Style 11+ Non-Verbal Reasoning Pack',
		'sku'        => 'DEMO-GL-STYLE-11-NVR',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => 'Sutton SET 11+ Full Mock Exam Pack',
		'sku'        => 'DEMO-SUTTON-SET-11-FULL-MOCK',
		'price'      => '13.99',
		'categories' => array( '11+ Practice Papers', 'Sutton SET', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Sutton SET' ),
			'exam_style' => array( 'SET Style' ),
			'subject'    => array( 'English', 'Maths' ),
			'format'     => array( 'Printable PDF', 'Mock Exam' ),
			'difficulty' => array( 'Advanced' ),
			'school'     => array( 'Sutton Grammar School', "Wilson's School" ),
		),
	),
	array(
		'title'      => 'Sutton SET 11+ English Practice Pack',
		'sku'        => 'DEMO-SUTTON-SET-11-ENGLISH',
		'price'      => '8.49',
		'categories' => array( '11+ Practice Papers', 'Sutton SET' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Sutton SET' ),
			'exam_style' => array( 'SET Style' ),
			'subject'    => array( 'English', 'Comprehension' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => 'Sutton SET 11+ Maths Practice Pack',
		'sku'        => 'DEMO-SUTTON-SET-11-MATHS',
		'price'      => '8.49',
		'categories' => array( '11+ Practice Papers', 'Sutton SET' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Sutton SET' ),
			'exam_style' => array( 'SET Style' ),
			'subject'    => array( 'Maths', 'Problem Solving' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => 'Kent Test 11+ Full Mock Pack',
		'sku'        => 'DEMO-KENT-TEST-11-FULL-MOCK',
		'price'      => '14.99',
		'categories' => array( '11+ Practice Papers', 'Kent Test', 'Mock Exams', 'Bundles' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Kent Test' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam', 'Bundle' ),
			'difficulty' => array( 'Standard' ),
			'school'     => array( 'Dartford Grammar School' ),
		),
	),
	array(
		'title'      => 'Kent Test 11+ Reasoning Pack',
		'sku'        => 'DEMO-KENT-TEST-11-REASONING',
		'price'      => '9.49',
		'categories' => array( '11+ Practice Papers', 'Kent Test' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Kent Test' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'Kent Test 11+ Maths Pack',
		'sku'        => 'DEMO-KENT-TEST-11-MATHS',
		'price'      => '8.99',
		'categories' => array( '11+ Practice Papers', 'Kent Test' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Kent Test' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'Maths', 'Problem Solving' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'Bexley 11+ Full Mock Exam Pack',
		'sku'        => 'DEMO-BEXLEY-11-FULL-MOCK',
		'price'      => '14.49',
		'categories' => array( '11+ Practice Papers', 'Bexley 11+', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Bexley 11+' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'Buckinghamshire 11+ GL Mock Pack',
		'sku'        => 'DEMO-BUCKINGHAMSHIRE-11-GL-MOCK',
		'price'      => '13.99',
		'categories' => array( '11+ Practice Papers', 'Buckinghamshire 11+', 'Mock Exams' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Buckinghamshire 11+' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning', 'Non-Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Mock Exam' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'Tiffin 11+ Stage 1 Practice Pack',
		'sku'        => 'DEMO-TIFFIN-11-STAGE-1',
		'price'      => '12.99',
		'categories' => array( '11+ Practice Papers', 'Tiffin 11+' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Tiffin 11+' ),
			'exam_style' => array( 'School-specific' ),
			'subject'    => array( 'English', 'Maths' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
			'school'     => array( 'Tiffin School', "The Tiffin Girls' School" ),
		),
	),
	array(
		'title'      => 'Medway Test 11+ Practice Pack',
		'sku'        => 'DEMO-MEDWAY-TEST-11-PRACTICE',
		'price'      => '11.99',
		'categories' => array( '11+ Practice Papers', 'Medway Test' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'Medway Test' ),
			'exam_style' => array( 'GL Style' ),
			'subject'    => array( 'English', 'Maths', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'West Midlands Grammar Schools 11+ Practice Pack',
		'sku'        => 'DEMO-WEST-MIDLANDS-11-PRACTICE',
		'price'      => '12.99',
		'categories' => array( '11+ Practice Papers', 'West Midlands Grammar Schools 11+' ),
		'attributes' => array(
			'exam_level' => array( '11+' ),
			'exam_area'  => array( 'West Midlands Grammar Schools 11+' ),
			'exam_style' => array( 'Consortium-specific' ),
			'subject'    => array( 'English', 'Maths', 'Vocabulary', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Challenge' ),
			'school'     => array( 'King Edward VI Grammar School' ),
		),
	),
	array(
		'title'      => 'Pre-11+ English Foundation Pack',
		'sku'        => 'DEMO-PRE-11-ENGLISH-FOUNDATION',
		'price'      => '6.99',
		'categories' => array( 'Pre-11+ Practice' ),
		'attributes' => array(
			'exam_level' => array( 'Pre-11+' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'General Practice' ),
			'subject'    => array( 'English', 'Comprehension' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => 'Pre-11+ Maths Foundation Pack',
		'sku'        => 'DEMO-PRE-11-MATHS-FOUNDATION',
		'price'      => '6.99',
		'categories' => array( 'Pre-11+ Practice' ),
		'attributes' => array(
			'exam_level' => array( 'Pre-11+' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'General Practice' ),
			'subject'    => array( 'Maths', 'Problem Solving' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => 'Pre-11+ Vocabulary Foundation Pack',
		'sku'        => 'DEMO-PRE-11-VOCAB-FOUNDATION',
		'price'      => '6.99',
		'categories' => array( 'Pre-11+ Practice' ),
		'attributes' => array(
			'exam_level' => array( 'Pre-11+' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'General Practice' ),
			'subject'    => array( 'Vocabulary', 'Verbal Reasoning' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => '13+ English Entrance Practice Pack',
		'sku'        => 'DEMO-13-ENGLISH-ENTRANCE',
		'price'      => '10.99',
		'categories' => array( '13+ Practice Papers' ),
		'attributes' => array(
			'exam_level' => array( '13+' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'General Practice' ),
			'subject'    => array( 'English', 'Creative Writing', 'Comprehension' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => '13+ Maths Entrance Practice Pack',
		'sku'        => 'DEMO-13-MATHS-ENTRANCE',
		'price'      => '10.99',
		'categories' => array( '13+ Practice Papers' ),
		'attributes' => array(
			'exam_level' => array( '13+' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'General Practice' ),
			'subject'    => array( 'Maths', 'Problem Solving' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Advanced' ),
		),
	),
	array(
		'title'      => 'SATs Year 6 Maths Practice Pack',
		'sku'        => 'DEMO-SATS-Y6-MATHS',
		'price'      => '5.99',
		'categories' => array( 'SATs Practice Papers' ),
		'attributes' => array(
			'exam_level' => array( 'SATs' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'General Practice' ),
			'subject'    => array( 'Maths', 'Problem Solving' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'SATs Year 6 Reading Practice Pack',
		'sku'        => 'DEMO-SATS-Y6-READING',
		'price'      => '5.99',
		'categories' => array( 'SATs Practice Papers' ),
		'attributes' => array(
			'exam_level' => array( 'SATs' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'General Practice' ),
			'subject'    => array( 'English', 'Comprehension' ),
			'format'     => array( 'Printable PDF', 'Practice Questions' ),
			'difficulty' => array( 'Standard' ),
		),
	),
	array(
		'title'      => 'GCSE Maths Foundation Practice Pack',
		'sku'        => 'DEMO-GCSE-MATHS-FOUNDATION',
		'price'      => '9.99',
		'categories' => array( 'GCSE Practice Papers' ),
		'attributes' => array(
			'exam_level' => array( 'GCSE' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'General Practice' ),
			'subject'    => array( 'Maths', 'Problem Solving' ),
			'format'     => array( 'Printable PDF', 'Practice Questions', 'Revision Pack' ),
			'difficulty' => array( 'Foundation' ),
		),
	),
	array(
		'title'      => 'GCSE English Language Practice Pack',
		'sku'        => 'DEMO-GCSE-ENGLISH-LANGUAGE',
		'price'      => '9.99',
		'categories' => array( 'GCSE Practice Papers' ),
		'attributes' => array(
			'exam_level' => array( 'GCSE' ),
			'exam_area'  => array( 'General' ),
			'exam_style' => array( 'General Practice' ),
			'subject'    => array( 'English', 'Creative Writing', 'Grammar & Punctuation' ),
			'format'     => array( 'Printable PDF', 'Practice Questions', 'Revision Pack' ),
			'difficulty' => array( 'Standard' ),
		),
	),
);

$created = array();
$updated = array();
$skipped = array();

foreach ( $products as $product_data ) {
	$existing_id = wc_get_product_id_by_sku( $product_data['sku'] );

	if ( ! $existing_id ) {
		$duplicate_title = get_page_by_title( $product_data['title'], OBJECT, 'product' );

		if ( $duplicate_title instanceof WP_Post ) {
			$skipped[] = $product_data['title'] . ' (title already exists with another SKU)';
			continue;
		}
	}

	$product = $existing_id ? wc_get_product( $existing_id ) : new WC_Product_Simple();

	if ( ! $product instanceof WC_Product_Simple ) {
		$skipped[] = $product_data['title'] . ' (existing SKU is not a simple product)';
		continue;
	}

	$product->set_name( $product_data['title'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_sku( $product_data['sku'] );
	$product->set_regular_price( $product_data['price'] );
	$product->set_virtual( true );
	$product->set_downloadable( true );
	$product->set_download_limit( -1 );
	$product->set_download_expiry( -1 );
	$product->set_short_description( 'Demo downloadable PDF pack for testing Exampapers layouts, SEO categories, filters and checkout flow.' );
	$product->set_description(
		'<p>This is a fake demo product for testing the Exampapers WooCommerce theme.</p>' .
		'<p>It includes a placeholder downloadable PDF, sample attributes, categories and product copy so the shop, product page and filters can be reviewed safely.</p>' .
		'<p>Do not use this content as a real exam resource.</p>'
	);

	$download = new WC_Product_Download();
	$download->set_id( wp_generate_uuid4() );
	$download->set_name( 'Exampapers demo PDF' );
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

	$product->set_category_ids( array_values( array_unique( $product_category_ids ) ) );

	$product_attributes = array();
	$position           = 0;

	foreach ( $product_data['attributes'] as $attribute_key => $term_names ) {
		if ( empty( $attribute_taxonomies[ $attribute_key ] ) ) {
			continue;
		}

		$attribute = exampapers_seed_product_attribute( $attribute_taxonomies[ $attribute_key ], $term_names, $position );

		if ( $attribute instanceof WC_Product_Attribute ) {
			$product_attributes[] = $attribute;
			$position++;
		}
	}

	$product->set_attributes( $product_attributes );

	$product_id = $product->save();

	if ( $product_id ) {
		if ( $existing_id ) {
			$updated[] = $product_data['title'];
		} else {
			$created[] = $product_data['title'];
		}
	}
}

exampapers_seed_open_output( 'Exampapers demo product seeder' );

exampapers_seed_line( 'PDF ready: wp-content/uploads/demo-papers/sample-exampapers-demo.pdf' );

if ( $placeholder_image_id ) {
	exampapers_seed_line( 'Product image ready: wp-content/uploads/demo-products/product-placeholder.webp' );
} else {
	exampapers_seed_line( 'Product image missing: wp-content/uploads/demo-products/product-placeholder.webp' );
}

foreach ( $created as $title ) {
	exampapers_seed_line( 'Created: ' . $title );
}

foreach ( $updated as $title ) {
	exampapers_seed_line( 'Updated: ' . $title );
}

foreach ( $skipped as $title ) {
	exampapers_seed_line( 'Skipped: ' . $title );
}

exampapers_seed_line( 'Done. Created ' . count( $created ) . ' product(s), updated ' . count( $updated ) . ' product(s), skipped ' . count( $skipped ) . ' product(s).' );

exampapers_seed_close_output();
