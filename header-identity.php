<?php
/**
 * The header for the theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package cb-identitygroup2026
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( session_status() === PHP_SESSION_NONE ) {
    session_start();
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta
        charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, minimum-scale=1">
    <link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/SuisseIntl-Light.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/SuisseIntl-Regular.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/SuisseIntl-SemiBold.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
	<script src="https://cdn.c360a.salesforce.com/beacon/c360a/e8456c26-3421-4df1-bfd7-274c60e29ab8/scripts/c360a.min.js?wtcp_id=1NDQ200000006i9OAA"></script>
    <?php
    if ( ! is_user_logged_in() ) {
        if ( get_field( 'ga_property', 'options' ) ) {
            ?>
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async
                src="<?= esc_url( 'https://www.googletagmanager.com/gtag/js?id=' . get_field( 'ga_property', 'options' ) ); ?>">
            </script>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }
                gtag('js', new Date());
                gtag('config',
                    '<?= esc_js( get_field( 'ga_property', 'options' ) ); ?>'
                );
            </script>
        	<?php
        }
        if ( get_field( 'gtm_property', 'options' ) ) {
            ?>
            <!-- Google Tag Manager -->
            <script>
                (function(w, d, s, l, i) {
                    w[l] = w[l] || [];
                    w[l].push({
                        'gtm.start': new Date().getTime(),
                        event: 'gtm.js'
                    });
                    var f = d.getElementsByTagName(s)[0],
                        j = d.createElement(s),
                        dl = l != 'dataLayer' ? '&l=' + l : '';
                    j.async = true;
                    j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                    f.parentNode.insertBefore(j, f);
                })(window, document, 'script', 'dataLayer',
                    '<?= esc_js( get_field( 'gtm_property', 'options' ) ); ?>'
                );
            </script>
            <!-- End Google Tag Manager -->
    		<?php
        }
    }
	if ( get_field( 'google_site_verification', 'options' ) ) {
		echo '<meta name="google-site-verification" content="' . esc_attr( get_field( 'google_site_verification', 'options' ) ) . '" />';
	}
	if ( get_field( 'bing_site_verification', 'options' ) ) {
		echo '<meta name="msvalidate.01" content="' . esc_attr( get_field( 'bing_site_verification', 'options' ) ) . '" />';
	}

	wp_head();
	?>
</head>

<body <?php body_class( is_front_page() ? 'homepage' : '' ); ?>
    <?php understrap_body_attributes(); ?>>
    <?php
	do_action( 'wp_body_open' );
	if ( ! is_user_logged_in() ) {
    	if ( get_field( 'gtm_property', 'options' ) ) {
        	?>
            <!-- Google Tag Manager (noscript) -->
            <noscript><iframe
                    src="<?= esc_url( 'https://www.googletagmanager.com/ns.html?id=' . get_field( 'gtm_property', 'options' ) ); ?>"
                    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
            <!-- End Google Tag Manager (noscript) -->
    		<?php
    	}
	}
	?>
<header id="wrapper-navbar" class="fixed-top py-2">
	<nav class="navbar navbar-expand-lg">
		<div class="id-container d-flex px-4 px-md-5 gap-4">
            <div class="d-flex justify-content-between w-100 w-lg-auto align-items-center py-0">
                <a href="/" class="logo-clip" id="site-logo-clip" aria-label="Identity Homepage">
					<div class="logo-inner" id="site-logo-inner">
						<svg id="Layer_2" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 835.66 81.83">
							<defs>
								<style>
								.cls-1 {
									fill: #fff;
								}
								</style>
							</defs>
							<g id="Layer_1-2" data-name="Layer 1">
								<g id="logo-bars">
									<polygon class="cls-1" points="74.96 0 0 0 0 14.97 74.96 14.97 149.91 14.97 149.91 0 74.96 0"/>
									<polygon class="cls-1" points="0 33.42 0 48.4 74.96 48.4 149.91 48.4 149.91 33.42 74.96 33.42 0 33.42"/>
									<polygon class="cls-1" points="0 66.84 0 81.82 74.96 81.82 149.91 81.82 149.91 66.84 74.96 66.84 0 66.84"/>
								</g>
								<g>
								<polygon class="cls-1" points="480.02 56.98 480.02 0 497.58 0 497.58 81.82 482.03 81.82 421.49 25.2 421.49 81.82 403.93 81.82 403.93 0 419.47 0 480.02 56.98"/>
								<path class="cls-1" d="M754.99,0c10.14,13.09,29.99,40.55,29.99,40.55L814.62,0h21.04l-42.07,54.98v26.85h-17.56v-26.48L733.59.01h21.4Z"/>
								<polygon class="cls-1" points="728.2 0 728.2 14.98 692.34 14.98 692.34 81.82 674.78 81.82 674.78 14.98 638.93 14.98 638.93 0 728.2 0"/>
								<polygon class="cls-1" points="598.35 0 598.35 14.98 562.5 14.97 562.5 81.82 544.94 81.82 544.94 14.97 509.09 14.97 509.09 0 598.35 0"/>
								<rect class="cls-1" x="169.36" width="17.93" height="81.82"/>
								<polygon class="cls-1" points="388.21 14.97 388.21 0 321.06 0 306.08 0 306.08 81.82 321.06 81.82 388.21 81.82 388.21 66.85 321.06 66.84 321.06 48.4 388.21 48.4 388.21 33.42 321.06 33.42 321.06 14.97 388.21 14.97"/>
								<path class="cls-1" d="M282.22,10.69c-7.46-7.09-18.34-10.69-32.32-10.69h-43.17v81.82h43.17c28.02,0,43.46-14.55,43.46-40.97,0-12.99-3.75-23.14-11.14-30.16h0ZM224.16,15.04h24.61c8.85,0,15.58,2.08,20,6.19,4.57,4.24,6.88,10.84,6.88,19.62,0,17.44-8.8,25.92-26.89,25.92h-24.61V15.03h0Z"/>
								<rect class="cls-1" x="609.86" width="17.56" height="81.82"/>
								</g>
							</g>
						</svg>
					</div>
				</a>
				</div>
                <button class="navbar-toggler align-self-center" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div id="navbar" class="collapse navbar-collapse">
				<!-- Navigation -->
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary_nav',
						'container'      => false,
						'menu_class'     => 'navbar-nav w-100 justify-content-end gap-4 me-4',
						'fallback_cb'    => '',
						'depth'          => 3,
						'walker'         => new Understrap_WP_Bootstrap_Navwalker(),
					)
				);
				?>
            </div>
		</div>
	</nav>
</header>