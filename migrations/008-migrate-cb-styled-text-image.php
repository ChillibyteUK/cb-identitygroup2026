<?php
/**
 * One-off content migration: cb/cb-styled-text-image -> acf/cb-styled-text-image.
 *
 * This block was believed unused anywhere (see migration 005/007's
 * comments and the 2026-08-06 audit that added a since-removed deprecated
 * notice to it) but is in fact used once, in a specific context. Same pure
 * rename as 006/007, no field remapping needed - the registered
 * acf/cb-styled-text-image field group already matches the one real
 * instance's saved content.
 *
 * Idempotent: once the post is migrated it no longer contains the old
 * block name, so re-running this script is always safe.
 *
 * Usage (from the site's webroot):
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/008-migrate-cb-styled-text-image.php
 *     -> dry run, lists what WOULD change, writes nothing.
 *
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/008-migrate-cb-styled-text-image.php write
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
	WHERE post_content LIKE '%wp:cb/cb-styled-text-image %'
	AND post_status = 'publish'
	AND post_type NOT IN ( 'revision', 'nav_menu_item' )"
);

if ( ! $posts ) {
	WP_CLI::log( 'No posts contain a wp:cb/cb-styled-text-image block. Nothing to do.' );
	return;
}

$total_hits = 0;

foreach ( $posts as $post ) {
	$content  = $post->post_content;
	$original = $content;

	$content = str_replace( 'wp:cb/cb-styled-text-image ', 'wp:acf/cb-styled-text-image ', $content, $count1 );
	$content = str_replace( '"name":"cb/cb-styled-text-image"', '"name":"acf/cb-styled-text-image"', $content, $count2 );

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
	$remaining = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%wp:cb/cb-styled-text-image %'" );
	$now_acf   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE '%wp:acf/cb-styled-text-image %'" );
	WP_CLI::log( "Remaining wp:cb/cb-styled-text-image (should be 0): {$remaining}" );
	WP_CLI::log( "Now wp:acf/cb-styled-text-image: {$now_acf}" );
} else {
	WP_CLI::log( "\nDry run only - re-run with --write to apply." );
}
