<?php
/**
 * Block template for CB Specialist Travel Nav.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$specialist_parent = get_page_by_path( 'specialist-travel' );

if ( ! $specialist_parent instanceof WP_Post ) {
	return;
}


$current_page_id       = get_queried_object_id();
$current_parent_id     = $current_page_id ? wp_get_post_parent_id( $current_page_id ) : 0;
$is_specialist_page    = $current_page_id && (int) $specialist_parent->ID === (int) $current_parent_id;
$heading_column_class  = 'col-lg-4';
$summary_column_class  = $is_specialist_page ? 'col-lg-4' : 'col-lg-2';
$card_column_class     = 'col-md-6 col-lg-2';

$specialist_pages = get_pages(
	array(
		'child_of'    => $specialist_parent->ID,
		'parent'      => $specialist_parent->ID,
		'sort_column' => 'menu_order,post_title',
		'sort_order'  => 'ASC',
	)
);

if ( $is_specialist_page ) {
	$specialist_pages = array_values(
		array_filter(
			$specialist_pages,
			static function( $page ) use ( $current_page_id ) {
				return (int) $page->ID !== (int) $current_page_id;
			}
		)
	);
}

if ( empty( $specialist_pages ) ) {
	return;
}

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-specialist-travel-nav-' );
$extra_classes   = trim( (string) ( $block['className'] ?? '' ) );
$section_classes = array( 'cb-specialist-travel-nav' );

// Support Gutenberg color picker.
$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_classes = array_merge( $section_classes, array_filter( array( $bg, $fg ) ) );

if ( $extra_classes ) {
	$section_classes[] = $extra_classes;
}

$cards = array();

foreach ( $specialist_pages as $page ) {
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

$hide_heading_column = ! $is_specialist_page && 3 === count( $cards );

if ( $hide_heading_column ) {
	$summary_column_class = 'col-lg-6';
	$card_column_class    = 'col-md-6 col-lg-2';
}

$active_card = $cards[0];

if ( empty( $active_card['title'] ) ) {
	return;
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="id-container px-4 px-md-5 py-5">
		<div class="row gx-4 gy-4 align-items-start">
			<?php if ( ! $hide_heading_column ) : ?>
				<div class="<?= esc_attr( $heading_column_class ); ?>">
					<?php if ( get_field( 'title' ) ) : ?>
						<h2 class="cb-specialist-travel-nav__heading"><?= esc_html( get_field( 'title' ) ); ?></h2>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="<?= esc_attr( $summary_column_class ); ?>">
				<div class="cb-specialist-travel-nav__intro" data-solutions-nav-panel>
					<h3 class="cb-specialist-travel-nav__title" data-solutions-nav-title><?= esc_html( $active_card['title'] ); ?></h3>
					<div class="cb-specialist-travel-nav__summary" data-solutions-nav-summary>
						<?= wp_kses_post( $active_card['summary'] ); ?>
					</div>
				</div>
			</div>

			<?php foreach ( $cards as $index => $card ) : ?>
				<div class="<?= esc_attr( $card_column_class ); ?>">
					<a
						class="cb-specialist-travel-nav__card<?= 0 === $index ? ' is-active' : ''; ?>"
						href="<?= esc_url( $card['url'] ); ?>"
						data-solutions-nav-card
						data-card-title="<?= esc_attr( $card['title'] ); ?>"
						<?= $current_page_id === (int) $card['id'] ? 'aria-current="page"' : ''; ?>
					>
						<div class="cb-specialist-travel-nav__card-media">
							<?php if ( $card['image'] ) : ?>
								<?= wp_get_attachment_image( $card['image'], 'full', false, array( 'class' => 'cb-specialist-travel-nav__card-image' ) ); ?>
							<?php endif; ?>
						</div>
						<div class="cb-specialist-travel-nav__card-title">
							<span class="cb-specialist-travel-nav__card-title-text"><?= esc_html( $card['title'] ); ?></span>
							<span class="cb-specialist-travel-nav__card-arrow"><?= cb_sanitise_svg( get_stylesheet_directory() . '/img/arrow-n600.svg', 'cb-specialist-travel-nav__card-arrow-icon', 16, 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</div>
						<div class="cb-specialist-travel-nav__card-summary" hidden>
							<?= wp_kses_post( $card['summary'] ); ?>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
