<?php
/**
 * Custom taxonomies for the Hub simco theme.
 *
 * This file defines and registers custom taxonomies such as 'Services', 'Themes', and 'Regions'.
 *
 * @package cb-identitygroup2026
 */

/**
 * Register custom taxonomies for the theme.
 *
 * @return void
 */
function cb_register_taxes() {

	$args = array(
		'labels'             => array(
			'name'          => 'People',
			'singular_name' => 'Person',
		),
        'public'             => true,
        'publicly_queryable' => true,
        'hierarchical'       => true,
        'show_ui'            => true,
		'show_in_nav_menus'  => true,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => array(
			'slug'       => 'people',
			'with_front' => false,
		),
	);
    register_taxonomy( 'person', array( 'post' ), $args );

	// Was registered public=>false / publicly_queryable=>false / query_var=>false
	// for 'post' only, matching idtravel's own theme (which only uses 'theme'
	// for its internal default-term auto-assignment on blog posts, see below).
	// identity's and coda's own themes register it public/queryable and
	// extend it to 'case_study' too — case-study theme filtering (e.g.
	// cb-related-work's theme_filter field) can't work at all without that,
	// since the taxonomy relationship can't be set on a post type it isn't
	// registered for. Matching identity/coda's more permissive config here;
	// idtravel's own auto-assignment logic doesn't depend on these settings.
	$args = array(
		'labels'             => array(
			'name'          => 'Themes',
			'singular_name' => 'Theme',
		),
        'public'             => true,
        'publicly_queryable' => true,
        'hierarchical'       => true,
        'show_ui'            => true,
		'show_in_nav_menus'  => true,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => false,
	);
	register_taxonomy( 'theme', array( 'case_study', 'post' ), $args );

	// 'service' and 'region' are registered by identity's and coda's own
	// themes (for case_study + post) but were never carried over into this
	// shared theme at all — breaking any block that filters case studies by
	// service (cb-featured-work, cb-related-work, cb-case-study-key-stats)
	// or by region (cb-work-by-region) on every site, since the taxonomy
	// doesn't exist to filter against. idtravel doesn't use either (no case
	// studies), so registering them is harmless there.
	$args = array(
		'labels'             => array(
			'name'          => 'Services',
			'singular_name' => 'Service',
		),
		'public'             => true,
		'publicly_queryable' => true,
		'hierarchical'       => true,
		'show_ui'            => true,
		'show_in_nav_menus'  => true,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => false,
	);
	register_taxonomy( 'service', array( 'case_study', 'post' ), $args );

	$args = array(
		'labels'             => array(
			'name'          => 'Regions',
			'singular_name' => 'Region',
		),
		'public'             => true,
		'publicly_queryable' => true,
		'hierarchical'       => true,
		'show_ui'            => true,
		'show_in_nav_menus'  => true,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'show_in_rest'       => true,
		'rewrite'            => false,
	);
	register_taxonomy( 'region', array( 'case_study', 'post' ), $args );

}
add_action( 'init', 'cb_register_taxes' );

/**
 * Get or create the default theme term.
 *
 * @return int Default term ID, or 0 on failure.
 */
function cb_get_default_theme_term_id() {
	$term = get_term_by( 'slug', 'business-travel', 'theme' );

	if ( $term && ! is_wp_error( $term ) ) {
		return (int) $term->term_id;
	}

	$created = wp_insert_term(
		'Business Travel',
		'theme',
		array(
			'slug' => 'business-travel',
		)
	);

	if ( is_wp_error( $created ) ) {
		return 0;
	}

	return (int) $created['term_id'];
}

/**
 * Assign the default theme term when a post has none.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 * @param bool    $update  Whether this is an existing post being updated.
 * @return void
 */
function cb_assign_default_theme_term( $post_id, $post, $update ) {
	if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( 'post' !== $post->post_type ) {
		return;
	}

	if ( has_term( '', 'theme', $post_id ) ) {
		return;
	}

	$default_term_id = cb_get_default_theme_term_id();
	if ( ! $default_term_id ) {
		return;
	}

	wp_set_object_terms( $post_id, array( $default_term_id ), 'theme', false );
}
add_action( 'save_post_post', 'cb_assign_default_theme_term', 10, 3 );

/**
 * Backfill posts missing a theme term.
 *
 * @return void
 */
function cb_backfill_default_theme_term() {
	if ( get_option( 'cb_theme_default_term_backfill_done' ) ) {
		return;
	}

	$default_term_id = cb_get_default_theme_term_id();
	if ( ! $default_term_id ) {
		return;
	}

	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => array( 'publish', 'future', 'draft', 'pending', 'private' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy' => 'theme',
					'operator' => 'NOT EXISTS',
				),
			),
		)
	);

	foreach ( $posts as $post_id ) {
		wp_set_object_terms( $post_id, array( $default_term_id ), 'theme', false );
	}

	update_option( 'cb_theme_default_term_backfill_done', 1 );
}
add_action( 'admin_init', 'cb_backfill_default_theme_term' );
