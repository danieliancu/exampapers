<?php
/**
 * SEO landing page helpers.
 *
 * @package Exampapers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Landing page definitions keyed by WordPress page slug.
 *
 * @return array<string,array<string,mixed>>
 */
function exampapers_landing_pages_config() {
	return array(
		'11-plus-practice-papers'              => array(
			'h1'               => '11+ Practice Papers',
			'meta_title'       => '11+ Practice Papers | Downloadable Mock Exam PDFs',
			'meta_description' => 'Browse printable 11+ practice papers, mock exams and subject packs for UK grammar school and independent school preparation.',
			'intro'            => 'Browse downloadable 11+ practice papers, mock exam packs and focused subject resources for UK entrance exam preparation.',
			'query'            => array(
				'exam_level' => '11+',
				'limit'      => 8,
			),
			'for'              => 'This page is for families comparing 11+ mock exams and printable practice packs before choosing area-specific or subject-specific papers.',
			'recommended'      => 'Start with a full mock exam pack, then add English, maths or reasoning packs based on the skills your child needs to practise most.',
			'faqs'             => array(
				array(
					'question' => 'Which 11+ paper should I choose first?',
					'answer'   => 'Start with your target exam area where possible, then use subject packs to strengthen English, maths and reasoning skills.',
				),
				array(
					'question' => 'Are these products downloadable?',
					'answer'   => 'Yes. Products are set up as virtual downloadable PDF resources through WooCommerce.',
				),
				array(
					'question' => 'Are answers included?',
					'answer'   => 'Demo products are structured to represent practice packs with answer support where noted in the product data.',
				),
			),
			'links'            => array(
				'CSSE Essex'  => '/csse-essex-11-plus-practice-papers/',
				'GL Style'    => '/gl-style-11-plus-practice-papers/',
				'Sutton SET'  => '/sutton-set-practice-papers/',
				'Kent Test'   => '/kent-test-practice-papers/',
				'English'     => '/11-plus-english-practice-papers/',
				'Maths'       => '/11-plus-maths-practice-papers/',
				'Free Samples' => '/product-category/free-samples/',
			),
		),
		'csse-essex-11-plus-practice-papers'   => array(
			'h1'               => 'CSSE Essex 11+ Practice Papers',
			'meta_title'       => 'CSSE Essex 11+ Practice Papers | English, Maths & Mock Packs',
			'meta_description' => 'Download CSSE Essex 11+ practice papers for English, maths, vocabulary and full mock exam preparation.',
			'intro'            => 'Find CSSE Essex 11+ mock papers and focused English, maths, vocabulary and verbal reasoning practice packs.',
			'query'            => array(
				'exam_level' => '11+',
				'exam_area'  => 'CSSE Essex 11+',
				'limit'      => 8,
			),
			'for'              => 'This page is for pupils preparing for CSSE Essex 11+ papers and families looking for targeted printable PDF practice.',
			'recommended'      => 'Use the full mock exam pack for exam-style practice, then add English, maths or vocabulary packs for focused revision.',
			'faqs'             => array(
				array(
					'question' => 'Which subjects are covered for CSSE Essex?',
					'answer'   => 'The CSSE Essex demo range covers English, maths, vocabulary and verbal reasoning style preparation.',
				),
				array(
					'question' => 'Should I start with a mock pack or subject pack?',
					'answer'   => 'A mock pack is useful for exam readiness; subject packs are better when a specific skill needs more practice.',
				),
				array(
					'question' => 'How are the papers delivered?',
					'answer'   => 'Products are configured as downloadable PDFs available after checkout.',
				),
			),
			'links'            => array(
				'11+ Practice' => '/11-plus-practice-papers/',
				'English'     => '/11-plus-english-practice-papers/',
				'Maths'       => '/11-plus-maths-practice-papers/',
				'Free Samples' => '/product-category/free-samples/',
				'Shop'        => '/shop/',
			),
		),
		'gl-style-11-plus-practice-papers'     => array(
			'h1'               => 'GL Style 11+ Practice Papers',
			'meta_title'       => 'GL Style 11+ Practice Papers | Printable Mock Exam PDFs',
			'meta_description' => 'Find GL Style 11+ practice papers covering English, maths, verbal reasoning and non-verbal reasoning.',
			'intro'            => 'Browse GL Style 11+ practice packs for English, maths, verbal reasoning and non-verbal reasoning preparation.',
			'query'            => array(
				'exam_level' => '11+',
				'exam_style' => 'GL Style',
				'limit'      => 8,
			),
			'for'              => 'This page is for families preparing for GL Style 11+ exams or GL-style regional papers.',
			'recommended'      => 'Combine a full mock pack with subject packs to cover timing, accuracy and weaker skill areas.',
			'faqs'             => array(
				array(
					'question' => 'What does GL Style cover?',
					'answer'   => 'GL Style preparation commonly includes English, maths, verbal reasoning and non-verbal reasoning practice.',
				),
				array(
					'question' => 'Can these be used for specific areas?',
					'answer'   => 'Use area-specific pages where available, and use GL Style packs for broader question-style practice.',
				),
				array(
					'question' => 'Which difficulty should I choose?',
					'answer'   => 'Start with standard packs, then move to advanced or challenge resources as confidence improves.',
				),
			),
			'links'            => array(
				'Kent Test'       => '/kent-test-practice-papers/',
				'Bexley'          => '/product-category/bexley-11/',
				'Buckinghamshire' => '/product-category/buckinghamshire-11/',
				'English'         => '/11-plus-english-practice-papers/',
				'Maths'           => '/11-plus-maths-practice-papers/',
				'Shop'            => '/shop/',
			),
		),
		'sutton-set-practice-papers'           => array(
			'h1'               => 'Sutton SET Practice Papers',
			'meta_title'       => 'Sutton SET Practice Papers | 11+ English & Maths PDFs',
			'meta_description' => 'Download Sutton SET 11+ practice papers and mock packs for English and maths preparation.',
			'intro'            => 'Find Sutton SET 11+ English, maths and mock exam practice packs for focused preparation.',
			'query'            => array(
				'exam_level' => '11+',
				'exam_area'  => 'Sutton SET',
				'limit'      => 8,
			),
			'for'              => 'This page is for Sutton SET preparation, especially English and maths practice with exam-style timing.',
			'recommended'      => 'Use the full mock exam pack to practise pace, then use English or maths packs for targeted improvement.',
			'faqs'             => array(
				array(
					'question' => 'Which subjects matter for Sutton SET?',
					'answer'   => 'The Sutton SET demo range focuses on English and maths preparation.',
				),
				array(
					'question' => 'Can these help with timing?',
					'answer'   => 'Mock packs are intended to support timed practice and exam routine building.',
				),
				array(
					'question' => 'Are the papers printable?',
					'answer'   => 'Products are configured as printable PDF downloads.',
				),
			),
			'links'            => array(
				'11+ Practice' => '/11-plus-practice-papers/',
				'English'     => '/11-plus-english-practice-papers/',
				'Maths'       => '/11-plus-maths-practice-papers/',
				'Free Samples' => '/product-category/free-samples/',
				'Shop'        => '/shop/',
			),
		),
		'kent-test-practice-papers'            => array(
			'h1'               => 'Kent Test Practice Papers',
			'meta_title'       => 'Kent Test Practice Papers | 11+ Mock Exams & Reasoning Packs',
			'meta_description' => 'Browse Kent Test 11+ practice papers, reasoning packs and maths packs for focused preparation.',
			'intro'            => 'Browse Kent Test mock papers, reasoning resources and maths packs for 11+ preparation.',
			'query'            => array(
				'exam_level' => '11+',
				'exam_area'  => 'Kent Test',
				'limit'      => 8,
			),
			'for'              => 'This page is for pupils preparing for the Kent Test and families looking for downloadable practice packs.',
			'recommended'      => 'Start with the full mock pack, then use reasoning or maths packs to strengthen specific parts of preparation.',
			'faqs'             => array(
				array(
					'question' => 'What skills do Kent Test packs cover?',
					'answer'   => 'The demo range includes mock exam, reasoning and maths-focused Kent Test practice.',
				),
				array(
					'question' => 'Is Kent Test preparation GL-style?',
					'answer'   => 'The demo products are tagged with GL Style where relevant for filtering and comparison.',
				),
				array(
					'question' => 'Which pack is best to start with?',
					'answer'   => 'A full mock pack is useful for a baseline; subject packs are better for targeted practice.',
				),
			),
			'links'            => array(
				'GL Style'    => '/gl-style-11-plus-practice-papers/',
				'11+ Practice' => '/11-plus-practice-papers/',
				'Maths'       => '/11-plus-maths-practice-papers/',
				'Free Samples' => '/product-category/free-samples/',
				'Shop'        => '/shop/',
			),
		),
		'11-plus-english-practice-papers'      => array(
			'h1'               => '11+ English Practice Papers',
			'meta_title'       => '11+ English Practice Papers | Comprehension & Writing PDFs',
			'meta_description' => 'Download 11+ English practice papers for comprehension, grammar, punctuation and creative writing preparation.',
			'intro'            => 'Find 11+ English practice papers for comprehension, grammar, punctuation and creative writing preparation.',
			'query'            => array(
				'exam_level' => '11+',
				'subject'    => array( 'English', 'Comprehension', 'Creative Writing', 'Grammar & Punctuation' ),
				'limit'      => 8,
			),
			'for'              => 'This page is for pupils who need focused 11+ English practice across comprehension, writing and language skills.',
			'recommended'      => 'Choose an English pack for focused practice, or combine it with an area-specific mock exam pack.',
			'faqs'             => array(
				array(
					'question' => 'Which English skills are covered?',
					'answer'   => 'Relevant packs may cover comprehension, grammar, punctuation and creative writing skills.',
				),
				array(
					'question' => 'Should I use area-specific English papers?',
					'answer'   => 'Use area-specific packs where available, especially for CSSE Essex or Sutton SET preparation.',
				),
				array(
					'question' => 'Are answers included?',
					'answer'   => 'Products are structured to represent downloadable packs with answer support where noted.',
				),
			),
			'links'            => array(
				'CSSE Essex' => '/csse-essex-11-plus-practice-papers/',
				'GL Style'   => '/gl-style-11-plus-practice-papers/',
				'Sutton SET' => '/sutton-set-practice-papers/',
				'11+ Maths'  => '/11-plus-maths-practice-papers/',
				'Free Samples' => '/product-category/free-samples/',
			),
		),
		'11-plus-maths-practice-papers'        => array(
			'h1'               => '11+ Maths Practice Papers',
			'meta_title'       => '11+ Maths Practice Papers | Printable Problem Solving PDFs',
			'meta_description' => 'Find printable 11+ maths practice papers and problem-solving packs for UK entrance exam preparation.',
			'intro'            => 'Browse printable 11+ maths papers and problem-solving packs for entrance exam preparation.',
			'query'            => array(
				'exam_level' => '11+',
				'subject'    => array( 'Maths', 'Problem Solving' ),
				'limit'      => 8,
			),
			'for'              => 'This page is for pupils building 11+ maths fluency, accuracy and problem-solving confidence.',
			'recommended'      => 'Use maths practice packs for skill-building and full mock packs for timed exam preparation.',
			'faqs'             => array(
				array(
					'question' => 'What maths topics are included?',
					'answer'   => 'The product tags focus on maths and problem solving; individual product descriptions show the pack focus.',
				),
				array(
					'question' => 'Which difficulty should I start with?',
					'answer'   => 'Foundation or standard packs are useful early on; advanced packs are better once core skills are secure.',
				),
				array(
					'question' => 'Should maths packs be used with mock exams?',
					'answer'   => 'Yes. Maths packs help build accuracy, while mock exams help practise timing and exam routine.',
				),
			),
			'links'            => array(
				'Kent Test'  => '/kent-test-practice-papers/',
				'GL Style'   => '/gl-style-11-plus-practice-papers/',
				'Sutton SET' => '/sutton-set-practice-papers/',
				'11+ English' => '/11-plus-english-practice-papers/',
				'Free Samples' => '/product-category/free-samples/',
			),
		),
	);
}

/**
 * Get config for the current published landing page.
 *
 * @return array<string,mixed>|null
 */
function exampapers_get_current_landing_page_config() {
	if ( ! is_page() ) {
		return null;
	}

	$page = get_queried_object();

	if ( ! $page instanceof WP_Post || 'publish' !== $page->post_status ) {
		return null;
	}

	$config = exampapers_landing_pages_config();
	$slug   = $page->post_name;

	if ( empty( $config[ $slug ] ) ) {
		return null;
	}

	$config[ $slug ]['slug'] = $slug;

	return $config[ $slug ];
}

/**
 * Use the landing page template for configured WordPress Pages.
 *
 * @param string $template Template path.
 * @return string
 */
function exampapers_landing_page_template( $template ) {
	if ( ! exampapers_get_current_landing_page_config() ) {
		return $template;
	}

	$landing_template = locate_template( 'template-parts/landing/landing-page.php' );

	return $landing_template ? $landing_template : $template;
}
add_filter( 'template_include', 'exampapers_landing_page_template' );

/**
 * Set document title for configured landing pages.
 *
 * @param array<string,string> $parts Title parts.
 * @return array<string,string>
 */
function exampapers_landing_page_title_parts( $parts ) {
	$config = exampapers_get_current_landing_page_config();

	if ( ! $config || empty( $config['meta_title'] ) ) {
		return $parts;
	}

	$parts['title'] = $config['meta_title'];
	unset( $parts['site'] );

	return $parts;
}
add_filter( 'document_title_parts', 'exampapers_landing_page_title_parts' );

/**
 * Print meta description for configured landing pages.
 */
function exampapers_landing_page_meta_description() {
	$config = exampapers_get_current_landing_page_config();

	if ( ! $config || empty( $config['meta_description'] ) ) {
		return;
	}

	echo '<meta name="description" content="' . esc_attr( $config['meta_description'] ) . '">' . "\n";
}
add_action( 'wp_head', 'exampapers_landing_page_meta_description', 1 );

/**
 * Convert product grid filters to a WooCommerce product query.
 *
 * @param array<string,mixed> $args Product grid arguments.
 * @return array<string,mixed>
 */
function exampapers_product_grid_query_args( array $args ) {
	$taxonomy_map = array(
		'exam_level' => 'pa_exam-level',
		'exam_area'  => 'pa_exam-area',
		'exam_style' => 'pa_exam-style',
		'subject'    => 'pa_subject',
		'format'     => 'pa_format',
		'difficulty' => 'pa_difficulty',
		'school'     => 'pa_school',
		'category'   => 'product_cat',
	);

	$tax_query = array();

	foreach ( $taxonomy_map as $key => $taxonomy ) {
		if ( empty( $args[ $key ] ) || ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		$terms = is_array( $args[ $key ] ) ? $args[ $key ] : array( $args[ $key ] );
		$terms = array_values( array_filter( array_map( 'trim', $terms ) ) );

		if ( empty( $terms ) ) {
			continue;
		}

		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'name',
			'terms'    => $terms,
			'operator' => 'IN',
		);
	}

	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	$query_args = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => ! empty( $args['limit'] ) ? max( 1, (int) $args['limit'] ) : 8,
		'orderby'             => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	if ( ! empty( $tax_query ) ) {
		$query_args['tax_query'] = $tax_query;
	}

	return $query_args;
}

/**
 * Render a WooCommerce product grid using the existing product card template.
 *
 * @param array<string,mixed> $args Product grid arguments.
 */
function exampapers_render_product_grid( array $args = array() ) {
	get_template_part(
		'template-parts/landing/product-grid',
		null,
		array(
			'query_args' => exampapers_product_grid_query_args( $args ),
		)
	);
}

/**
 * Parse a comma-separated shortcode value.
 *
 * @param string $value Shortcode value.
 * @return string|string[]
 */
function exampapers_landing_shortcode_value( $value ) {
	$value = trim( (string) $value );

	if ( false === strpos( $value, ',' ) ) {
		return $value;
	}

	return array_values( array_filter( array_map( 'trim', explode( ',', $value ) ) ) );
}

/**
 * Shortcode wrapper for reusable product grids.
 *
 * @param array<string,string> $atts Shortcode attributes.
 * @return string
 */
function exampapers_products_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'exam_level' => '',
			'exam_area'  => '',
			'exam_style' => '',
			'subject'    => '',
			'format'     => '',
			'difficulty' => '',
			'school'     => '',
			'category'   => '',
			'limit'      => 8,
		),
		$atts,
		'exampapers_products'
	);

	$args = array(
		'limit' => (int) $atts['limit'],
	);

	foreach ( array( 'exam_level', 'exam_area', 'exam_style', 'subject', 'format', 'difficulty', 'school', 'category' ) as $key ) {
		if ( '' !== $atts[ $key ] ) {
			$args[ $key ] = exampapers_landing_shortcode_value( $atts[ $key ] );
		}
	}

	ob_start();
	exampapers_render_product_grid( $args );
	return ob_get_clean();
}
add_shortcode( 'exampapers_products', 'exampapers_products_shortcode' );
