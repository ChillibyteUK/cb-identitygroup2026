<?php
/**
 * Block template for CB Work by Region.
 *
 * DEPRECATED (2026-08-17) - superseded by cb-related-work's `region_filter`
 * field, which does the same job through the same query path as its existing
 * `theme_filter`. Kept registered only so the last instances still render;
 * nothing new should use it.
 *
 * Used on Identity Global only, on two pages: "middle-east" (region 65) and
 * "usa" (region 64). No migration script was written for so few instances -
 * to retire one, swap the block for CB Related Work, set Region Filter to the
 * same region, and set Count to -1 (this block was always unbounded, where
 * cb-related-work defaults to 4; only "usa", with 8 case studies to Middle
 * East's 4, actually needs it).
 *
 * Once both are swapped, delete this file, its
 * acf-json/group_cb_work_by_region.json field group, and its
 * acf_register_block_type() call in inc/cb-blocks.php. Nothing else references
 * the block - the `region` taxonomy itself stays, since cb-related-work's
 * region_filter now uses it.
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
		// The first card is full width but deliberately keeps the default
		// gutter compensation (2026-08-17). .row.g-2 carries
		// margin-top: -0.5rem, which this Bootstrap build cancels with a
		// matching margin-top on .row > * - so the mt-0 that used to be
		// here removed the compensation for this one card only, leaving it
		// 8px above the section's own top edge, overflowing upward and
		// painting over the bottom of whatever block precedes it (obvious
		// against cb-region-page-header on the region pages). Without
		// mt-0 the card's top lands exactly on the section top, which is
		// the flush look the mt-0 was reaching for anyway.
		$cols  = $first ? '12' : '6';
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
