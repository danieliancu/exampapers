<?php
/**
 * Single post template.
 *
 * @package Exampapers
 */

get_header();
?>

<main id="primary" class="exampapers-page-section exampapers-content">
	<?php
	while ( have_posts() ) {
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<h1><?php the_title(); ?></h1>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="exampapers-featured-image">
					<?php the_post_thumbnail( 'large' ); ?>
				</figure>
			<?php endif; ?>

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
