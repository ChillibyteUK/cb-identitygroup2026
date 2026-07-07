<?php
/**
 * Footer template for the Identity Group 2025 theme.
 *
 * This file contains the footer section of the theme, including navigation menus,
 * office addresses, and colophon information.
 *
 * @package cb-identitygroup2026
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="footer-top"></div>

<footer class="footer pt-5 pb-4">
    <div class="id-container px-4 px-md-5">
        <div class="row pb-4 g-4">
			<!-- 1. Social icons -->
			<div class="col-12 col-md-6 col-lg-4 order-9 order-md-1">
				<!-- <div class="footer-title--lg mb-3">Connect. Share. Follow.</div> -->
				<?= do_shortcode( '[social_icons class="fa-2x"]' ); ?>
			</div>
			<!-- 2. Services -->
			<div class="col-12 col-sm-6 col-md-4 col-lg-2 order-2 order-md-3 order-lg-2">
				<div class="footer-title mb-4"><a href="/services/">Services</a></div>
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_services',
						'menu_class'     => 'footer__menu',
					)
				);
				?>
			</div>
			<!-- 3. About -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 order-4 order-md-4 order-lg-3">
				<div class="footer-title mb-4"><a href="/about/">About</a></div>
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_about',
						'menu_class'     => 'footer__menu',
					)
				);
				?>
			</div>
			<!-- 4. Our Brands -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-4 order-6 order-md-7 order-lg-4">
				<div class="footer-title mb-4"><a href="/about/#brands">Our Brands</a></div>
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_identity',
						'menu_class'     => 'footer__menu cols-lg-2',
					)
				);
				?>
			</div>
			<!-- ROW 2 -->
			<!-- 6. Email -->
			<div class="col-12 col-md-4 order-8 order-md-2 order-lg-6">
				<strong>
				<div class="mb-5">Let's talk.</div>
				<?= do_shortcode( '[contact_email]' ); ?>
				</strong>
			</div>
			<!-- 7. Work -->
			<div class="col-12 col-sm-6 col-md-4 col-lg-2 order-1 order-md-5 order-lg-7">
				<div class="footer-title mb-3"><a href="/work/">Work</a></div>
				<div class="footer-title mb-3"><a href="/world-expo/">World Expo</a></div>
				<div class="footer-title mb-4"><a href="/innovation/">Innovation Lab</a></div>
            </div>
			<!-- 8. News -->
			<div class="col-12 col-sm-6 col-md-4 col-lg-2 order-3 order-md-6 order-lg-8">
				<div class="footer-title mb-4"><a href="/news/">News</a></div>
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_media',
						'menu_class'     => 'footer__menu',
					)
				);
				?>
			</div>
			<!-- 9. Locations -->
			<div class="col-12 col-sm-6 col-md-4 col-lg-2 order-4 order-md-8 order-lg-9">
				<div class="footer-title mb-4"><a href="/contact/#locations">Locations</a></div>
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_global',
						'menu_class'     => 'footer__menu',
					)
				);
				?>
			</div>
			<!-- 10. Legal -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 order-7 order-md-9 order-lg-10">
				<div class="footer-title mb-4">Legal &amp; info</div>
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_legal',
						'menu_class'     => 'footer__menu',
					)
				);
				?>
			</div>
		</div>
	</div>
	<div class="footer__logo">
		<div class="id-container py-5 px-4 px-md-5">
			<div id="footer-logo-clip" class="footer__logo-clip">
				<div id="footer-logo-inner" class="footer__logo-inner">
					<!-- Inline logo SVG (long form) so we can animate precisely -->
					<svg id="footer-logo-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1332.6 81.83" aria-hidden="true" focusable="false">
						<defs>
							<style>
							.cls-1{fill: #fff}
							</style>
						</defs>
						<g>
							<g>
								<polygon class="cls-1" points="323.44 0 0 0 0 14.97 323.44 14.97 646.85 14.97 646.85 0 323.44 0"/>
								<polygon class="cls-1" points="0 33.42 0 48.4 323.44 48.4 646.85 48.4 646.85 33.42 323.44 33.42 0 33.42"/>
								<polygon class="cls-1" points="0 66.84 0 81.82 323.44 81.82 646.85 81.82 646.85 66.84 323.44 66.84 0 66.84"/>
							</g>
							<g>
								<polygon class="cls-1" points="976.96 56.99 976.96 0 994.52 0 994.52 81.83 978.97 81.83 918.43 25.21 918.43 81.83 900.87 81.83 900.87 0 916.41 0 976.96 56.99"/>
								<path class="cls-1" d="M1251.93,0c10.14,13.09,29.99,40.55,29.99,40.55l29.64-40.55h21.04l-42.07,54.98v26.85h-17.56v-26.48L1230.53.02h21.4v-.02Z"/>
								<polygon class="cls-1" points="1225.14 0 1225.14 14.99 1189.28 14.99 1189.28 81.83 1171.72 81.83 1171.72 14.99 1135.87 14.99 1135.87 0 1225.14 0"/>
								<polygon class="cls-1" points="1095.29 0 1095.29 14.99 1059.44 14.98 1059.44 81.83 1041.88 81.83 1041.88 14.98 1006.03 14.98 1006.03 0 1095.29 0"/>
								<rect class="cls-1" x="666.3" width="17.93" height="81.82"/>
								<polygon class="cls-1" points="885.15 14.98 885.15 0 818 0 803.02 0 803.02 81.83 818 81.83 885.15 81.83 885.15 66.86 818 66.85 818 48.41 885.15 48.41 885.15 33.43 818 33.43 818 14.98 885.15 14.98"/>
								<path class="cls-1" d="M779.16,10.7c-7.46-7.09-18.34-10.7-32.32-10.7h-43.17v81.82h43.17c28.02,0,43.46-14.55,43.46-40.97,0-12.99-3.75-23.14-11.14-30.16h0ZM721.1,15.05h24.61c8.85,0,15.58,2.08,20,6.19,4.57,4.24,6.88,10.84,6.88,19.62,0,17.44-8.8,25.92-26.89,25.92h-24.61V15.04h.01Z"/>
								<rect class="cls-1" x="1106.8" width="17.56" height="81.82"/>
							</g>
						</g>
					</svg>
				</div>
			</div>
		</div>
	</div>
	<div class="id-container px-4 px-md-5 pt-4 footer__colophon">
		Identity Events Management Ltd, Registered Number - 04217845 | VAT Number - GB 813 0913 60
	</div>
</footer>
<script>
(function(){
    const clip = document.getElementById('footer-logo-clip');
    const inner = document.getElementById('footer-logo-inner');
    const svg = document.getElementById('footer-logo-svg');
    if (!clip || !inner || !svg) return;

    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let triggered = false;

    function prepareAndAnimate() {
        clip.style.width = '100%';
        inner.style.transformOrigin = 'left center';
        inner.style.width = '200%';
        inner.style.display = 'block';
        inner.style.transform = 'translateX(0)';
        if (prefersReduced) {
            inner.style.transform = 'translateX(-50%)';
            return;
        }
        const animDuration = 1.6;
        const gsapEase = 'power3.out';
        if (window.gsap && typeof window.gsap.to === 'function') {
            window.gsap.to(inner, { xPercent: -50, duration: animDuration, ease: gsapEase });
        } else {
            inner.style.transition = 'transform ' + animDuration + 's cubic-bezier(.22,.9,.32,1)';
            requestAnimationFrame(() => { inner.style.transform = 'translateX(-50%)'; });
        }
    }

    function triggerIfVisible(el) {
        const rect = el.getBoundingClientRect();
        const vh = window.innerHeight || document.documentElement.clientHeight;
        return rect.top < vh && rect.bottom > 0;
    }

    const triggerEl = document.querySelector('.footer__colophon') || document.querySelector('.footer__logo') || clip;

    if (triggerEl) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach(entry => {
                if (triggered) return;
                if (entry.isIntersecting && entry.intersectionRatio > 0) {
                    triggered = true;
                    prepareAndAnimate();
                    obs.disconnect();
                }
            });
        }, { rootMargin: '0px 0px -10px 0px', threshold: [0.1] });

        observer.observe(triggerEl);

        // Immediately check if already visible (e.g., on fast loads or short pages)
        if (triggerIfVisible(triggerEl)) {
            triggered = true;
            prepareAndAnimate();
            observer.disconnect();
        }
    }

    let resizeTimer = null;
    window.addEventListener('resize', () => {
        if (triggered) return;
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            clip.style.width = '100%';
            inner.style.width = '200%';
        }, 120);
    });
})();
</script>

<?php wp_footer(); ?>
<!-- Insert these code snippets into your external site page -->
<script async src="https://go.identityglobal.com/forms/assets/scripts/external-form-handler-host.min.js" onload="SFDCFormHandler.init('https://go.identityglobal.com/forms')"></script>
</body>

</html>