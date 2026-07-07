<?php
/**
 * Per-site design token overrides, driven by the cb_site Site-Wide Settings
 * field (see acf-json/group_site_wide_settings.json).
 *
 * The shared theme was forked from idtravel, so its compiled CSS currently
 * only has idtravel's token values baked in. Until Phase C (renaming all
 * block SCSS to the plan's generic --col-brand / --ff-heading / etc. names)
 * is done, this file overrides BOTH the generic names and each site's own
 * legacy variable names at runtime, so switching cb_site changes colours and
 * type sizes immediately for testing/preview — without a rebuild, and
 * without waiting for the SCSS rename.
 *
 * Values are taken directly from each site's own src/sass/theme/_tokens.scss
 * at the time of the consolidation (see CB-THEME-CONSOLIDATION-PLAN.md §5).
 * Gaps noted in that plan (e.g. idtravel has no --fs-300/800/950) are left
 * out here rather than invented.
 *
 * @package cb-identitygroup2026
 */

/**
 * Returns the full per-site token table.
 *
 * @return array<string, array<string, string>>
 */
function cb_get_site_tokens_table() {
	return array(
		'identity' => array(
			// Generic names (plan §5.2).
			'--col-brand'          => '#B8FF52',
			'--col-brand-light'    => '#CAFC83',
			'--col-brand-dark'     => '#4c8200',
			'--col-secondary'      => '#2f13ba',
			'--col-secondary-light' => '#e0deff',
			'--col-secondary-dark' => '#190A83',
			'--col-accent'         => '#e03030',
			'--col-accent-400'     => '#e03030',
			'--col-accent-500'     => '#ec5a5a',
			'--col-text'           => '#0D0D0C',
			'--col-bg'             => '#fff',
			'--col-black'          => '#0D0D0C',
			'--ff-heading'         => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-body'            => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-accent'          => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			// Legacy names, so identity's own un-renamed block SCSS still resolves.
			'--col-green-400'      => '#B8FF52',
			'--col-green-300'      => '#CAFC83',
			'--col-green-900'      => '#4c8200',
			'--col-purple-900'     => '#2f13ba',
			'--col-purple-200'     => '#e0deff',
			'--col-purple-1000'    => '#190A83',
			'--col-red-600'        => '#e03030',
			'--col-red-500'        => '#ec5a5a',
			'--col-primary-black'  => '#0D0D0C',
			'--col-primary'        => '#0D0D0C',
			'--col-white'          => '#fff',
			'--col-neutral-50'     => '#f8f7f0',
			// Font sizes (identity's own clamp scale).
			'--fs-300' => 'clamp(1rem, 0.95rem + 0.5vw, 1.25rem)',
			'--fs-400' => 'clamp(1.0625rem, 0.9rem + 0.6vw, 1.375rem)',
			'--fs-500' => 'clamp(1.1875rem, 0.9rem + 1vw, 1.751rem)',
			'--fs-600' => 'clamp(1.3125rem, 0.9rem + 1.2vw, 1.999rem)',
			'--fs-700' => 'clamp(1.4375rem, 0.9rem + 1.4vw, 2.25rem)',
			'--fs-800' => 'clamp(1.5rem, 0.9rem + 1.6vw, 2.5rem)',
			'--fs-850' => 'clamp(1.875rem, 0.95rem + 1.9vw, 3.125rem)',
			'--fs-900' => 'clamp(3rem, 1rem + 3vw, 5rem)',
			'--fs-950' => 'clamp(3.4375rem, 1rem + 4vw, 6.25rem)',
			'--lh-tightest' => '1',
			'--lh-tight'     => '1.1',
			'--lh-snug'      => '1.2',
			'--lh-normal'    => '1.5',
		),
		'coda'     => array(
			'--col-brand'          => '#b8ff52',
			'--col-brand-light'    => '#CAFC83',
			'--col-brand-dark'     => '#4c8200',
			'--col-secondary'      => '#2f13ba',
			'--col-secondary-light' => '#e0deff',
			'--col-secondary-dark' => '#190a83',
			'--col-accent'         => '#e03030',
			'--col-accent-400'     => '#e03030',
			'--col-accent-500'     => '#ec5a5a',
			'--col-text'           => '#0d0d0c',
			'--col-bg'             => '#fff',
			'--col-black'          => '#0D0D0C',
			'--ff-heading'         => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-body'            => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-accent'          => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--col-lime-400'       => '#b8ff52',
			'--col-lime-300'       => '#CAFC83',
			'--col-lime-900'       => '#4c8200',
			'--col-purple-900'     => '#2f13ba',
			'--col-purple-200'     => '#e0deff',
			'--col-purple-1000'    => '#190a83',
			'--col-red-600'        => '#e03030',
			'--col-red-500'        => '#ec5a5a',
			'--col-primary-black'  => '#0d0d0c',
			'--col-primary'        => '#0d0d0c',
			'--col-white'          => '#fff',
			'--col-neutral-50'     => '#f8f7f0',
			'--fs-300' => 'clamp(1rem, 0.95rem + 0.5vw, 1.25rem)',
			'--fs-400' => 'clamp(1.0625rem, 0.9rem + 0.6vw, 1.375rem)',
			'--fs-500' => 'clamp(1.1875rem, 0.9rem + 1vw, 1.751rem)',
			'--fs-600' => 'clamp(1.3125rem, 0.9rem + 1.2vw, 1.999rem)',
			'--fs-700' => 'clamp(1.4375rem, 0.9rem + 1.4vw, 2.25rem)',
			'--fs-800' => 'clamp(1.5rem, 0.9rem + 1.6vw, 2.5rem)',
			'--fs-850' => 'clamp(1.875rem, 0.95rem + 1.9vw, 3.125rem)',
			'--fs-900' => 'clamp(2.5rem, 1rem + 3vw, 5rem)',
			'--fs-950' => 'clamp(3.4375rem, 1rem + 4vw, 6.25rem)',
			'--lh-tightest' => '1',
			'--lh-tight'     => '1.1',
			'--lh-snug'      => '1.2',
			'--lh-normal'    => '1.5',
		),
		'idtravel' => array(
			'--col-brand'          => '#e32447',
			'--col-brand-light'    => '#ff9bae',
			'--col-brand-dark'     => '#900720',
			'--col-secondary'      => '#2f13ba',
			'--col-secondary-light' => '#d0ccff',
			'--col-secondary-dark' => '#13086b',
			'--col-accent'         => '#cc1939',
			'--col-accent-400'     => '#cc1939',
			'--col-accent-500'     => '#cc1939',
			'--col-text'           => '#110d25',
			'--col-bg'             => '#ffffff',
			'--col-black'          => '#040013',
			'--ff-heading'         => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-body'            => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-accent'          => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--col-raspberry'      => '#e32447',
			'--col-raspberry-300'  => '#ff9bae',
			'--col-raspberry-600'  => '#cc1939',
			'--col-raspberry-900'  => '#900720',
			'--col-purple'         => '#2f13ba',
			'--col-purple-300'     => '#d0ccff',
			'--col-purple-1100'    => '#13086b',
			'--col-ink'            => '#110d25',
			'--col-white'          => '#ffffff',
			'--col-neutral-050'    => '#f7f6fa',
			'--font-family'        => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			// idtravel has no --fs-300/800/950 in its own tokens file (a pre-existing gap, not invented here).
			'--fs-400' => 'clamp(1.2222rem, 1.1rem + 0.6vw, 1.375rem)',
			'--fs-500' => 'clamp(1.4444rem, 1.25rem + 0.9vw, 1.75rem)',
			'--fs-600' => 'clamp(1.6667rem, 1.4rem + 1.2vw, 2rem)',
			'--fs-700' => 'clamp(1.9444rem, 1.6rem + 1.4vw, 2.25rem)',
			'--fs-850' => 'clamp(2.3333rem, 1.9rem + 2vw, 3.125rem)',
			'--fs-900' => 'clamp(3.3333rem, 2.6rem + 3vw, 5rem)',
			'--lh-tightest' => '1',    // --lh-900
			'--lh-tight'     => '1.05', // --lh-875
			'--lh-snug'      => '1.1',  // --lh-850
			'--lh-normal'    => '1.55', // --lh-100
			'--lh-50'  => '1.4',
			'--lh-100' => '1.55',
			'--lh-200' => '1.5',
			'--lh-400' => '1.3',
			'--lh-500' => '1.25',
			'--lh-600' => '1.2',
			'--lh-700' => '1.15',
			'--lh-850' => '1.1',
			'--lh-875' => '1.05',
			'--lh-900' => '1',
		),
	);
}

/**
 * Outputs the current cb_site's token overrides as a :root CSS block, on
 * both the frontend and in the block editor (via enqueue_block_assets, which
 * fires in both contexts).
 */
function cb_output_site_token_overrides() {
	$site  = get_field( 'cb_site', 'option' ) ?: 'idtravel';
	$table = cb_get_site_tokens_table();

	if ( ! isset( $table[ $site ] ) ) {
		return;
	}

	$declarations = array();
	foreach ( $table[ $site ] as $var => $value ) {
		$declarations[] = esc_attr( $var ) . ': ' . $value . ';';
	}

	printf(
		'<style id="cb-site-tokens">:root{%s}</style>',
		implode( ' ', $declarations )
	);
}
add_action( 'wp_head', 'cb_output_site_token_overrides' );
add_action( 'admin_head', 'cb_output_site_token_overrides' );

/**
 * Injects the current cb_site's colour palette and font sizes into the
 * block editor's theme.json data, so the colour swatches and font-size
 * dropdown editors see also switch with cb_site (not just frontend CSS).
 *
 * @param WP_Theme_JSON_Data $theme_json Theme JSON data object.
 * @return WP_Theme_JSON_Data
 */
function cb_filter_editor_theme_json( $theme_json ) {
	$site = get_field( 'cb_site', 'option' ) ?: 'idtravel';

	$palettes = array(
		'identity' => array(
			array( 'slug' => 'brand', 'name' => 'Brand', 'color' => '#B8FF52' ),
			array( 'slug' => 'secondary', 'name' => 'Secondary', 'color' => '#2f13ba' ),
			array( 'slug' => 'accent', 'name' => 'Accent', 'color' => '#e03030' ),
			array( 'slug' => 'text', 'name' => 'Text', 'color' => '#0D0D0C' ),
		),
		'coda'     => array(
			array( 'slug' => 'brand', 'name' => 'Brand', 'color' => '#b8ff52' ),
			array( 'slug' => 'secondary', 'name' => 'Secondary', 'color' => '#2f13ba' ),
			array( 'slug' => 'accent', 'name' => 'Accent', 'color' => '#e03030' ),
			array( 'slug' => 'text', 'name' => 'Text', 'color' => '#0d0d0c' ),
		),
		'idtravel' => array(
			array( 'slug' => 'brand', 'name' => 'Brand', 'color' => '#e32447' ),
			array( 'slug' => 'secondary', 'name' => 'Secondary', 'color' => '#2f13ba' ),
			array( 'slug' => 'accent', 'name' => 'Accent', 'color' => '#cc1939' ),
			array( 'slug' => 'text', 'name' => 'Text', 'color' => '#110d25' ),
		),
	);

	$font_sizes = array(
		'identity' => array(
			array( 'slug' => 'small', 'name' => 'Small', 'size' => 'clamp(1rem, 0.95rem + 0.5vw, 1.25rem)' ),
			array( 'slug' => 'medium', 'name' => 'Medium', 'size' => 'clamp(1.1875rem, 0.9rem + 1vw, 1.751rem)' ),
			array( 'slug' => 'large', 'name' => 'Large', 'size' => 'clamp(1.875rem, 0.95rem + 1.9vw, 3.125rem)' ),
			array( 'slug' => 'x-large', 'name' => 'X-Large', 'size' => 'clamp(3rem, 1rem + 3vw, 5rem)' ),
		),
		'coda'     => array(
			array( 'slug' => 'small', 'name' => 'Small', 'size' => 'clamp(1rem, 0.95rem + 0.5vw, 1.25rem)' ),
			array( 'slug' => 'medium', 'name' => 'Medium', 'size' => 'clamp(1.1875rem, 0.9rem + 1vw, 1.751rem)' ),
			array( 'slug' => 'large', 'name' => 'Large', 'size' => 'clamp(1.875rem, 0.95rem + 1.9vw, 3.125rem)' ),
			array( 'slug' => 'x-large', 'name' => 'X-Large', 'size' => 'clamp(2.5rem, 1rem + 3vw, 5rem)' ),
		),
		'idtravel' => array(
			array( 'slug' => 'small', 'name' => 'Small', 'size' => 'clamp(1.2222rem, 1.1rem + 0.6vw, 1.375rem)' ),
			array( 'slug' => 'medium', 'name' => 'Medium', 'size' => 'clamp(1.4444rem, 1.25rem + 0.9vw, 1.75rem)' ),
			array( 'slug' => 'large', 'name' => 'Large', 'size' => 'clamp(2.3333rem, 1.9rem + 2vw, 3.125rem)' ),
			array( 'slug' => 'x-large', 'name' => 'X-Large', 'size' => 'clamp(3.3333rem, 2.6rem + 3vw, 5rem)' ),
		),
	);

	if ( ! isset( $palettes[ $site ] ) ) {
		return $theme_json;
	}

	return $theme_json->update_with(
		array(
			'version'  => 3,
			'settings' => array(
				'color'      => array(
					'palette' => $palettes[ $site ],
				),
				'typography' => array(
					'fontSizes' => $font_sizes[ $site ],
				),
			),
		)
	);
}
add_filter( 'wp_theme_json_data_theme', 'cb_filter_editor_theme_json' );
