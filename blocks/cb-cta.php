<?php
/**
 * Block template for CB CTA.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$block_id   = $block['id'] ?? wp_unique_id( 'cb-cta-' );
$cta_option = get_query_var( 'cta_choice', get_field( 'cta_choice' ) );

if ( ! $cta_option ) {
	$ctas = get_field( 'ctas', 'option' );
	if ( is_array( $ctas ) && ! empty( $ctas[0]['cta_id'] ) ) {
		$cta_option = $ctas[0]['cta_id'];
	}
}

$cta_title = '';
$content   = '';
$link      = array();
$bg        = 0;
$image     = 0;
$mask      = 'A';

if ( have_rows( 'ctas', 'option' ) ) {
	while ( have_rows( 'ctas', 'option' ) ) {
		the_row();
		$cta_id = get_sub_field( 'cta_id' );

		if ( $cta_id === $cta_option ) {
			$cta_title = get_sub_field( 'title' );
			$content   = get_sub_field( 'content' );
			$link      = get_sub_field( 'link' );
			$bg        = get_sub_field( 'background' );
			$image     = get_sub_field( 'image' );
			$mask      = get_sub_field( 'mask' ) ?: 'A';
			break;
		}
	}
}

$background_url = $bg ? wp_get_attachment_image_url( $bg, 'full' ) : '';
$allowed_masks  = array( 'A', 'B', 'C' );
$mask           = in_array( $mask, $allowed_masks, true ) ? $mask : 'A';

?>
<?php if ( $background_url ) : ?>
	<style>
		#<?= esc_attr( $block_id ); ?> {
			--_bg-url: url('<?= esc_url( $background_url ); ?>');
		}
	</style>
<?php endif; ?>

<section id="<?= esc_attr( $block_id ); ?>" class="cb-cta">
	<div class="id-container py-5 px-4 px-md-5">
		<div class="row g-5">
			<div class="col-md-6">
				<div class="cb-cta__clip-group cb-cta__clip-group--<?= esc_attr( $mask ); ?>">
					<?php
					if ( $image ) {
						echo wp_get_attachment_image(
							$image,
							'full',
							false,
							array(
								'class' => 'img-fluid cb-cta__image',
								'alt'   => get_post_meta( $image, '_wp_attachment_image_alt', true ),
							)
						);
					}
					?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="cb-cta__content-wrap d-flex flex-column justify-content-center h-100" data-aos="fade">
					<?php
					if ( $cta_title ) {
						?>
						<h2 class="cb-cta__title mb-5"><?= wp_kses_post( $cta_title ); ?></h2>
						<?php
					}
					if ( $content ) {
						?>
						<div class="cb-cta__content mb-5"><?= wp_kses_post( $content ); ?></div>
						<?php
					}
					if ( ! empty( $link ) && is_array( $link ) && ! empty( $link['url'] ) ) {
						?>
						<div class="cb-cta__button">
							<a href="<?= esc_url( $link['url'] ); ?>" class="id-button" target="<?= esc_attr( $link['target'] ?: '_self' ); ?>">
								<?= ! empty( $link['title'] ) ? esc_html( $link['title'] ) : esc_html__( 'Learn More', 'cb-identitygroup2026' ); ?>
							</a>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
	var section = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!section) return;

	var img = section.querySelector('.cb-cta__image');
	if (!img) return;

	var ticking = false;

	function update() {
		var rect = section.getBoundingClientRect();
		var windowHeight = window.innerHeight;

		if (rect.bottom > 0 && rect.top < windowHeight) {
			var percent = (windowHeight - rect.top) / (windowHeight + rect.height);
			percent = Math.max(0, Math.min(1, percent));
			var translateY = (percent - 0.5) * 240;
			img.style.transform = 'translateY(' + translateY.toFixed(1) + 'px)';
		}

		ticking = false;
	}

	function onScroll() {
		if (!ticking) {
			window.requestAnimationFrame(update);
			ticking = true;
		}
	}

	window.addEventListener('scroll', onScroll, { passive: true });
	window.addEventListener('resize', onScroll);
	onScroll();
});
</script>
