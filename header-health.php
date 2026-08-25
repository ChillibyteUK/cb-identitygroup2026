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
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/SuisseIntl-Light.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/SuisseIntl-Regular.woff2' ); ?>"
        as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload"
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/SuisseIntl-Book.woff2' ); ?>"
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
                <a href="/" class="logo-clip" id="site-logo-clip" aria-label="Identity Health Homepage">
					<div class="logo-inner" id="site-logo-inner">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 350.13 25">
						<defs>
							<style>
							.cls-1h {
								fill: #008292;
							}
							</style>
						</defs>
						<g>
							<polygon class="cls-1h" points="100.28 0 94.92 0 94.92 17.41 76.42 0 71.67 0 71.67 25 77.03 25 77.03 7.7 95.53 25 100.28 25 100.28 0"/>
							<path class="cls-1h" d="M185.36,25h5.37v-8.2L203.57,0h-6.43l-9.05,12.39S182.03,4,178.93,0h-6.54l12.97,16.91v8.09Z"/>
							<polygon class="cls-1h" points="154.42 25 159.79 25 159.79 4.58 170.74 4.58 170.74 0 143.47 0 143.47 4.58 154.42 4.58 154.42 25"/>
							<polygon class="cls-1h" points="120.12 25 120.12 4.58 131.07 4.58 131.07 0 103.8 0 103.8 4.58 114.75 4.58 114.75 25 120.12 25"/>
							<rect class="cls-1h" y="0" width="5.48" height="25"/>
							<polygon class="cls-1h" points="41.77 0 41.77 25 46.35 25 66.87 25 66.87 20.42 46.35 20.42 46.35 14.79 66.87 14.79 66.87 10.21 46.35 10.21 46.35 4.58 66.87 4.58 66.87 0 46.35 0 41.77 0"/>
							<path class="cls-1h" d="M24.3,0h-13.19v25s13.19,0,13.19,0c8.56,0,13.28-4.45,13.28-12.52,0-3.97-1.14-7.07-3.4-9.22C31.9,1.1,28.57,0,24.3,0ZM23.95,20.4h-7.52V4.6h7.52c2.7,0,4.76.64,6.11,1.89,1.4,1.3,2.1,3.31,2.1,5.99,0,5.33-2.69,7.92-8.22,7.92Z"/>
							<rect class="cls-1h" x="134.59" y="0" width="5.37" height="25"/>
						</g>
						<g>
							<polygon class="cls-1h" points="224.78 10.96 209.78 10.96 209.78 0 206.71 0 206.71 25 209.78 25 209.78 13.64 224.78 13.64 224.78 25 227.89 25 227.89 0 224.78 0 224.78 10.96"/>
							<polygon class="cls-1h" points="237.8 13.64 250.55 13.64 250.55 10.96 237.8 10.96 237.8 2.68 252.13 2.68 252.13 0 234.73 0 234.73 25 252.63 25 252.63 22.32 237.8 22.32 237.8 13.64"/>
							<path class="cls-1h" d="M268.35,0l-11.36,25h3.21l2.86-6.46h13.63l2.87,6.46h3.25l-11.39-25h-3.07ZM264.18,16.04l5.7-12.88,5.72,12.88h-11.41Z"/>
							<polygon class="cls-1h" points="290.52 0 287.45 0 287.45 25 304.31 25 304.31 22.32 290.52 22.32 290.52 0"/>
							<polygon class="cls-1h" points="303.25 2.68 311.93 2.68 311.93 25 315 25 315 2.68 323.68 2.68 323.68 0 303.25 0 303.25 2.68"/>
							<polygon class="cls-1h" points="347.03 10.96 332.03 10.96 332.03 0 328.95 0 328.95 25 332.03 25 332.03 13.64 347.03 13.64 347.03 25 350.13 25 350.13 0 347.03 0 347.03 10.96"/>
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