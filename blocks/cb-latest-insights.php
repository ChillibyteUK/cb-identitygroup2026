<?php
/**
 * Block template for CB Latest Insights.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$class = '';
if ( is_front_page() ) {
	$class = 'cb-latest-insights--front';
}

$taxonomy_filter = get_field( 'taxonomy_filter' );

?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-latest-insights <?= esc_attr( $class ); ?>">
	<div class="cb-latest-insights__pre-title">
		<div class="id-container pt-1 pb-0 px-4 px-md-5">
			LATEST NEWS &amp; INSIGHTS
		</div>
	</div>
	<div class="id-container py-5 px-4 px-md-5">

		<div class="row g-5">
			<?php
			$query_args = array(
				'post_type'      => 'post',
				'posts_per_page' => 6,
				'orderby'        => 'date',
				'order'          => 'DESC',
			);

			if ( ! empty( $taxonomy_filter ) && is_array( $taxonomy_filter ) ) {
				$query_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => 'theme',
						'field'    => 'term_id',
						'terms'    => $taxonomy_filter,
					),
				);
			}

			$q = new WP_Query( $query_args );
			if ( $q->have_posts() ) {
				$counter = 0;
				while ( $q->have_posts() ) {
					$q->the_post();
					++$counter;
					switch ( $counter ) {
						case 1:
							$col_class = 'col-md-4 col-lg-3 cb-latest-insights__card-1';
							break;
						case 2:
							$col_class = 'col-md-8 col-lg-6 cb-latest-insights__card-2';
							break;
						case 3:
							$col_class = 'col-md-8 col-lg-3 cb-latest-insights__card-3';
							break;
						case 4:
							$col_class = 'col-md-4 col-lg-6 cb-latest-insights__card-4';
							break;
						case 5:
							$col_class = 'col-md-6 col-lg-3 cb-latest-insights__card-5';
							break;
						case 6:
							$col_class = 'col-md-6 col-lg-3 cb-latest-insights__card-6';
							break;
						default:
							$col_class = 'col-md-6';
							break;
					}

					?>
			<div class="<?php echo esc_attr( $col_class ); ?>">			
				<a href="<?php echo esc_url( get_permalink() ); ?>" class="cb-latest-insights__card">
					<div class="cb-latest-insights__image-wrapper">
						<?php
						if ( get_the_post_thumbnail( get_the_ID() ) ) {
							echo get_the_post_thumbnail(
								get_the_ID(),
								'full',
								array(
									'class' => 'cb-latest-insights__image',
									'alt'   => get_post_meta(
										get_post_thumbnail_id( get_the_ID() ),
										'_wp_attachment_image_alt',
										true
									),
								)
							);
						} else {
							echo '<img src="' . esc_url( get_stylesheet_directory_uri() . '/img/default-post-image.png' ) . '" alt="" class="cb-latest-insights__image" />';
						}
						?>
					</div>
					<div class="cb-latest-insights__content">
						<div class="insight-type-grid__category">
							<?php
							$categories = get_the_category();
							if ( ! empty( $categories ) ) {
								echo esc_html( $categories[0]->name );
							}
							?>
						</div>
						<div class="cb-latest-insights__title">
							<?php the_title(); ?>
						</div>
						<div class="cb-latest-insights__date d-flex align-items-center gap-2">
							<?php echo get_the_date( 'j F Y' ); ?> 
 							<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-n900.svg' ); ?>" width=14 height=13 alt="" />
						</div>
					</div>
				</a>	
			</div>
					<?php
				}
				wp_reset_postdata();
			}
			?>
		</div>
	</div>
</section>
