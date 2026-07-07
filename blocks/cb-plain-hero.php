<?php
/**
 * Block template for CB Plain Hero.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$block_id         = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-plain-hero-' );
$title            = get_field( 'title' );
$intro            = get_field( 'intro' );
$background_image = get_field( 'background_image' );

$section_classes = array( 'cb-plain-hero' );
$section_style   = '';

if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}

if ( $background_image ) {
	$background_url = wp_get_attachment_image_url( $background_image, 'full' );

	if ( $background_url ) {
		$section_style     = sprintf( '--cb-plain-hero-bg: url(%s);', esc_url_raw( $background_url ) );
		$section_classes[] = 'cb-plain-hero--has-background-image';
	}
}

?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<div class="cb-plain-hero__title">
		<h1 class="id-container px-4 px-md-5">
			<span class="hero-animate"><?= esc_html( $title ); ?></span>
		</h1>
	</div>
	<div class="id-container px-4 px-md-5 pt-5">
		<div class="row">
			<div class="col-md-8">
				<div class="cb-plain-hero__intro pb-5 hero-animate hero-animate--delay-1">
					<?= wp_kses_post( $intro ); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if ( $section_style ) : ?>
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
			var translateY = (percent - 0.5) * 240; // Adjust the multiplier for more/less parallax
			section.style.setProperty('--cb-plain-hero-parallax-y', translateY.toFixed(1) + 'px');
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
