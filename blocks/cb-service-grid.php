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
			if ( have_rows( 'content' ) ) {
				$item_index = 0;
				while ( have_rows( 'content' ) ) {
					the_row();
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
				<?= wp_get_attachment_image( get_sub_field( 'image' ), 'large', false, array( 'class' => 'cb-service-grid__image', 'style' => $image_style ) ); ?>
				<div class="<?= esc_attr( implode( ' ', $body_classes ) ); ?>" style="<?= esc_attr( $body_style ); ?>">
					<div class="cb-service-grid__title"><?= esc_html( get_sub_field( 'title' ) ); ?></div>
					<div class="cb-service-grid__content"><?= wp_kses_post( get_sub_field( 'content' ) ); ?></div>
				</div>
			</div>
					<?php
					++$item_index;
				}
			}
			?>
		</div>
	</div>
</section>
