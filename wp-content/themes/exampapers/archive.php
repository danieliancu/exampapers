<?php
/**
 * Archive template.
 *
 * @package Exampapers
 */

get_header();
?>

<main id="primary" class="exampapers-page-section exampapers-content">
	<h1><?php the_archive_title(); ?></h1>
	<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>

	<?php if ( have_posts() ) : ?>
		<div class="exampapers-resource-grid">
			<?php
			while ( have_posts() ) {
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'exampapers-card' ); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
				<?php
			}
			?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php endif; ?>
</main>

<?php
get_footer();
