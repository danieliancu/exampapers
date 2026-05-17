<?php
/**
 * 404 template.
 *
 * @package Exampapers
 */

get_header();
?>

<main id="primary" class="exampapers-page-section exampapers-content">
	<h1><?php esc_html_e( 'Page not found', 'exampapers' ); ?></h1>
	<p><?php esc_html_e( 'Try searching for exam papers, subjects or exam areas.', 'exampapers' ); ?></p>
	<?php get_search_form(); ?>
</main>

<?php
get_footer();
