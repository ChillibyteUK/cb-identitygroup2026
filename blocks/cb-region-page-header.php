<?php
/**
 * CB Region Page Header Block Template
 *
 * @package  cb-identitygroup2026
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Block ID.
$block_id = $block['id'] ?? '';

$bg = get_query_var( 'background', get_field( 'background' ) );

?>
<style>
.cb-region-page-header {
	--_bg-url: url('<?= esc_url( wp_get_attachment_image_url( $bg, 'full' ) ); ?>');
}
</style>
<section id="<?php echo esc_attr( $block_id ); ?>" class="cb-region-page-header">
    <div class="cb-region-page-header__top">
        <div class="intro-overlay"></div>
        <div class="id-container px-4 px-md-5">
            <h1><?= wp_kses_post( get_field( 'title' ) ); ?></h1>
            <div class="row">
                <div class="col-md-9">
                    <div class="cb-region-page-header__title">
                        <div class="line"><div class="bar bar1"></div><div class="text text1">Experience</div></div>
                        <div class="line"><div class="bar bar2"></div><div class="text text2">Changes</div></div>
                        <div class="line"><div class="bar bar3"></div><div class="text text3">Everything</div></div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="cb-region-page-header__intro-text"><?= wp_kses_post( get_field( 'intro_text' ) ); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="cb-region-page-header__content-wrapper">
        <div class="region-overlay"></div>
        <div class="id-container px-4 px-md-5">
            <div class="row">
                <div class="col-md-9">
                    <div class="cb-region-page-header__intro">
                        <?= wp_kses_post( get_field( 'secondary_text' ) ); ?>
                    </div>
                </div>
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
		trigger: '.cb-region-page-header',
		start: 'top center',     // when the top of the section hits middle of viewport
		toggleActions: 'play none none none',
		once: true               // run once only
		}
	});

	tl.fromTo(".cb-region-page-header__title .bar1", {x: "-150%", opacity: 0}, {x: 0, opacity: 1, duration: 0.8}, 0)
	.fromTo(".cb-region-page-header__title .bar2", {x: "150%", opacity: 0}, {x: 0, opacity: 1, duration: 0.8}, 0.3)
	.fromTo(".cb-region-page-header__title .bar3", {x: "-150%", opacity: 0}, {x: 0, opacity: 1, duration: 0.8}, 0.6)
	.to(".cb-region-page-header__title .bar1", {rotate: -3, duration: 0.4}, "+=0.1")
	.to(".cb-region-page-header__title .bar2", {rotate: 5, duration: 0.4}, "-=0.3")
	.to(".cb-region-page-header__title .bar3", {rotate: -6, duration: 0.4}, "-=0.3")
	.to(".cb-region-page-header__title .text", {opacity: 1, duration: 0.6, stagger: 0.2}, "+=0.3");

	tl.timeScale(2);
});
</script>
		<?php
		// phpcs:enable WordPress.WP.EnqueuedResources.NonEnqueuedScript
	}
);