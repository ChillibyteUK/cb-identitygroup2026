<?php
/**
 * Block template for CB Business Travel Nav.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$business_travel_parent = get_page_by_path( 'business-travel' );

if ( ! $business_travel_parent instanceof WP_Post ) {
	return;
}

$excluded_page = get_page_by_path( 'business-travel/your-travel-programme' );
$excluded_id   = $excluded_page instanceof WP_Post ? (int) $excluded_page->ID : 0;

$current_page_id       = get_queried_object_id();
$current_parent_id     = $current_page_id ? wp_get_post_parent_id( $current_page_id ) : 0;
$is_business_travel    = $current_page_id && (int) $business_travel_parent->ID === (int) $current_parent_id;
$intro_column_class    = $is_business_travel ? 'col-lg-4' : 'col-lg-2';
$cards_column_class    = $is_business_travel ? 'col-lg-8' : 'col-lg-10';
$cards_columns_desktop = $is_business_travel ? '4' : '5';

$business_travel_pages = get_pages(
	array(
		'child_of'    => $business_travel_parent->ID,
		'parent'      => $business_travel_parent->ID,
		'sort_column' => 'menu_order,post_title',
		'sort_order'  => 'ASC',
	)
);

$business_travel_pages = array_values(
	array_filter(
		$business_travel_pages,
		static function( $page ) use ( $current_page_id, $excluded_id, $is_business_travel ) {
			if ( $excluded_id && (int) $page->ID === $excluded_id ) {
				return false;
			}

			if ( $is_business_travel && (int) $page->ID === (int) $current_page_id ) {
				return false;
			}

			return true;
		}
	)
);

if ( empty( $business_travel_pages ) ) {
	return;
}

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-business-travel-nav-' );
$extra_classes   = trim( (string) ( $block['className'] ?? '' ) );
$section_classes = array( 'cb-business-travel-nav' );

if ( $extra_classes ) {
	$section_classes[] = $extra_classes;
}

$cards = array();

foreach ( $business_travel_pages as $page ) {
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
			<div class="<?= esc_attr( $intro_column_class ); ?>">
				<div class="cb-business-travel-nav__intro" data-solutions-nav-panel data-aos="fade">
					<h3 class="cb-business-travel-nav__title" data-solutions-nav-title><?= esc_html( $active_card['title'] ); ?></h3>
					<div class="cb-business-travel-nav__summary" data-solutions-nav-summary>
						<?= wp_kses_post( $active_card['summary'] ); ?>
					</div>
				</div>
			</div>

			<div class="<?= esc_attr( $cards_column_class ); ?> pb-5">
				<div class="cb-business-travel-nav__cards" style="--cb-business-travel-columns: <?= esc_attr( $cards_columns_desktop ); ?>;" data-aos-stagger-group>
					<?php foreach ( $cards as $index => $card ) : ?>
						<a
							class="cb-business-travel-nav__card<?= 0 === $index ? ' is-active' : ''; ?>"
							href="<?= esc_url( $card['url'] ); ?>"
							data-solutions-nav-card
							data-card-title="<?= esc_attr( $card['title'] ); ?>"
							data-aos="fade"
							<?= $current_page_id === (int) $card['id'] ? 'aria-current="page"' : ''; ?>
						>
							<div class="cb-business-travel-nav__card-media">
								<?php if ( $card['image'] ) : ?>
									<?= wp_get_attachment_image( $card['image'], 'full', false, array( 'class' => 'cb-business-travel-nav__card-image' ) ); ?>
								<?php endif; ?>
							</div>
							<div class="cb-business-travel-nav__card-title">
								<span class="cb-business-travel-nav__card-title-text"><?= esc_html( $card['title'] ); ?></span>
								<span class="cb-business-travel-nav__card-arrow"><?= cb_sanitise_svg( get_stylesheet_directory() . '/img/arrow-n600.svg', 'cb-business-travel-nav__card-arrow-icon', 16, 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							</div>
							<div class="cb-business-travel-nav__card-summary" hidden>
								<?= wp_kses_post( $card['summary'] ); ?>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
