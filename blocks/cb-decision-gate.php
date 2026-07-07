<?php
/**
 * Block template for CB Decision Gate.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

// Support Gutenberg color picker.
$bg         = ! empty( $block['backgroundColor'] ) ? 'has-' . $block['backgroundColor'] . '-background-color' : '';
$fg         = ! empty( $block['textColor'] ) ? 'has-' . $block['textColor'] . '-color' : '';
$section_id = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-decision-gate-' );
$extra      = $block['className'] ?? '';

$background_image = get_field( 'background' );

$section_classes = array( 'cb-decision-gate' );
$section_classes = array_merge( $section_classes, array_filter( array( $bg, $fg, $extra ) ) );

$section_style = '';

if ( $background_image ) {
	$background_url = wp_get_attachment_image_url( $background_image, 'full' );

	if ( $background_url ) {
		$section_style     = sprintf( '--cb-decision-gate-bg: url(%s);', esc_url_raw( $background_url ) );
		$section_classes[] = 'cb-decision-gate--has-background-image';
	}
}

$section_title   = get_field( 'section_title' );
$section_content = get_field( 'section_content' );

$link_left = get_field( 'left_link' );
$link_right = get_field( 'right_link' );

?>
<section class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>" id="<?= esc_attr( $section_id ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<div class="cb-decision-gate__first py-5">
		<div class="id-container px-4 px-md-5 py-5">
			<div class="row">
				<div class="col-md-6">
					<h2 class="cb-decision-gate__section-title"><?= esc_html( $section_title ); ?></h2>
				</div>
				<div>
				<hr>
				</div>
				<div class="col-md-8">
					<div class="cb-decision-gate__section-content"><?= wp_kses_post( wpautop( $section_content ) ); ?></div>
				</div>
			</div>
		</div>
	</div>
	<div class="cb-decision-gate__second">
		<div class="id-container px-4 px-md-5 pb-5">
			<div class="cb-decision-gate__grid">
				<div class="cb-decision-gate__left">
					<a href="<?= esc_url( $link_left['url'] ); ?>" aria-label="<?= esc_attr( $link_left['title'] ); ?>">
						<div class="cb-decision-gate__title"><?= esc_html( get_field( 'left_title' ) ); ?></div>
						<div class="cb-decision-gate__content"><?= wp_kses_post( get_field( 'left_content' ) ); ?></div>
					</a>
					<a href="<?= esc_url( $link_left['url'] ); ?>" class="id-button"><?= esc_html( $link_left['title'] ); ?></a>
				</div>
				<div class="cb-decision-gate__right">
					<a href="<?= esc_url( $link_right['url'] ); ?>" aria-label="<?= esc_attr( $link_right['title'] ); ?>">
						<div class="cb-decision-gate__title"><?= esc_html( get_field( 'right_title' ) ); ?></div>
						<div class="cb-decision-gate__content"><?= wp_kses_post( get_field( 'right_content' ) ); ?></div>
					</a>
					<a href="<?= esc_url( $link_right['url'] ); ?>" class="id-button"><?= esc_html( $link_right['title'] ); ?></a>
				</div>
			</div>
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
			var translateY = (percent - 0.5) * 240;
			section.style.setProperty('--cb-decision-gate-parallax-y', translateY.toFixed(1) + 'px');
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
