<?php
/**
 * Front page template.
 *
 * @package Exampapers
 */

get_header();
?>

<main id="primary" class="exampapers-front-page" style="margin-top:0;">
	<?php
	get_template_part( 'patterns/homepage-hero-search' );
	get_template_part( 'patterns/homepage-exam-levels' );
	get_template_part( 'patterns/homepage-popular-areas' );
	get_template_part( 'patterns/homepage-subjects' );
	get_template_part( 'patterns/homepage-best-sellers' );
	get_template_part( 'patterns/homepage-trust' );
	get_template_part( 'patterns/homepage-faq' );
	?>
</main>

<?php
get_footer();
