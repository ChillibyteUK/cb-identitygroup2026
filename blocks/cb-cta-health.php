<?php
/**
 * Block template for CB CTA - health variant.
 *
 * Rendered by blocks/cb-cta.php via get_template_part() when cb_site is
 * 'health'. No clipped/masked foreground image (health drops the Image and
 * Mask fields entirely) - a single text column over a full-bleed parallax
 * background instead.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$block_id       = $args['block_id'] ?? wp_unique_id( 'cb-cta-' );
$cta_title      = $args['cta_title'] ?? '';
$content        = $args['content'] ?? '';
$link           = $args['link'] ?? array();
$background_url = $args['background_url'] ?? '';

?>
<?php if ( $background_url ) : ?>
	<style>
		#<?= esc_attr( $block_id ); ?> {
			--cb-cta-health-bg: url('<?= esc_url( $background_url ); ?>');
		}
	</style>
<?php endif; ?>

<section id="<?= esc_attr( $block_id ); ?>" class="cb-cta-health<?= $background_url ? ' cb-cta-health--has-background' : ''; ?>">
	<div class="id-container py-5 px-4 px-md-5">
		<div class="row">
			<div class="col-12 col-md-8 col-lg-6">
				<div class="cb-cta-health__content-wrap" data-aos="fade">
					<?php
					if ( $cta_title ) {
						?>
						<h2 class="cb-cta-health__title mb-5"><?= wp_kses_post( $cta_title ); ?></h2>
						<?php
					}
					if ( $content ) {
						?>
						<div class="cb-cta-health__content mb-5"><?= wp_kses_post( $content ); ?></div>
						<?php
					}
					if ( ! empty( $link ) && is_array( $link ) && ! empty( $link['url'] ) ) {
						?>
						<div class="cb-cta-health__button">
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

<?php if ( $background_url ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var section = document.getElementById(<?= wp_json_encode( $block_id ); ?>);
	if (!section) return;

	var ticking = false;

	function update() {
		var rect = section.getBoundingClientRect();
		var windowHeight = window.innerHeight;

		if (rect.bottom > 0 && rect.top < windowHeight) {
			var percent = (windowHeight - rect.top) / (windowHeight + rect.height);
			percent = Math.max(0, Math.min(1, percent));
			var translateY = (percent - 0.5) * 240;
			section.style.setProperty('--cb-cta-health-parallax-y', translateY.toFixed(1) + 'px');
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
<?php endif; ?>
