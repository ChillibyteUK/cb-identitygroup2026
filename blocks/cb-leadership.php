<?php
/**
 * Block template for CB Leadership.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

// coda's real design is a plain, static section with no colour-picker
// support at all (`class="leadership has-neutral-300-background-color"`,
// hardcoded) - idtravel's real design genuinely does support the
// Gutenberg colour picker with this dynamic dark-lines/light-lines logic.
// This file was originally built from idtravel's source, which silently
// became coda's design too until this was checked against coda's own
// real block directly.
if ( 'coda' === cb_site_template_suffix() ) {
	$section_classes = array( 'leadership', 'has-neutral-300-background-color' );
} else {
	// Support Gutenberg color picker.
	$bg              = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
	$fg              = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
	$line_class      = 'dark-lines';
	$section_classes = array( 'leadership' );

	if ( ! empty( $block['backgroundColor'] ) ) {
		if ( preg_match( '/(\d+)(?!.*\d)/', $block['backgroundColor'], $matches ) ) {
			$line_class = (int) $matches[1] >= 600 ? 'light-lines' : 'dark-lines';
		} else {
			$line_class = 'light-lines';
		}
	}

	$section_classes = array_merge( $section_classes, array_filter( array( $bg, $fg, $line_class ) ) );
}


?>
<section class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="id-container px-4 px-md-5">
		<div class="w-constrained py-5 fw-regular fs-500" style="--width:60ch;"><?= wp_kses_post( get_field( 'intro' ) ); ?></div>
		<div class="row g-5" data-aos-stagger-group>
			<?php
			while ( have_rows( 'team' ) ) {
				the_row();
				$name  = get_sub_field( 'name' );
				$lrole = get_sub_field( 'role' );
				$bio   = get_sub_field( 'bio' );
				?>
			<div class="col-md-4" data-aos="fade">
				<div class="leadership__item">
					<div class="leadership__image">
						<?= wp_get_attachment_image( get_sub_field( 'image' ), 'large' ); ?>
					</div>
					<div class="leadership__name">
						<?= esc_html( $name ); ?>
					</div>
					<div class="leadership__role">
						<?= esc_html( $lrole ); ?>
					</div>
					<div class="leadership__bio">
						<?= wp_kses_post( $bio ); ?>
					</div>
				</div>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</section>
