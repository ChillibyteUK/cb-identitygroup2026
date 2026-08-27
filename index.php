<?php
/**
 * Template for displaying the blog index page.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// identity's/coda's real news index designs are genuinely different from
// idtravel's (the base body below) — see index-identity.php/index-coda.php.
$cb_site = cb_site_template_suffix();
if ( 'identity' === $cb_site || 'coda' === $cb_site ) {
	get_template_part( 'index-' . $cb_site );
	return;
}

$page_for_posts = get_option( 'page_for_posts' );
$is_health       = 'health' === $cb_site;

get_header( cb_site_template_suffix() );
?>
<main id="main" class="news-insights">
	<section class="news-insights-hero">
		<?php if ( $is_health ) : ?>
			<?php // matches service-page-header__title's DOM: the h1 itself carries the full-bleed top/bottom border, with an inner id-container div for the padded/contained text (2026-08-27). ?>
		<div class="news-insights-hero__title my-4">
			<h1>
				<div class="id-container px-4 px-md-5 pt-2 pb-1">
					Newsroom and perspectives
				</div>
			</h1>
		</div>
		<?php else : ?>
		<div class="mt-5">
			<h1 class="id-container px-4 px-md-5">
				Newsroom and perspectives
			</h1>
		</div>
		<?php endif; ?>
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
                'order'          => 'DESC', // Descending order.
                'posts_per_page' => -1,    // Get all posts.
				// only in the insights or perspective categories.
				'tax_query'      => array(
					array(
						'taxonomy' => 'category',
						'field'    => 'slug',
						'terms'    => array( 'insights', 'news' ),
					),
				),
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
					case 4:
						$col_class = 'col-md-6 insight-type-grid__card-4';
						break;
					case 5:
						$col_class = 'col-md-3 insight-type-grid__card-5';
						break;
					case 6:
						$col_class = 'col-md-3 insight-type-grid__card-6';
						break;
					case 7:
						$col_class = 'col-md-12 insight-type-grid__card-7';
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
							<?= cb_sanitise_svg( get_stylesheet_directory_uri() . '/img/arrow-n600.svg', 'insight-type-grid__arrow', 14, 13 ) ?>
						</div>
					</div>
				</a>	
			</div>
				<?php
				if ( $counter >= 7 ) {
					$counter = 0;
				}
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