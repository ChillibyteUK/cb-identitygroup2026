<?php
/**
 * Block template for CB Hero Prop CTA.
 *
 * @package Identity Travel
 */

defined( 'ABSPATH' ) || exit;

// Block ID.
$block_id = $block['id'] ?? '';

// Block classes.
$block_classes = array( 'block', 'cb-hero-prop-cta' );
if ( ! empty( $block['className'] ) ) {
    $block_classes[] = $block['className'];
}
if ( ! empty( $block['align'] ) ) {
    $block_classes[] = 'align' . $block['align'];
}

$section_attrs = 'class="pt-5 ' . esc_attr( implode( ' ', $block_classes ) ) . '"';
if ( ! empty( $block_id ) ) {
	$section_attrs = 'id="' . esc_attr( $block_id ) . '" ' . $section_attrs;
}
?>
<section <?= wp_kses_post( $section_attrs ); ?>>
	<div class="id-container px-4 px-md-5 py-5">
		<div class="cb-hero-prop-cta__container">
			<?php
			$section_title = get_field( 'title' );
			$section_title = is_string( $section_title ) ? trim( $section_title ) : '';
			if ( '' !== $section_title ) {
				$lines   = preg_split( '/<br\s*\/?>/i', $section_title );
				$lines   = array_filter( array_map( 'trim', $lines ) );
				$c       = 1;
				$wrapped = array_map(
					function ( $line ) use ( &$c ) {
						$output = '<div class="line"><div class="bar bar' . $c . '"></div><div class="text text' . $c . '">' . wp_kses_post( $line ) . '</div></div>';
						$c++;
						return $output;
					},
					$lines
				);
				?>
				<div class="row">
					<div class="col-md-8 mb-5 d-flex justify-content-center align-items-center">
						<div class="cb-hero-prop-cta__title ps-5 ps-md-0">
							<?= wp_kses_post( implode( '', $wrapped ) ); ?>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		<?php
		$content_heading  = get_field( 'content_heading' );
		$content          = get_field( 'content' );
		$cta_link         = get_field( 'link' );
		$link_url         = is_array( $cta_link ) ? ( $cta_link['url'] ?? '' ) : '';
		$link_title       = is_array( $cta_link ) ? ( $cta_link['title'] ?? '' ) : '';
		$link_target      = is_array( $cta_link ) ? ( $cta_link['target'] ?? '' ) : '';
		$link_target_attr = ! empty( $link_target ) ? ' target="' . esc_attr( $link_target ) . '"' : '';
		$has_content      = ! empty( $content_heading ) || ! empty( $content ) || ( $link_url && $link_title );
		if ( $has_content ) {
			?>
			<div class="cb-brand-title-text__content-wrapper pb-5">
				<div class="row">
					<div class="col-md-6 offset-md-6">
						<?php
						if ( ! empty( $content ) ) {
							?>
							<div class="cb-hero-prop-cta__content text-balance pb-4" data-aos="fade" data-aos-delay="100">
								<?= wp_kses_post( $content ); ?>
							</div>
							<?php
						}
						if ( $link_url && $link_title ) {
							?>
							<div class="cb-hero-prop-cta__link mt-4" data-aos="fade" data-aos-delay="200">
								<a href="<?= esc_url( $link_url ); ?>"<?= wp_kses_post( $link_target_attr ); ?> class="id-button">
									<?= esc_html( $link_title ); ?>
								</a>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
</section>
<?php
add_action(
	'wp_footer',
	function () {
		// phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedScript
		?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
	gsap.registerPlugin(ScrollTrigger);

	const tl = gsap.timeline({
		defaults: { ease: 'power3.out' },
		scrollTrigger: {
		trigger: '.cb-hero-prop-cta',
		start: 'top center',     // when the top of the section hits middle of viewport
		toggleActions: 'play none none none',
		once: true               // run once only
		}
	});

	tl.fromTo(".cb-hero-prop-cta__title .bar1", {x: "-150%", opacity: 0}, {x: 0, opacity: 1, duration: 0.8}, 0)
	.fromTo(".cb-hero-prop-cta__title .bar2", {x: "150%", opacity: 0}, {x: 0, opacity: 1, duration: 0.8}, 0.3)
	.fromTo(".cb-hero-prop-cta__title .bar3", {x: "-150%", opacity: 0}, {x: 0, opacity: 1, duration: 0.8}, 0.6)
	.to(".cb-hero-prop-cta__title .bar1", {rotate: -3, duration: 0.4}, "+=0.1")
	.to(".cb-hero-prop-cta__title .bar2", {rotate: 5, duration: 0.4}, "-=0.3")
	.to(".cb-hero-prop-cta__title .bar3", {rotate: -6, duration: 0.4}, "-=0.3")
	.to(".cb-hero-prop-cta__title .text", {opacity: 1, duration: 0.6, stagger: 0.2}, "+=0.3");

	tl.timeScale(2);
});
</script>
		<?php
		// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript
	}
);