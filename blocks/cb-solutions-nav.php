<?php
/**
 * Block template for CB Solutions Nav.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$solutions_parent = get_page_by_path( 'solutions' );

if ( ! $solutions_parent instanceof WP_Post ) {
	return;
}

$current_page_id    = get_queried_object_id();
$current_parent_id  = $current_page_id ? wp_get_post_parent_id( $current_page_id ) : 0;
$is_solution_page   = $current_page_id && (int) $solutions_parent->ID === (int) $current_parent_id;
$intro_column_class = $is_solution_page ? 'col-lg-6' : 'col-lg-4';
$cards_column_class = $is_solution_page ? 'col-lg-6' : 'col-lg-8';
$card_column_class  = $is_solution_page ? 'col-md-6 col-lg-4' : 'col-md-6 col-lg-3';

$solution_pages = get_pages(
	array(
		'child_of'    => $solutions_parent->ID,
		'parent'      => $solutions_parent->ID,
		'sort_column' => 'menu_order,post_title',
		'sort_order'  => 'ASC',
	)
);

if ( $is_solution_page ) {
	$solution_pages = array_values(
		array_filter(
			$solution_pages,
			static function( $page ) use ( $current_page_id ) {
				return (int) $page->ID !== (int) $current_page_id;
			}
		)
	);
}

if ( empty( $solution_pages ) ) {
	return;
}

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-solutions-nav-' );
$extra_classes   = trim( (string) ( $block['className'] ?? '' ) );
$section_classes = array( 'cb-solutions-nav' );

if ( $extra_classes ) {
	$section_classes[] = $extra_classes;
}

// Support Gutenberg color picker.
$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_classes = array_merge( $section_classes, array_filter( array( $bg, $fg ) ) );

$cards = array();

foreach ( $solution_pages as $page ) {
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
		<?php
		if ( get_field( 'title' ) ) {
			?>
		<h2 class="my-5"><?= esc_html( get_field( 'title' ) ); ?></h2>
			<?php
		}
		?>
		<div class="hr"></div>
		<div class="row gx-4 gy-4 align-items-start">
			<div class="<?= esc_attr( $intro_column_class ); ?>">
				<div class="cb-solutions-nav__intro" data-solutions-nav-panel data-aos="fade">
					<h3 class="cb-solutions-nav__title" data-solutions-nav-title><?= esc_html( $active_card['title'] ); ?></h3>
					<div class="cb-solutions-nav__summary" data-solutions-nav-summary>
						<?= wp_kses_post( $active_card['summary'] ); ?>
					</div>
				</div>
			</div>

			<div class="<?= esc_attr( $cards_column_class ); ?> pb-5">
				<div class="row gx-4 gy-4" data-aos-stagger-group>
					<?php foreach ( $cards as $index => $card ) : ?>
						<div class="<?= esc_attr( $card_column_class ); ?>" data-aos="fade">
							<a
								class="cb-solutions-nav__card<?= 0 === $index ? ' is-active' : ''; ?>"
								href="<?= esc_url( $card['url'] ); ?>"
								data-solutions-nav-card
								data-card-title="<?= esc_attr( $card['title'] ); ?>"
								<?= $current_page_id === (int) $card['id'] ? 'aria-current="page"' : ''; ?>
							>
								<div class="cb-solutions-nav__card-media">
									<?php if ( $card['image'] ) : ?>
										<?= wp_get_attachment_image( $card['image'], 'full', false, array( 'class' => 'cb-solutions-nav__card-image' ) ); ?>
									<?php endif; ?>
								</div>
								<div class="cb-solutions-nav__card-title">
									<span class="cb-solutions-nav__card-title-text"><?= esc_html( $card['title'] ); ?></span>
									<span class="cb-solutions-nav__card-arrow"><?= cb_sanitise_svg( get_stylesheet_directory() . '/img/arrow-n600.svg', 'cb-solutions-nav__card-arrow-icon', 16, 16 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								</div>
								<div class="cb-solutions-nav__card-summary" hidden>
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
