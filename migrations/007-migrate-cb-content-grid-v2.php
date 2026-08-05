<?php
/**
 * One-off content migration: cb/cb-content-grid-v2 -> acf/cb-content-grid-v2.
 *
 * This block was left on the legacy `cb/` namespace during the original
 * merge and relied entirely on the render-time rename filter
 * (cb_rename_legacy_block_names() in inc/cb-utility.php) instead of a DB
 * migration - correct on the frontend, but the block editor does its own
 * registered-block-type lookup against the raw blockName stored in
 * post_content, which that render-time filter never touches, so editors
 * see a "doesn't include support for this block" placeholder even though
 * it renders fine. This is a pure rename, same as 006's cb-content-grid:
 * the registered acf/cb-content-grid-v2 field group already matches
 * identity's real content (no schema rebuild was needed here, unlike
 * cb-content-grid v1's 33-field vs 2-field mismatch).
 *
 * Does NOT touch plain "cb-content-grid" (no trailing "-v2") - migration
 * 006 already handles that, separately, since it's a different block with
 * a different field schema history.
 *
 * Idempotent: once a post is migrated it no longer contains the old block
 * name, so re-running this script is always safe.
 *
 * Usage (from the site's webroot):
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/007-migrate-cb-content-grid-v2.php
 *     -> dry run, lists what WOULD change, writes nothing.
 *
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/007-migrate-cb-content-grid-v2.php write
 *     -> applies the changes.
 *
 * Take a DB backup before running with the "write" argument.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this via WP-CLI: wp eval-file <path-to-this-file> [write]\n" );
}

global $wpdb;

$write = in_array( 'write', (array) ( $args ?? array() ), true );

$posts = $wpdb->get_results(
	"SELECT ID, post_title, post_content FROM {$wpdb->posts}
	WHERE post_content LIKE '%wp:cb/cb-content-grid-v2 %'
	AND post_status = 'publish'
	AND post_type NOT IN ( 'revision', 'nav_menu_item' )"
);

if ( ! $posts ) {
	WP_CLI::log( 'No posts contain a wp:cb/cb-content-grid-v2 block. Nothing to do.' );
	return;
}

$total_hits = 0;

foreach ( $posts as $post ) {
	$content  = $post->post_content;
	$original = $content;

	$content = str_replace( 'wp:cb/cb-content-grid-v2 ', 'wp:acf/cb-content-grid-v2 ', $content, $count1 );
	$content = str_replace( '"name":"cb/cb-content-grid-v2"', '"name":"acf/cb-content-grid-v2"', $content, $count2 );

	if ( $content === $original ) {
		continue;
	}

	$total_hits += $count1;

	WP_CLI::log( sprintf( '%s post %d (%s) - %d block(s) renamed', $write ? 'Migrating' : '[dry run] Would migrate', $post->ID, $post->post_title, $count1 ) );

	if ( ! $write ) {
		continue;
	}

	$wpdb->update(
		$wpdb->posts,
		array( 'post_content' => $content ),
		array( 'ID' => $post->ID )
	);

	clean_post_cache( $post->ID );

	WP_CLI::success( "Post {$post->ID} migrated." );
}

WP_CLI::log( "\nTotal block instances renamed: {$total_hits}" );

if ( $write ) {
	$remaining = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%wp:cb/cb-content-grid-v2 %'" );
	$now_acf   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%wp:acf/cb-content-grid-v2 %'" );
	WP_CLI::log( "Remaining wp:cb/cb-content-grid-v2 (should be 0): {$remaining}" );
	WP_CLI::log( "Now wp:acf/cb-content-grid-v2: {$now_acf}" );
} else {
	WP_CLI::log( "\nDry run only - re-run with --write to apply." );
}
