<?php
/**
 * Temporary Exam Area to School relationship seeder for Exampapers.
 *
 * Browser:
 *   1. Log in as an administrator.
 *   2. Open /wp-content/themes/exampapers/tools/seed-school-areas.php?run=1
 *
 * CLI:
 *   php wp-content/themes/exampapers/tools/seed-school-areas.php --run
 *
 * Delete this file after confirming the school area relationships.
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
	if ( ! is_user_logged_in() || ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'You must be logged in as an administrator with WooCommerce management access.', 'exampapers' ) );
	}

	if ( ! $is_run ) {
		wp_die( esc_html__( 'Add ?run=1 to seed school area relationships.', 'exampapers' ) );
	}
} elseif ( ! $is_run ) {
	exit( 'Add --run to seed school area relationships.' . PHP_EOL );
}

/**
 * Print one line for CLI or browser.
 *
 * @param string $message Message.
 */
function exampapers_seed_school_line( $message ) {
	if ( 'cli' === PHP_SAPI ) {
		echo $message . PHP_EOL;
		return;
	}

	echo '<li>' . esc_html( $message ) . '</li>';
}

/**
 * Ensure a WooCommerce global attribute exists.
 *
 * @param string $label Attribute label.
 * @param string $slug Attribute slug without pa_ prefix.
 */
function exampapers_seed_school_ensure_attribute( $label, $slug ) {
	if ( ! function_exists( 'wc_create_attribute' ) || ! function_exists( 'wc_attribute_taxonomy_id_by_name' ) ) {
		return;
	}

	if ( wc_attribute_taxonomy_id_by_name( $slug ) ) {
		return;
	}

	$result = wc_create_attribute(
		array(
			'name'         => $label,
			'slug'         => $slug,
			'type'         => 'select',
			'order_by'     => 'name',
			'has_archives' => true,
		)
	);

	if ( is_wp_error( $result ) ) {
		exampapers_seed_school_line( 'Failed to create attribute ' . $label . ': ' . $result->get_error_message() );
		return;
	}

	delete_transient( 'wc_attribute_taxonomies' );
	exampapers_seed_school_line( 'Created attribute: ' . $label );
}

/**
 * Ensure an attribute taxonomy is available during this script run.
 *
 * @param string $taxonomy Taxonomy name.
 */
function exampapers_seed_school_ensure_taxonomy( $taxonomy ) {
	if ( taxonomy_exists( $taxonomy ) ) {
		return;
	}

	register_taxonomy(
		$taxonomy,
		array( 'product' ),
		array(
			'hierarchical' => false,
			'public'       => true,
			'show_ui'      => false,
			'query_var'    => true,
			'rewrite'      => false,
		)
	);
}

/**
 * Ensure a term exists and return it.
 *
 * @param string $name Term name.
 * @param string $taxonomy Taxonomy.
 * @return WP_Term|null
 */
function exampapers_seed_school_ensure_term( $name, $taxonomy ) {
	$term = get_term_by( 'name', $name, $taxonomy );

	if ( $term instanceof WP_Term ) {
		return $term;
	}

	$result = wp_insert_term( $name, $taxonomy );

	if ( is_wp_error( $result ) ) {
		if ( $result->get_error_data( 'term_exists' ) ) {
			$term = get_term( (int) $result->get_error_data( 'term_exists' ), $taxonomy );
			return $term instanceof WP_Term ? $term : null;
		}

		exampapers_seed_school_line( 'Failed to create term ' . $name . ': ' . $result->get_error_message() );
		return null;
	}

	$term = get_term( (int) $result['term_id'], $taxonomy );
	return $term instanceof WP_Term ? $term : null;
}

/**
 * Source-backed Exam Area to Schools data.
 *
 * @return array<string,array<string,array<int,mixed>>>
 */
function exampapers_seed_school_area_data() {
	return array(
		'CSSE Essex 11+'                       => array(
			'sources' => array(
				array(
					'label' => 'CSSE member schools',
					'url'   => 'https://csse.org.uk/',
				),
			),
			'schools' => array(
				'King Edward VI Grammar School',
				'Colchester County High School for Girls',
				'Colchester Royal Grammar School',
				'Southend High School for Girls',
				'Southend High School for Boys',
				'Westcliff High School for Girls',
				'Westcliff High School for Boys',
				'St Bernard\'s High School',
				'St Thomas More High School',
				'Shoeburyness High School',
			),
		),
		'Buckinghamshire 11+'                  => array(
			'sources' => array(
				array(
					'label' => 'The Buckinghamshire Grammar Schools',
					'url'   => 'https://www.thebucksgrammarschools.org/tbgs-schools',
				),
			),
			'schools' => array(
				'Aylesbury Grammar School',
				'Aylesbury High School',
				'Beaconsfield High School',
				'Burnham Grammar School',
				'Chesham Grammar School',
				'Dr Challoner\'s Grammar School',
				'Dr Challoner\'s High School',
				'John Hampden Grammar School',
				'Royal Grammar School',
				'Royal Latin School',
				'Sir Henry Floyd Grammar School',
				'Sir William Borlase\'s Grammar School',
				'Wycombe High School',
			),
		),
		'West Midlands Grammar Schools 11+'    => array(
			'sources' => array(
				array(
					'label' => 'West Midlands Grammar Schools partnership schools',
					'url'   => 'https://westmidlandsgrammarschools.co.uk/schools',
				),
			),
			'schools' => array(
				'Haberdashers\' Adams',
				'Newport Girls\' High School',
				'Queen Mary\'s Grammar School',
				'Queen Mary\'s High School',
				'Wolverhampton Girls\' High School',
				'Bishop Vesey\'s Grammar School',
				'King Edward VI Aston School',
				'King Edward VI Camp Hill School for Boys',
				'King Edward VI Camp Hill School for Girls',
				'King Edward VI Five Ways School',
				'King Edward VI Handsworth Grammar School for Boys',
				'King Edward VI Handsworth School for Girls',
				'Sutton Coldfield Grammar School for Girls',
				'Alcester Grammar School',
				'Ashlawn School',
				'King Edward VI School, Stratford-Upon-Avon',
				'Lawrence Sheriff School',
				'Rugby High School',
				'Stratford Girls\' Grammar School',
			),
		),
		'Trafford 11+'                         => array(
			'sources' => array(
				array(
					'label' => 'Trafford Grammar Schools Consortium admissions',
					'url'   => 'https://www.stretfordgrammar.com/page/?pid=151&title=Admissions',
				),
			),
			'schools' => array(
				'Altrincham Grammar School for Boys',
				'Altrincham Grammar School for Girls',
				'Sale Grammar School',
				'Stretford Grammar School',
				'Urmston Grammar',
			),
		),
		'Tiffin 11+'                           => array(
			'sources' => array(
				array(
					'label' => 'Tiffin School admissions',
					'url'   => 'https://www.tiffinschool.co.uk/admissions/',
				),
				array(
					'label' => 'The Tiffin Girls\' School admissions',
					'url'   => 'https://www.tiffingirls.org/admissions/',
				),
			),
			'schools' => array(
				'Tiffin School',
				'The Tiffin Girls\' School',
			),
		),
	);
}

exampapers_seed_school_ensure_attribute( 'Exam Area', 'exam-area' );
exampapers_seed_school_ensure_attribute( 'School', 'school' );
exampapers_seed_school_ensure_taxonomy( 'pa_exam-area' );
exampapers_seed_school_ensure_taxonomy( 'pa_school' );

$meta_keys = function_exists( 'exampapers_area_school_meta_keys' )
	? exampapers_area_school_meta_keys()
	: array(
		'school_terms' => '_exampapers_area_school_terms',
		'sources'      => '_exampapers_area_school_sources',
	);

if ( ! $is_cli ) {
	echo '<!doctype html><html><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '"><title>Exampapers school area seeder</title></head><body>';
	echo '<h1>Exampapers school area seeder</h1><ul>';
}

exampapers_seed_school_line( 'Exampapers school area seeder' );

$area_count   = 0;
$school_count = 0;

foreach ( exampapers_seed_school_area_data() as $area_name => $area_data ) {
	$area = exampapers_seed_school_ensure_term( $area_name, 'pa_exam-area' );

	if ( ! $area instanceof WP_Term ) {
		continue;
	}

	$school_ids = array();

	foreach ( $area_data['schools'] as $school_name ) {
		$school = exampapers_seed_school_ensure_term( $school_name, 'pa_school' );

		if ( ! $school instanceof WP_Term ) {
			continue;
		}

		$school_ids[] = (int) $school->term_id;
		$school_count++;
	}

	$school_ids = array_values( array_unique( array_filter( $school_ids ) ) );
	$sources    = array();

	foreach ( $area_data['sources'] as $source ) {
		if ( empty( $source['label'] ) || empty( $source['url'] ) ) {
			continue;
		}

		$sources[] = array(
			'label' => sanitize_text_field( $source['label'] ),
			'url'   => esc_url_raw( $source['url'] ),
		);
	}

	update_term_meta( $area->term_id, $meta_keys['school_terms'], $school_ids );
	update_term_meta( $area->term_id, $meta_keys['sources'], $sources );

	$area_count++;
	exampapers_seed_school_line( 'Linked ' . $area_name . ' to ' . count( $school_ids ) . ' school(s).' );
}

exampapers_seed_school_line( 'Done. Updated ' . $area_count . ' exam area relationship(s), ensured ' . $school_count . ' school assignment(s).' );
exampapers_seed_school_line( 'No products, orders, media, checkout settings or WooCommerce settings were modified.' );

if ( ! $is_cli ) {
	echo '</ul><p>Delete this script after confirming the school area relationships.</p></body></html>';
}
