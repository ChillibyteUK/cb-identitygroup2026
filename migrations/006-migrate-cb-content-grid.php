<?php
/**
 * One-off content migration: cb/cb-content-grid -> acf/cb-content-grid.
 *
 * Unlike the other identity namespace migrations, this is a pure rename
 * with NO field remapping needed: the shared theme's cb-content-grid field
 * group was rebuilt from scratch to match identity's real 2-field
 * `grid_rows` structure (it was originally built from coda's own
 * 33-field schema, which didn't match identity's real content at all -
 * see SESSION-HANDOFF.md's 2026-07-10 "cb-content-grid rebuilt from
 * scratch" entry). Once the schema matched, the only remaining work was
 * the namespace rename.
 *
 * "cb-content-grid" is a literal string prefix of "cb-content-grid-v2" (a
 * separate, deliberately-still-deferred block - relies on the render-time
 * rename filter in inc/cb-utility.php, never migrated at the DB level).
 * Guards against that collision by requiring a trailing space after the
 * block name in the HTML comment token, and an exact closing quote on the
 * JSON "name" attribute.
 *
 * Idempotent: once a post is migrated it no longer contains the old block
 * name, so re-running this script is always safe.
 *
 * Usage (from the site's webroot):
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/006-migrate-cb-content-grid.php
 *     -> dry run, lists what WOULD change, writes nothing.
 *
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/006-migrate-cb-content-grid.php write
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
	WHERE post_content LIKE '%wp:cb/cb-content-grid %'
	AND post_status = 'publish'
	AND post_type NOT IN ( 'revision', 'nav_menu_item' )"
);

if ( ! $posts ) {
	WP_CLI::log( 'No posts contain a wp:cb/cb-content-grid block. Nothing to do.' );
	return;
}

$total_hits = 0;

foreach ( $posts as $post ) {
	$content  = $post->post_content;
	$original = $content;

	$content = str_replace( 'wp:cb/cb-content-grid ', 'wp:acf/cb-content-grid ', $content, $count1 );
	$content = str_replace( '"name":"cb/cb-content-grid"', '"name":"acf/cb-content-grid"', $content, $count2 );

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
	$remaining_grid = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%wp:cb/cb-content-grid %'" );
	$remaining_v2   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%wp:cb/cb-content-grid-v2%'" );
	$now_acf        = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%wp:acf/cb-content-grid %'" );
	WP_CLI::log( "Remaining wp:cb/cb-content-grid (should be 0): {$remaining_grid}" );
	WP_CLI::log( "cb-content-grid-v2 instances (should be unchanged, still deferred): {$remaining_v2}" );
	WP_CLI::log( "Now wp:acf/cb-content-grid: {$now_acf}" );
} else {
	WP_CLI::log( "\nDry run only - re-run with --write to apply." );
}
