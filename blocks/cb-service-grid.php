<?php
/**
 * Block template for CB Service Grid.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$block_id        = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-service-grid-' );
$extra_classes   = trim( (string) ( $block['className'] ?? '' ) );
$section_classes = array( 'cb-service-grid', 'pb-5' );
$start_row       = (int) get_field( 'start_row' );

if ( $start_row < 1 || $start_row > 3 ) {
	$start_row = 1;
}

$pattern_offset = ( $start_row - 1 ) * 2;
$row_offset     = $pattern_offset;

// Support Gutenberg color picker.
$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_classes = array_merge( $section_classes, array_filter( array( $bg, $fg ) ) );

if ( $extra_classes ) {
	$section_classes[] = $extra_classes;
}


?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="id-container px-4 px-md-5 py-5">
		<div class="cb-service-grid__grid" data-aos-stagger-group>
			<?php
			// get_field(), not have_rows()/the_row()/get_sub_field(): on pages with several
			// other ACF blocks that also have a field literally named "content", ACF's
			// have_rows() internal loop-stack can end up returning false here even though
			// the field data is present and correct - get_field() doesn't use that loop
			// stack, so it isn't affected (found via live var_dump debugging on Kinsta, where
			// have_rows('content') was false but get_field('content') returned the right
			// rows - 2026-08-28).
			$content_rows = get_field( 'content' );
			if ( $content_rows ) {
				foreach ( $content_rows as $item_index => $row ) {
					$layout_index  = $item_index + $pattern_offset;
					$pattern_index = $layout_index % 6;
					$cycle_index   = (int) floor( $layout_index / 6 );
					$base_row      = ( $cycle_index * 5 ) + 1 - $row_offset;
					$body_classes  = array( 'cb-service-grid__body' );

					if ( in_array( $pattern_index, array( 2, 3, 4 ), true ) ) {
						$body_classes[] = 'cb-service-grid__body--align-end';
					}

					switch ( $pattern_index ) {
						case 0:
							$image_style = sprintf( 'grid-column:1 / 3; grid-row:%1$d / span 2;', $base_row );
							$body_style  = sprintf( 'grid-column:3 / 4; grid-row:%1$d / span 1; --sg-justify:flex-start;', $base_row );
							break;
						case 1:
							$image_style = sprintf( 'grid-column:3 / 4; grid-row:%1$d / span 1;', $base_row + 1 );
							$body_style  = sprintf( 'grid-column:4 / 5; grid-row:%1$d / span 1; --sg-justify:flex-start;', $base_row + 1 );
							break;
						case 2:
							$image_style = sprintf( 'grid-column:2 / 3; grid-row:%1$d / span 1;', $base_row + 2 );
							$body_style  = sprintf( 'grid-column:1 / 2; grid-row:%1$d / span 1; --sg-justify:flex-start;', $base_row + 2 );
							break;
						case 3:
							$image_style = sprintf( 'grid-column:3 / 5; grid-row:%1$d / span 2;', $base_row + 2 );
							$body_style  = sprintf( 'grid-column:2 / 3; grid-row:%1$d / span 1; --sg-justify:flex-end;', $base_row + 3 );
							break;
						case 4:
							$image_style = sprintf( 'grid-column:2 / 3; grid-row:%1$d / span 1;', $base_row + 4 );
							$body_style  = sprintf( 'grid-column:1 / 2; grid-row:%1$d / span 1; --sg-justify:flex-start;', $base_row + 4 );
							break;
						default:
							$image_style = sprintf( 'grid-column:3 / 4; grid-row:%1$d / span 1;', $base_row + 4 );
							$body_style  = sprintf( 'grid-column:4 / 5; grid-row:%1$d / span 1; --sg-justify:flex-start;', $base_row + 4 );
							break;
					}
					?>
			<div class="cb-service-grid__item" data-aos="fade">
				<?= wp_get_attachment_image( $row['image'], 'large', false, array( 'class' => 'cb-service-grid__image', 'style' => $image_style ) ); ?>
				<div class="<?= esc_attr( implode( ' ', $body_classes ) ); ?>" style="<?= esc_attr( $body_style ); ?>">
					<div class="cb-service-grid__title"><?= esc_html( $row['title'] ); ?></div>
					<div class="cb-service-grid__content"><?= wp_kses_post( $row['content'] ); ?></div>
				</div>
			</div>
					<?php
				}
			}
			?>
		</div>
	</div>
</section>
