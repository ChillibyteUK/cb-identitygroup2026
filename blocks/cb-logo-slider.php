<?php
/**
 * CB Logo Slider Block Template.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$block_id    = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-logo-slider-' );
$logo_source = get_field( 'logo_source' ) ?: 'site_wide';
$logos       = 'specific' === $logo_source ? get_field( 'logo_gallery' ) : get_field( 'logos', 'option' );

if ( empty( $logos ) || ! is_array( $logos ) ) {
	return;
}

$classes = array( 'cb-logo-slider' );

if ( ! empty( $block['className'] ) ) {
	$classes[] = $block['className'];
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="id-container cb-logo-slider__marquee" aria-label="Logo slider">
		<div class="cb-logo-slider__track">
			<?php for ( $loop = 0; $loop < 2; $loop++ ) : ?>
				<div class="cb-logo-slider__group" <?= 1 === $loop ? 'aria-hidden="true"' : ''; ?>>
					<?php foreach ( $logos as $logo ) : ?>
						<div class="cb-logo-slider__item">
							<?= wp_get_attachment_image( $logo, 'full', false, array( 'class' => 'cb-logo-slider__logo img-fluid', 'alt' => get_post_meta( $logo, '_wp_attachment_image_alt', true ) ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endfor; ?>
		</div>
	</div>
</section>
<?php if ( 'identity' === cb_site_template_suffix() ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var section = document.getElementById('<?= esc_js( $block_id ); ?>');
	var track = section && section.querySelector('.cb-logo-slider__track');
	if (!track) return;
	var pxPerSecond = 80; // matches identityglobal.com's real cb-logo-slider pace
	track.style.animationDuration = (track.scrollWidth / 2 / pxPerSecond) + 's';
});
</script>
<?php endif; ?>
