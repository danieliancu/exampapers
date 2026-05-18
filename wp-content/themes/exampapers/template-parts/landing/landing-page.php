<?php
/**
 * SEO landing page template.
 *
 * @package Exampapers
 */

defined( 'ABSPATH' ) || exit;

$config = function_exists( 'exampapers_get_current_landing_page_config' ) ? exampapers_get_current_landing_page_config() : null;

if ( ! $config ) {
	get_template_part( 'page' );
	return;
}

get_header();
?>

<main id="primary" class="exampapers-page-section exampapers-landing-page">
	<div class="exampapers-section-inner">
		<section class="exampapers-landing-hero">
			<h1><?php echo esc_html( $config['h1'] ); ?></h1>

			<?php if ( ! empty( $config['intro'] ) ) : ?>
				<p class="exampapers-lede"><?php echo esc_html( $config['intro'] ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $config['post_content'] ) ) : ?>
				<div class="entry-content">
					<?php echo apply_filters( 'the_content', $config['post_content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>

			<div class="exampapers-actions">
				<?php if ( ! empty( $config['cta_label'] ) && ! empty( $config['cta_url'] ) ) : ?>
					<a class="wp-element-button" href="<?php echo esc_url( exampapers_landing_url( $config['cta_url'] ) ); ?>"><?php echo esc_html( $config['cta_label'] ); ?></a>
				<?php endif; ?>
				<a class="wp-element-button is-style-outline" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Browse Shop', 'exampapers' ); ?></a>
			</div>
		</section>

		<section class="exampapers-landing-products" aria-labelledby="exampapers-landing-products-title">
			<h2 id="exampapers-landing-products-title"><?php esc_html_e( 'Recommended papers', 'exampapers' ); ?></h2>
			<?php exampapers_render_product_grid( ! empty( $config['query'] ) && is_array( $config['query'] ) ? $config['query'] : array() ); ?>
		</section>

		<div class="exampapers-landing-info-grid">
			<?php if ( ! empty( $config['for'] ) ) : ?>
				<section class="exampapers-card">
					<h2><?php esc_html_e( 'What this page is for', 'exampapers' ); ?></h2>
					<p><?php echo esc_html( $config['for'] ); ?></p>
				</section>
			<?php endif; ?>

			<?php if ( ! empty( $config['recommended'] ) ) : ?>
				<section class="exampapers-card">
					<h2><?php esc_html_e( 'Popular packs', 'exampapers' ); ?></h2>
					<p><?php echo esc_html( $config['recommended'] ); ?></p>
				</section>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $config['faqs'] ) && is_array( $config['faqs'] ) ) : ?>
			<section class="exampapers-card exampapers-landing-faq" aria-labelledby="exampapers-landing-faq-title">
				<h2 id="exampapers-landing-faq-title"><?php esc_html_e( 'FAQ', 'exampapers' ); ?></h2>
				<div class="exampapers-faq-list">
					<?php foreach ( $config['faqs'] as $faq ) : ?>
						<?php if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<details>
							<summary><?php echo esc_html( $faq['question'] ); ?></summary>
							<p><?php echo esc_html( $faq['answer'] ); ?></p>
						</details>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $config['internal_links'] ) && is_array( $config['internal_links'] ) ) : ?>
			<section class="exampapers-landing-links" aria-labelledby="exampapers-landing-links-title">
				<h2 id="exampapers-landing-links-title"><?php esc_html_e( 'Related pages', 'exampapers' ); ?></h2>
				<div class="exampapers-link-list">
					<?php foreach ( $config['internal_links'] as $link ) : ?>
						<?php if ( empty( $link['label'] ) || empty( $link['url'] ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<a href="<?php echo esc_url( exampapers_landing_url( $link['url'] ) ); ?>"><?php echo esc_html( $link['label'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<aside class="exampapers-disclaimer">
			<?php echo esc_html( ! empty( $config['disclaimer'] ) ? $config['disclaimer'] : __( 'Disclaimer: 11+ exam formats and requirements differ by area, school and admission year. Always check the latest guidance from the relevant school, consortium or local authority.', 'exampapers' ) ); ?>
		</aside>
	</div>
</main>

<?php
get_footer();
