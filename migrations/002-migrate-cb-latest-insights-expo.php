<?php
/**
 * One-off content migration: cb/cb-latest-insights-expo ->
 * acf/cb-latest-insights (taxonomy_filter: Expo).
 *
 * cb-latest-insights already has a `taxonomy_filter` field whose own
 * instructions say it exists to replace this exact old block ("select
 * 'Expo' to replace the old cb-latest-insights-expo block") - the field
 * existed, but the saved content was never actually rewritten to use it.
 *
 * The old block's only other field, posts_per_page (3 or 6), has no
 * equivalent on cb-latest-insights (which always shows 6) - this is
 * accepted as a minor, cosmetic loss (showing 6 posts instead of 3) in
 * exchange for the section rendering at all, which it currently doesn't
 * for the one real instance (identityglobal.com's "World Expo" page).
 *
 * Only known real instance at the time this was written: World Expo page
 * (ID 1743 on idglobal-test). Generalised to run safely against any
 * site/environment, including ones with zero matches (a no-op).
 *
 * Idempotent: once a post is migrated it no longer contains the old block
 * name, so re-running this script is always safe.
 *
 * Usage (from the site's webroot):
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/002-migrate-cb-latest-insights-expo.php
 *     -> dry run, lists what WOULD change, writes nothing.
 *
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/002-migrate-cb-latest-insights-expo.php write
 *     -> applies the changes.
 *
 * Take a DB backup before running with the "write" argument.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this via WP-CLI: wp eval-file <path-to-this-file> [write]\n" );
}

global $wpdb;

$write = in_array( 'write', (array) ( $args ?? array() ), true );

$expo_term = get_term_by( 'slug', 'expo', 'theme' );

if ( ! $expo_term || is_wp_error( $expo_term ) ) {
	WP_CLI::error( "Could not find the 'expo' term in the 'theme' taxonomy on this site - aborting.", false );
	return;
}

$old_block_open = '<!-- wp:cb/cb-latest-insights-expo ';

$posts = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT ID, post_title, post_content FROM {$wpdb->posts}
		WHERE post_content LIKE %s
		AND post_status = 'publish'
		AND post_type NOT IN ( 'revision', 'nav_menu_item' )",
		'%' . $wpdb->esc_like( $old_block_open ) . '%'
	)
);

if ( ! $posts ) {
	WP_CLI::log( 'No posts contain a cb/cb-latest-insights-expo block. Nothing to do.' );
	return;
}

$pattern = '#<!-- wp:cb/cb-latest-insights-expo (\{.*?\}) /-->#s';

foreach ( $posts as $post ) {
	$changed = false;

	$new_content = preg_replace_callback(
		$pattern,
		function ( $matches ) use ( &$changed, $post, $expo_term ) {
			$block = json_decode( $matches[1], true );

			if ( ! is_array( $block ) ) {
				WP_CLI::warning( "Post {$post->ID}: found a cb-latest-insights-expo block but couldn't parse it - skipping this block, left untouched." );
				return $matches[0];
			}

			$new_data = array(
				'taxonomy_filter'   => array( (string) $expo_term->term_id ),
				'_taxonomy_filter'  => 'field_cb_latest_insights_tax_filter',
			);

			$new_block = array(
				'name' => 'acf/cb-latest-insights',
				'data' => $new_data,
				'mode' => $block['mode'] ?? 'edit',
			);

			$changed = true;

			return '<!-- wp:acf/cb-latest-insights ' . wp_json_encode( $new_block, JSON_UNESCAPED_SLASHES ) . ' /-->';
		},
		$post->post_content
	);

	if ( ! $changed ) {
		continue;
	}

	WP_CLI::log( sprintf( '%s post %d (%s)', $write ? 'Migrating' : '[dry run] Would migrate', $post->ID, $post->post_title ) );

	if ( ! $write ) {
		continue;
	}

	// $wpdb->update() writes the raw string as-is - unlike wp_update_post()/
	// wp_insert_post(), it does not call wp_unslash() on its way in, so
	// there's no risk of double-unslashing content that's already
	// unslashed.
	$wpdb->update(
		$wpdb->posts,
		array( 'post_content' => $new_content ),
		array( 'ID' => $post->ID )
	);

	clean_post_cache( $post->ID );

	WP_CLI::success( "Post {$post->ID} migrated." );
}

if ( ! $write ) {
	WP_CLI::log( "\nDry run only - re-run with --write to apply." );
}
