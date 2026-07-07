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
        href="<?= esc_url( get_stylesheet_directory_uri() . '/fonts/SuisseIntl-SemiBold.woff2' ); ?>"
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
	?>
	<!-- Load Adobe Fonts asynchronously to prevent blocking -->
	<?php // phpcs:disable WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet  ?>
	<link rel="stylesheet" href="https://use.typekit.net/hnr7skm.css" as="style">
	<?php
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
	<nav class="navbar navbar-expand-lg">
		<div class="d-flex px-4 px-md-5 gap-4 w-100 w-lg-auto">
            <div class="d-flex justify-content-between w-100 w-lg-auto align-items-center py-0">
                <a href="/" class="logo-clip" id="site-logo-clip" aria-label="Coda Homepage">
					<div class="logo-inner" id="site-logo-inner">
						<svg id="Layer_2" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg" viewBox="141.08 0 1001.05 97.47">
							<defs>
								<style>
								.cls-1d {
									fill: #0d0d0c;
								}

								.cls-2d {
									fill: #478f00;
								}
								</style>
							</defs>
							<g id="Layer_2-2" data-name="Layer 2">
								<g id="Layer_1-2" data-name="Layer 1-2">
								<path class="cls-2d" d="M979.48,57.57c-.01,17.11,14.3,31.13,32.05,31.05,18.04-.07,32.1-13.8,32.18-31.12.08-17.31-14.2-31.01-32.19-30.98-17.78.03-32.02,13.83-32.04,31.04M1043.62,31.86c.03-.56.09-1.12.09-1.69,0-8.18-.01-16.36.02-24.54.01-3.65,2.3-5.74,6.03-5.63,1.82.05,2.9,1.06,3,2.84.02.34,0,.69,0,1.03v53.49c0,17.83-11.64,33.09-29.01,38.28-18.36,5.48-37.69-1.94-47.29-17.35-6.21-9.97-7.88-20.68-4.38-31.79,4.7-14.97,15.23-24.4,30.98-27.86,10.41-2.28,20.45-.63,29.68,4.75,3.48,2.03,6.64,4.47,9.31,7.47.35.4.64.86.96,1.29.2-.1.39-.19.59-.3"/>
								<path class="cls-2d" d="M1100.85,26.54c-17.69-.05-32.12,13.78-32.23,30.89-.11,17.03,14.32,31.23,32.12,31.19,17.92-.03,32.1-13.6,32.18-31.08.08-17.2-14.33-30.95-32.07-31M1132.45,82.99c-.49.55-.96,1.12-1.47,1.64-11.21,11.09-24.69,15.31-40.26,11.42-16.51-4.12-26.7-14.73-30.24-30.78-3.8-17.18,4.9-34.59,20.92-42.85,8.11-4.18,16.77-5.53,25.88-4.16,10.31,1.55,18.75,6.34,25.37,14.03,6.06,7.04,9.22,15.31,9.37,24.44.2,12.39.05,24.78.05,37.16,0,2.41-1.28,3.59-3.78,3.56-3.06-.03-5.18-1.96-5.27-4.92-.08-2.59-.03-5.19-.05-7.78,0-.52-.05-1.04-.08-1.56-.15-.07-.3-.14-.45-.2"/>
								<path class="cls-2d" d="M954.61,57.6c-.06-17.27-14.35-31.07-32.18-31.06-17.82,0-32.09,13.84-32.1,31.12,0,17.01,14.47,30.96,32.16,31,17.56.03,32.18-14.1,32.12-31.06M922.41,17.72c22.64.06,40.91,17.51,41.19,39.33.28,21.84-17.52,40.05-40.59,40.34-23.15.29-41.26-17.36-41.72-38.95-.48-22.51,17.79-40.44,41.12-40.72"/>
								<path class="cls-2d" d="M849.18,17.75c11.59.14,21.38,4.29,29.47,12.27,1.44,1.41,1.47,2.43.11,3.94-2.07,2.28-5.47,2.41-8.24.57-3.03-2.02-6.02-4.27-9.37-5.61-9.48-3.81-18.94-3.27-27.91,1.64-8.26,4.53-13.62,11.39-15.58,20.39-2.48,11.37.7,21.32,9.45,29.28,11.5,10.47,28.62,11.21,41.26,2.1.98-.7,1.85-1.53,2.82-2.24,2.45-1.79,5.76-1.36,7.59.94,1.14,1.44,1.11,2.96-.21,4.24-7.11,6.92-15.63,11.03-25.71,11.94-11.7,1.06-22.07-2.2-30.89-9.6-8.26-6.92-13.03-15.78-14-26.37-1.15-12.52,3.03-23.3,12.2-32.09,7.98-7.65,17.78-11.39,29.03-11.4"/>
								<polygon class="cls-1d" points="460.32 19 460.32 73.23 401.23 19 386.06 19 386.06 96.87 403.2 96.87 403.2 42.98 462.29 96.87 477.46 96.87 477.46 19 460.32 19"/>
								<path class="cls-1d" d="M728.69,18.99c9.9,12.45,29.27,38.59,29.27,38.59l28.92-38.59h20.53l-41.06,52.32v25.55h-17.14v-25.21l-41.42-52.67h20.9Z"/>
								<polygon class="cls-1d" points="615.42 19 615.42 33.25 650.41 33.25 650.41 96.87 667.55 96.87 667.55 33.25 702.55 33.25 702.55 19 615.42 19"/>
								<polygon class="cls-1d" points="488.69 19 488.69 33.25 523.68 33.25 523.68 96.87 540.82 96.87 540.82 33.25 575.82 33.25 575.82 19 488.69 19"/>
								<rect class="cls-1d" x="157.12" y="19" width="17.5" height="77.87"/>
								<polygon class="cls-1d" points="305.18 18.99 290.56 18.99 290.56 96.87 305.18 96.87 370.71 96.87 370.71 82.61 305.18 82.61 305.18 65.06 370.71 65.06 370.71 50.8 305.18 50.8 305.18 33.25 370.71 33.25 370.71 18.99 305.18 18.99"/>
								<path class="cls-1d" d="M210.6,33.31h24.02c8.64,0,15.2,1.99,19.52,5.9,4.46,4.04,6.72,10.32,6.72,18.67,0,16.6-8.59,24.67-26.24,24.67h-24.02s0-49.24,0-49.24ZM267.27,29.17c-7.28-6.75-17.9-10.18-31.55-10.18h-42.13v77.87h42.13c27.35,0,42.42-13.85,42.42-38.99,0-12.36-3.66-22.02-10.87-28.71"/>
								<rect class="cls-1d" x="587.05" y="19" width="17.14" height="77.87"/>
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