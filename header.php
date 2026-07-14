<?php
/**
 * The header for the theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package cb-identitygroup2026
 */

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
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/Suisse-Light.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/Suisse-Regular.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/Suisse-SemiBold.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">

	
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
	/*
	phpcs:disable
	?>
	<!-- Load Adobe Fonts asynchronously to prevent blocking -->
	<?php // phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet  ?>
	<link rel="stylesheet" href="https://use.typekit.net/hnr7skm.css" as="style">
	<?php
	phpcs:enable
	*/
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
<header id="wrapper-navbar" class="sticky py-2">
	<nav class="navbar navbar-expand-xl">
		<div class="d-flex px-4 px-md-5 gap-4 w-100 w-xl-auto">
            <div class="d-flex justify-content-between w-100 w-xl-auto align-items-center py-0">
                <a href="/" class="logo-clip" id="site-logo-clip" aria-label="Identity Travel Homepage">
					<div class="logo-inner" id="site-logo-inner">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 254.9 18.2">
						<defs>
							<style>
							.cls-1 {
								fill: #fff;
							}
							</style>
						</defs>
						<g>
							<polygon class="cls-1" points="150.31 0 150.31 1.95 156.63 1.95 156.63 18.2 158.86 18.2 158.86 1.95 165.18 1.95 165.18 0 150.31 0"/>
							<path class="cls-1" d="M175.69,10.63h-4.65V1.95h4.65c1.73,0,3.05.38,3.95,1.13.9.75,1.35,1.82,1.35,3.21s-.45,2.44-1.35,3.2c-.9.76-2.22,1.14-3.95,1.14M179.77,11.8c1.13-.5,1.99-1.22,2.59-2.16.6-.94.9-2.05.9-3.35,0-1.98-.67-3.52-2-4.63C179.92.56,178.08,0,175.74,0h-6.94v18.2h2.24v-5.64h4.71c.46,0,.89-.03,1.31-.07l4.05,5.71h2.47l-4.4-6.17c.2-.07.41-.14.6-.22"/>
							<path class="cls-1" d="M194.84,2.3l4.16,9.37h-8.31l4.15-9.37ZM193.73,0l-8.27,18.2h2.34l2.08-4.71h9.92l2.09,4.71h2.37L195.97,0h-2.24Z"/>
							<polygon class="cls-1" points="219.06 0 212.26 15.39 205.49 0 203.04 0 211.08 18.2 213.31 18.2 221.32 0 219.06 0"/>
							<polygon class="cls-1" points="224.47 0 224.47 18.2 237.49 18.2 237.49 16.25 226.7 16.25 226.7 9.93 235.98 9.93 235.98 7.98 226.7 7.98 226.7 1.95 237.13 1.95 237.13 0 224.47 0"/>
							<polygon class="cls-1" points="242.62 0 242.62 18.2 254.9 18.2 254.9 16.25 244.86 16.25 244.86 0 242.62 0"/>
							<polygon class="cls-1" points="69.1 0 69.1 12.68 55.63 0 52.17 0 52.17 18.2 56.08 18.2 56.08 5.61 69.55 18.2 73.01 18.2 73.01 0 69.1 0"/>
							<path class="cls-1" d="M130.26,0c2.26,2.91,6.67,9.02,6.67,9.02l6.59-9.02h4.68l-9.36,12.23v5.97h-3.91v-5.89L125.5,0h4.76Z"/>
							<polygon class="cls-1" points="104.45 0 104.45 3.33 112.42 3.33 112.42 18.2 116.33 18.2 116.33 3.33 124.3 3.33 124.3 0 104.45 0"/>
							<polygon class="cls-1" points="75.57 0 75.57 3.33 83.54 3.33 83.54 18.2 87.45 18.2 87.45 3.33 95.42 3.33 95.42 0 75.57 0"/>
							<rect class="cls-1" width="3.99" height="18.2"/>
							<polygon class="cls-1" points="30.41 0 30.41 18.2 33.74 18.2 48.68 18.2 48.68 14.87 33.74 14.87 33.74 10.77 48.68 10.77 48.68 7.43 33.74 7.43 33.74 3.33 48.68 3.33 48.68 0 30.41 0"/>
							<path class="cls-1" d="M11.97,3.35h5.47c1.97,0,3.47.46,4.45,1.38,1.02.94,1.53,2.41,1.53,4.36,0,3.88-1.96,5.77-5.98,5.77h-5.47V3.35ZM24.88,2.38C23.22.8,20.8,0,17.69,0h-9.6v18.2s9.6,0,9.6,0c6.23,0,9.67-3.24,9.67-9.11,0-2.89-.83-5.15-2.48-6.71"/>
							<rect class="cls-1" x="97.98" width="3.91" height="18.2"/>
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