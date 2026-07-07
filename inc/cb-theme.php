<?php
/**
 * LC Theme Functions
 *
 * This file contains theme-specific functions and customizations for the LC Harrier 2025 theme.
 *
 * @package cb-identitygroup2026
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

require_once CB_THEME_DIR . '/inc/cb-utility.php';
require_once CB_THEME_DIR . '/inc/cb-acf-theme-palette.php';
require_once CB_THEME_DIR . '/inc/cb-posttypes.php';
require_once CB_THEME_DIR . '/inc/cb-taxonomies.php';
require_once CB_THEME_DIR . '/inc/cb-site-tokens.php';


require_once CB_THEME_DIR . '/inc/cb-blocks.php';

/**
 * Editor styles: opt-in so WP loads editor.css in the block editor.
 * With theme.json present, this just adds your custom CSS on top (variables, helpers).
 */
add_action(
    'after_setup_theme',
    function () {
        add_theme_support( 'editor-styles' );
        add_editor_style( 'css/custom-editor-style.min.css' );
    },
    5
);

/**
 * Neutralise legacy palette/font-size support (if parent/Understrap adds them).
 * theme.json is authoritative, but some themes still register supports in PHP.
 * Remove them AFTER the parent has added them (high priority).
 */
add_action(
    'after_setup_theme',
    function () {
        remove_theme_support( 'editor-color-palette' );
        remove_theme_support( 'editor-gradient-presets' );
        remove_theme_support( 'editor-font-sizes' );
    },
    99
);

/**
 * (Optional) Ensure custom colours *aren’t* forcibly disabled by parent.
 * If Understrap disables custom colours, this re-enables them so theme.json works fully.
 */
add_filter( 'should_load_separate_core_block_assets', '__return_true' ); // performance nicety.

/**
 * Removes specific page templates from the available templates list.
 *
 * @param array $page_templates The list of page templates.
 * @return array The modified list of page templates.
 */
function child_theme_remove_page_template( $page_templates ) {
    unset(
        $page_templates['page-templates/blank.php'],
        $page_templates['page-templates/empty.php'],
        $page_templates['page-templates/left-sidebarpage.php'],
        $page_templates['page-templates/right-sidebarpage.php'],
        $page_templates['page-templates/both-sidebarspage.php']
    );
    return $page_templates;
}
add_filter( 'theme_page_templates', 'child_theme_remove_page_template' );

/**
 * Removes support for specific post formats in the theme.
 */
function remove_understrap_post_formats() {
    remove_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );
}
add_action( 'after_setup_theme', 'remove_understrap_post_formats', 11 );


if ( function_exists( 'acf_add_options_page' ) ) {
    acf_add_options_page(
        array(
            'page_title' => 'Site-Wide Settings',
            'menu_title' => 'Site-Wide Settings',
            'menu_slug'  => 'theme-general-settings',
            'capability' => 'edit_posts',
        )
    );
}

/**
 * Initializes widgets, menus, and theme supports.
 *
 * This function registers navigation menus, unregisters sidebars and menus,
 * and adds theme support for custom editor color palettes.
 */
function widgets_init() {

    register_nav_menus(
        array(
            'primary_nav'            => 'Primary Nav',
            'footer_menu_business'   => 'Footer Business',
            'footer_menu_about'      => 'Footer About',
            'footer_menu_solutions'  => 'Footer Solutions',
            'footer_menu_specialist' => 'Footer Specialist',
            'footer_menu_resources'  => 'Footer Resources',
            'footer_menu_identity'   => 'Footer Identity',
            'footer_menu_legal'      => 'Footer Legal & Info',
            // Used by footer-identity.php and footer-coda.php.
            'footer_menu_services'   => 'Footer Services',
            'footer_menu_media'      => 'Footer Media',
            'footer_menu_global'     => 'Footer Global',
        )
    );

    unregister_sidebar( 'hero' );
    unregister_sidebar( 'herocanvas' );
    unregister_sidebar( 'statichero' );
    unregister_sidebar( 'left-sidebar' );
    unregister_sidebar( 'right-sidebar' );
    unregister_sidebar( 'footerfull' );
    unregister_nav_menu( 'primary' );

    add_theme_support( 'disable-custom-colors' );
}
add_action( 'widgets_init', 'widgets_init', 11 );

// phpcs:disable
// add_filter('wpseo_breadcrumb_links', function( $links ) {
//     global $post;
//     if ( is_singular( 'post' ) ) {
//         $t = get_the_category($post->ID);
//         $breadcrumb[] = array(
//             'url' => '/guides/',
//             'text' => 'Guides',
//         );

//         array_splice( $links, 1, -2, $breadcrumb );
//     }
//     return $links;
// }
// );
// phpcs:enable


/**
 * Enqueues theme-specific scripts and styles.
 *
 * This function deregisters jQuery and disables certain styles and scripts
 * that are commented out for potential use in the theme.
 */
function hub_theme_enqueue() {
    $the_theme = wp_get_theme();

	// phpcs:disable
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox-plus-jquery.min.js', array(), $the_theme->get('Version'), true);
    // wp_enqueue_script('parallax', get_stylesheet_directory_uri() . '/js/parallax.min.js', array('jquery'), null, true);
    // wp_enqueue_style( 'splide-stylesheet', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/css/splide.min.css', array(), null );
    // wp_enqueue_script( 'splide-scripts', 'https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/js/splide.min.js', array(), null, true );
    // wp_enqueue_style('lightbox-stylesheet', get_stylesheet_directory_uri() . '/css/lightbox.min.css', array(), $the_theme->get('Version'));
    // wp_enqueue_script('lightbox-scripts', get_stylesheet_directory_uri() . '/js/lightbox.min.js', array(), $the_theme->get('Version'), true);
    // wp_enqueue_style( 'glightbox-style', 'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/css/glightbox.min.css', array(), $the_theme->get( 'Version' ) );
    // wp_enqueue_script( 'glightbox', 'https://cdnjs.cloudflare.com/ajax/libs/glightbox/3.3.1/js/glightbox.min.js', array(), $the_theme->get( 'Version' ), true );
    // wp_deregister_script( 'jquery' ); // needed by gravity forms
    // phpcs:enable

    wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
    wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
    wp_enqueue_style( 'aos-style', 'https://unpkg.com/aos@2.3.1/dist/aos.css', array() ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
    wp_enqueue_script( 'aos', 'https://unpkg.com/aos@2.3.1/dist/aos.js', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_script( 'lenis', 'https://unpkg.com/lenis@1.3.11/dist/lenis.min.js', array(), null, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'lenis-style', 'https://unpkg.com/lenis@1.3.11/dist/lenis.css', array() ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'tom-select', 'https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css', array(), '2.3.1' );
    wp_enqueue_script( 'tom-select', 'https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js', array(), '2.3.1', true );
}
add_action( 'wp_enqueue_scripts', 'hub_theme_enqueue' );

// Performance: Remove WordPress global styles and SVG filters (WP 6.0+).
// This prevents FOUC by removing unnecessary inline styles in the head.
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );


add_action(
    'admin_head',
    function () {
        echo '<style>
        .block-editor-page #wpwrap {
        overflow-y: auto !important;
        }
   </style>';
    }
);

add_action(
	'wp_footer',
	function () {
		?>
<script>
document.addEventListener('DOMContentLoaded', function () {
	if (typeof Lenis === 'undefined') return;
	const lenis = new Lenis({
		smooth: true,
		lerp: 0.1
	});
	function raf(time) {
		lenis.raf(time);
		requestAnimationFrame(raf);
	}
	requestAnimationFrame(raf);
});
</script>
		<?php
	}
);

/**
 * Add current-menu-parent class to /work/ menu item when viewing case study posts.
 */
add_filter(
	'wp_nav_menu_objects',
	function ( $items ) {
        $news_page_id = get_option( 'page_for_posts' );
        $work_url     = home_url( '/work/' );
        foreach ( $items as $item ) {
            // Remove highlight classes from both News and Work by default.
            if ( intval( $item->object_id ) === intval( $news_page_id ) ) {
                $item->classes = array_diff( $item->classes, array( 'current_page_parent', 'current-menu-parent', 'active' ) );
            }
            if ( $item->url === $work_url ) {
                $item->classes = array_diff( $item->classes, array( 'current_page_parent', 'current-menu-parent', 'active' ) );
            }
        }
        if ( is_singular( 'post' ) ) {
            // Highlight News for single posts.
            foreach ( $items as $item ) {
                if ( intval( $item->object_id ) === intval( $news_page_id ) ) {
                    $item->classes[] = 'current-menu-parent';
                    $item->classes[] = 'active';
                }
            }
        } elseif ( is_singular( 'case_study' ) ) {
			// Highlight Work for single case_study.
			foreach ( $items as $item ) {
				if ( $item->url === $work_url ) {
					$item->classes[] = 'current-menu-parent';
					$item->classes[] = 'active';
				}
			}
		}
		return $items;
	},
	20
);

/**
 * Add defer attribute to scripts for better performance.
 *
 * @param string $tag    The script tag HTML.
 * @param string $handle The script handle.
 * @return string Modified script tag.
 */
function add_defer_to_scripts( $tag, $handle ) {
    // Defer child theme scripts.
    if ( 'child-theme-scripts' === $handle ) {
        return str_replace( ' src', ' defer src', $tag );
    }

	// phpcs:disable
    // Defer jQuery when it's loaded (for Gravity Forms pages).
    // Note: This may break some plugins if they expect jQuery immediately.
    // if ( in_array( $handle, array( 'jquery-core', 'jquery-migrate' ), true ) ) {
    //     return str_replace( ' src', ' defer src', $tag );
    // }
	// phpcs:enable

    return $tag;
}
add_filter( 'script_loader_tag', 'add_defer_to_scripts', 10, 2 );

/**
 * Remove id attributes from nav menu links to prevent duplicate IDs.
 * This is necessary when using the same menu in both desktop and mobile (offcanvas) views.
 *
 * @param array $atts HTML attributes applied to the anchor element.
 * @return array Modified attributes without id.
 */
function remove_nav_menu_item_id( $atts ) {
    unset( $atts['id'] );
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'remove_nav_menu_item_id' );


/**
 * Shortcode to display parent categories of service taxonomy terms assigned to the current post.
 */
add_shortcode( 'service_parents', 'cb_service_parents_shortcode' );

/**
 * Displays parent categories of service taxonomy terms assigned to the current post.
 *
 * @return string HTML markup for parent service categories or empty string.
 */
function cb_service_parents_shortcode() {

    if ( ! is_singular() ) {
        return '';
    }

    $post_id = get_the_ID();
	$terms   = get_the_terms( $post_id, 'service' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}

    $parents = array();
    foreach ( $terms as $term ) {
        if ( $term->parent ) {
            $parent = get_term( $term->parent, 'service' );
            if ( $parent && ! is_wp_error( $parent ) ) {
                $parents[ $parent->term_id ] = $parent;
            }
        } else {
            $parents[ $term->term_id ] = $term;
        }
    }

	if ( empty( $parents ) ) {
		return '';
	}

    $output = '<ul class="service-parents">';
    foreach ( $parents as $parent ) {
        $output .= '<li><a href="' . esc_url( home_url( '/work/?service=' . $parent->slug ) ) . '">' . esc_html( $parent->name ) . '</a></li>';
    }
	$output .= '</ul>';

	return $output;
}


if ( ! function_exists( 'get_work_image' ) ) {
    /**
     * Returns the best available image for a work/case study post, in order:
     * 1. Post thumbnail
     * 2. Vimeo video thumbnail (if vimeo_url ACF field is set)
     * 3. First cb-full-image block image
     * 4. Default post image
     *
     * @param int    $post_id  The post ID.
     * @param string $css_class The CSS class to apply to the image.
     * @return string HTML <img> tag for the image.
     */
    function get_work_image( $post_id, $css_class = 'work-card__image' ) {
        // 1. Try post thumbnail
        if ( get_the_post_thumbnail( $post_id ) ) {
            return get_the_post_thumbnail(
                $post_id,
                'full',
                array(
                    'class' => $css_class,
                    'alt'   => get_post_meta(
                        get_post_thumbnail_id( $post_id ),
                        '_wp_attachment_image_alt',
                        true
                    ),
                )
            );
        }

        // 2. Try Vimeo video thumbnail as fallback
        $vimeo_url   = get_field( 'vimeo_url', $post_id );
        $vimeo_thumb = '';
        if ( $vimeo_url ) {
            if ( preg_match( '/vimeo\\.com\\/(?:video\/)?(\\d+)/', $vimeo_url, $matches ) ) {
                $vimeo_id = $matches[1];
                if ( function_exists( 'get_vimeo_data_from_id' ) ) {
                    $vimeo_thumb = get_vimeo_data_from_id( $vimeo_id, 'thumbnail_url' );
                }
            }
        }
        if ( $vimeo_thumb ) {
            return '<img src="' . esc_url( $vimeo_thumb ) . '" alt="" class="' . esc_attr( $css_class ) . '" />';
        }

        // 3. Try first cb-full-image block image
        $post_blocks = parse_blocks( get_the_content( null, false, $post_id ) );
        if ( ! function_exists( 'cb_find_first_full_image_url' ) ) {
            /**
             * Recursively find the first cb-full-image block image URL.
             *
             * @param array $blocks The parsed blocks array.
             * @return string Image URL if found, empty string otherwise.
             */
            function cb_find_first_full_image_url( $blocks ) {
                foreach ( $blocks as $block ) {
                    if (
                        isset( $block['blockName'] ) &&
                        'cb/cb-full-image' === $block['blockName'] &&
                        ! empty( $block['attrs']['data']['image'] )
                    ) {
                        $image_id = $block['attrs']['data']['image'];
                        $img_url  = wp_get_attachment_image_url( $image_id, 'full' );
                        if ( $img_url ) {
                            return $img_url;
                        }
                    }
                    if ( ! empty( $block['innerBlocks'] ) ) {
                        $found = cb_find_first_full_image_url( $block['innerBlocks'] );
                        if ( $found ) {
                            return $found;
                        }
                    }
                }
                return '';
            }
        }
        $full_image_url = cb_find_first_full_image_url( $post_blocks );
        if ( $full_image_url ) {
            return '<img src="' . esc_url( $full_image_url ) . '" alt="" class="' . esc_attr( $css_class ) . '" />';
        }

        // 4. Default post image
        return '<img src="' . esc_url( get_stylesheet_directory_uri() . '/img/default-post-image.png' ) . '" alt="" class="' . esc_attr( $css_class ) . '" />';
    }
}

// this populates the CB CTA select with the options defined in the ACF options page repeater field.
add_filter(
	'acf/load_field/name=cta_choice',
	function ( $field ) {
		$field['choices'] = array();

		// Get repeater rows from options.
		if ( have_rows( 'ctas', 'option' ) ) {
			while ( have_rows( 'ctas', 'option' ) ) {
				the_row();
				$title = get_sub_field( 'cta_id' );
				if ( $title ) {
					// Use title as both key and label, or set your own key.
					$field['choices'][ $title ] = $title;
				}
			}
		}

		return $field;
	}
);

add_filter(
	'acf/load_field/name=insight_cta',
	function ( $field ) {
		$field['choices'] = array();

		// Get repeater rows from options.
		if ( have_rows( 'ctas', 'option' ) ) {
			while ( have_rows( 'ctas', 'option' ) ) {
				the_row();
				$title = get_sub_field( 'cta_id' );
				if ( $title ) {
					// Use title as both key and label, or set your own key.
					$field['choices'][ $title ] = $title;
				}
			}
		}

		return $field;
	}
);
add_filter(
	'acf/load_field/name=press_cta',
	function ( $field ) {
		$field['choices'] = array();

		// Get repeater rows from options.
		if ( have_rows( 'ctas', 'option' ) ) {
			while ( have_rows( 'ctas', 'option' ) ) {
				the_row();
				$title = get_sub_field( 'cta_id' );
				if ( $title ) {
					// Use title as both key and label, or set your own key.
					$field['choices'][ $title ] = $title;
				}
			}
		}

		return $field;
	}
);

add_filter(
	'gform_submit_button',
	function ( $button, $form ) {
    	return '<button class="gform_button button id-button" style="background-color:transparent;" id="gform_submit_button_' . $form['id'] . '">' . esc_html( $form['button']['text'] ) . '</button>';
	},
	10,
	2
);


add_action(
	'after_setup_theme',
	function() {
		remove_filter( 'tiny_mce_before_init', 'understrap_tiny_mce_before_init' );
	},
	20
);

add_filter( 'tiny_mce_before_init', 'cb_tiny_mce_before_init', 20 );

/**
 * Limit TinyMCE style formats to child theme options.
 *
 * @param array $settings TinyMCE settings array.
 * @return array
 */
function cb_tiny_mce_before_init( $settings ) {
	$settings['style_formats'] = wp_json_encode(
		array(
			array(
				'title'   => 'Standfirst',
				'block'   => 'p',
				'classes' => 'font-lead',
			),
		)
	);
	$settings['style_formats_merge'] = false;

	return $settings;
}
