<?php
/**
 * Page template.
 *
 * @package Exampapers
 */

get_header();

$heading_tag = ( function_exists( 'is_account_page' ) && is_account_page() ) ? 'h2' : 'h1';
?>

<main id="primary" class="exampapers-page-section exampapers-content">
	<?php
	while ( have_posts() ) {
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<<?php echo $heading_tag; ?>><?php the_title(); ?></<?php echo $heading_tag; ?>>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
		<?php
	}
	?>
</main>

<?php
get_footer();
