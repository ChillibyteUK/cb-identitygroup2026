<?php
/**
 * Block template for CB Work by Region.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

$region = get_field( 'region' );

if ( ! $region ) {
	return;
}

$q = new WP_Query(
	array(
		'post_type'      => 'case_study',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'region',
				'field'    => 'term_id',
				'terms'    => $region,
			),
		),
	)
);

if ( $q->have_posts() ) {
	?>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-related-work">
	<div class="id-container">
		<div class="row g-2">
	<?php
	$first = true;
	while ( $q->have_posts() ) {
		$q->the_post();
		$cols  = $first ? '12 mt-0' : '6';
		$first = false;
		?>
			<div class="col-md-<?= esc_attr( $cols ); ?>">
				<a href="<?= esc_url( get_the_permalink() ); ?>" class="cb-related-work__card">
					<?= get_work_image( get_the_ID(), 'cb-related-work__image' ); ?>
					<div class="cb-related-work__content px-4 px-md-5">
						<div class="cb-related-work__title">
							<?php the_title(); ?> <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/img/arrow-wh.svg' ); ?>" width="23" height="21" alt="" class="cb-services-nav__item-icon" />
						</div>
						<div class="cb-related-work__desc">
							<?php
							$post_blocks = parse_blocks( get_the_content( null, false, get_the_ID() ) );
							$subtitle    = cb_find_hero_subtitle( $post_blocks );
							if ( $subtitle ) {
								echo esc_html( $subtitle );
							} else {
								echo wp_kses_post( wp_trim_words( get_the_excerpt(), 18, '...' ) );
							}
							?>
						</div>
					</div>
				</a>
			</div>
					<?php
	}
	?>
		</div>
	</div>
</section>
	<?php
	wp_reset_postdata();
}
