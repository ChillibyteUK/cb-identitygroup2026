# Session Handoff: cb-identitygroup2026

Picks up a theme-consolidation project (merging `cb-identity2025`, `cb-coda2026`,
`cb-idtravel2026` into this shared theme) that was built in a cloud sandbox with
no WordPress instance to test against. Everything below is either committed on
`claude/theme-review-requirements-lzbfse` in `chillibyteuk/cb-identitygroup2026`,
or flagged here as not yet done. **This branch is already on GitHub** — clone
directly, no tarball needed:

```
git clone https://github.com/ChillibyteUK/cb-identitygroup2026.git
cd cb-identitygroup2026
git checkout claude/theme-review-requirements-lzbfse
```

Related repos also on GitHub, same branch name, each holding a fact-checked
revision of the original consolidation plan at their repo root
(`CB-THEME-CONSOLIDATION-PLAN.md` — read that first, it's the actual spec this
work follows):
- `chillibyteuk/cb-identity2025`
- `chillibyteuk/cb-idtravel2026`
- `chillibyteuk/cb-coda2026`

## What's done

**Phase A** (fork + merge): Forked idtravel as the base (it and coda share the
same classic-ACF block registration; identity used block.json). Copied 9 blocks
from coda that idtravel lacked. Converted identity's 8 one-off blocks from
block.json/`register_block_type()` to classic `acf_register_block_type()`.
Extracted `cb_find_hero_subtitle()` (was duplicated 4x) to `inc/cb-utility.php`.

**Phase B** (config consolidation): Added config fields to 6 shared blocks so
they can absorb one-off variants — `cb-details` (pre_title, render_style),
`cb-logo-slider` (logo_source), `cb-featured-work` (taxonomy_filter, hero_mode),
`cb-latest-insights` (taxonomy_filter), `cb-stats` (show_hero), `cb-full-video`
(full_width, hero_mode). New block `cb-child-page-nav` consolidates three
near-identical idtravel nav blocks behind a `parent_slug` field.

**Theme switcher** (`cb_site` field on Site-Wide Settings, identity/coda/idtravel):
- `inc/cb-site-tokens.php` — per-site colour/type-size/line-height token table,
  output as `:root` CSS overrides on `wp_head`/`admin_head`. Also hooks
  `wp_theme_json_data_theme` to swap the block editor's colour palette/font-size
  presets, and adds a `cb-site-{slug}` body class for structural (not just
  colour-value) per-site CSS differences — see `_header-site-overrides.scss`.
- `header-identity.php`, `header-coda.php`, `footer-identity.php`,
  `footer-coda.php` — full copies of each site's real header/footer (not
  reconstructed). Selected via `get_header( cb_site_template_suffix() )` /
  `get_footer(...)` in every template file. idtravel has no
  `header-idtravel.php`/`footer-idtravel.php` — it falls through to the
  default `header.php`/`footer.php`, which is already idtravel's content from
  the fork.
- **All blocks are registered unconditionally on every site** — `cb_site` only
  drives colours/logos/header/footer, never which blocks exist. (Earlier in
  the session blocks were gated per-site; this was wrong and has been reverted
  — see commit `6c94ecd`.)

## Bugs found and fixed along the way

- **37 of 47 blocks** had ACF field-group `location` values that didn't match
  their actual registered block name (e.g. field group targeted `acf/cb-faq`,
  block registered as `acf/cb_faq`) — inherited from an incomplete
  hyphen-to-underscore rename in idtravel/coda's history. Fixed all 37; this
  is why custom-fields panels weren't showing for most blocks in wp-admin.
- `theme.json` (forked from idtravel) never defined `lime-900`, `lime-1000`,
  `lime-1100`, or `primary-black` palette slugs at all, despite several copied
  block templates hardcoding `has-lime-900-color` etc. classes against them —
  so those elements rendered no colour whatsoever, on any site. Fixed.
- Default WordPress colour/gradient/duotone swatches were showing in the block
  editor alongside the theme's own palette — `disable-custom-colors` only
  hides the arbitrary hex picker, not the presets. Added
  `defaultPalette`/`defaultGradients`/`defaultDuotone: false` to `theme.json`.
- All three sites' header logo links had `aria-label="Identity Homepage"`
  regardless of actual site (copy-paste artifact). Fixed per site.
- `header-identity.php`/`footer-coda.php`/`footer-identity.php` reference nav
  `theme_location`s (`footer_menu_services`, `footer_menu_media`,
  `footer_menu_global`) that were never registered — idtravel's own footer
  uses a different location set entirely. Added the missing three to
  `inc/cb-theme.php`'s `register_nav_menus()`.
- `cb-decision-gate`'s SCSS never set a text colour anywhere despite clearly
  being a dark-background block (`rgba(255,255,255,0.4)` borders) — text fell
  through to Bootstrap reboot's default. Fixed. Checked every other block's
  SCSS for the same pattern; this was the only instance.
- `cb-details`'s field group (inherited from coda) had the same location
  mismatch as the systemic bug above, plus the block's `details` repeater
  sub-field is named `content` in coda vs `description` in identity — noted
  in case content migration between sites' data is ever needed.

## Known non-issues (don't re-chase these)

- **"This block contains no editable fields" in the editor** — confirmed by
  the user to be an ACF database-vs-Local-JSON sync conflict on the test
  install, not a code defect (DB takes precedence over JSON by default). Fix
  on the WordPress side: **Custom Fields → Field Groups** in wp-admin, use the
  **Sync** action on any group flagged as out of sync, or trash the stale DB
  field-group post entirely (Local JSON supplies it standalone with no DB
  record needed).

## Not done yet / explicitly out of scope this session

- **Phase C (design-token SCSS rename)**: the plan's generic token names
  (`--col-brand`, `--ff-heading`, etc.) are defined in `inc/cb-site-tokens.php`
  as a runtime override, but the actual block SCSS still references each
  site's original variable names (`--col-raspberry`, `--font-family`, etc.).
  The runtime override covers both, so switching works today — but the SCSS
  itself was never renamed to the generic scheme. Do this if/when consolidating
  onto one canonical naming convention matters more than "it works."
- **Phase D (deploy scripts, per-site builds)**: not started.
- **Two uncatalogued blocks** exist in 2+ source themes but were never
  classified in the plan: `cb-hero-prop-cta` (coda + idtravel) and
  `cb-service-grid` (idtravel + identity). Decide: shared-as-is or one-off.
- **Deprecated-but-still-registered blocks**: `cb-video-hero`, `cb-plain-hero`,
  `cb-cta-hero` (→ replaced by `cb-full-video`/`cb-image-feature-overlay`
  config fields) and `cb-lined-title` (→ `cb-signpost-header`) are still fully
  registered and functional. The plan calls for an admin deprecation notice on
  their templates for one release cycle before removal — not implemented.
- **Coda's own copy is missing `cb-contact-page`, `cb-careers-page`, and
  `cb-signpost-header`** (all exist in idtravel/identity, confirmed absent in
  coda's actual repo) — irrelevant to this shared theme, but means coda's
  *own* site can't use those blocks until someone adds them there directly.
- **CSS build pipeline**: this sandbox could run `sass` and `clean-css-cli`
  via `npx`, but not the project's custom local PostCSS plugin
  (`postcss-understrap-palette-generator`) or `autoprefixer` — no access to
  install them here. All CSS fixes in this session were therefore hand-patched
  into the committed `child-theme.css`/`.min.css` (verified as pure additions
  via diff, not full recompiles, specifically to avoid silently stripping
  vendor prefixes theme-wide). **Run the full `npm run css` pipeline locally
  once** to produce a clean, fully-prefixed build — it should produce the same
  rules, just properly autoprefixed and passed through the palette generator.

## How to verify locally

1. `npm install && npm run css` — full compile/postcss/minify pipeline.
2. Point a WordPress install at this theme, activate it, run ACF's Sync if
   prompted.
3. Site-Wide Settings → General tab → **Site** field → switch between
   identity/coda/idtravel and confirm: header/footer logo changes, nav
   background/text colour changes (coda is light-nav/dark-text; identity and
   idtravel are both dark-nav/light-text), block editor colour palette changes.
4. Confirm every block is available in the inserter regardless of the Site
   value (nothing should be conditionally hidden).
