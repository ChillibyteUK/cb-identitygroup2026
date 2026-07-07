<?php
/**
 * Block template for CB About Hero.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

$block_id         = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-about-hero-' );
$background_image = get_field( 'background_image' );
$overlay_image    = get_field( 'overlay_image' );
$title            = get_field( 'title' );
$intro            = get_field( 'intro' );
$content          = get_field( 'content' );

$section_classes = array( 'cb-about-hero' );

if ( ! empty( $block['className'] ) ) {
	$section_classes[] = $block['className'];
}

$section_style = '';

if ( $background_image ) {
	$background_url = wp_get_attachment_image_url( $background_image, 'full' );

	if ( $background_url ) {
		$section_style     = sprintf( '--cb-about-hero-bg: url(%s);', esc_url_raw( $background_url ) );
		$section_classes[] = 'cb-about-hero--has-background-image';
	}
}

if ( ! $title && ! $intro && ! $content && ! $overlay_image ) {
	return;
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<div class="cb-about-hero__top">
		<?php if ( $overlay_image ) : ?>
			<div class="cb-about-hero__overlay-image-wrap">
				<?= wp_get_attachment_image( $overlay_image, 'full', false, array( 'class' => 'cb-about-hero__overlay-image img-fluid', 'alt' => get_post_meta( $overlay_image, '_wp_attachment_image_alt', true ) ) ); ?>
			</div>
		<?php endif; ?>

		<div class="id-container px-4 px-md-5 py-5">
			<div class="cb-about-hero__top-inner">
				<?php if ( $title ) : ?>
					<hr class="cb-about-hero__rule">
					<h1 class="cb-about-hero__title hero-animate"><?= esc_html( $title ); ?></h1>
					<hr class="cb-about-hero__rule">
				<?php endif; ?>
				<?php if ( $intro ) : ?>
					<div class="cb-about-hero__intro hero-animate hero-animate--delay-1"><?= wpautop( esc_html( $intro ) ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<?php if ( $content ) : ?>
		<div class="cb-about-hero__content-wrap" data-aos="fade">
			<div class="id-container px-4 px-md-5 py-5">
				<div class="cb-about-hero__content">
					<?= wp_kses_post( $content ); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
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
			section.style.setProperty('--cb-about-hero-parallax-y', translateY.toFixed(1) + 'px');
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
