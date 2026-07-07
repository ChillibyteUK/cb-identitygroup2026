<?php
/**
 * Understrap Child Theme functions and definitions
 *
 * @package UnderstrapChild
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

define( 'CB_THEME_DIR', get_stylesheet_directory() );

require_once CB_THEME_DIR . '/inc/cb-theme.php';
require_once CB_THEME_DIR . '/inc/cb-block-usage.php';

/**
 * Removes the parent themes stylesheet and scripts from inc/enqueue.php
 */
function understrap_remove_scripts() {
	wp_dequeue_style( 'understrap-styles' );
	wp_deregister_style( 'understrap-styles' );

	wp_dequeue_script( 'understrap-scripts' );
	wp_deregister_script( 'understrap-scripts' );

	// Remove WordPress block library styles (mostly cruft).
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-blocks-style' );
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );


/**
 * Enqueue our stylesheet and javascript file
 */

/**
 * Enqueue child-theme.min.css with filemtime versioning.
 * No dependencies to ensure immediate loading and prevent FOUC.
 */
function cb_enqueue_theme_css() {
	$rel = '/css/child-theme.min.css';
	$abs = get_stylesheet_directory() . $rel;
	wp_enqueue_style(
		'cb-theme',
		get_stylesheet_directory_uri() . $rel,
		array(), // No dependencies - load immediately.
		file_exists( $abs ) ? filemtime( $abs ) : null
	);
}
// Load at default priority (10) for early rendering, before parent removal at priority 20.
add_action( 'wp_enqueue_scripts', 'cb_enqueue_theme_css' );

/**
 * Enqueue child-theme.min.js with filemtime versioning.
 */
function cb_enqueue_theme_js() {
	$rel = '/js/child-theme.min.js';
	$abs = get_stylesheet_directory() . $rel;
	if ( file_exists( $abs ) ) {
		wp_enqueue_script(
			'cb-theme-js',
			get_stylesheet_directory_uri() . $rel,
			array(),
			filemtime( $abs ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'cb_enqueue_theme_js', 20 );


/**
 * Load the child theme's text domain
 */
function add_child_theme_textdomain() {
	load_child_theme_textdomain( 'cb-identitygroup2026', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );


/**
 * Overrides the theme_mod to default to Bootstrap 5
 *
 * This function uses the `theme_mod_{$name}` hook and
 * can be duplicated to override other theme settings.
 *
 * @return string
 */
function understrap_default_bootstrap_version() {
	return 'bootstrap5';
}
add_filter( 'theme_mod_understrap_bootstrap_version', 'understrap_default_bootstrap_version', 20 );



/**
 * Loads javascript for showing customizer warning dialog.
 */
function understrap_child_customize_controls_js() {
	wp_enqueue_script(
		'understrap_child_customizer',
		get_stylesheet_directory_uri() . '/js/customizer-controls.js',
		array( 'customize-preview' ),
		'20130508',
		true
	);
}
add_action( 'customize_controls_enqueue_scripts', 'understrap_child_customize_controls_js' );

/**
 * AJAX handler for post search functionality
 */
function cb_ajax_search_posts() {
	// Verify nonce for security.
	if ( ! wp_verify_nonce( $_POST['nonce'], 'post_search_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		wp_die( 'Security check failed' );
	}

	$search_term = isset( $_POST['search_term'] ) ? sanitize_text_field( wp_unslash( $_POST['search_term'] ) ) : '';
	$category    = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
	$year        = isset( $_POST['year'] ) ? sanitize_text_field( wp_unslash( $_POST['year'] ) ) : '';

	$args = array(
		'post_type'      => 'post',
		'post_status'    => array( 'publish', 'future' ),
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	// Add search term if provided.
	if ( ! empty( $search_term ) ) {
		$args['s'] = $search_term;
	}

	// Add category filter if provided.
	if ( ! empty( $category ) && 'all' !== $category ) {
		$args['category_name'] = $category;
	}

	// Add year filter if provided.
	if ( ! empty( $year ) && 'all' !== $year ) {
		$args['date_query'] = array(
			array(
				'year' => intval( $year ),
			),
		);
	}

	$query = new WP_Query( $args );

	ob_start();

	if ( $query->have_posts() ) {
		$d = 0;
		while ( $query->have_posts() ) {
			$query->the_post();

			// Get categories for data attribute.
			$categories = get_the_category();
			if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
				$first_category = $categories[0];
				if ( count( $categories ) > 1 ) {
					$categories = array_slice( $categories, 0, 1 );
				}
				$categories = implode( ' ', wp_list_pluck( $categories, 'slug' ) );
			} else {
				$categories = '';
			}
			?>
			<div class="col-md-6 col-lg-4" data-aos="fade" data-aos-delay="<?= esc_attr( $d ); ?>" data-category="<?= esc_attr( $categories ); ?>" data-year="<?= esc_attr( get_the_date( 'Y' ) ); ?>">
				<a href="<?= esc_url( get_permalink() ); ?>" class="latest-insights__item">
					<div class="latest-insights__img-wrapper">
						<?php
						$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
						if ( $thumbnail_id ) {
							echo wp_get_attachment_image(
								$thumbnail_id,
								'large',
								false,
								array(
									'class' => 'img-fluid mb-3',
									'alt'   => '',
								)
							);
						}
						?>
					</div>
					<div class="latest-insights__inner">
						<h3><?= esc_html( get_the_title() ); ?></h3>
						<div class="latest-insights__meta">
							<span><i class="fa-regular fa-calendar"></i> <?= esc_html( get_the_date( 'jS F Y' ) ); ?></span>
							<span><i class="fa-regular fa-clock"></i> <?= wp_kses_post( estimate_reading_time_in_minutes( get_the_content() ) ); ?> minute read</span>
						</div>
						<div><?= esc_html( get_the_excerpt() ); ?></div>
					</div>
				</a>
			</div>
			<?php
			$d += 100;
		}
	} else {
		echo '<div class="col-12"><p class="text-center">No posts found matching your criteria.</p></div>';
	}

	wp_reset_postdata();

	$html = ob_get_clean();

	wp_send_json_success( array( 'html' => $html ) );
}

add_action( 'wp_ajax_search_posts', 'cb_ajax_search_posts' );
add_action( 'wp_ajax_nopriv_search_posts', 'cb_ajax_search_posts' );

/**
 * Add data-text attribute to primary nav links for width reservation.
 *
 * @param array  $atts Link attributes.
 * @param object $item Menu item.
 * @param object $args Menu args.
 * @return array
 */
function cb_add_primary_nav_data_text( $atts, $item, $args ) {
	if ( isset( $args->theme_location ) && 'primary_nav' === $args->theme_location ) {
		$label             = wp_strip_all_tags( $item->title );
		$atts['data-text'] = $label;
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'cb_add_primary_nav_data_text', 10, 3 );

/**
 * Add submenu indicator markup to primary nav items with children.
 *
 * @param string  $title Menu item title.
 * @param WP_Post $item  Menu item object.
 * @param array   $args  Menu args.
 * @param int     $depth Menu depth.
 * @return string
 */
function cb_add_primary_nav_submenu_indicator( $title, $item, $args, $depth ) {
	if ( ! isset( $args->theme_location ) || 'primary_nav' !== $args->theme_location ) {
		return $title;
	}

	if ( 0 !== (int) $depth || empty( $item->classes ) || ! in_array( 'menu-item-has-children', $item->classes, true ) ) {
		return $title;
	}

	return $title . '<span class="nav-submenu-indicator" aria-hidden="true"></span>';
}
// add_filter( 'nav_menu_item_title', 'cb_add_primary_nav_submenu_indicator', 10, 4 );

/**
 * Render menu divider items as non-clickable labels.
 *
 * @param string   $item_output The menu item's starting HTML output.
 * @param WP_Post  $item        Menu item data object.
 * @param int      $depth       Depth of menu item.
 * @param stdClass $args        An object of wp_nav_menu() arguments.
 * @return string
 */
function cb_render_primary_nav_menu_divider( $item_output, $item, $depth, $args ) {
	if ( ! isset( $args->theme_location ) || 'primary_nav' !== $args->theme_location ) {
		return $item_output;
	}

	if ( empty( $item->classes ) || ! in_array( 'menu-divider-label', $item->classes, true ) ) {
		return $item_output;
	}

	return '<span class="dropdown-divider-label">' . esc_html( $item->title ) . '</span>';
}
add_filter( 'walker_nav_menu_start_el', 'cb_render_primary_nav_menu_divider', 10, 4 );
