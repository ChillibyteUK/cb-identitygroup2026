<?php
/**
 * Template for displaying the "Press" category archive — identity's own
 * real design. WordPress's template hierarchy picks this up automatically
 * for the "press" category slug (category-{slug}.php); the shared theme
 * otherwise falls back to the generic archive template, which is what
 * idtravel/coda still get since they don't have this category.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$page_for_posts = get_option( 'page_for_posts' );

get_header( cb_site_template_suffix() );
?>
<main id="main" class="news-insights">
	<section class="news-hero has-primary-black-background-color pt-5">
		<h1 class="mt-5">
			<div class="id-container px-4 px-md-5 pt-1">
				Press, news &amp; media
			</div>
		</h1>
		<h2>
			<div class="id-container px-4 px-md-5 pt-2">
				Experience changes everything. Here’s how we’re shaping what’s next.
			</div>
		</h2>
		<div class="row">
			<div class="col-md-9 offset-md-3 news-hero__content">
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
	</section>
	<section class="insight-type has-purple-900-background-color">
		<div class="insight-type-grid grid-type-full id-container py-5 px-4 px-md-5">
			<div class="row g-5">
			<?php
			$args = array(
				'post_type'      => 'post',
				'post_status'    => array( 'publish' ),
				'orderby'        => 'date',
				'order'          => 'DESC',
				'posts_per_page' => -1,
				'category_name'  => 'press',
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
							<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-n600-solid.svg' ); ?>" width="14" height="13" alt="" />
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
		<a class="insight-type__header" href="/news/category/insights/">
			<div class="id-container d-flex align-items-center justify-content-between px-4 px-md-5">
				<div>Insights &amp; perspectives</div>
				<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width="65" height="60" alt="" class="cb-services-nav__item-icon" />
			</div>
		</a>
	</section>
	<?php
	$cta = get_field( 'press_cta', 'option' );
	set_query_var( 'cta_choice', $cta );
	get_template_part( 'blocks/cb-cta' );
	?>
</main>
<?php
get_footer( cb_site_template_suffix() );
