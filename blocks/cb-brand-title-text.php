<?php
/**
 * CB Brand Title Text Block Template
 *
 * @package cb-identitygroup2026
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Block ID.
$block_id = $block['id'] ?? '';

// Block classes.
$block_classes = array( 'block', 'cb-brand-title-text' );
if ( ! empty( $block['className'] ) ) {
	$block_classes[] = $block['className'];
}
if ( ! empty( $block['align'] ) ) {
	$block_classes[] = 'align' . $block['align'];
}

$section_attrs = 'class="' . esc_attr( implode( ' ', $block_classes ) ) . '"';
if ( ! empty( $block_id ) ) {
	$section_attrs = 'id="' . esc_attr( $block_id ) . '" ' . $section_attrs;
}

// Link field — safe access.
$cta_link         = get_field( 'link' );
$link_url         = is_array( $cta_link ) ? ( $cta_link['url'] ?? '' ) : '';
$link_title       = is_array( $cta_link ) ? ( $cta_link['title'] ?? '' ) : '';
$link_target      = is_array( $cta_link ) ? ( $cta_link['target'] ?? '' ) : '';
$link_target_attr = ! empty( $link_target ) ? ' target="' . esc_attr( $link_target ) . '"' : '';

?>
<section <?= wp_kses_post( $section_attrs ); ?>>
	<div class="cb-brand-title-text__pre-title">
		<h1 class="id-container px-4 px-md-5">
			<?= esc_html( get_field( 'pre_title' ) ); ?>
		</h1>
	</div>
	<div class="id-container px-4 px-md-5 pt-4 pt-md-5 pb-5">
		<div class="cb-brand-title-text__container pt-5 pb-4">
			<div class="row g-5">
				<div class="col-md-6 cb-brand-title-text__anim mb-md-5 d-flex align-items-center">
					<div class="cb-brand-title-text__title ps-5 ps-lg-0">
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
						echo wp_kses_post( implode( '', $wrapped ) );
					}
					?>
					</div>
				</div>
				<?php
				$content_heading = get_field( 'content_heading' );
				$content         = get_field( 'content' );
				$has_content     = ! empty( $content_heading ) || ! empty( $content ) || ( $link_url && $link_title );
				if ( $has_content ) {
					?>
				<div class="col-md-6">
					<?php if ( ! empty( $content_heading ) ) { ?>
					<div class="cb-brand-title-text__content-heading mb-4" data-aos="fade-up">
						<?= wp_kses_post( $content_heading ); ?>
					</div>
					<?php } ?>
					<?php if ( ! empty( $content ) ) { ?>
					<div class="cb-brand-title-text__content" data-aos="fade-up" data-aos-delay="100">
						<?= wp_kses_post( $content ); ?>
					</div>
					<?php } ?>
					<?php if ( $link_url && $link_title ) { ?>
					<div class="cb-brand-title-text__link mt-4" data-aos="fade-up" data-aos-delay="200">
						<a href="<?= esc_url( $link_url ); ?>"<?= wp_kses_post( $link_target_attr ); ?> class="id-button">
							<?= esc_html( $link_title ); ?>
						</a>
					</div>
					<?php } ?>
				</div>
					<?php
				}
				?>
			</div>
		</div>
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
		trigger: '.cb-brand-title-text',
		start: 'top center',     // when the top of the section hits middle of viewport
		toggleActions: 'play none none none',
		once: true               // run once only
		}
	});

	tl.fromTo(".cb-brand-title-text__title .bar1", {x: "-150%", opacity: 0}, {x: 0, opacity: 1, duration: 0.8}, 0)
	.fromTo(".cb-brand-title-text__title .bar2", {x: "150%", opacity: 0}, {x: 0, opacity: 1, duration: 0.8}, 0.3)
	.fromTo(".cb-brand-title-text__title .bar3", {x: "-150%", opacity: 0}, {x: 0, opacity: 1, duration: 0.8}, 0.6)
	.to(".cb-brand-title-text__title .bar1", {rotate: -3, duration: 0.4}, "+=0.1")
	.to(".cb-brand-title-text__title .bar2", {rotate: 5, duration: 0.4}, "-=0.3")
	.to(".cb-brand-title-text__title .bar3", {rotate: -6, duration: 0.4}, "-=0.3")
	.to(".cb-brand-title-text__title .text", {opacity: 1, duration: 0.6, stagger: 0.2}, "+=0.3");

	tl.timeScale(2);
});
</script>
		<?php
		// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript
	}
);
