<?php
/**
 * Block template for CB Image Feature Overlay.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$section_id    = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-image-feature-overlay-' );
$extra_classes = $block['className'] ?? '';
$image_id      = get_field( 'image' );
$content       = get_field( 'content' );

$section_classes = array( 'cb-image-feature-overlay' );

$presentation = get_field( 'presentation' );
if ( 'Hero' === $presentation ) {
	$section_classes[] = 'cb-image-feature-overlay--hero';
}

$section_style_declarations = array();
$height = get_field( 'block_height' );

if ( '' !== (string) $height ) {
	$section_style_declarations[] = sprintf( '--_height: %svh;', esc_attr( $height ) );
}

if ( $extra_classes ) {
	$section_classes[] = $extra_classes;
}
if ( $image_id ) {
	$image_url = wp_get_attachment_image_url( $image_id, 'full' );

	if ( $image_url ) {
		$section_style_declarations[] = sprintf( '--_bg-url: url(%s);', esc_url_raw( $image_url ) );
		$section_classes[] = 'cb-image-feature-overlay--has-background-image';
	}
}

$section_style = implode( ' ', $section_style_declarations );

?>
<section
	id="<?= esc_attr( $section_id ); ?>"
	class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"
	<?= $section_style ? 'style="' . esc_attr( $section_style ) . '"' : ''; ?>
>
	<div class="cb-image-feature-overlay__overlay">
		<div class="id-container px-4 px-md-5 py-4 py-md-5">
			<?php
			if ( 'Inline' === $presentation ) {
				?>
			<div class="cb-image-feature-overlay__content">
				<?= wpautop( esc_html( $content ) ); ?>
			</div>
				<?php
			} else {
				$block_link = get_field( 'cta_link' );
				?>
			<hr>
			<div class="row g-5 pt-4 pb-5">
				<div class="col-lg-8">
					<h1><?= wp_kses_post( get_field( 'title' ) ); ?></h1>
				</div>
				<div class="col-lg-4">
					<?php if ( ! empty( $block_link['url'] ) && ! empty( $block_link['title'] ) ) : ?>
						<a class="id-button mb-5 mt-3" href="<?= esc_url( $block_link['url'] ); ?>" target="<?= esc_attr( $block_link['target'] ?: '_self' ); ?>"><?= esc_html( $block_link['title'] ); ?></a>
					<?php endif; ?>
					<div class="cta-hero__cta-title"><?= esc_html( get_field( 'cta_intro' ) ); ?></div>
				</div>
			</div>
				<?php
			}
			?>
		</div>
	</div>
</section>

<?php if ( $section_style ) : ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	var section = document.getElementById(<?= wp_json_encode( $section_id ); ?>);
	if (!section) return;

	var ticking = false;

	function update() {
		var rect = section.getBoundingClientRect();
		var windowHeight = window.innerHeight;

		if (rect.bottom > 0 && rect.top < windowHeight) {
			var percent = (windowHeight - rect.top) / (windowHeight + rect.height);
			percent = Math.max(0, Math.min(1, percent));
			var translateY = (percent - 0.5) * 240; // Adjust the multiplier for more/less parallax
			section.style.setProperty('--cb-image-feature-overlay-parallax-y', translateY.toFixed(1) + 'px');
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
