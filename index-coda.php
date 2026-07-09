<?php
/**
 * Template for displaying the blog index page — coda's own real design.
 *
 * coda's real news index hero is a two-heading, lime-bordered light design,
 * genuinely different from idtravel's single-heading dark hero (the shared
 * index.php this was forked from). Included from index.php when
 * cb_site_template_suffix() === 'coda'.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$page_for_posts = get_option( 'page_for_posts' );

get_header( cb_site_template_suffix() );
?>
<main id="main" class="news-insights">
	<section class="news-insights-hero has-lime-1000-border-bottom">
		<div class="has-lime-1000-border-top has-lime-1000-border-bottom mt-4">
			<h1 class="id-container px-4 px-md-5 fs-850 fw-light has-lime-1100-color lh-tightest pt-2 pb-1">
				Insights &amp; perspectives
			</h1>
		</div>
		<div class="has-lime-1000-border-bottom mb-4">
			<div class="id-container px-4 px-md-5">
				<h1 class="fw-light has-neutral-700-color fs-500 lh-tightest pt-2 pb-1">
					Here's how we're shaping what's next
				</h1>
			</div>
		</div>
		<div class="id-container px-4 px-md-5 pt-5 pb-5">
			<div class="row">
				<div class="col-md-9 offset-md-3 fs-500 fw-light pb-5">
					<?php
					// get content from page_for_posts.
					echo wp_kses_post(
						apply_filters(
							'the_content',
							$page_for_posts ? get_post_field( 'post_content', $page_for_posts ) : ''
						)
					);
					?>
				</div>
			</div>
		</div>
	</section>
	<section class="insight-type">
		<div class="insight-type-grid grid-type-1 id-container py-5 px-4 px-md-5">
			<div class="row g-5">
			<?php
			$args = array(
				'post_type'      => 'post',
				'post_status'    => array( 'publish' ),
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => -1,
			);
			$q = new WP_Query( $args );

			$counter = 0;
			while ( $q->have_posts() ) {
				$q->the_post();
				++$counter;
				switch ( $counter ) {
					case 1:
						$col_class = 'col-md-3 insight-type-grid__card-1';
						break;
					case 2:
						$col_class = 'col-md-6 insight-type-grid__card-2';
						break;
					case 3:
						$col_class = 'col-md-3 insight-type-grid__card-3';
						break;
					default:
						$col_class = 'col-md-6';
						break;
				}
				?>
			<div class="<?php echo esc_attr( $col_class ); ?>">
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="insight-type-grid__card">
					<div class="insight-type-grid__image-wrapper">
						<?php
						if ( get_the_post_thumbnail( get_the_ID() ) ) {
							echo get_the_post_thumbnail( get_the_ID(), 'full', array( 'class' => 'insight-type-grid__image', 'alt' => get_post_meta( get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) ) );
						} else {
							echo '<img src="' . esc_url( get_stylesheet_directory_uri() . '/img/default-post-image.png' ) . '" alt="" class="insight-type-grid__image" />';
						}
						?>
					</div>
					<div class="insight-type-grid__content">
						<div class="insight-type-grid__category">
							<?php
							$categories = get_the_category();
							if ( ! empty( $categories ) ) {
								echo esc_html( $categories[0]->name );
							}
							?>
						</div>
						<div class="insight-type-grid__title">
							<?php the_title(); ?>
						</div>
						<div class="insight-type-grid__date d-flex align-items-center gap-2">
							<?php echo get_the_date( 'j F Y' ); ?>
							<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-n900.svg' ); ?>" width="14" height="13" alt="" />
						</div>
					</div>
				</a>
			</div>
				<?php
			}
			wp_reset_postdata();
			?>
			</div>
		</div>
	</section>
	<?php

	// include cta template.
	$cta = get_field( 'insight_cta', 'option' );
	set_query_var( 'cta_choice', $cta );
	get_template_part( 'blocks/cb-cta' );

	?>
</main>
<?php
get_footer( cb_site_template_suffix() );
?>
