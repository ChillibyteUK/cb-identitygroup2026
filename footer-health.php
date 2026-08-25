<?php
/**
 * Footer template for the Identity Coda 2026 theme.
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
			<div class="col-12 col-md-6 col-lg-4 order-9 order-md-1 d-flex justify-content-between flex-column">
				<img src="<?= esc_url( get_stylesheet_directory_uri() . '/img/IdentityHealth_Logo_Primary_DkBlueberry_RGB.svg' ); ?>"
					alt="Identity Health Logo" class="mb-3" width="297" height="auto" />
				<div>
					<strong><?= do_shortcode( '[contact_email]' ); ?></strong>
					<?= do_shortcode( '[social_icons class="fa-2x"]' ); ?>
				</div>
			</div>
			<!-- 2. Services -->
			<div class="col-12 col-sm-6 col-md-4 col-lg-2 order-2 order-md-3 order-lg-2 d-flex justify-content-between flex-column">
				<div>
					<div class="footer-title mb-3"><a href="/services/">Services</a></div>
					<?=
					wp_nav_menu(
						array(
							'theme_location' => 'footer_menu_services',
							'menu_class'     => 'footer__menu',
						)
					);
					?>
				</div>
				<div class="footer-title mt-4 mb-4"><a href="/work/">Work</a></div>
			</div>
			<!-- 3. About -->
			<div class="col-12 col-sm-6 col-md-4 col-lg-2 order-4 order-md-4 order-lg-3 d-flex justify-content-between flex-column">
				<div>
					<div class="footer-title mb-3"><a href="/about/">About</a></div>
					<?=
					wp_nav_menu(
						array(
							'theme_location' => 'footer_menu_about',
							'menu_class'     => 'footer__menu',
						)
					);
					?>
				</div>
				<div class="footer-title mb-4"><a href="/news/">News</a></div>
			</div>
			<!-- 4. Our Brands -->
			<div class="col-12 col-sm-6 col-md-4 col-lg-2 order-6 order-md-7 order-lg-4">
				<div class="footer-title mb-3">Identity Brands</div>
				<?=
				wp_nav_menu(
					array(
						'theme_location' => 'footer_menu_identity',
						'menu_class'     => 'footer__menu',
					)
				);
				?>
			</div>
			<!-- 5. Legal -->
			<div class="col-12 col-sm-6 col-md-4 col-lg-2 order-6 order-md-7 order-lg-4">
				<div class="footer-title mb-3">Legal &amp; info</div>
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
	<div class="id-container px-4 px-md-5 pt-4 footer__colophon">
		Identity Events Management Ltd, Registered Number - 04217845 | VAT Number - GB 813 0913 60
	</div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
