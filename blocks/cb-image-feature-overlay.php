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
$overlay_image = get_field( 'overlay_image' );
$content       = get_field( 'content' );

$section_classes = array( 'cb-image-feature-overlay' );

$presentation = get_field( 'presentation' );
if ( 'Hero' === $presentation ) {
	$section_classes[] = 'cb-image-feature-overlay--hero';
}

$section_style_declarations = array();
$height = get_field( 'block_height' );

// null: untouched block, this field never existed in its stored data (every
// Hero block before this field applied to both presentations, plus any
// really old Inline block predating the field entirely) - fall through to
// the SCSS var()'s own fallback for the current presentation (70vh Inline,
// near-fullscreen Hero) rather than forcing a number here. 70 exact: ACF's
// block editor eagerly serialises the field's default_value into a fresh
// block's data the moment it's inserted, so a Hero block that's had the
// slider left alone also reads as 70, not null - same fallback applies.
// Can't tell that apart from someone deliberately choosing 70vh for Hero;
// accepted as a rare, low-stakes edge case (2026-08-28).
$height_is_customized = null !== $height && '' !== (string) $height
	&& ! ( 'Hero' === $presentation && 70 === (int) $height );

if ( $height_is_customized ) {
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

if ( $overlay_image ) {
	$overlay_image_url = wp_get_attachment_image_url( $overlay_image, 'full' );

	if ( $overlay_image_url ) {
		$section_style_declarations[] = sprintf( '--_overlay-bg-url: url(%s);', esc_url_raw( $overlay_image_url ) );
	}
}

$section_style = implode( ' ', $section_style_declarations );

$block_link = get_field( 'cta_link' );

if ( 'Inline' === $presentation ) {
	$has_overlay_content = '' !== trim( (string) $content );
} else {
	$has_overlay_content = '' !== trim( (string) get_field( 'title' ) )
		|| ( ! empty( $block_link['url'] ) && ! empty( $block_link['title'] ) )
		|| '' !== trim( (string) get_field( 'cta_intro' ) );
}

?>
<section
	id="<?= esc_attr( $section_id ); ?>"
	class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"
	<?= $section_style ? 'style="' . esc_attr( $section_style ) . '"' : ''; ?>
>
	<?php if ( $has_overlay_content ) : ?>
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
				?>
			<hr>
			<div class="row g-5 pt-4 pb-5">
				<div class="col-lg-8">
					<?php
					$title_tag = in_array( get_field( 'title_semantic' ), array( 'h1', 'h2', 'h3' ), true ) ? get_field( 'title_semantic' ) : 'h1';
					$title_fs  = get_field( 'title_font_size' ) ?: 'fs-900';
					// health's own default is semibold, not fw-book - see
					// cb_health_image_feature_overlay_title_font_weight_default()
					// in inc/cb-site-tokens.php, which only covers the ACF admin
					// UI's pre-selected value, not this runtime fallback for
					// content saved before the field existed (2026-08-28).
					$title_fw = get_field( 'title_font_weight' ) ?: ( 'health' === cb_site_template_suffix() ? 'fw-semibold' : 'fw-book' );
					?>
					<<?= esc_attr( $title_tag ); ?> class="<?= esc_attr( $title_fs ); ?> <?= esc_attr( $title_fw ); ?>"><?= wp_kses_post( get_field( 'title' ) ); ?></<?= esc_attr( $title_tag ); ?>>
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
	<?php endif; ?>
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
