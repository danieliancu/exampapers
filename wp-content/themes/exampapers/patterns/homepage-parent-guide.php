<?php
/**
 * Title: Homepage parent guide
 * Slug: exampapers/homepage-parent-guide
 * Categories: posts
 *
 * @package Exampapers
 */
?>

<section class="exampapers-page-section exampapers-section-white">
	<div class="exampapers-section-inner">
		<h2><?php esc_html_e( 'Parent guide', 'exampapers' ); ?></h2>
		<div class="exampapers-resource-grid">
			<?php
			$exampapers_posts = new WP_Query(
				array(
					'post_type'           => 'post',
					'posts_per_page'      => 3,
					'ignore_sticky_posts' => true,
				)
			);

			if ( $exampapers_posts->have_posts() ) :
				while ( $exampapers_posts->have_posts() ) :
					$exampapers_posts->the_post();
					?>
					<article class="exampapers-card">
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php the_excerpt(); ?>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
			endif;
			?>
		</div>
	</div>
</section>
