<?php
/**
 * Block template for CB Stats.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;

$block_id         = $block['anchor'] ?? $block['id'] ?? wp_unique_id( 'cb-stats-' );
$background_image = get_field( 'background_image' );
$show_hero         = get_field( 'show_hero' );
$hero_title         = get_field( 'hero_title' );

$section_classes = array( 'cb-stats' );
if ( $show_hero ) {
	$section_classes[] = 'cb-stats--hero';
}
$section_style   = '';

if ( $background_image ) {
	$background_url = wp_get_attachment_image_url( $background_image, 'full' );

	if ( $background_url ) {
		$section_style     = sprintf( '--cb-stats-bg: url(%s);', esc_url_raw( $background_url ) );
		$section_classes[] = 'cb-stats--has-background-image';
	}
}

$stats = array();

for ( $index = 1; $index <= 4; $index++ ) {
	$stats[] = array(
		'intro'  => get_field( 'stat_intro_' . $index ),
		'prefix' => get_field( 'stat_' . $index . '_prefix' ),
		'value'  => get_field( 'stat_' . $index ),
		'suffix' => get_field( 'stat_' . $index . '_suffix' ),
	);
}

if ( empty( $hero_title ) && ! array_reduce(
	$stats,
	function ( $carry, $stat ) {
		return $carry || '' !== (string) $stat['intro'] || '' !== (string) $stat['prefix'] || '' !== (string) $stat['value'] || '' !== (string) $stat['suffix'];
	},
	false
) ) {
	return;
}
?>
<section id="<?= esc_attr( $block_id ); ?>" class="<?= esc_attr( implode( ' ', $section_classes ) ); ?>"<?= $section_style ? ' style="' . esc_attr( $section_style ) . '"' : ''; ?>>
	<div class="id-container px-4 px-md-5 py-5">
		<?php if ( $show_hero && $hero_title ) : ?>
		<div class="cb-stats__hero-title-wrapper">
			<h1><?= esc_html( $hero_title ); ?></h1>
		</div>
		<?php endif; ?>
		<div class="row g-4 py-5 justify-content-center" data-aos-stagger-group>
			<?php
			if ( get_field( 'cta_message' ) || get_field( 'cta_link' ) ) {
				$l = get_field( 'cta_link' );
				?>
				<div class="col-12 col-lg-4" data-aos="fade">
				<?php
				if ( get_field( 'cta_message' ) ) {
					?>
					<div class="cb-stats__cta-message fs-500 mb-4"><?= esc_html( get_field( 'cta_message' ) ); ?></div>
					<?php
				}
				if ( $l ) {
					?>
					<a href="<?= esc_url( $l['url'] ); ?>" class="id-button id-button--primary"><?= esc_html( $l['title'] ); ?></a>
					<?php
				}
				?>
				</div>
				<?php
			}
			foreach ( $stats as $stat ) {
				?>
				<div class="col-md-6 col-lg-2" data-aos="fade">
					<div class="cb-stats__item">
						<?php if ( ! empty( $stat['intro'] ) ) : ?>
							<div class="cb-stats__intro"><?= esc_html( $stat['intro'] ); ?></div>
						<?php endif; ?>
						<div class="cb-stats__stat">
							<?php if ( '' !== (string) $stat['prefix'] ) : ?>
								<span class="cb-stats__stat-prefix"><?= esc_html( $stat['prefix'] ); ?></span>
							<?php endif; ?>
							<span class="cb-stats__stat-value" data-stat-target="<?= esc_attr( is_numeric( $stat['value'] ) ? $stat['value'] : 0 ); ?>">0</span>
							<?php if ( '' !== (string) $stat['suffix'] ) : ?>
								<span class="cb-stats__stat-suffix"><?= esc_html( $stat['suffix'] ); ?></span>
							<?php endif; ?>
						</div>
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
			section.style.setProperty('--cb-stats-parallax-y', translateY.toFixed(1) + 'px');
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
