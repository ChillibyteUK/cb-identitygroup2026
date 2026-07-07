<?php
/**
 * Block template for CB Child Page Nav.
 *
 * Consolidates cb-business-travel-nav, cb-solutions-nav, and
 * cb-specialist-travel-nav (all idtravel) — each was the same
 * "list the child pages of a fixed parent slug as a card nav" pattern with
 * a hardcoded parent slug and minor per-site layout tweaks. This block
 * generalises the parent slug via config and normalises the layout to one
 * shared grid rather than replicating each site's bespoke column widths.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$parent_slug = get_field( 'parent_slug' );

if ( ! $parent_slug ) {
	return;
}

$parent_page = get_page_by_path( $parent_slug );

if ( ! $parent_page instanceof WP_Post ) {
	return;
}

$excluded_slug = get_field( 'excluded_slug' );
$excluded_id   = 0;
if ( $excluded_slug ) {
	$excluded_page = get_page_by_path( trim( $parent_slug, '/' ) . '/' . trim( $excluded_slug, '/' ) );
	$excluded_id   = $excluded_page instanceof WP_Post ? (int) $excluded_page->ID : 0;
}

$current_page_id   = get_queried_object_id();
$current_parent_id = $current_page_id ? wp_get_post_parent_id( $current_page_id ) : 0;
$is_child_page      = $current_page_id && (int) $parent_page->ID === (int) $current_parent_id;

$child_pages = get_pages(
	array(
		'child_of'    => $parent_page->ID,
		'parent'      => $parent_page->ID,
		'sort_column' => 'menu_order,post_title',
		'sort_order'  => 'ASC',
	)
);

$child_pages = array_values(
	array_filter(
		$child_pages,
		static function ( $page ) use ( $current_page_id, $excluded_id, $is_child_page ) {
			if ( $excluded_id && (int) $page->ID === $excluded_id ) {
				return false;
			}

			if ( $is_child_page && (int) $page->ID === (int) $current_page_id ) {
				return false;
			}

			return true;
		}
	)
);

if ( empty( $child_pages ) ) {
	return;
}

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-child-page-nav-' );
$extra_classes    = trim( (string) ( $block['className'] ?? '' ) );
$section_classes = array( 'cb-child-page-nav' );

if ( $extra_classes ) {
	$section_classes[] = $extra_classes;
}

$bg               = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg               = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_classes = array_merge( $section_classes, array_filter( array( $bg, $fg ) ) );

$cards = array();

foreach ( $child_pages as $page ) {
	$card_title   = get_field( 'nav_card_title', $page->ID );
	$card_summary = get_field( 'nav_card_summary', $page->ID );
	$card_image   = get_field( 'nav_card_image', $page->ID );

	$cards[] = array(
		'id'      => $page->ID,
		'title'   => $card_title ? $card_title : get_the_title( $page ),
		'summary' => $card_summary,
		'image'   => $card_image,
		'url'     => get_permalink( $page ),
	);
}

$active_card = $cards[0];

if ( empty( $active_card['title'] ) ) {
	return;
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="id-container px-4 px-md-5 py-5">
		<?php if ( get_field( 'title' ) ) : ?>
			<h2 class="my-5"><?= esc_html( get_field( 'title' ) ); ?></h2>
		<?php endif; ?>
		<div class="hr"></div>
		<div class="row gx-4 gy-4 align-items-start">
			<div class="col-lg-4">
				<div class="cb-child-page-nav__intro" data-child-page-nav-panel data-aos="fade">
					<h3 class="cb-child-page-nav__title" data-child-page-nav-title><?= esc_html( $active_card['title'] ); ?></h3>
					<div class="cb-child-page-nav__summary" data-child-page-nav-summary>
						<?= wp_kses_post( $active_card['summary'] ); ?>
					</div>
				</div>
			</div>

			<div class="col-lg-8 pb-5">
				<div class="row gx-4 gy-4" data-aos-stagger-group>
					<?php foreach ( $cards as $index => $card ) : ?>
						<div class="col-md-6 col-lg-3" data-aos="fade">
							<a
								class="cb-child-page-nav__card<?= 0 === $index ? ' is-active' : ''; ?>"
								href="<?= esc_url( $card['url'] ); ?>"
								data-child-page-nav-card
								data-card-title="<?= esc_attr( $card['title'] ); ?>"
								<?= $current_page_id === (int) $card['id'] ? 'aria-current="page"' : ''; ?>
							>
								<div class="cb-child-page-nav__card-media">
									<?php if ( $card['image'] ) : ?>
										<?= wp_get_attachment_image( $card['image'], 'full', false, array( 'class' => 'cb-child-page-nav__card-image' ) ); ?>
									<?php endif; ?>
								</div>
								<div class="cb-child-page-nav__card-title">
									<span class="cb-child-page-nav__card-title-text"><?= esc_html( $card['title'] ); ?></span>
									<span class="cb-child-page-nav__card-arrow"><?= cb_sanitise_svg( get_stylesheet_directory() . '/img/arrow-n600.svg', 'cb-child-page-nav__card-arrow-icon', 16, 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</div>
								<div class="cb-child-page-nav__card-summary" hidden>
									<?= wp_kses_post( $card['summary'] ); ?>
								</div>
							</a>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
