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
	return get_field( 'cb_site', 'option' ) ? get_field( 'cb_site', 'option' ) : 'idtravel';
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
 * Hides the CTA repeater's Image/Mask fields on the Site-Wide Settings
 * options page when cb_site is 'health' - health's own CTA template
 * (blocks/cb-cta-health.php) never reads either field, so they're pure
 * clutter for a Health content editor (2026-08-25).
 *
 * Returning false from acf/prepare_field removes the field from the edit
 * screen entirely. ACF's own conditional_logic UI can't do this instead:
 * it only wires up sibling fields within the same repeater row, and cb_site
 * lives on an entirely different tab of this same options page.
 *
 * @param array $field ACF field settings.
 * @return array|false
 */
function cb_hide_health_extraneous_cta_fields( $field ) {
	if ( 'health' === cb_site_template_suffix() ) {
		return false;
	}
	return $field;
}
add_filter( 'acf/prepare_field/key=field_6908877075c95', 'cb_hide_health_extraneous_cta_fields' ); // ctas_image
add_filter( 'acf/prepare_field/key=field_6908877e75c96', 'cb_hide_health_extraneous_cta_fields' ); // ctas_mask

/**
 * Hides CB Lined Title's Line Colour field for every site except health -
 * only health's own border-colour rules
 * (.cb-lined-title--line-dark/-light in _cb_lined_title.scss) read it at
 * all, so it's pure clutter for identity/coda/idtravel editors (2026-08-25).
 *
 * @param array $field ACF field settings.
 * @return array|false
 */
function cb_hide_non_health_lined_title_line_colour( $field ) {
	if ( 'health' !== cb_site_template_suffix() ) {
		return false;
	}
	return $field;
}
add_filter( 'acf/prepare_field/key=field_cb_lined_title_line_colour', 'cb_hide_non_health_lined_title_line_colour' );

/**
 * Replaces CB Testimonial's Style choices with health's own 5 colours
 * (Blueberry/Strawberry/Gooseberry/Spearmint/White) when cb_site is
 * 'health' - identity/coda/idtravel's own Light/Raspberry/Purple choices
 * are irrelevant there, not real options anyone would pick (2026-08-25).
 *
 * Values are has-{slug}-background-color, matching this field's own
 * existing convention (see _cb_testimonial.scss's other .has-*-background-
 * color rules) - all 5 slugs exist in health's own isolated theme.json
 * palette (see cb_filter_editor_theme_json()), so WordPress generates the
 * background-color rule for each automatically.
 *
 * @param array $field ACF field settings.
 * @return array
 */
function cb_health_testimonial_style_choices( $field ) {
	if ( 'health' === cb_site_template_suffix() ) {
		$field['choices'] = array(
			'has-blueberry-background-color'  => 'Blueberry',
			'has-strawberry-background-color' => 'Strawberry',
			'has-gooseberry-background-color' => 'Gooseberry',
			'has-spearmint-background-color'  => 'Spearmint',
			'has-white-background-color'      => 'White',
		);
	}
	return $field;
}
add_filter( 'acf/prepare_field/key=field_cb_testimonial_style', 'cb_health_testimonial_style_choices' );

/**
 * Returns the full per-site token table.
 *
 * @return array<string, array<string, string>>
 */
function cb_get_site_tokens_table() {
	return array(
		'identity' => array(
			// Generic names (plan §5.2).
			'--col-brand'                        => '#B8FF52',
			'--hsl-brand'                        => '85 100% 66%',
			'--col-brand-light'                  => '#CAFC83',
			// same hex-vs-hsl rounding issue as --col-lime-900 (identical
			// underlying value) - see the fuller comment on coda's copy of
			// this key below.
			'--col-brand-dark'                   => 'hsl(var(--hsl-lime-900))',
			'--col-secondary'                    => '#2f13ba',
			'--col-secondary-light'              => '#e0deff',
			'--col-secondary-dark'               => '#190A83',
			'--col-accent'                       => '#e03030',
			'--col-accent-500'                   => '#ec5a5a',
			// .id-button's accent isn't the site's brand colour - identity's
			// own _buttons.scss uses its own red (matches --col-red-600),
			// not lime.
			'--col-button'                       => '#e03030',
			'--col-text'                         => '#0D0D0C',
			'--col-bg'                           => '#fff',
			'--col-black'                        => '#0D0D0C',
			'--ff-heading'                       => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-body'                          => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-accent'                        => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			// identity's own _tokens.scss calls this scale "green"; the shared
			// SCSS has been standardised on coda's "lime" naming instead
			// (more accurate name for this hue, and coda's is the more
			// complete/systematic token file) — values are colour-identical.
			'--col-lime-400'                     => '#B8FF52',
			'--col-lime-300'                     => '#CAFC83',
			// identity's own green scale has no shade beyond 1000 — reused
			// for the 1100 slot coda's own scale goes one step darker to.
			'--col-lime-1100'                    => '#3d6900',
			'--col-lime-1000'                    => '#3d6900',
			'--col-lime-900'                     => '#4c8200',
			'--col-lime-800'                     => '#5e9f00',
			'--col-lime-600'                     => '#94dd2c',
			'--col-lime-500'                     => '#ADF448',
			'--col-lime-200'                     => '#DDFBB2',
			'--col-lime-100'                     => '#EEFED9',
			'--hsl-lime-1000'                    => '85 100% 21%',
			'--hsl-lime-900'                     => '85 100% 25%',
			'--hsl-lime-300'                     => '85 95% 75%',
			'--hsl-lime-200'                     => '85 90% 84%',
			// idtravel has no equivalent hue for its own "raspberry" scale —
			// identity has no raspberry concept at all. Where shared blocks
			// (idtravel one-offs, but registered everywhere) use raspberry
			// as a light/dark accent role, reusing identity's own brand
			// light/dark tint for the same role rather than a literal shade
			// match (the two scales don't line up shade-for-shade).
			'--col-raspberry-100'                => '#EEFED9',
			'--col-raspberry-400'                => '#B8FF52',
			'--hsl-raspberry-400'                => '85 100% 66%',
			'--col-raspberry-600'                => 'hsl(var(--hsl-raspberry-600))',
			'--hsl-raspberry-600'                => '85 100% 25%',
			'--col-neutral-1100'                 => '#22211E',
			// Rest of identity's own neutral scale.
			'--col-neutral-900'                  => '#505049',
			'--col-neutral-800'                  => '#5e5d55',
			'--hsl-neutral-800'                  => '53 5% 35%',
			'--col-neutral-700'                  => '#77766c',
			'--col-neutral-600'                  => '#939287', // identity's/coda's own real value (differs from the shared base).
			'--col-neutral-500'                  => '#aeada1',
			'--col-neutral-400'                  => '#c9c7bc',
			'--hsl-neutral-400'                  => '51 11% 76%',
			'--col-neutral-300'                  => '#dddcd2',
			'--col-neutral-200'                  => '#ebe9e1',
			'--col-neutral-100'                  => '#f8f7f0',
			// identity's scale starts at 100 (no 050 step) — reusing its own
			// lightest neutral as the nearest equivalent.
			'--col-neutral-050'                  => '#f8f7f0',
			'--hsl-neutral-050'                  => '53 36% 96%',
			// Rest of identity's own purple scale.
			'--col-purple-700'                   => '#7162e1',
			'--col-purple-500'                   => '#a49bfd',
			'--col-purple-400'                   => '#bcb7ff',
			'--col-purple-300'                   => '#d0ccff',
			// Bare --col-purple/--col-ink aren't in identity's own naming —
			// both resolve to values it already defines elsewhere (main
			// purple #2f13ba is identical across all 3 themes; "ink" is
			// identity's near-black text colour, same as its primary-black).
			'--col-purple'                       => '#2f13ba',
			'--col-ink'                          => '#0D0D0C',
			'--hsl-ink'                          => '60 4% 5%',
			'--hsl-primary-black'                => '60 4% 5%',
			// idtravel's own real value is 450, not the shared base's 500 -
			// matched here as part of the 2026-07-14 typography decision
			// (this one actually differs from the base, unlike most of the
			// other fs-*/fw-* tokens removed above, which were redundant
			// with it).
			'--fw-semi'                          => '450',
			'--col-purple-900'                   => '#2f13ba',
			'--col-purple-200'                   => '#e0deff',
			'--col-purple-1000'                  => '#190A83',
			// identity's purple scale has no shade beyond 1000 — reusing it
			// for the 1100 slot idtravel's own scale goes one step darker to.
			'--col-purple-1100'                  => '#190A83',
			'--hsl-purple-1100'                  => '247 86% 28%',
			'--col-red-600'                      => '#e03030',
			'--col-red-500'                      => '#ec5a5a',
			'--col-primary-black'                => '#0D0D0C',
			'--col-primary'                      => '#0D0D0C',
			'--col-white'                        => '#fff',
			'--col-neutral-50'                   => '#f8f7f0',
			// Deliberate client decision (2026-07-14): identity's typography
			// (sizes, weights, letter-spacing, line-height) is now idtravel's,
			// not identity's own real values. Most of the --fs-*/--fw-*/
			// --ls-* overrides that used to live here were simply removed so
			// identity falls through to the shared base _tokens.scss, which
			// is idtravel's own scale verbatim for those (the theme was
			// forked from idtravel) - confirmed by diffing idtravel's own row
			// in this table against the base, token by token, rather than
			// assumed. --fw-semi (above), the 4 --lh-* aliases below, and
			// --fs-800 are the exceptions: idtravel's own row genuinely
			// overrides --fw-semi away from the base (450, not 500), and
			// --lh-tightest/tight/snug/normal and --fs-800 don't exist in the
			// base _tokens.scss at all - idtravel's own scale has no "800"
			// rung, it jumps 700 straight to 850 - they're consumed directly,
			// with no fallback, by shared components identity also renders
			// (e.g. cb-pushthrough's __desc uses --lh-snug) or by
			// identity-exclusive markup with no idtravel equivalent to fall
			// through to (.insight-type__header, .news-hero__content -
			// 2026-07-29) - removing them outright rather than matching
			// idtravel's actual values (or, where idtravel has none,
			// identity's own real original value) would have left them
			// genuinely undefined, a real regression caught by checking
			// every removed token's consumers for a missing fallback, not by
			// assuming "removed = falls through correctly" held for all of
			// them. This is intentional and no longer a bug: identity's real
			// production typography is deliberately NOT the reference for
			// these properties any more. Colour tokens are untouched.
			'--lh-tightest'                      => '1',
			'--lh-tight'                         => '1.05',
			'--lh-snug'                          => '1.1',
			'--lh-normal'                        => '1.55',
			// identity's own real value (cb-identity2025/_tokens.scss) -
			// idtravel has no "800" rung to fall through to instead.
			'--fs-800'                           => 'clamp(1.5rem, 0.9rem + 1.6vw, 2.5rem)',
			// Same story as --fs-800 above: idtravel's scale tops out at
			// --fs-900, no "950" rung to fall through to. Confirmed via
			// identity's own real cb-identity2025/_tokens.scss (2026-07-29) -
			// used by cb-about-page-header h1, which was silently rendering
			// at the browser default (18px) with nothing to fall back on.
			'--fs-950'                           => 'clamp(3.4375rem, 1rem + 4vw, 6.25rem)',
			// Same story again: idtravel has no "300" rung either (documented
			// pre-existing gap, see the file-level comment near the top of this
			// function). Used by cb-contact-page__emails a and
			// cb-contact-addresses__title, both silently rendering at the
			// browser default (2026-07-29).
			'--fs-300'                           => 'clamp(1rem, 0.95rem + 0.5vw, 1.25rem)',
			// Default Heading-block weights (h1.wp-block-heading etc. in
			// _typography.scss) per explicit 2026-08-06 spec - identity's are
			// the only ones that diverge from the --fw-book fallback on every
			// level.
			'--fw-wpb-h1'                        => 'var(--fw-semibold)',
			'--fw-wpb-h2'                        => 'var(--fw-light)',
			'--fw-wpb-h3'                        => 'var(--fw-light)',
			'--col-footer-link-hover'            => 'var(--col-neutral-500)', // identity's own real footer link hover.
			// WP-generated has-{slug}-color/-background-color utility class targets —
			// see cb_filter_editor_theme_json() for why these specific slugs.
			'--wp--preset--color--primary-black' => '#0D0D0C',
			'--wp--preset--color--ink'           => '#0D0D0C',
			'--wp--preset--color--lime-900'      => '#4c8200',
			'--wp--preset--color--lime-1000'     => '#3d6900',
			'--wp--preset--color--lime-1100'     => '#345a00',
			'--wp--preset--color--raspberry'     => '#4c8200',
			'--wp--preset--color--raspberry-450' => '#5e9f00',
			'--wp--preset--color--neutral-400'   => '#c9c7bc',
			'--wp--preset--color--neutral-700'   => '#77766c',
			'--wp--preset--color--neutral-800'   => '#5e5d55',
			'--wp--preset--color--white'         => '#fff',
		),
		'coda'     => array(
			'--col-brand'                        => '#b8ff52',
			'--hsl-brand'                        => '85 100% 66%',
			'--col-brand-light'                  => '#CAFC83',
			// --col-brand-dark is this shared theme's own generic alias for
			// lime-900 (not a name real coda's _tokens.scss uses itself) -
			// same hex-vs-hsl rounding bug as --col-lime-900: confirmed via
			// .text-page .post-content's --_colour on modern-slavery, work,
			// and privacy-policy (real=rgb(74,128,0), local=rgb(76,130,0)
			// before this fix).
			'--col-brand-dark'                   => 'hsl(var(--hsl-lime-900))',
			'--col-secondary'                    => '#2f13ba',
			'--col-secondary-light'              => '#e0deff',
			'--col-secondary-dark'               => '#190a83',
			'--col-accent'                       => '#e03030',
			'--col-accent-500'                   => '#ec5a5a',
			// coda's own .id-button uses its main purple, not its lime brand.
			'--col-button'                       => '#2f13ba',
			'--col-text'                         => '#0d0d0c',
			'--col-bg'                           => '#fff',
			'--col-black'                        => '#0D0D0C',
			'--ff-heading'                       => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-body'                          => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-accent'                        => '"Suisse", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--col-lime-400'                     => '#b8ff52',
			// coda's real _tokens.scss computes lime-300/900/1000/200 from
			// their --hsl-* value at render time (`hsl(var(--hsl-lime-XXX))`),
			// not a hardcoded hex - the hex comments in that file are only
			// approximations. This shared theme previously hardcoded those
			// approximations as the actual values, which are off by 1-2 per
			// channel from what the browser actually computes from the HSL
			// - confirmed as the exact, recurring "rounding" colour diffs
			// across many coda pages (contact-us, about, work, services/*,
			// modern-slavery, privacy-policy). Switched to the same dynamic
			// hsl(var()) form real coda uses so these resolve identically.
			'--col-lime-300'                     => 'hsl(var(--hsl-lime-300))',
			'--col-lime-900'                     => 'hsl(var(--hsl-lime-900))',
			// Rest of coda's own lime scale (_tokens.scss).
			'--col-lime-1100'                    => '#345a00',
			'--col-lime-1000'                    => 'hsl(var(--hsl-lime-1000))',
			'--col-lime-800'                     => '#5e9f00',
			'--col-lime-600'                     => '#94dd2c',
			'--col-lime-200'                     => 'hsl(var(--hsl-lime-200))',
			'--col-lime-100'                     => '#eefed9',
			'--hsl-lime-1000'                    => '85 100% 21%',
			'--hsl-lime-900'                     => '85 100% 25%',
			'--hsl-lime-300'                     => '85 95% 75%',
			'--hsl-lime-200'                     => '85 90% 84%',
			'--hsl-primary-black'                => '60 4% 5%',
			// coda has no raspberry concept either — same substitution as
			// identity's (see its own comment on this).
			'--col-raspberry-100'                => '#eefed9',
			'--col-raspberry-400'                => '#b8ff52',
			'--hsl-raspberry-400'                => '85 100% 66%',
			'--col-raspberry-600'                => 'hsl(var(--hsl-raspberry-600))',
			'--hsl-raspberry-600'                => '85 100% 25%',
			'--col-lime-500'                     => '#adf448',
			'--col-neutral-1100'                 => '#22211e',
			// Rest of coda's own neutral scale.
			'--col-neutral-900'                  => '#505049',
			'--col-neutral-800'                  => '#5e5d55',
			'--hsl-neutral-800'                  => '53 5% 35%',
			'--col-neutral-700'                  => '#77766c',
			'--col-neutral-600'                  => '#939287', // identity's/coda's own real value (differs from the shared base).
			'--col-neutral-500'                  => '#aeada1',
			'--col-neutral-400'                  => '#c9c7bc',
			'--hsl-neutral-400'                  => '51 11% 76%',
			'--col-neutral-300'                  => '#dddcd2',
			'--col-neutral-200'                  => '#ebe9e1',
			'--col-neutral-100'                  => '#f8f7f0',
			'--col-neutral-050'                  => '#f8f7f0',
			'--hsl-neutral-050'                  => '53 36% 96%',
			// Rest of coda's own purple scale (present but commented out in
			// its _tokens.scss — same hex values as identity's, since both
			// share the same purple story, only the "main" 900 is active).
			'--col-purple-700'                   => '#7162e1',
			'--col-purple-500'                   => '#a49bfd',
			'--col-purple-400'                   => '#bcb7ff',
			'--col-purple-300'                   => '#d0ccff',
			'--col-purple'                       => '#2f13ba',
			'--col-ink'                          => '#0d0d0c',
			'--hsl-ink'                          => '60 4% 5%',
			'--fw-semi'                          => '500',
			'--col-purple-900'                   => '#2f13ba',
			'--col-purple-200'                   => '#e0deff',
			'--col-purple-1000'                  => '#190a83',
			'--col-purple-1100'                  => '#190a83',
			'--hsl-purple-1100'                  => '247 86% 28%',
			'--col-red-600'                      => '#e03030',
			'--col-red-500'                      => '#ec5a5a',
			'--col-primary-black'                => '#0d0d0c',
			'--col-primary'                      => '#0d0d0c',
			'--col-white'                        => '#fff',
			'--col-neutral-50'                   => '#f8f7f0',
			'--fs-200'                           => '1.125rem', // coda's own real value too (differs from the shared base, which is idtravel's).
			'--fs-300'                           => 'clamp(1rem, 0.95rem + 0.5vw, 1.25rem)',
			'--fs-400'                           => 'clamp(1.0625rem, 0.9rem + 0.6vw, 1.375rem)',
			'--fs-500'                           => 'clamp(1.1875rem, 0.9rem + 1vw, 1.751rem)',
			'--fs-h2'                            => 'var(--fs-500)',
			'--fw-h2'                            => 'var(--fw-light)',  // coda's own real h2.
			// Default Heading-block weight (h2.wp-block-heading in
			// _typography.scss) per explicit 2026-08-06 spec - coda's h1/h3
			// both want the --fw-book fallback, so only h2 needs an override
			// here.
			'--fw-wpb-h2'                        => 'var(--fw-light)',
			'--col-footer-link-hover'            => 'var(--col-lime-300)', // coda's own real footer link hover.
			'--col-card-hover'                   => 'var(--col-primary-black)', // coda's own real insight-type-grid__card hover colour (light bg, needs dark text).
			'--fs-600'                           => 'clamp(1.3125rem, 0.9rem + 1.2vw, 1.999rem)',
			'--fs-700'                           => 'clamp(1.4375rem, 0.9rem + 1.4vw, 2.25rem)',
			'--fs-800'                           => 'clamp(1.5rem, 0.9rem + 1.6vw, 2.5rem)',
			'--fs-850'                           => 'clamp(1.875rem, 0.95rem + 1.9vw, 3.125rem)',
			'--fs-900'                           => 'clamp(2.5rem, 1rem + 3vw, 5rem)',
			'--fs-950'                           => 'clamp(3.4375rem, 1rem + 4vw, 6.25rem)',
			'--lh-tightest'                      => '1',
			'--lh-tight'                         => '1.1',
			'--lh-snug'                          => '1.2',
			'--lh-normal'                        => '1.5',
			// same missing has-{slug}-font-size gap as identity above - see its
			// comment for the full explanation.
			'--wp--preset--font-size--500'       => 'var(--fs-500)',
			'--wp--preset--font-size--700'       => 'var(--fs-700)',
			'--wp--preset--font-size--850'       => 'var(--fs-850)',
			'--wp--preset--color--primary-black' => '#0d0d0c',
			'--wp--preset--color--ink'           => '#0d0d0c',
			// same hex-vs-hsl rounding fix as --col-lime-900/1000 above -
			// these feed the has-lime-900/1000-color Gutenberg preset
			// classes directly, which is what several of the flagged pages
			// (contact-us, about) actually use.
			'--wp--preset--color--lime-900'      => 'hsl(var(--hsl-lime-900))',
			'--wp--preset--color--lime-1000'     => 'hsl(var(--hsl-lime-1000))',
			'--wp--preset--color--lime-1100'     => '#345a00',
			'--wp--preset--color--raspberry'     => '#4c8200',
			'--wp--preset--color--raspberry-450' => '#5e9f00',
			'--wp--preset--color--neutral-400'   => '#c9c7bc',
			'--wp--preset--color--neutral-700'   => '#77766c',
			'--wp--preset--color--neutral-800'   => '#5e5d55',
			'--wp--preset--color--white'         => '#fff',
		),
		'idtravel' => array(
			'--col-brand'                        => '#e32447',
			'--hsl-brand'                        => '349 77% 52%',
			'--col-brand-light'                  => '#ff9bae',
			'--col-brand-dark'                   => '#900720',
			'--col-secondary'                    => '#2f13ba',
			'--col-secondary-light'              => '#d0ccff',
			'--col-secondary-dark'               => '#13086b',
			'--col-accent'                       => '#cc1939',
			'--col-accent-500'                   => '#cc1939',
			// idtravel's own .id-button uses its raspberry/brand colour -
			// same as --col-brand, set explicitly rather than relying on
			// both tokens coincidentally matching.
			'--col-button'                       => '#e32447',
			'--col-text'                         => '#110d25',
			'--col-bg'                           => '#ffffff',
			'--col-black'                        => '#040013',
			'--ff-heading'                       => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-body'                          => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--ff-accent'                        => '"Suisse International", Arial, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif',
			'--col-raspberry'                    => '#e32447',
			'--col-raspberry-100'                => '#ffdbe3',
			'--col-raspberry-300'                => '#ff9bae',
			'--col-raspberry-400'                => '#f66f88',
			'--col-raspberry-600'                => '#cc1939',
			'--col-raspberry-900'                => '#900720',
			'--hsl-raspberry-400'                => '349 88% 70%',
			'--hsl-raspberry-600'                => '349 78% 45%',
			'--col-purple'                       => '#2f13ba',
			'--col-purple-900'                   => '#2f13ba',
			'--col-purple-700'                   => '#7162e1',
			'--col-purple-500'                   => '#a49bfd',
			'--col-purple-400'                   => '#bcb7ff',
			'--col-purple-300'                   => '#d0ccff',
			'--col-purple-1100'                  => '#13086b',
			'--hsl-purple-1100'                  => '247 86% 23%',
			'--col-ink'                          => '#110d25',
			'--hsl-ink'                          => '250 48% 10%',
			'--col-white'                        => '#ffffff',
			'--col-neutral-900'                  => '#1d1933',
			'--col-neutral-800'                  => '#3b3652',
			'--hsl-neutral-800'                  => '251 21% 27%',
			'--col-neutral-700'                  => '#55506b',
			'--col-neutral-500'                  => '#9793a8',
			'--col-neutral-400'                  => '#b6b3c3',
			'--hsl-neutral-400'                  => '251 12% 73%',
			'--col-neutral-300'                  => '#cfcdd9',
			'--col-neutral-200'                  => '#e0dfe6',
			'--col-neutral-100'                  => '#f0eff4',
			'--col-neutral-050'                  => '#f7f6fa',
			'--hsl-neutral-050'                  => '255 29% 97%',
			// identity/coda's own naming has no leading zero ("neutral-50"),
			// used site-wide for body background — idtravel's own value is
			// the same colour as its own --col-neutral-050 above.
			'--col-neutral-50'                   => '#f7f6fa',
			// idtravel has no "lime" hue — reusing the same raspberry shades
			// already chosen for --wp--preset--color--lime-900/1000 below,
			// converted to HSL since these blocks use hsl(var(--hsl-lime-*)).
			// idtravel's raspberry scale has no shade beyond 1000 either —
			// reused for the 1100 slot.
			'--col-lime-1100'                    => '#7b0319',
			'--col-lime-1000'                    => '#7b0319',
			'--hsl-lime-1000'                    => '349 95% 25%',
			'--col-lime-900'                     => '#900720',
			'--hsl-lime-900'                     => '349 91% 30%',
			// Rest of the same raspberry-scale substitution, matched by
			// scale position rather than exact hex (the two scales don't
			// line up shade-for-shade): lime-800→raspberry-800,
			// lime-500→raspberry-450 (idtravel's step right after its own
			// "main", same relative position as lime-500 sits just after
			// lime-400/coda's own main), lime-300/200/100→raspberry-300/200/100.
			'--col-lime-800'                     => '#a30b27',
			'--col-lime-600'                     => '#cc1939',
			'--col-lime-500'                     => '#ec4a67',
			'--col-lime-300'                     => '#ff9bae',
			'--hsl-lime-300'                     => '349 100% 80%',
			'--col-lime-200'                     => '#ffb8c6',
			'--hsl-lime-200'                     => '348 100% 86%',
			'--col-lime-100'                     => '#ffdbe3',
			// idtravel's neutral scale peaks at 1000 (already its darkest,
			// no natural "one step further") — reused for the 1100 slot.
			'--col-neutral-1100'                 => '#040013',
			// idtravel's own near-black, reused for the borrowed primary-black slot.
			'--col-primary-black'                => '#110d25',
			'--hsl-primary-black'                => '250 48% 10%',
			'--col-primary'                      => '#110d25',
			// idtravel has no --fw-semi of its own — nearest existing weight
			// is its own --fw-book (450), reused here rather than inventing
			// a number. Flag for design review if a different weight is wanted.
			'--fw-semi'                          => '450',
			// idtravel has no --fs-300/800/950 in its own tokens file (a pre-existing gap, not invented here).
			'--fs-400'                           => 'clamp(1.2222rem, 1.1rem + 0.6vw, 1.375rem)',
			'--fs-500'                           => 'clamp(1.4444rem, 1.25rem + 0.9vw, 1.75rem)',
			'--fs-h2'                            => 'var(--fs-700)',
			'--fw-h2'                            => 'var(--fw-book)',   // idtravel's own real h2: matches the base fallback.
			'--col-footer-link-hover'            => 'var(--col-purple-400)', // idtravel's own real footer link hover.
			'--fs-600'                           => 'clamp(1.6667rem, 1.4rem + 1.2vw, 2rem)',
			'--fs-700'                           => 'clamp(1.9444rem, 1.6rem + 1.4vw, 2.25rem)',
			'--fs-850'                           => 'clamp(2.3333rem, 1.9rem + 2vw, 3.125rem)',
			'--fs-900'                           => 'clamp(3.3333rem, 2.6rem + 3vw, 5rem)',
			'--lh-tightest'                      => '1',    // --lh-900
			'--lh-tight'                         => '1.05', // --lh-875
			'--lh-snug'                          => '1.1',  // --lh-850
			'--lh-normal'                        => '1.55', // --lh-100
			'--lh-50'                            => '1.4',
			'--lh-100'                           => '1.55',
			'--lh-200'                           => '1.5',
			'--lh-400'                           => '1.3',
			'--lh-500'                           => '1.25',
			'--lh-600'                           => '1.2',
			'--lh-700'                           => '1.15',
			'--lh-850'                           => '1.1',
			'--lh-875'                           => '1.05',
			'--lh-900'                           => '1',
			// idtravel already defines --wp--preset--color--ink/raspberry/neutral-* etc.
			// in theme.json natively; only the slugs borrowed from coda/identity blocks
			// (lime-*, primary-black) need mapping to an idtravel-appropriate colour.
			'--wp--preset--color--primary-black' => '#110d25',
			'--wp--preset--color--lime-900'      => '#900720',
			'--wp--preset--color--lime-1000'     => '#7b0319',
			'--wp--preset--color--lime-1100'     => '#7b0319',
		),
		// health is a new 2026-08-25 context, built from idtravel's row as its
		// starting point (typography scale, structural tokens) — colour values
		// are placeholders pending the client's actual palette. idtravel's
		// --col-bg is already '#ffffff' with no `body.cb-site-idtravel` dark
		// override anywhere in the SCSS (only `body.cb-site-identity` sets one
		// — see _header-site-overrides.scss), so health inherits a genuinely
		// white page background for free the same way idtravel does; no new
		// CSS was needed for that part of the brief.
		'health'   => array(
			// Dead-token sweep (2026-08-25): --col-brand-light, --col-secondary-
			// light/-dark, bare --col-accent, --ff-heading/-body/-accent,
			// --col-text, --col-bg, --col-raspberry-300/-900, and the 4
			// --wp--preset--color--* overrides further down were all removed
			// from health's row — confirmed zero `var(--token)` consumers
			// anywhere in src/sass (--ff-heading/-body/-accent have never
			// been consumed theme-wide; --font-family, defined directly in
			// the base _tokens.scss with no per-site override at all, is
			// what every font-family declaration actually reads). The
			// --wp--preset--color--* ones are additionally dead specifically
			// for health now: theme.json's editor palette isolation (see
			// cb_filter_editor_theme_json()) means health's palette no
			// longer has primary-black/lime-900/1000/1100 slugs for WP to
			// generate those custom properties from at all. Left untouched
			// on identity/coda/idtravel, where the same tokens are equally
			// dead but this wasn't asked for.
			'--col-brand'                        => '#e32447',
			'--hsl-brand'                        => '349 77% 52%',
			'--col-brand-dark'                   => '#900720',
			'--col-secondary'                    => '#2f13ba',
			'--col-accent-500'                   => '#cc1939',
			// #a300ac is Strawberry 850 in idh-gooseberry.css (2026-08-25
			// client-specified button colour) - referenced by token rather
			// than repeating the literal hex, since --col-strawberry-850 is
			// already defined below in this same array.
			'--col-button'                       => 'var(--col-strawberry-850)',
			// idh-gooseberry.css's real --idh-black (#0d0d0c) supersedes the
			// idtravel-cloned placeholder (#040013) that used to be here.
			'--col-black'                        => '#0d0d0c',
			'--col-raspberry'                    => '#e32447',
			'--col-raspberry-100'                => '#ffdbe3',
			'--col-raspberry-400'                => '#f66f88',
			'--col-raspberry-600'                => '#cc1939',
			'--hsl-raspberry-400'                => '349 88% 70%',
			'--hsl-raspberry-600'                => '349 78% 45%',
			'--col-purple'                       => '#2f13ba',
			'--col-purple-900'                   => '#2f13ba',
			'--col-purple-700'                   => '#7162e1',
			'--col-purple-500'                   => '#a49bfd',
			'--col-purple-400'                   => '#bcb7ff',
			'--col-purple-300'                   => '#d0ccff',
			'--col-purple-1100'                  => '#13086b',
			'--hsl-purple-1100'                  => '247 86% 23%',
			'--col-ink'                          => '#110d25',
			'--hsl-ink'                          => '250 48% 10%',
			'--col-white'                        => '#ffffff',
			// Real idh-neutral-* scale (idh-gooseberry.css, 2026-08-25)
			// supersedes the idtravel-cloned placeholder scale that used to be
			// here — a genuinely different, warmer hue family (matches
			// identity's/coda's own neutral scale, not idtravel's cooler
			// purple-tinted one). --hsl-neutral-800/400/050 recomputed from
			// the new hex values so hsl(var(--hsl-neutral-*)/alpha) call
			// sites stay in sync; no hsl companion existed for the newly
			// added 600/750/1000/1050 rungs, so none is added for them.
			'--col-neutral-1050'                 => '#2b2a28',
			'--col-neutral-1000'                 => '#353431',
			'--col-neutral-900'                  => '#505049',
			'--col-neutral-800'                  => '#5e5d55',
			'--hsl-neutral-800'                  => '53 5% 35%',
			'--col-neutral-750'                  => '#6b6962',
			'--col-neutral-700'                  => '#77766c',
			'--col-neutral-600'                  => '#939287',
			'--col-neutral-500'                  => '#aeada1',
			'--col-neutral-400'                  => '#c9c7bc',
			'--hsl-neutral-400'                  => '51 11% 76%',
			'--col-neutral-300'                  => '#dddcd2',
			'--col-neutral-200'                  => '#eae9df',
			'--col-neutral-100'                  => '#f5f4eb',
			'--col-neutral-050'                  => '#fdfcf4',
			'--hsl-neutral-050'                  => '53 69% 97%',
			'--col-neutral-50'                   => '#fdfcf4',
			'--col-lime-1100'                    => '#7b0319',
			'--col-lime-1000'                    => '#7b0319',
			'--hsl-lime-1000'                    => '349 95% 25%',
			'--col-lime-900'                     => '#900720',
			'--hsl-lime-900'                     => '349 91% 30%',
			'--col-lime-800'                     => '#a30b27',
			'--col-lime-600'                     => '#cc1939',
			'--col-lime-500'                     => '#ec4a67',
			'--col-lime-300'                     => '#ff9bae',
			'--hsl-lime-300'                     => '349 100% 80%',
			'--col-lime-200'                     => '#ffb8c6',
			'--hsl-lime-200'                     => '348 100% 86%',
			'--col-lime-100'                     => '#ffdbe3',
			'--col-neutral-1100'                 => '#22211e',
			'--col-primary-black'                => '#110d25',
			'--hsl-primary-black'                => '250 48% 10%',
			'--col-primary'                      => '#110d25',
			'--fw-semi'                          => '450',
			'--fs-400'                           => 'clamp(1.2222rem, 1.1rem + 0.6vw, 1.375rem)',
			'--fs-500'                           => 'clamp(1.4444rem, 1.25rem + 0.9vw, 1.75rem)',
			'--fs-h2'                            => 'var(--fs-700)',
			'--fw-h2'                            => 'var(--fw-book)',
			'--col-footer-link-hover'            => 'var(--col-purple-400)',
			'--fs-600'                           => 'clamp(1.6667rem, 1.4rem + 1.2vw, 2rem)',
			'--fs-700'                           => 'clamp(1.9444rem, 1.6rem + 1.4vw, 2.25rem)',
			'--fs-850'                           => 'clamp(2.3333rem, 1.9rem + 2vw, 3.125rem)',
			'--fs-900'                           => 'clamp(3.3333rem, 2.6rem + 3vw, 5rem)',
			'--lh-normal'                        => '1.55',
			'--lh-50'                            => '1.4',
			'--lh-100'                           => '1.55',
			'--lh-200'                           => '1.5',
			'--lh-400'                           => '1.3',
			'--lh-500'                           => '1.25',
			'--lh-600'                           => '1.2',
			'--lh-700'                           => '1.15',
			'--lh-850'                           => '1.1',
			'--lh-875'                           => '1.05',
			'--lh-900'                           => '1',
			// Health's real brand palette (idh-gooseberry.css, 2026-08-25),
			// renamed from its source file's --idh-* namespace to --col- so
			// it follows the same naming convention as every other token
			// here (generate-theme-json.js's tokens.startsWith('col-') filter
			// expects this prefix, though note that script currently only
			// reads src/sass/theme/_tokens.scss, not this file — the prefix
			// match alone doesn't make these appear via that script yet).
			// neutral/white/black overlapped with already-existing --col-*
			// slugs above; the real values from this file replaced the
			// idtravel-cloned placeholders there rather than being
			// duplicated under new names — see the comments beside
			// --col-black and --col-neutral-* above. Source file has a typo
			// on blueberry-800 (`---idh-blueberry-800`, three leading
			// dashes, breaking the 050-1000 sequence) - corrected here;
			// its value (#008292) is exactly the colour already in use for
			// the header logo (header-health.php), confirming it's the
			// intended shade, not a different one under a mistyped name.
			'--col-gooseberry-050'               => '#faffe6',
			'--col-gooseberry-100'               => '#f6ffd2',
			'--col-gooseberry-200'               => '#f2ffbb',
			'--col-gooseberry-300'               => '#ebff99',
			'--col-gooseberry-400'               => '#e0ff6e',
			'--col-gooseberry'                   => '#d4ff33',
			'--col-gooseberry-550'               => '#caf728',
			'--col-gooseberry-600'               => '#c0ef1c',
			'--col-gooseberry-650'               => '#b0e208',
			'--col-gooseberry-700'               => '#99ca00',
			'--col-gooseberry-750'               => '#83b000',
			'--col-gooseberry-800'               => '#699100',
			'--col-gooseberry-850'               => '#5a7f00',
			'--col-gooseberry-900'               => '#476700',
			'--col-gooseberry-1000'              => '#395400',
			'--col-spearmint-050'                => '#f4fff9',
			'--col-spearmint-100'                => '#edfff3',
			'--col-spearmint-200'                => '#e1ffef',
			'--col-spearmint-300'                => '#ceffe5',
			'--col-spearmint-400'                => '#b9fed9',
			'--col-spearmint'                    => '#9cf9c8',
			'--col-spearmint-550'                => '#7ff4ba',
			'--col-spearmint-600'                => '#65e5ab',
			'--col-spearmint-650'                => '#50d69c',
			'--col-spearmint-700'                => '#2aba86',
			'--col-spearmint-750'                => '#1c9975',
			'--col-spearmint-800'                => '#007259',
			'--col-spearmint-850'                => '#005d4d',
			'--col-spearmint-900'                => '#004a41',
			'--col-spearmint-1000'               => '#00413a',
			'--col-blueberry-050'                => '#f5ffff',
			'--col-blueberry-100'                => '#e7fbfd',
			'--col-blueberry-200'                => '#def9fc',
			'--col-blueberry-300'                => '#d5f7fb',
			'--col-blueberry-400'                => '#c3f4fa',
			'--col-blueberry'                    => '#b1f0f8',
			'--col-blueberry-550'                => '#9ae5ef',
			'--col-blueberry-600'                => '#83dbe6',
			'--col-blueberry-650'                => '#68cdd9',
			'--col-blueberry-700'                => '#2fadbd',
			'--col-blueberry-750'                => '#0d96a7',
			'--col-blueberry-800'                => '#008292',
			'--col-blueberry-850'                => '#006b78',
			'--col-blueberry-900'                => '#005761',
			'--col-blueberry-1000'               => '#00464e',
			'--col-strawberry-050'               => '#fffbff',
			'--col-strawberry-100'               => '#fff7ff',
			'--col-strawberry-200'               => '#ffeaff',
			'--col-strawberry-300'               => '#ffe1ff',
			'--col-strawberry-400'               => '#ffd9ff',
			'--col-strawberry'                   => '#fecdff',
			'--col-strawberry-550'               => '#f9b8fe',
			'--col-strawberry-600'               => '#f49afc',
			'--col-strawberry-650'               => '#ed84f6',
			'--col-strawberry-700'               => '#dd5ce7',
			'--col-strawberry-750'               => '#c739d2',
			'--col-strawberry-800'               => '#b720bf',
			'--col-strawberry-850'               => '#a300ac',
			'--col-strawberry-900'               => '#84008c',
			'--col-strawberry-1000'              => '#64006a',
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

	// !important on every declaration: something in this install still
	// prints its own :root{ --wp--preset--color--*: ... } block (theme.json's
	// global styles - cb-theme.php's `remove_action( 'wp_enqueue_scripts',
	// 'wp_enqueue_global_styles' )` does NOT fully suppress it, confirmed by
	// inspecting the rendered page directly) regardless of this function's
	// wp_head priority - a plain cascade-order fix (printing later) doesn't
	// reliably win. This is exactly why the --wp--preset--color--lime-900/
	// 1000 fix had zero effect on has-lime-900/1000-color utility classes:
	// --col-lime-* (not a --wp--preset name) was never re-declared by that
	// other block, so those were never clobbered, but the --wp--preset-*
	// ones always were. !important resolves the tie unconditionally,
	// independent of print order.
	$declarations = array();
	foreach ( $table[ $site ] as $var => $value ) {
		$declarations[] = esc_attr( $var ) . ': ' . $value . ' !important;';
	}

	printf(
		'<style id="cb-site-tokens">:root{%s}</style>',
		implode( ' ', $declarations )
	);
}
add_action( 'wp_head', 'cb_output_site_token_overrides', 100 );
add_action( 'admin_head', 'cb_output_site_token_overrides', 100 );

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
 * changes what they render as.
 *
 * IMPORTANT: `update_with()` REPLACES the settings.color.palette array
 * wholesale — it does NOT merge palette entries by slug (an earlier version
 * of this doc comment claimed it did; that was wrong and meant this filter
 * silently wiped out 48 of the theme's 59 declared colours, on every page
 * load, site-wide, since only the ~11 slugs below survived). Fixed by
 * reading the full base palette from theme.json and only overriding the
 * per-site-varying slugs' values within it, so the rest of the palette
 * (neutral/purple/indigo/raspberry scales etc.) is preserved.
 *
 * @param WP_Theme_JSON_Data $theme_json Theme JSON data object.
 * @return WP_Theme_JSON_Data
 */
function cb_filter_editor_theme_json( $theme_json ) {
	$site = cb_site_template_suffix();

	$palettes = array(
		'identity' => array(
			array(
				'slug'  => 'primary-black',
				'name'  => 'Primary Black',
				'color' => '#0D0D0C',
			),
			array(
				'slug'  => 'ink',
				'name'  => 'Ink',
				'color' => '#0D0D0C',
			),
			array(
				'slug'  => 'lime-900',
				'name'  => 'Lime 900',
				'color' => '#4c8200',
			),
			array(
				'slug'  => 'lime-700',
				'name'  => 'Lime 700',
				'color' => '#6bb600',
			),
			array(
				'slug'  => 'lime-200',
				'name'  => 'Lime 200',
				'color' => '#ddfbb2',
			),
			array(
				'slug'  => 'green-400',
				'name'  => 'Green 400',
				'color' => '#B8FF52',
			),
			array(
				'slug'  => 'lime-600',
				'name'  => 'Lime 600',
				'color' => '#94dd2c',
			),
			array(
				'slug'  => 'lime-1000',
				'name'  => 'Lime 1000',
				'color' => '#3d6900',
			),
			array(
				'slug'  => 'lime-1100',
				'name'  => 'Lime 1100',
				'color' => '#345a00',
			),
			array(
				'slug'  => 'raspberry',
				'name'  => 'Raspberry',
				'color' => '#4c8200',
			),
			array(
				'slug'  => 'raspberry-450',
				'name'  => 'Raspberry 450',
				'color' => '#5e9f00',
			),
			array(
				'slug'  => 'neutral-100',
				'name'  => 'Neutral 100',
				'color' => '#f8f7f0',
			),
			array(
				'slug'  => 'neutral-200',
				'name'  => 'Neutral 200',
				'color' => '#ebe9e1',
			),
			array(
				'slug'  => 'neutral-300',
				'name'  => 'Neutral 300',
				'color' => '#dddcd2',
			),
			array(
				'slug'  => 'neutral-400',
				'name'  => 'Neutral 400',
				'color' => '#c9c7bc',
			),
			array(
				'slug'  => 'neutral-500',
				'name'  => 'Neutral 500',
				'color' => '#aeada1',
			),
			array(
				'slug'  => 'neutral-600',
				'name'  => 'Neutral 600',
				'color' => '#939287',
			),
			array(
				'slug'  => 'neutral-700',
				'name'  => 'Neutral 700',
				'color' => '#77766c',
			),
			array(
				'slug'  => 'neutral-800',
				'name'  => 'Neutral 800',
				'color' => '#5e5d55',
			),
			array(
				'slug'  => 'neutral-900',
				'name'  => 'Neutral 900',
				'color' => '#505049',
			),
			array(
				'slug'  => 'neutral-1000',
				'name'  => 'Neutral 1000',
				'color' => '#363531',
			),
			array(
				'slug'  => 'neutral-1100',
				'name'  => 'Neutral 1100',
				'color' => '#22211e',
			),
			array(
				'slug'  => 'white',
				'name'  => 'White',
				'color' => '#fff',
			),
		),
		'coda'     => array(
			array(
				'slug'  => 'primary-black',
				'name'  => 'Primary Black',
				'color' => '#0d0d0c',
			),
			array(
				'slug'  => 'ink',
				'name'  => 'Ink',
				'color' => '#0d0d0c',
			),
			array(
				'slug'  => 'lime-900',
				'name'  => 'Lime 900',
				'color' => '#4c8200',
			),
			array(
				'slug'  => 'lime-700',
				'name'  => 'Lime 700',
				'color' => '#6bb600',
			),
			array(
				'slug'  => 'lime-200',
				'name'  => 'Lime 200',
				'color' => '#ddfbb2',
			),
			array(
				'slug'  => 'green-400',
				'name'  => 'Green 400',
				'color' => '#B8FF52',
			),
			array(
				'slug'  => 'lime-600',
				'name'  => 'Lime 600',
				'color' => '#94dd2c',
			),
			array(
				'slug'  => 'lime-1000',
				'name'  => 'Lime 1000',
				'color' => '#3d6900',
			),
			array(
				'slug'  => 'lime-1100',
				'name'  => 'Lime 1100',
				'color' => '#345a00',
			),
			array(
				'slug'  => 'raspberry',
				'name'  => 'Raspberry',
				'color' => '#4c8200',
			),
			array(
				'slug'  => 'raspberry-450',
				'name'  => 'Raspberry 450',
				'color' => '#5e9f00',
			),
			array(
				'slug'  => 'neutral-100',
				'name'  => 'Neutral 100',
				'color' => '#f8f7f0',
			),
			array(
				'slug'  => 'neutral-200',
				'name'  => 'Neutral 200',
				'color' => '#ebe9e1',
			),
			array(
				'slug'  => 'neutral-300',
				'name'  => 'Neutral 300',
				'color' => '#dddcd2',
			),
			array(
				'slug'  => 'neutral-400',
				'name'  => 'Neutral 400',
				'color' => '#c9c7bc',
			),
			array(
				'slug'  => 'neutral-500',
				'name'  => 'Neutral 500',
				'color' => '#aeada1',
			),
			array(
				'slug'  => 'neutral-600',
				'name'  => 'Neutral 600',
				'color' => '#939287',
			),
			array(
				'slug'  => 'neutral-700',
				'name'  => 'Neutral 700',
				'color' => '#77766c',
			),
			array(
				'slug'  => 'neutral-800',
				'name'  => 'Neutral 800',
				'color' => '#5e5d55',
			),
			array(
				'slug'  => 'neutral-900',
				'name'  => 'Neutral 900',
				'color' => '#505049',
			),
			array(
				'slug'  => 'neutral-1000',
				'name'  => 'Neutral 1000',
				'color' => '#363531',
			),
			array(
				'slug'  => 'neutral-1100',
				'name'  => 'Neutral 1100',
				'color' => '#22211e',
			),
			array(
				'slug'  => 'white',
				'name'  => 'White',
				'color' => '#fff',
			),
		),
		'idtravel' => array(
			array(
				'slug'  => 'primary-black',
				'name'  => 'Primary Black',
				'color' => '#110d25',
			),
			array(
				'slug'  => 'ink',
				'name'  => 'Ink',
				'color' => '#110d25',
			),
			array(
				'slug'  => 'lime-900',
				'name'  => 'Lime 900',
				'color' => '#900720',
			),
			array(
				'slug'  => 'lime-700',
				'name'  => 'Lime 700',
				'color' => '#cc1939',
			),
			array(
				'slug'  => 'lime-200',
				'name'  => 'Lime 200',
				'color' => '#ffb8c6',
			),
			array(
				'slug'  => 'green-400',
				'name'  => 'Green 400',
				'color' => '#f66f88',
			),
			array(
				'slug'  => 'lime-600',
				'name'  => 'Lime 600',
				'color' => '#cc1939',
			),
			array(
				'slug'  => 'lime-1000',
				'name'  => 'Lime 1000',
				'color' => '#7b0319',
			),
			array(
				'slug'  => 'lime-1100',
				'name'  => 'Lime 1100',
				'color' => '#7b0319',
			),
			array(
				'slug'  => 'raspberry',
				'name'  => 'Raspberry',
				'color' => '#e32447',
			),
			array(
				'slug'  => 'raspberry-450',
				'name'  => 'Raspberry 450',
				'color' => '#ec4a67',
			),
			array(
				'slug'  => 'neutral-400',
				'name'  => 'Neutral 400',
				'color' => '#b6b3c3',
			),
			array(
				'slug'  => 'neutral-700',
				'name'  => 'Neutral 700',
				'color' => '#55506b',
			),
			array(
				'slug'  => 'neutral-800',
				'name'  => 'Neutral 800',
				'color' => '#3b3652',
			),
			array(
				'slug'  => 'neutral-1100',
				'name'  => 'Neutral 1100',
				'color' => '#040013',
			),
			array(
				'slug'  => 'white',
				'name'  => 'White',
				'color' => '#ffffff',
			),
		),
		// health is a new site with no legacy saved content, so unlike
		// identity/coda/idtravel above it doesn't need to borrow their
		// slug names (lime-900, primary-black, raspberry, etc. only exist
		// because 3 already-live sites' saved post_content still reference
		// those exact has-{slug}-color classes from before the 2026-07
		// theme consolidation — see CB-THEME-CONSOLIDATION-PLAN.md). health
		// gets its own real palette (idh-gooseberry.css, 2026-08-25) under
		// its own names, and — see the `'health' === $site` branch below —
		// isn't merged onto the shared base palette at all, so its editor
		// swatch list is just these colours, not all ~59 the other 3
		// sites' shared colours.
		'health'   => array(
			array(
				'slug'  => 'gooseberry-050',
				'name'  => 'Gooseberry 050',
				'color' => '#faffe6',
			),
			array(
				'slug'  => 'gooseberry-100',
				'name'  => 'Gooseberry 100',
				'color' => '#f6ffd2',
			),
			array(
				'slug'  => 'gooseberry-200',
				'name'  => 'Gooseberry 200',
				'color' => '#f2ffbb',
			),
			array(
				'slug'  => 'gooseberry-300',
				'name'  => 'Gooseberry 300',
				'color' => '#ebff99',
			),
			array(
				'slug'  => 'gooseberry-400',
				'name'  => 'Gooseberry 400',
				'color' => '#e0ff6e',
			),
			array(
				'slug'  => 'gooseberry',
				'name'  => 'Gooseberry',
				'color' => '#d4ff33',
			),
			array(
				'slug'  => 'gooseberry-550',
				'name'  => 'Gooseberry 550',
				'color' => '#caf728',
			),
			array(
				'slug'  => 'gooseberry-600',
				'name'  => 'Gooseberry 600',
				'color' => '#c0ef1c',
			),
			array(
				'slug'  => 'gooseberry-650',
				'name'  => 'Gooseberry 650',
				'color' => '#b0e208',
			),
			array(
				'slug'  => 'gooseberry-700',
				'name'  => 'Gooseberry 700',
				'color' => '#99ca00',
			),
			array(
				'slug'  => 'gooseberry-750',
				'name'  => 'Gooseberry 750',
				'color' => '#83b000',
			),
			array(
				'slug'  => 'gooseberry-800',
				'name'  => 'Gooseberry 800',
				'color' => '#699100',
			),
			array(
				'slug'  => 'gooseberry-850',
				'name'  => 'Gooseberry 850',
				'color' => '#5a7f00',
			),
			array(
				'slug'  => 'gooseberry-900',
				'name'  => 'Gooseberry 900',
				'color' => '#476700',
			),
			array(
				'slug'  => 'gooseberry-1000',
				'name'  => 'Gooseberry 1000',
				'color' => '#395400',
			),
			array(
				'slug'  => 'spearmint-050',
				'name'  => 'Spearmint 050',
				'color' => '#f4fff9',
			),
			array(
				'slug'  => 'spearmint-100',
				'name'  => 'Spearmint 100',
				'color' => '#edfff3',
			),
			array(
				'slug'  => 'spearmint-200',
				'name'  => 'Spearmint 200',
				'color' => '#e1ffef',
			),
			array(
				'slug'  => 'spearmint-300',
				'name'  => 'Spearmint 300',
				'color' => '#ceffe5',
			),
			array(
				'slug'  => 'spearmint-400',
				'name'  => 'Spearmint 400',
				'color' => '#b9fed9',
			),
			array(
				'slug'  => 'spearmint',
				'name'  => 'Spearmint',
				'color' => '#9cf9c8',
			),
			array(
				'slug'  => 'spearmint-550',
				'name'  => 'Spearmint 550',
				'color' => '#7ff4ba',
			),
			array(
				'slug'  => 'spearmint-600',
				'name'  => 'Spearmint 600',
				'color' => '#65e5ab',
			),
			array(
				'slug'  => 'spearmint-650',
				'name'  => 'Spearmint 650',
				'color' => '#50d69c',
			),
			array(
				'slug'  => 'spearmint-700',
				'name'  => 'Spearmint 700',
				'color' => '#2aba86',
			),
			array(
				'slug'  => 'spearmint-750',
				'name'  => 'Spearmint 750',
				'color' => '#1c9975',
			),
			array(
				'slug'  => 'spearmint-800',
				'name'  => 'Spearmint 800',
				'color' => '#007259',
			),
			array(
				'slug'  => 'spearmint-850',
				'name'  => 'Spearmint 850',
				'color' => '#005d4d',
			),
			array(
				'slug'  => 'spearmint-900',
				'name'  => 'Spearmint 900',
				'color' => '#004a41',
			),
			array(
				'slug'  => 'spearmint-1000',
				'name'  => 'Spearmint 1000',
				'color' => '#00413a',
			),
			array(
				'slug'  => 'blueberry-050',
				'name'  => 'Blueberry 050',
				'color' => '#f5ffff',
			),
			array(
				'slug'  => 'blueberry-100',
				'name'  => 'Blueberry 100',
				'color' => '#e7fbfd',
			),
			array(
				'slug'  => 'blueberry-200',
				'name'  => 'Blueberry 200',
				'color' => '#def9fc',
			),
			array(
				'slug'  => 'blueberry-300',
				'name'  => 'Blueberry 300',
				'color' => '#d5f7fb',
			),
			array(
				'slug'  => 'blueberry-400',
				'name'  => 'Blueberry 400',
				'color' => '#c3f4fa',
			),
			array(
				'slug'  => 'blueberry',
				'name'  => 'Blueberry',
				'color' => '#b1f0f8',
			),
			array(
				'slug'  => 'blueberry-550',
				'name'  => 'Blueberry 550',
				'color' => '#9ae5ef',
			),
			array(
				'slug'  => 'blueberry-600',
				'name'  => 'Blueberry 600',
				'color' => '#83dbe6',
			),
			array(
				'slug'  => 'blueberry-650',
				'name'  => 'Blueberry 650',
				'color' => '#68cdd9',
			),
			array(
				'slug'  => 'blueberry-700',
				'name'  => 'Blueberry 700',
				'color' => '#2fadbd',
			),
			array(
				'slug'  => 'blueberry-750',
				'name'  => 'Blueberry 750',
				'color' => '#0d96a7',
			),
			array(
				'slug'  => 'blueberry-800',
				'name'  => 'Blueberry 800',
				'color' => '#008292',
			),
			array(
				'slug'  => 'blueberry-850',
				'name'  => 'Blueberry 850',
				'color' => '#006b78',
			),
			array(
				'slug'  => 'blueberry-900',
				'name'  => 'Blueberry 900',
				'color' => '#005761',
			),
			array(
				'slug'  => 'blueberry-1000',
				'name'  => 'Blueberry 1000',
				'color' => '#00464e',
			),
			array(
				'slug'  => 'strawberry-050',
				'name'  => 'Strawberry 050',
				'color' => '#fffbff',
			),
			array(
				'slug'  => 'strawberry-100',
				'name'  => 'Strawberry 100',
				'color' => '#fff7ff',
			),
			array(
				'slug'  => 'strawberry-200',
				'name'  => 'Strawberry 200',
				'color' => '#ffeaff',
			),
			array(
				'slug'  => 'strawberry-300',
				'name'  => 'Strawberry 300',
				'color' => '#ffe1ff',
			),
			array(
				'slug'  => 'strawberry-400',
				'name'  => 'Strawberry 400',
				'color' => '#ffd9ff',
			),
			array(
				'slug'  => 'strawberry',
				'name'  => 'Strawberry',
				'color' => '#fecdff',
			),
			array(
				'slug'  => 'strawberry-550',
				'name'  => 'Strawberry 550',
				'color' => '#f9b8fe',
			),
			array(
				'slug'  => 'strawberry-600',
				'name'  => 'Strawberry 600',
				'color' => '#f49afc',
			),
			array(
				'slug'  => 'strawberry-650',
				'name'  => 'Strawberry 650',
				'color' => '#ed84f6',
			),
			array(
				'slug'  => 'strawberry-700',
				'name'  => 'Strawberry 700',
				'color' => '#dd5ce7',
			),
			array(
				'slug'  => 'strawberry-750',
				'name'  => 'Strawberry 750',
				'color' => '#c739d2',
			),
			array(
				'slug'  => 'strawberry-800',
				'name'  => 'Strawberry 800',
				'color' => '#b720bf',
			),
			array(
				'slug'  => 'strawberry-850',
				'name'  => 'Strawberry 850',
				'color' => '#a300ac',
			),
			array(
				'slug'  => 'strawberry-900',
				'name'  => 'Strawberry 900',
				'color' => '#84008c',
			),
			array(
				'slug'  => 'strawberry-1000',
				'name'  => 'Strawberry 1000',
				'color' => '#64006a',
			),
			array(
				'slug'  => 'white',
				'name'  => 'White',
				'color' => '#ffffff',
			),
			array(
				'slug'  => 'neutral-050',
				'name'  => 'Neutral 050',
				'color' => '#fdfcf4',
			),
			array(
				'slug'  => 'neutral-100',
				'name'  => 'Neutral 100',
				'color' => '#f5f4eb',
			),
			array(
				'slug'  => 'neutral-200',
				'name'  => 'Neutral 200',
				'color' => '#eae9df',
			),
			array(
				'slug'  => 'neutral-300',
				'name'  => 'Neutral 300',
				'color' => '#dddcd2',
			),
			array(
				'slug'  => 'neutral-400',
				'name'  => 'Neutral 400',
				'color' => '#c9c7bc',
			),
			array(
				'slug'  => 'neutral-500',
				'name'  => 'Neutral 500',
				'color' => '#aeada1',
			),
			array(
				'slug'  => 'neutral-600',
				'name'  => 'Neutral 600',
				'color' => '#939287',
			),
			array(
				'slug'  => 'neutral-700',
				'name'  => 'Neutral 700',
				'color' => '#77766c',
			),
			array(
				'slug'  => 'neutral-750',
				'name'  => 'Neutral 750',
				'color' => '#6b6962',
			),
			array(
				'slug'  => 'neutral-800',
				'name'  => 'Neutral 800',
				'color' => '#5e5d55',
			),
			array(
				'slug'  => 'neutral-900',
				'name'  => 'Neutral 900',
				'color' => '#505049',
			),
			array(
				'slug'  => 'neutral-1000',
				'name'  => 'Neutral 1000',
				'color' => '#353431',
			),
			array(
				'slug'  => 'neutral-1050',
				'name'  => 'Neutral 1050',
				'color' => '#2b2a28',
			),
			array(
				'slug'  => 'neutral-1100',
				'name'  => 'Neutral 1100',
				'color' => '#22211e',
			),
			array(
				'slug'  => 'black',
				'name'  => 'Black',
				'color' => '#0d0d0c',
			),
		),
	);

	$font_sizes = array(
		// identity now uses idtravel's font-size scale (deliberate 2026-07-14
		// client decision - see cb_get_site_tokens_table() above), so its
		// editor presets match idtravel's rather than defining its own.
		'identity' => array(
			array(
				'slug' => 'small',
				'name' => 'Small',
				'size' => 'clamp(1.2222rem, 1.1rem + 0.6vw, 1.375rem)',
			),
			array(
				'slug' => 'medium',
				'name' => 'Medium',
				'size' => 'clamp(1.4444rem, 1.25rem + 0.9vw, 1.75rem)',
			),
			array(
				'slug' => 'large',
				'name' => 'Large',
				'size' => 'clamp(2.3333rem, 1.9rem + 2vw, 3.125rem)',
			),
			array(
				'slug' => 'x-large',
				'name' => 'X-Large',
				'size' => 'clamp(3.3333rem, 2.6rem + 3vw, 5rem)',
			),
		),
		'coda'     => array(
			array(
				'slug' => 'small',
				'name' => 'Small',
				'size' => 'clamp(1rem, 0.95rem + 0.5vw, 1.25rem)',
			),
			array(
				'slug' => 'medium',
				'name' => 'Medium',
				'size' => 'clamp(1.1875rem, 0.9rem + 1vw, 1.751rem)',
			),
			array(
				'slug' => 'large',
				'name' => 'Large',
				'size' => 'clamp(1.875rem, 0.95rem + 1.9vw, 3.125rem)',
			),
			array(
				'slug' => 'x-large',
				'name' => 'X-Large',
				'size' => 'clamp(2.5rem, 1rem + 3vw, 5rem)',
			),
		),
		'idtravel' => array(
			array(
				'slug' => 'small',
				'name' => 'Small',
				'size' => 'clamp(1.2222rem, 1.1rem + 0.6vw, 1.375rem)',
			),
			array(
				'slug' => 'medium',
				'name' => 'Medium',
				'size' => 'clamp(1.4444rem, 1.25rem + 0.9vw, 1.75rem)',
			),
			array(
				'slug' => 'large',
				'name' => 'Large',
				'size' => 'clamp(2.3333rem, 1.9rem + 2vw, 3.125rem)',
			),
			array(
				'slug' => 'x-large',
				'name' => 'X-Large',
				'size' => 'clamp(3.3333rem, 2.6rem + 3vw, 5rem)',
			),
		),
		// health uses idtravel's font-size scale too, same starting-point
		// reasoning as above.
		'health'   => array(
			array(
				'slug' => 'small',
				'name' => 'Small',
				'size' => 'clamp(1.2222rem, 1.1rem + 0.6vw, 1.375rem)',
			),
			array(
				'slug' => 'medium',
				'name' => 'Medium',
				'size' => 'clamp(1.4444rem, 1.25rem + 0.9vw, 1.75rem)',
			),
			array(
				'slug' => 'large',
				'name' => 'Large',
				'size' => 'clamp(2.3333rem, 1.9rem + 2vw, 3.125rem)',
			),
			array(
				'slug' => 'x-large',
				'name' => 'X-Large',
				'size' => 'clamp(3.3333rem, 2.6rem + 3vw, 5rem)',
			),
		),
	);

	if ( ! isset( $palettes[ $site ] ) ) {
		return $theme_json;
	}

	// `update_with()` replaces these settings arrays wholesale, so start from
	// the theme's full base declarations and only overwrite the per-site
	// slugs — everything else in the base palette/font-size scale survives.
	// This is what keeps identity/coda/idtravel's borrowed slugs (lime-900,
	// primary-black, etc.) resolvable for their existing saved content.
	//
	// health has no such content and no borrowed slugs to preserve, so it
	// skips the merge entirely and uses its own palette as the full editor
	// list — not health's ~15 colours bolted onto the other 3 sites' shared
	// ~59, which is what merging would otherwise produce (2026-08-25).
	$base         = wp_json_file_decode( get_stylesheet_directory() . '/theme.json', array( 'associative' => true ) );
	$base_palette = $base['settings']['color']['palette'] ?? array();
	$base_sizes   = $base['settings']['typography']['fontSizes'] ?? array();

	if ( 'health' === $site ) {
		$merged_palette = $palettes[ $site ];
	} else {
		$merged_palette = cb_merge_theme_json_list_by_slug( $base_palette, $palettes[ $site ] );
	}
	$merged_sizes = cb_merge_theme_json_list_by_slug( $base_sizes, $font_sizes[ $site ] );

	return $theme_json->update_with(
		array(
			'version'  => 3,
			'settings' => array(
				'color'      => array(
					'palette' => $merged_palette,
				),
				'typography' => array(
					'fontSizes' => $merged_sizes,
				),
			),
		)
	);
}

/**
 * Overlays `$overrides` onto `$base` by matching `slug`, preserving every
 * base entry whose slug isn't being overridden and appending any override
 * whose slug doesn't already exist in the base list.
 *
 * @param array $base      Full list of theme.json entries (palette or fontSizes).
 * @param array $overrides Per-site entries that should replace matching slugs.
 * @return array
 */
function cb_merge_theme_json_list_by_slug( $base, $overrides ) {
	$by_slug = array();
	foreach ( $base as $entry ) {
		$by_slug[ $entry['slug'] ] = $entry;
	}
	foreach ( $overrides as $entry ) {
		$by_slug[ $entry['slug'] ] = $entry;
	}
	return array_values( $by_slug );
}
add_filter( 'wp_theme_json_data_theme', 'cb_filter_editor_theme_json' );
