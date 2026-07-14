<?php
/**
 * One-off content migration: identity's real production content uses a
 * custom `cb/` block namespace (cb-identity2025 registers every block via
 * block.json with an explicit "cb/" prefix), not the ACF-default `acf/`
 * namespace this shared theme (like idtravel's/coda's own original themes)
 * uses. Every one of these 31 block types is a true 1:1 rename - field
 * names already match between identity's original field groups and the
 * shared theme's, confirmed by a full field-schema diff before this script
 * was written. Two of the 31 also need a field-key rename within the same
 * pass (see below) - everything else is a pure namespace swap.
 *
 * Reconstructed from an earlier ad-hoc session script (never committed) -
 * that version used a bare `str_replace( "wp:cb/{$name}", ... )` with no
 * trailing boundary, which incorrectly also matched "wp:cb/cb-related-work"
 * as a substring prefix of "wp:cb/cb-related-work-expo" and
 * "wp:cb/cb-related-work-sports" (same for "cb-latest-insights" inside
 * "cb-latest-insights-expo") - corrupting those three still-deferred block
 * names. This version guards against that by requiring a trailing space
 * after the block name in the HTML comment token, and an exact closing
 * quote in the JSON "name" attribute, so "cb-related-work" can never match
 * "cb-related-work-expo/-sports", and "cb-latest-insights" can never match
 * "cb-latest-insights-expo". Verified against idglobal-test's current
 * (already-migrated) content: running this script there is a no-op, and a
 * `sort -u` of every remaining `wp:cb/…` name after running matches exactly
 * the two block types intentionally left on the old namespace forever
 * (`cb-content-grid-v2`, `cb-styled-text-image` - see cb_rename_legacy_block_names()
 * in inc/cb-utility.php, which handles those two at render time instead).
 *
 * Does NOT touch: cb-content-grid (separate script, 006 - different schema
 * history), cb-content-grid-v2 / cb-styled-text-image (deliberately never
 * migrated at DB level, rely on the render-time rename filter),
 * cb-awards-slider / cb-sport-logos (migration 001), cb-latest-insights-expo
 * (migration 002), cb-related-work-expo / cb-related-work-sports
 * (migrations 003 / 004).
 *
 * Idempotent: once a post is migrated it no longer contains the old block
 * name, so re-running this script is always safe.
 *
 * Usage (from the site's webroot):
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/005-migrate-identity-block-namespace.php
 *     -> dry run, lists what WOULD change, writes nothing.
 *
 *   wp eval-file wp-content/themes/cb-identitygroup2026/migrations/005-migrate-identity-block-namespace.php write
 *     -> applies the changes.
 *
 * Take a DB backup before running with the "write" argument.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Run this via WP-CLI: wp eval-file <path-to-this-file> [write]\n" );
}

global $wpdb;

$write = in_array( 'write', (array) ( $args ?? array() ), true );

$safe_blocks = array(
	'cb-about-detail', 'cb-about-page-header', 'cb-brand-title-text', 'cb-careers-page',
	'cb-case-study-hero', 'cb-case-study-key-stats', 'cb-contact-form', 'cb-contact-page',
	'cb-cta', 'cb-culture-page-header', 'cb-faq', 'cb-featured-work', 'cb-file-block',
	'cb-full-image', 'cb-full-video', 'cb-image-feature-overlay', 'cb-innovation-header',
	'cb-latest-insights', 'cb-logo-slider', 'cb-our-brands', 'cb-policies-page', 'cb-pushthrough',
	'cb-recent-news', 'cb-region-page-header', 'cb-related-work', 'cb-service-detail',
	'cb-service-page-header', 'cb-services-nav', 'cb-testimonial', 'cb-work-by-region', 'cb-work-index',
);

$posts = $wpdb->get_results(
	"SELECT ID, post_title, post_content FROM {$wpdb->posts}
	WHERE post_content LIKE '%wp:cb/%'
	AND post_status = 'publish'
	AND post_type NOT IN ( 'revision', 'nav_menu_item' )"
);

if ( ! $posts ) {
	WP_CLI::log( 'No posts contain a wp:cb/… block. Nothing to do.' );
	return;
}

$total_hits = 0;

foreach ( $posts as $post ) {
	$content  = $post->post_content;
	$original = $content;
	$hits     = 0;

	foreach ( $safe_blocks as $name ) {
		// Trailing space after the block name in the HTML comment token, and
		// an exact closing quote on the JSON "name" attribute, so a name that
		// is a literal prefix of another real block name (e.g. "cb-related-work"
		// vs "cb-related-work-expo") can never accidentally match.
		$content = preg_replace( '/wp:cb\/' . preg_quote( $name, '/' ) . ' /', 'wp:acf/' . $name . ' ', $content, -1, $count1 );
		$content = str_replace( '"name":"cb/' . $name . '"', '"name":"acf/' . $name . '"', $content, $count2 );
		$hits    += $count1;
	}

	// cb-full-image: top_gradient -> top_border (both checkboxes, coda's own
	// theme already uses top_border - migrated identity's data to match
	// rather than renaming the shared field).
	$content = str_replace( '"top_gradient":', '"top_border":', $content );
	$content = preg_replace( '/"_top_gradient":"field_[a-z0-9]+"/', '"_top_border":"field_699333b8ff970"', $content );

	// cb-service-page-header: service_title -> title, service_intro_text -> content
	// (coda's own theme already uses the shorter names).
	$content = str_replace( '"service_title":', '"title":', $content );
	$content = preg_replace( '/"_service_title":"field_[a-z0-9]+"/', '"_title":"field_698c66e71952b"', $content );
	$content = str_replace( '"service_intro_text":', '"content":', $content );
	$content = preg_replace( '/"_service_intro_text":"field_[a-z0-9]+"/', '"_content":"field_698c672ab6549"', $content );

	if ( $content === $original ) {
		continue;
	}

	$total_hits += $hits;

	WP_CLI::log( sprintf( '%s post %d (%s) - %d block(s) renamed', $write ? 'Migrating' : '[dry run] Would migrate', $post->ID, $post->post_title, $hits ) );

	if ( ! $write ) {
		continue;
	}

	// $wpdb->update() writes the raw string as-is - unlike wp_update_post()/
	// wp_insert_post(), it does not call wp_unslash() on its way in, so
	// there's no risk of double-unslashing already-unslashed content.
	$wpdb->update(
		$wpdb->posts,
		array( 'post_content' => $content ),
		array( 'ID' => $post->ID )
	);

	clean_post_cache( $post->ID );

	WP_CLI::success( "Post {$post->ID} migrated." );
}

WP_CLI::log( "\nTotal block instances renamed: {$total_hits}" );

$remaining = $wpdb->get_results(
	"SELECT ID, post_title FROM {$wpdb->posts}
	WHERE post_content LIKE '%wp:cb/%'
	AND post_status = 'publish'
	AND post_type NOT IN ( 'revision', 'nav_menu_item' )"
);

if ( $write ) {
	WP_CLI::log( "\nPosts still containing wp:cb/… after this migration (expected: cb-content-grid, cb-content-grid-v2, cb-styled-text-image, cb-awards-slider, cb-sport-logos, cb-related-work-expo, cb-related-work-sports, cb-latest-insights-expo - handled by other migrations/the render-time rename filter):" );
	foreach ( $remaining as $r ) {
		WP_CLI::log( "  {$r->ID}\t{$r->post_title}" );
	}
} else {
	WP_CLI::log( "\nDry run only - re-run with --write to apply." );
}
