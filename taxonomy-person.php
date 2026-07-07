<?php
/**
 * Template for person taxonomy archives.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$term = get_queried_object();

if ( ! $term instanceof WP_Term ) {
	return;
}

get_header( cb_site_template_suffix() );
?>
<main id="main" class="news-insights news-insights--person">
	<section class="news-insights-hero has-neutral-400-border-bottom">
		<div class="has-neutral-400-border-top has-neutral-400-border-bottom mt-5">
			<h1 class="id-container px-4 px-md-5 has-white-color font-hero pt-2 pb-2">
				<?= esc_html( $term->name ); ?>
			</h1>
		</div>
		<?php if ( ! empty( $term->description ) ) : ?>
			<div class="id-container px-4 px-md-5 pt-5 pb-5">
				<div class="row">
					<div class="col-md-9 offset-md-3 fs-500 fw-light pb-5">
						<?= wp_kses_post( wpautop( $term->description ) ); ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>
	<section class="insight-type">
		<div class="insight-type-grid grid-type-1 id-container py-5 px-4 px-md-5">
			<div class="row g-5">
				<?php
				$counter = 0;

				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();
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
						<div class="<?= esc_attr( $col_class ); ?>">
							<a href="<?= esc_url( get_permalink() ); ?>" class="insight-type-grid__card">
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
									<div class="insight-type-grid__title"><?php the_title(); ?></div>
									<div class="insight-type-grid__date d-flex align-items-center gap-2">
										<?= esc_html( get_the_date( 'j F Y' ) ); ?>
										<?= cb_sanitise_svg( get_stylesheet_directory_uri() . '/img/arrow-n600.svg', 'insight-type-grid__arrow', 14, 13 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								</div>
							</a>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
	</section>
	<?php
	$cta = get_field( 'press_cta', 'option' );
	set_query_var( 'cta_choice', $cta );
	get_template_part( 'blocks/cb-cta' );
	?>
</main>
<?php
get_footer( cb_site_template_suffix() );
