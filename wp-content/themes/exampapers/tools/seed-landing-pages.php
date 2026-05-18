<?php
/**
 * Temporary SEO landing page seeder for Exampapers.
 *
 * Browser:
 *   1. Log in as an administrator.
 *   2. Open /wp-content/themes/exampapers/tools/seed-landing-pages.php?run=1
 *
 * CLI:
 *   php wp-content/themes/exampapers/tools/seed-landing-pages.php --run
 *
 * Delete this file after confirming the pages.
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
		wp_die( esc_html__( 'Add ?run=1 to seed landing pages.', 'exampapers' ) );
	}
} elseif ( ! $is_run ) {
	exit( 'Add --run to seed landing pages.' . PHP_EOL );
}

/**
 * Print one line for CLI or browser.
 *
 * @param string $message Message.
 */
function exampapers_seed_landing_line( $message ) {
	if ( 'cli' === PHP_SAPI ) {
		echo $message . PHP_EOL;
		return;
	}

	echo '<li>' . esc_html( $message ) . '</li>';
}

/**
 * Landing page meta keys.
 *
 * @return array<string,string>
 */
function exampapers_seed_landing_meta_keys() {
	if ( function_exists( 'exampapers_landing_meta_keys' ) ) {
		return exampapers_landing_meta_keys();
	}

	return array(
		'is_landing_page'  => '_exampapers_is_landing_page',
		'meta_title'       => '_exampapers_meta_title',
		'meta_description' => '_exampapers_meta_description',
		'intro'            => '_exampapers_intro',
		'what_for'         => '_exampapers_what_for',
		'recommended'      => '_exampapers_recommended',
		'cta_label'        => '_exampapers_cta_label',
		'cta_url'          => '_exampapers_cta_url',
		'disclaimer'       => '_exampapers_disclaimer',
		'faqs'             => '_exampapers_faqs',
		'internal_links'   => '_exampapers_internal_links',
		'exam_level'       => '_exampapers_target_exam_level',
		'exam_area'        => '_exampapers_target_exam_area',
		'exam_style'       => '_exampapers_target_exam_style',
		'subject'          => '_exampapers_target_subject',
		'format'           => '_exampapers_target_format',
		'difficulty'       => '_exampapers_target_difficulty',
		'product_limit'    => '_exampapers_product_limit',
	);
}

$default_disclaimer = 'Disclaimer: 11+ exam formats and requirements differ by area, school and admission year. Always check the latest guidance from the relevant school, consortium or local authority.';

$landing_pages = array(
	array(
		'title'            => '11+ Practice Papers',
		'slug'             => '11-plus-practice-papers',
		'meta_title'       => '11+ Practice Papers | Downloadable Mock Exam PDFs',
		'meta_description' => 'Browse printable 11+ practice papers, mock exams and subject packs for UK grammar school and independent school preparation.',
		'intro'            => 'Browse downloadable 11+ practice papers, mock exam packs and focused subject resources for UK entrance exam preparation.',
		'what_for'         => 'This page is for families comparing 11+ mock exams and printable practice packs before choosing area-specific or subject-specific papers.',
		'recommended'      => 'Start with a full mock exam pack, then add English, maths or reasoning packs based on the skills your child needs to practise most.',
		'cta_label'        => 'Try Free Samples',
		'cta_url'          => '/product-category/free-samples/',
		'disclaimer'       => $default_disclaimer,
		'faqs'             => array(
			array( 'question' => 'Which 11+ paper should I choose first?', 'answer' => 'Start with your target exam area where possible, then use subject packs to strengthen English, maths and reasoning skills.' ),
			array( 'question' => 'Are these products downloadable?', 'answer' => 'Yes. Products are set up as virtual downloadable PDF resources through WooCommerce.' ),
			array( 'question' => 'Are answers included?', 'answer' => 'Demo products are structured to represent practice packs with answer support where noted in the product data.' ),
		),
		'internal_links'   => array(
			array( 'label' => 'CSSE Essex', 'url' => '/csse-essex-11-plus-practice-papers/' ),
			array( 'label' => 'GL Style', 'url' => '/gl-style-11-plus-practice-papers/' ),
			array( 'label' => 'Sutton SET', 'url' => '/sutton-set-practice-papers/' ),
			array( 'label' => 'Kent Test', 'url' => '/kent-test-practice-papers/' ),
			array( 'label' => 'English', 'url' => '/11-plus-english-practice-papers/' ),
			array( 'label' => 'Maths', 'url' => '/11-plus-maths-practice-papers/' ),
			array( 'label' => 'Free Samples', 'url' => '/product-category/free-samples/' ),
		),
		'exam_level'       => '11+',
		'product_limit'    => 8,
	),
	array(
		'title'            => 'CSSE Essex 11+ Practice Papers',
		'slug'             => 'csse-essex-11-plus-practice-papers',
		'meta_title'       => 'CSSE Essex 11+ Practice Papers | English, Maths & Mock Packs',
		'meta_description' => 'Download CSSE Essex 11+ practice papers for English, maths, vocabulary and full mock exam preparation.',
		'intro'            => 'Find CSSE Essex 11+ mock papers and focused English, maths, vocabulary and verbal reasoning practice packs.',
		'what_for'         => 'This page is for pupils preparing for CSSE Essex 11+ papers and families looking for targeted printable PDF practice.',
		'recommended'      => 'Use the full mock exam pack for exam-style practice, then add English, maths or vocabulary packs for focused revision.',
		'cta_label'        => 'Try Free Samples',
		'cta_url'          => '/product-category/free-samples/',
		'disclaimer'       => $default_disclaimer,
		'faqs'             => array(
			array( 'question' => 'Which subjects are covered for CSSE Essex?', 'answer' => 'The CSSE Essex demo range covers English, maths, vocabulary and verbal reasoning style preparation.' ),
			array( 'question' => 'Should I start with a mock pack or subject pack?', 'answer' => 'A mock pack is useful for exam readiness; subject packs are better when a specific skill needs more practice.' ),
			array( 'question' => 'How are the papers delivered?', 'answer' => 'Products are configured as downloadable PDFs available after checkout.' ),
		),
		'internal_links'   => array(
			array( 'label' => '11+ Practice', 'url' => '/11-plus-practice-papers/' ),
			array( 'label' => 'English', 'url' => '/11-plus-english-practice-papers/' ),
			array( 'label' => 'Maths', 'url' => '/11-plus-maths-practice-papers/' ),
			array( 'label' => 'Free Samples', 'url' => '/product-category/free-samples/' ),
			array( 'label' => 'Shop', 'url' => '/shop/' ),
		),
		'exam_level'       => '11+',
		'exam_area'        => 'CSSE Essex 11+',
		'product_limit'    => 8,
	),
	array(
		'title'            => 'GL Style 11+ Practice Papers',
		'slug'             => 'gl-style-11-plus-practice-papers',
		'meta_title'       => 'GL Style 11+ Practice Papers | Printable Mock Exam PDFs',
		'meta_description' => 'Find GL Style 11+ practice papers covering English, maths, verbal reasoning and non-verbal reasoning.',
		'intro'            => 'Browse GL Style 11+ practice packs for English, maths, verbal reasoning and non-verbal reasoning preparation.',
		'what_for'         => 'This page is for families preparing for GL Style 11+ exams or GL-style regional papers.',
		'recommended'      => 'Combine a full mock pack with subject packs to cover timing, accuracy and weaker skill areas.',
		'cta_label'        => 'Try Free Samples',
		'cta_url'          => '/product-category/free-samples/',
		'disclaimer'       => $default_disclaimer,
		'faqs'             => array(
			array( 'question' => 'What does GL Style cover?', 'answer' => 'GL Style preparation commonly includes English, maths, verbal reasoning and non-verbal reasoning practice.' ),
			array( 'question' => 'Can these be used for specific areas?', 'answer' => 'Use area-specific pages where available, and use GL Style packs for broader question-style practice.' ),
			array( 'question' => 'Which difficulty should I choose?', 'answer' => 'Start with standard packs, then move to advanced or challenge resources as confidence improves.' ),
		),
		'internal_links'   => array(
			array( 'label' => 'Kent Test', 'url' => '/kent-test-practice-papers/' ),
			array( 'label' => 'Bexley', 'url' => '/product-category/bexley-11/' ),
			array( 'label' => 'Buckinghamshire', 'url' => '/product-category/buckinghamshire-11/' ),
			array( 'label' => 'English', 'url' => '/11-plus-english-practice-papers/' ),
			array( 'label' => 'Maths', 'url' => '/11-plus-maths-practice-papers/' ),
			array( 'label' => 'Shop', 'url' => '/shop/' ),
		),
		'exam_level'       => '11+',
		'exam_style'       => 'GL Style',
		'product_limit'    => 8,
	),
	array(
		'title'            => 'Sutton SET Practice Papers',
		'slug'             => 'sutton-set-practice-papers',
		'meta_title'       => 'Sutton SET Practice Papers | 11+ English & Maths PDFs',
		'meta_description' => 'Download Sutton SET 11+ practice papers and mock packs for English and maths preparation.',
		'intro'            => 'Find Sutton SET 11+ English, maths and mock exam practice packs for focused preparation.',
		'what_for'         => 'This page is for Sutton SET preparation, especially English and maths practice with exam-style timing.',
		'recommended'      => 'Use the full mock exam pack to practise pace, then use English or maths packs for targeted improvement.',
		'cta_label'        => 'Try Free Samples',
		'cta_url'          => '/product-category/free-samples/',
		'disclaimer'       => $default_disclaimer,
		'faqs'             => array(
			array( 'question' => 'Which subjects matter for Sutton SET?', 'answer' => 'The Sutton SET demo range focuses on English and maths preparation.' ),
			array( 'question' => 'Can these help with timing?', 'answer' => 'Mock packs are intended to support timed practice and exam routine building.' ),
			array( 'question' => 'Are the papers printable?', 'answer' => 'Products are configured as printable PDF downloads.' ),
		),
		'internal_links'   => array(
			array( 'label' => '11+ Practice', 'url' => '/11-plus-practice-papers/' ),
			array( 'label' => 'English', 'url' => '/11-plus-english-practice-papers/' ),
			array( 'label' => 'Maths', 'url' => '/11-plus-maths-practice-papers/' ),
			array( 'label' => 'Free Samples', 'url' => '/product-category/free-samples/' ),
			array( 'label' => 'Shop', 'url' => '/shop/' ),
		),
		'exam_level'       => '11+',
		'exam_area'        => 'Sutton SET',
		'product_limit'    => 8,
	),
	array(
		'title'            => 'Kent Test Practice Papers',
		'slug'             => 'kent-test-practice-papers',
		'meta_title'       => 'Kent Test Practice Papers | 11+ Mock Exams & Reasoning Packs',
		'meta_description' => 'Browse Kent Test 11+ practice papers, reasoning packs and maths packs for focused preparation.',
		'intro'            => 'Browse Kent Test mock papers, reasoning resources and maths packs for 11+ preparation.',
		'what_for'         => 'This page is for pupils preparing for the Kent Test and families looking for downloadable practice packs.',
		'recommended'      => 'Start with the full mock pack, then use reasoning or maths packs to strengthen specific parts of preparation.',
		'cta_label'        => 'Try Free Samples',
		'cta_url'          => '/product-category/free-samples/',
		'disclaimer'       => $default_disclaimer,
		'faqs'             => array(
			array( 'question' => 'What skills do Kent Test packs cover?', 'answer' => 'The demo range includes mock exam, reasoning and maths-focused Kent Test practice.' ),
			array( 'question' => 'Is Kent Test preparation GL-style?', 'answer' => 'The demo products are tagged with GL Style where relevant for filtering and comparison.' ),
			array( 'question' => 'Which pack is best to start with?', 'answer' => 'A full mock pack is useful for a baseline; subject packs are better for targeted practice.' ),
		),
		'internal_links'   => array(
			array( 'label' => 'GL Style', 'url' => '/gl-style-11-plus-practice-papers/' ),
			array( 'label' => '11+ Practice', 'url' => '/11-plus-practice-papers/' ),
			array( 'label' => 'Maths', 'url' => '/11-plus-maths-practice-papers/' ),
			array( 'label' => 'Free Samples', 'url' => '/product-category/free-samples/' ),
			array( 'label' => 'Shop', 'url' => '/shop/' ),
		),
		'exam_level'       => '11+',
		'exam_area'        => 'Kent Test',
		'product_limit'    => 8,
	),
	array(
		'title'            => '11+ English Practice Papers',
		'slug'             => '11-plus-english-practice-papers',
		'meta_title'       => '11+ English Practice Papers | Comprehension & Writing PDFs',
		'meta_description' => 'Download 11+ English practice papers for comprehension, grammar, punctuation and creative writing preparation.',
		'intro'            => 'Find 11+ English practice papers for comprehension, grammar, punctuation and creative writing preparation.',
		'what_for'         => 'This page is for pupils who need focused 11+ English practice across comprehension, writing and language skills.',
		'recommended'      => 'Choose an English pack for focused practice, or combine it with an area-specific mock exam pack.',
		'cta_label'        => 'Try Free Samples',
		'cta_url'          => '/product-category/free-samples/',
		'disclaimer'       => $default_disclaimer,
		'faqs'             => array(
			array( 'question' => 'Which English skills are covered?', 'answer' => 'Relevant packs may cover comprehension, grammar, punctuation and creative writing skills.' ),
			array( 'question' => 'Should I use area-specific English papers?', 'answer' => 'Use area-specific packs where available, especially for CSSE Essex or Sutton SET preparation.' ),
			array( 'question' => 'Are answers included?', 'answer' => 'Products are structured to represent downloadable packs with answer support where noted.' ),
		),
		'internal_links'   => array(
			array( 'label' => 'CSSE Essex', 'url' => '/csse-essex-11-plus-practice-papers/' ),
			array( 'label' => 'GL Style', 'url' => '/gl-style-11-plus-practice-papers/' ),
			array( 'label' => 'Sutton SET', 'url' => '/sutton-set-practice-papers/' ),
			array( 'label' => '11+ Maths', 'url' => '/11-plus-maths-practice-papers/' ),
			array( 'label' => 'Free Samples', 'url' => '/product-category/free-samples/' ),
		),
		'exam_level'       => '11+',
		'subject'          => array( 'English', 'Comprehension', 'Creative Writing', 'Grammar & Punctuation' ),
		'product_limit'    => 8,
	),
	array(
		'title'            => '11+ Maths Practice Papers',
		'slug'             => '11-plus-maths-practice-papers',
		'meta_title'       => '11+ Maths Practice Papers | Printable Problem Solving PDFs',
		'meta_description' => 'Find printable 11+ maths practice papers and problem-solving packs for UK entrance exam preparation.',
		'intro'            => 'Browse printable 11+ maths papers and problem-solving packs for entrance exam preparation.',
		'what_for'         => 'This page is for pupils building 11+ maths fluency, accuracy and problem-solving confidence.',
		'recommended'      => 'Use maths practice packs for skill-building and full mock packs for timed exam preparation.',
		'cta_label'        => 'Try Free Samples',
		'cta_url'          => '/product-category/free-samples/',
		'disclaimer'       => $default_disclaimer,
		'faqs'             => array(
			array( 'question' => 'What maths topics are included?', 'answer' => 'The product tags focus on maths and problem solving; individual product descriptions show the pack focus.' ),
			array( 'question' => 'Which difficulty should I start with?', 'answer' => 'Foundation or standard packs are useful early on; advanced packs are better once core skills are secure.' ),
			array( 'question' => 'Should maths packs be used with mock exams?', 'answer' => 'Yes. Maths packs help build accuracy, while mock exams help practise timing and exam routine.' ),
		),
		'internal_links'   => array(
			array( 'label' => 'Kent Test', 'url' => '/kent-test-practice-papers/' ),
			array( 'label' => 'GL Style', 'url' => '/gl-style-11-plus-practice-papers/' ),
			array( 'label' => 'Sutton SET', 'url' => '/sutton-set-practice-papers/' ),
			array( 'label' => '11+ English', 'url' => '/11-plus-english-practice-papers/' ),
			array( 'label' => 'Free Samples', 'url' => '/product-category/free-samples/' ),
		),
		'exam_level'       => '11+',
		'subject'          => array( 'Maths', 'Problem Solving' ),
		'product_limit'    => 8,
	),
);

$keys    = exampapers_seed_landing_meta_keys();
$created = array();
$updated = array();

if ( ! $is_cli ) {
	header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
	echo '<!doctype html><html><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '"><title>Exampapers landing page seeder</title></head><body>';
	echo '<h1>Exampapers landing page seeder</h1><ul>';
}

foreach ( $landing_pages as $page_data ) {
	$existing = get_page_by_path( $page_data['slug'], OBJECT, 'page' );
	$postarr  = array(
		'post_title'   => $page_data['title'],
		'post_name'    => $page_data['slug'],
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_content' => '',
	);

	if ( $existing instanceof WP_Post ) {
		$postarr['ID'] = $existing->ID;
		$page_id       = wp_update_post( $postarr, true );
	} else {
		$postarr['post_author'] = get_current_user_id() ? get_current_user_id() : 1;
		$page_id                = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		exampapers_seed_landing_line( 'Failed: ' . $page_data['title'] );
		continue;
	}

	if ( $existing instanceof WP_Post ) {
		$updated[] = $page_data['title'];
	} else {
		$created[] = $page_data['title'];
	}

	update_post_meta( $page_id, $keys['is_landing_page'], '1' );

	foreach ( array( 'meta_title', 'meta_description', 'intro', 'what_for', 'recommended', 'cta_label', 'cta_url', 'disclaimer', 'exam_level', 'exam_area', 'exam_style', 'subject', 'format', 'difficulty', 'product_limit' ) as $field ) {
		update_post_meta( $page_id, $keys[ $field ], isset( $page_data[ $field ] ) ? $page_data[ $field ] : '' );
	}

	update_post_meta( $page_id, $keys['faqs'], $page_data['faqs'] );
	update_post_meta( $page_id, $keys['internal_links'], $page_data['internal_links'] );

	exampapers_seed_landing_line( ( $existing instanceof WP_Post ? 'Updated: ' : 'Created: ' ) . $page_data['title'] . ' (/' . $page_data['slug'] . '/)' );
}

exampapers_seed_landing_line( 'Done. Created ' . count( $created ) . ' page(s), updated ' . count( $updated ) . ' page(s).' );

if ( ! $is_cli ) {
	echo '</ul><p>Delete this script after confirming the landing pages.</p></body></html>';
}
