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
 * Returns the current site slug (identity/coda/idtravel), falling back to
 * idtravel (the fork base) if cb_site has never been set. Used both for the
 * token overrides below and to select header-{site}.php/footer-{site}.php
 * via get_header( cb_site_template_suffix() )/get_footer(...).
 *
 * @return string
 */
function cb_site_template_suffix() {
	return get_field( 'cb_site', 'option' ) ?: 'idtravel';
}

/**
 * Adds a cb-site-{slug} body class, so CSS can scope per-site design
 * differences that aren't just colour-value swaps (e.g. coda's header is
 * light-background/dark-text; identity and idtravel are both
 * dark-background/light-text — a structural rule difference, not something
 * a custom-property override alone can express). See _header.scss's
 * ".cb-site-coda header" override block.
 *
 * @param array $classes Body classes.
 * @return array
 */
function cb_add_site_body_class( $classes ) {
	$classes[] = 'cb-site-' . cb_site_template_suffix();
	return $classes;
}
add_filter( 'body_class', 'cb_add_site_body_class' );

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
			'--hsl-brand'          => '85 100% 66%',
			'--col-brand-light'    => '#CAFC83',
			'--col-brand-dark'     => '#4c8200',
			'--col-secondary'      => '#2f13ba',
			'--col-secondary-light' => '#e0deff',
			'--col-secondary-dark' => '#190A83',
			'--col-accent'         => '#e03030',
			'--col-accent-400'     => '#e03030',
			'--col-accent-500'     => '#ec5a5a',
			// .id-button's accent isn't the site's brand colour - identity's
			// own _buttons.scss uses its own red (matches --col-red-600),
			// not lime.
			'--id-button-accent'   => '#e03030',
			'--col-text'           => '#0D0D0C',
			'--col-bg'             => '#fff',
			'--col-black'          => '#0D0D0C',
			'--ff-heading'         => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-body'            => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-accent'          => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			// identity's own _tokens.scss calls this scale "green"; the shared
			// SCSS has been standardised on coda's "lime" naming instead
			// (more accurate name for this hue, and coda's is the more
			// complete/systematic token file) — values are colour-identical.
			'--col-lime-400'       => '#B8FF52',
			'--col-lime-300'       => '#CAFC83',
			// identity's own green scale has no shade beyond 1000 — reused
			// for the 1100 slot coda's own scale goes one step darker to.
			'--col-lime-1100'      => '#3d6900',
			'--col-lime-1000'      => '#3d6900',
			'--col-lime-900'       => '#4c8200',
			'--col-lime-800'       => '#5e9f00',
			'--col-lime-500'       => '#ADF448',
			'--col-lime-200'       => '#DDFBB2',
			'--col-lime-100'       => '#EEFED9',
			'--hsl-lime-1000'      => '85 100% 21%',
			'--hsl-lime-900'       => '85 100% 25%',
			'--hsl-lime-300'       => '85 95% 75%',
			'--hsl-lime-200'       => '85 90% 84%',
			// idtravel has no equivalent hue for its own "raspberry" scale —
			// identity has no raspberry concept at all. Where shared blocks
			// (idtravel one-offs, but registered everywhere) use raspberry
			// as a light/dark accent role, reusing identity's own brand
			// light/dark tint for the same role rather than a literal shade
			// match (the two scales don't line up shade-for-shade).
			'--col-raspberry-100'  => '#EEFED9',
			'--col-raspberry-400'  => '#B8FF52',
			'--hsl-raspberry-400'  => '85 100% 66%',
			'--col-raspberry-600'  => '#4c8200',
			'--hsl-raspberry-600'  => '85 100% 25%',
			'--col-neutral-1100'   => '#22211E',
			// Rest of identity's own neutral scale.
			'--col-neutral-900'    => '#505049',
			'--col-neutral-800'    => '#5e5d55',
			'--hsl-neutral-800'    => '53 5% 35%',
			'--col-neutral-700'    => '#77766c',
			'--col-neutral-500'    => '#aeada1',
			'--col-neutral-400'    => '#c9c7bc',
			'--hsl-neutral-400'    => '51 11% 76%',
			'--col-neutral-300'    => '#dddcd2',
			'--col-neutral-200'    => '#ebe9e1',
			'--col-neutral-100'    => '#f8f7f0',
			// identity's scale starts at 100 (no 050 step) — reusing its own
			// lightest neutral as the nearest equivalent.
			'--col-neutral-050'    => '#f8f7f0',
			'--hsl-neutral-050'    => '53 36% 96%',
			// Rest of identity's own purple scale.
			'--col-purple-700'     => '#7162e1',
			'--col-purple-500'     => '#a49bfd',
			'--col-purple-400'     => '#bcb7ff',
			'--col-purple-300'     => '#d0ccff',
			// Bare --col-purple/--col-ink aren't in identity's own naming —
			// both resolve to values it already defines elsewhere (main
			// purple #2f13ba is identical across all 3 themes; "ink" is
			// identity's near-black text colour, same as its primary-black).
			'--col-purple'         => '#2f13ba',
			'--col-ink'            => '#0D0D0C',
			'--hsl-ink'            => '60 4% 5%',
			'--hsl-primary-black'  => '60 4% 5%',
			'--fw-semi'            => '500',
			'--col-purple-900'     => '#2f13ba',
			'--col-purple-200'     => '#e0deff',
			'--col-purple-1000'    => '#190A83',
			// identity's purple scale has no shade beyond 1000 — reusing it
			// for the 1100 slot idtravel's own scale goes one step darker to.
			'--col-purple-1100'    => '#190A83',
			'--hsl-purple-1100'    => '247 86% 28%',
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
			// WP-generated has-{slug}-color/-background-color utility class targets —
			// see cb_filter_editor_theme_json() for why these specific slugs.
			'--wp--preset--color--primary-black'  => '#0D0D0C',
			'--wp--preset--color--ink'            => '#0D0D0C',
			'--wp--preset--color--lime-900'       => '#4c8200',
			'--wp--preset--color--lime-1000'      => '#3d6900',
			'--wp--preset--color--lime-1100'      => '#345a00',
			'--wp--preset--color--raspberry'      => '#4c8200',
			'--wp--preset--color--raspberry-450'  => '#5e9f00',
			'--wp--preset--color--neutral-400'    => '#c9c7bc',
			'--wp--preset--color--neutral-700'    => '#77766c',
			'--wp--preset--color--neutral-800'    => '#5e5d55',
			'--wp--preset--color--white'          => '#fff',
		),
		'coda'     => array(
			'--col-brand'          => '#b8ff52',
			'--hsl-brand'          => '85 100% 66%',
			'--col-brand-light'    => '#CAFC83',
			'--col-brand-dark'     => '#4c8200',
			'--col-secondary'      => '#2f13ba',
			'--col-secondary-light' => '#e0deff',
			'--col-secondary-dark' => '#190a83',
			'--col-accent'         => '#e03030',
			'--col-accent-400'     => '#e03030',
			'--col-accent-500'     => '#ec5a5a',
			// coda's own .id-button uses its main purple, not its lime brand.
			'--id-button-accent'   => '#2f13ba',
			'--col-text'           => '#0d0d0c',
			'--col-bg'             => '#fff',
			'--col-black'          => '#0D0D0C',
			'--ff-heading'         => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-body'            => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-accent'          => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--col-lime-400'       => '#b8ff52',
			'--col-lime-300'       => '#CAFC83',
			'--col-lime-900'       => '#4c8200',
			// Rest of coda's own lime scale (_tokens.scss).
			'--col-lime-1100'      => '#345a00',
			'--col-lime-1000'      => '#3d6900',
			'--col-lime-800'       => '#5e9f00',
			'--col-lime-200'       => '#ddfbb2',
			'--col-lime-100'       => '#eefed9',
			'--hsl-lime-1000'      => '85 100% 21%',
			'--hsl-lime-900'       => '85 100% 25%',
			'--hsl-lime-300'       => '85 95% 75%',
			'--hsl-lime-200'       => '85 90% 84%',
			'--hsl-primary-black'  => '60 4% 5%',
			// coda has no raspberry concept either — same substitution as
			// identity's (see its own comment on this).
			'--col-raspberry-100'  => '#eefed9',
			'--col-raspberry-400'  => '#b8ff52',
			'--hsl-raspberry-400'  => '85 100% 66%',
			'--col-raspberry-600'  => '#4c8200',
			'--hsl-raspberry-600'  => '85 100% 25%',
			'--col-lime-500'       => '#adf448',
			'--col-neutral-1100'   => '#22211e',
			// Rest of coda's own neutral scale.
			'--col-neutral-900'    => '#505049',
			'--col-neutral-800'    => '#5e5d55',
			'--hsl-neutral-800'    => '53 5% 35%',
			'--col-neutral-700'    => '#77766c',
			'--col-neutral-500'    => '#aeada1',
			'--col-neutral-400'    => '#c9c7bc',
			'--hsl-neutral-400'    => '51 11% 76%',
			'--col-neutral-300'    => '#dddcd2',
			'--col-neutral-200'    => '#ebe9e1',
			'--col-neutral-100'    => '#f8f7f0',
			'--col-neutral-050'    => '#f8f7f0',
			'--hsl-neutral-050'    => '53 36% 96%',
			// Rest of coda's own purple scale (present but commented out in
			// its _tokens.scss — same hex values as identity's, since both
			// share the same purple story, only the "main" 900 is active).
			'--col-purple-700'     => '#7162e1',
			'--col-purple-500'     => '#a49bfd',
			'--col-purple-400'     => '#bcb7ff',
			'--col-purple-300'     => '#d0ccff',
			'--col-purple'         => '#2f13ba',
			'--col-ink'            => '#0d0d0c',
			'--hsl-ink'            => '60 4% 5%',
			'--fw-semi'            => '500',
			'--col-purple-900'     => '#2f13ba',
			'--col-purple-200'     => '#e0deff',
			'--col-purple-1000'    => '#190a83',
			'--col-purple-1100'    => '#190a83',
			'--hsl-purple-1100'    => '247 86% 28%',
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
			'--wp--preset--color--primary-black'  => '#0d0d0c',
			'--wp--preset--color--ink'            => '#0d0d0c',
			'--wp--preset--color--lime-900'       => '#4c8200',
			'--wp--preset--color--lime-1000'      => '#3d6900',
			'--wp--preset--color--lime-1100'      => '#345a00',
			'--wp--preset--color--raspberry'      => '#4c8200',
			'--wp--preset--color--raspberry-450'  => '#5e9f00',
			'--wp--preset--color--neutral-400'    => '#c9c7bc',
			'--wp--preset--color--neutral-700'    => '#77766c',
			'--wp--preset--color--neutral-800'    => '#5e5d55',
			'--wp--preset--color--white'          => '#fff',
		),
		'idtravel' => array(
			'--col-brand'          => '#e32447',
			'--hsl-brand'          => '349 77% 52%',
			'--col-brand-light'    => '#ff9bae',
			'--col-brand-dark'     => '#900720',
			'--col-secondary'      => '#2f13ba',
			'--col-secondary-light' => '#d0ccff',
			'--col-secondary-dark' => '#13086b',
			'--col-accent'         => '#cc1939',
			'--col-accent-400'     => '#cc1939',
			'--col-accent-500'     => '#cc1939',
			// idtravel's own .id-button uses its raspberry/brand colour -
			// same as --col-brand, set explicitly rather than relying on
			// both tokens coincidentally matching.
			'--id-button-accent'   => '#e32447',
			'--col-text'           => '#110d25',
			'--col-bg'             => '#ffffff',
			'--col-black'          => '#040013',
			'--ff-heading'         => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-body'            => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-accent'          => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--col-raspberry'      => '#e32447',
			'--col-raspberry-100'  => '#ffdbe3',
			'--col-raspberry-300'  => '#ff9bae',
			'--col-raspberry-400'  => '#f66f88',
			'--col-raspberry-600'  => '#cc1939',
			'--col-raspberry-900'  => '#900720',
			'--hsl-raspberry-400'  => '349 88% 70%',
			'--hsl-raspberry-600'  => '349 78% 45%',
			'--col-purple'         => '#2f13ba',
			'--col-purple-900'     => '#2f13ba',
			'--col-purple-700'     => '#7162e1',
			'--col-purple-500'     => '#a49bfd',
			'--col-purple-400'     => '#bcb7ff',
			'--col-purple-300'     => '#d0ccff',
			'--col-purple-1100'    => '#13086b',
			'--hsl-purple-1100'    => '247 86% 23%',
			'--col-ink'            => '#110d25',
			'--hsl-ink'            => '250 48% 10%',
			'--col-white'          => '#ffffff',
			'--col-neutral-900'    => '#1d1933',
			'--col-neutral-800'    => '#3b3652',
			'--hsl-neutral-800'    => '251 21% 27%',
			'--col-neutral-700'    => '#55506b',
			'--col-neutral-500'    => '#9793a8',
			'--col-neutral-400'    => '#b6b3c3',
			'--hsl-neutral-400'    => '251 12% 73%',
			'--col-neutral-300'    => '#cfcdd9',
			'--col-neutral-200'    => '#e0dfe6',
			'--col-neutral-100'    => '#f0eff4',
			'--col-neutral-050'    => '#f7f6fa',
			'--hsl-neutral-050'    => '255 29% 97%',
			// identity/coda's own naming has no leading zero ("neutral-50"),
			// used site-wide for body background — idtravel's own value is
			// the same colour as its own --col-neutral-050 above.
			'--col-neutral-50'     => '#f7f6fa',
			// idtravel has no "lime" hue — reusing the same raspberry shades
			// already chosen for --wp--preset--color--lime-900/1000 below,
			// converted to HSL since these blocks use hsl(var(--hsl-lime-*)).
			// idtravel's raspberry scale has no shade beyond 1000 either —
			// reused for the 1100 slot.
			'--col-lime-1100'      => '#7b0319',
			'--col-lime-1000'      => '#7b0319',
			'--hsl-lime-1000'      => '349 95% 25%',
			'--col-lime-900'       => '#900720',
			'--hsl-lime-900'       => '349 91% 30%',
			// Rest of the same raspberry-scale substitution, matched by
			// scale position rather than exact hex (the two scales don't
			// line up shade-for-shade): lime-800→raspberry-800,
			// lime-500→raspberry-450 (idtravel's step right after its own
			// "main", same relative position as lime-500 sits just after
			// lime-400/coda's own main), lime-300/200/100→raspberry-300/200/100.
			'--col-lime-800'       => '#a30b27',
			'--col-lime-500'       => '#ec4a67',
			'--col-lime-300'       => '#ff9bae',
			'--hsl-lime-300'       => '349 100% 80%',
			'--col-lime-200'       => '#ffb8c6',
			'--hsl-lime-200'       => '348 100% 86%',
			'--col-lime-100'       => '#ffdbe3',
			// idtravel's neutral scale peaks at 1000 (already its darkest,
			// no natural "one step further") — reused for the 1100 slot.
			'--col-neutral-1100'   => '#040013',
			// idtravel's own near-black, reused for the borrowed primary-black slot.
			'--col-primary-black'  => '#110d25',
			'--hsl-primary-black'  => '250 48% 10%',
			'--col-primary'        => '#110d25',
			// idtravel has no --fw-semi of its own — nearest existing weight
			// is its own --fw-book (450), reused here rather than inventing
			// a number. Flag for design review if a different weight is wanted.
			'--fw-semi'            => '450',
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
			// idtravel already defines --wp--preset--color--ink/raspberry/neutral-* etc.
			// in theme.json natively; only the slugs borrowed from coda/identity blocks
			// (lime-*, primary-black) need mapping to an idtravel-appropriate colour.
			'--wp--preset--color--primary-black'  => '#110d25',
			'--wp--preset--color--lime-900'       => '#900720',
			'--wp--preset--color--lime-1000'      => '#7b0319',
			'--wp--preset--color--lime-1100'      => '#7b0319',
		),
	);
}

/**
 * Outputs the current cb_site's token overrides as a :root CSS block, on
 * both the frontend and in the block editor (via enqueue_block_assets, which
 * fires in both contexts).
 */
function cb_output_site_token_overrides() {
	$site  = cb_site_template_suffix();
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
 * Palette slugs here are the ones ACTUALLY referenced by has-{slug}-color /
 * has-{slug}-background-color classes hardcoded in the copied block
 * templates (e.g. cb-featured-work's "has-lime-900-color", cb-case-study-hero's
 * "has-primary-black-background-color") — not the generic plan names, since
 * WordPress resolves those classes against these exact theme.json slugs.
 * theme.json (the base file) already defines most of these for idtravel;
 * "lime-900/1000/1100" and "primary-black" were missing entirely (a real,
 * site-independent bug — those classes rendered no colour at all regardless
 * of cb_site) and have been added there with static fallback values. This
 * filter overrides all of them per site so switching cb_site actually
 * changes what they render as. WP_Theme_JSON merges palette entries by
 * slug, so this only touches the listed slugs — the rest of the base
 * palette (neutral/purple/indigo scales etc.) is untouched.
 *
 * @param WP_Theme_JSON_Data $theme_json Theme JSON data object.
 * @return WP_Theme_JSON_Data
 */
function cb_filter_editor_theme_json( $theme_json ) {
	$site = cb_site_template_suffix();

	$palettes = array(
		'identity' => array(
			array( 'slug' => 'primary-black', 'name' => 'Primary Black', 'color' => '#0D0D0C' ),
			array( 'slug' => 'ink', 'name' => 'Ink', 'color' => '#0D0D0C' ),
			array( 'slug' => 'lime-900', 'name' => 'Lime 900', 'color' => '#4c8200' ),
			array( 'slug' => 'lime-1000', 'name' => 'Lime 1000', 'color' => '#3d6900' ),
			array( 'slug' => 'lime-1100', 'name' => 'Lime 1100', 'color' => '#345a00' ),
			array( 'slug' => 'raspberry', 'name' => 'Raspberry', 'color' => '#4c8200' ),
			array( 'slug' => 'raspberry-450', 'name' => 'Raspberry 450', 'color' => '#5e9f00' ),
			array( 'slug' => 'neutral-400', 'name' => 'Neutral 400', 'color' => '#c9c7bc' ),
			array( 'slug' => 'neutral-700', 'name' => 'Neutral 700', 'color' => '#77766c' ),
			array( 'slug' => 'neutral-800', 'name' => 'Neutral 800', 'color' => '#5e5d55' ),
			array( 'slug' => 'white', 'name' => 'White', 'color' => '#fff' ),
		),
		'coda'     => array(
			array( 'slug' => 'primary-black', 'name' => 'Primary Black', 'color' => '#0d0d0c' ),
			array( 'slug' => 'ink', 'name' => 'Ink', 'color' => '#0d0d0c' ),
			array( 'slug' => 'lime-900', 'name' => 'Lime 900', 'color' => '#4c8200' ),
			array( 'slug' => 'lime-1000', 'name' => 'Lime 1000', 'color' => '#3d6900' ),
			array( 'slug' => 'lime-1100', 'name' => 'Lime 1100', 'color' => '#345a00' ),
			array( 'slug' => 'raspberry', 'name' => 'Raspberry', 'color' => '#4c8200' ),
			array( 'slug' => 'raspberry-450', 'name' => 'Raspberry 450', 'color' => '#5e9f00' ),
			array( 'slug' => 'neutral-400', 'name' => 'Neutral 400', 'color' => '#c9c7bc' ),
			array( 'slug' => 'neutral-700', 'name' => 'Neutral 700', 'color' => '#77766c' ),
			array( 'slug' => 'neutral-800', 'name' => 'Neutral 800', 'color' => '#5e5d55' ),
			array( 'slug' => 'white', 'name' => 'White', 'color' => '#fff' ),
		),
		'idtravel' => array(
			array( 'slug' => 'primary-black', 'name' => 'Primary Black', 'color' => '#110d25' ),
			array( 'slug' => 'ink', 'name' => 'Ink', 'color' => '#110d25' ),
			array( 'slug' => 'lime-900', 'name' => 'Lime 900', 'color' => '#900720' ),
			array( 'slug' => 'lime-1000', 'name' => 'Lime 1000', 'color' => '#7b0319' ),
			array( 'slug' => 'lime-1100', 'name' => 'Lime 1100', 'color' => '#7b0319' ),
			array( 'slug' => 'raspberry', 'name' => 'Raspberry', 'color' => '#e32447' ),
			array( 'slug' => 'raspberry-450', 'name' => 'Raspberry 450', 'color' => '#ec4a67' ),
			array( 'slug' => 'neutral-400', 'name' => 'Neutral 400', 'color' => '#b6b3c3' ),
			array( 'slug' => 'neutral-700', 'name' => 'Neutral 700', 'color' => '#55506b' ),
			array( 'slug' => 'neutral-800', 'name' => 'Neutral 800', 'color' => '#3b3652' ),
			array( 'slug' => 'white', 'name' => 'White', 'color' => '#ffffff' ),
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
