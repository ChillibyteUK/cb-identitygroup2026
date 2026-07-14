<?php
/**
 * One-off content migration: cb/cb-related-work-sports ->
 * acf/cb-related-work (theme_filter: Sports).
 *
 * The "sports" sibling of migrations/003-migrate-cb-related-work-expo.php -
 * flagged deferred in that migration's own header and in SESSION-HANDOFF.md.
 * Same shape confirmed directly this time before writing anything: all 7
 * real instances (identityglobal.com's "Sport" page + its 6 sport case
 * studies) have a bare `[]` saved data, no theme_filter value set. The
 * "Sports" term (slug "sports", term_id 68 on idglobal-test) exists in the
 * "theme" taxonomy with 10 tagged case studies.
 *
 * Idempotent: once a post is migrated it no longer contains the old block
 * name, so re-running this script is always safe.
 *
 * Usage (from the site's webroot):
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/004-migrate-cb-related-work-sports.php
 *     -> dry run, lists what WOULD change, writes nothing.
 *
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/004-migrate-cb-related-work-sports.php write
 *     -> applies the changes.
 *
 * Take a DB backup before running with the "write" argument.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this via WP-CLI: wp eval-file <path-to-this-file> [write]\n" );
}

global $wpdb;

$write = in_array( 'write', (array) ( $args ?? array() ), true );

$sports_term = get_term_by( 'slug', 'sports', 'theme' );

if ( ! $sports_term || is_wp_error( $sports_term ) ) {
	WP_CLI::error( "Could not find the 'sports' term in the 'theme' taxonomy on this site - aborting.", false );
	return;
}

$old_block_open = '<!-- wp:cb/cb-related-work-sports ';

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
	WP_CLI::log( 'No posts contain a cb/cb-related-work-sports block. Nothing to do.' );
	return;
}

$pattern = '#<!-- wp:cb/cb-related-work-sports (\{.*?\}) /-->#s';

foreach ( $posts as $post ) {
	$changed = false;

	$new_content = preg_replace_callback(
		$pattern,
		function ( $matches ) use ( &$changed, $post, $sports_term ) {
			$block = json_decode( $matches[1], true );

			if ( ! is_array( $block ) ) {
				WP_CLI::warning( "Post {$post->ID}: found a cb-related-work-sports block but couldn't parse it - skipping this block, left untouched." );
				return $matches[0];
			}

			$new_data = array(
				'theme_filter'  => array( (string) $sports_term->term_id ),
				'_theme_filter' => 'field_cb_related_work_theme_filter',
			);

			$new_block = array(
				'name' => 'acf/cb-related-work',
				'data' => $new_data,
				'mode' => $block['mode'] ?? 'edit',
			);

			$changed = true;

			return '<!-- wp:acf/cb-related-work ' . wp_json_encode( $new_block, JSON_UNESCAPED_SLASHES ) . ' /-->';
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
