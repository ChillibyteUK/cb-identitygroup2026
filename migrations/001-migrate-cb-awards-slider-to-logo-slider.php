<?php
/**
 * One-off content migration: cb/cb-awards-slider + cb/cb-sport-logos ->
 * acf/cb-logo-slider.
 *
 * Both old blocks store the exact same shape of data ({"logos": [...ids]}})
 * and were folded into cb-logo-slider's "logo_source: specific" mode during
 * the theme consolidation, but the saved block content was never rewritten.
 * This finds every post still using either old block comment and rewrites
 * it in place to the new schema.
 *
 * Known real instances at the time this was written: identityglobal.com's
 * "About" page (cb-awards-slider, migrated by hand on idglobal-test, ID 510,
 * 2026-07-09) and 2 cb-sport-logos instances found during a full-site sweep
 * the same day. This script generalises both fixes to run safely against
 * any site/environment, including ones with zero matches (a no-op) or more
 * than one.
 *
 * Idempotent: once a post is migrated it no longer contains either old
 * block name, so re-running this script is always safe.
 *
 * Usage (from the site's webroot):
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/001-migrate-cb-awards-slider-to-logo-slider.php
 *     -> dry run, lists what WOULD change, writes nothing.
 *
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/001-migrate-cb-awards-slider-to-logo-slider.php write
 *     -> applies the changes.
 *
 * Take a DB backup before running with the "write" argument.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this via WP-CLI: wp eval-file <path-to-this-file> [write]\n" );
}

global $wpdb;

// WP-CLI's eval-file forwards extra positional args via $args - it does
// NOT expose them through $GLOBALS['argv'] (WP-CLI's own arg parser
// rejects an unrecognised "--write" flag before the script ever runs).
$write = in_array( 'write', (array) ( $args ?? array() ), true );

$old_block_names = array( 'cb/cb-awards-slider', 'cb/cb-sport-logos' );

foreach ( $old_block_names as $old_block_name ) {
	$old_block_open = '<!-- wp:' . $old_block_name . ' ';

	// Revisions/autosaves carry old snapshots of post_content forever by
	// design - matching them here would be historically wrong (they're not
	// rendered anywhere) and would blow up the match count enormously (every
	// revision saved before the live migration also contains the old block).
	// Only touch actual live content.
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
		WP_CLI::log( "No posts contain a {$old_block_name} block. Nothing to do." );
		continue;
	}

	$pattern = '#<!-- wp:' . preg_quote( $old_block_name, '#' ) . ' (\{.*?\}) /-->#s';

	foreach ( $posts as $post ) {
		$changed = false;

		$new_content = preg_replace_callback(
			$pattern,
			function ( $matches ) use ( &$changed, $post, $old_block_name ) {
				$block = json_decode( $matches[1], true );

				if ( ! is_array( $block ) || empty( $block['data']['logos'] ) || ! is_array( $block['data']['logos'] ) ) {
					WP_CLI::warning( "Post {$post->ID}: found a {$old_block_name} block but couldn't parse its 'logos' data - skipping this block, left untouched." );
					return $matches[0];
				}

				$new_data = array(
					'logo_source'   => 'specific',
					'_logo_source'  => 'field_cb_logo_slider_source',
					'logo_gallery'  => $block['data']['logos'],
					'_logo_gallery' => 'field_cb_logo_slider_gallery',
				);

				// Both old blocks hardcode "py-3" directly in their own PHP
				// template (`class="cb-awards-slider py-3"` /
				// `class="cb-logo-slider py-3"`) - it's not a saved
				// className attribute, so it would otherwise be silently
				// lost on migration. Preserve it, and append (not replace)
				// any editor-added extra class already on the block.
				$new_class_names = array_filter( array( 'py-3', trim( (string) ( $block['className'] ?? '' ) ) ) );

				$new_block = array(
					'name'      => 'acf/cb-logo-slider',
					'data'      => $new_data,
					'mode'      => $block['mode'] ?? 'edit',
					'className' => implode( ' ', $new_class_names ),
				);

				$changed = true;

				return '<!-- wp:acf/cb-logo-slider ' . wp_json_encode( $new_block, JSON_UNESCAPED_SLASHES ) . ' /-->';
			},
			$post->post_content
		);

		if ( ! $changed ) {
			continue;
		}

		WP_CLI::log( sprintf( '%s post %d (%s) [%s]', $write ? 'Migrating' : '[dry run] Would migrate', $post->ID, $post->post_title, $old_block_name ) );

		if ( ! $write ) {
			continue;
		}

		// $wpdb->update() writes the raw string as-is - unlike wp_update_post()/
		// wp_insert_post(), it does not call wp_unslash() on its way in, so
		// there's no risk of double-unslashing content that's already
		// unslashed (the bug that corrupted post 510's entire post_content
		// during the original by-hand migration).
		$wpdb->update(
			$wpdb->posts,
			array( 'post_content' => $new_content ),
			array( 'ID' => $post->ID )
		);

		clean_post_cache( $post->ID );

		WP_CLI::success( "Post {$post->ID} migrated." );
	}
}

if ( ! $write ) {
	WP_CLI::log( "\nDry run only - re-run with --write to apply." );
}
