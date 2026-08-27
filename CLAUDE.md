# CLAUDE.md — Session Handoff (2026-08-27)

## Context

This dev environment (`coda.local`) is misleadingly named — its `cb_site` ACF option is actually set to **`health`** (confirmed via `wp option get options_cb_site`). Treat it as the Health site. All work this session has been **Health-only** unless stated otherwise — the user has been extremely strict about this: **do not touch identity/coda/idtravel styling unless explicitly asked.**

Health-context CSS scoping convention used throughout: `.cb-site-health <selector>` in SCSS, `'health' === cb_site_template_suffix()` in PHP.

## Environment gotchas (read before doing anything else)

1. **Node**: system default is v18.19.1 (`/usr/bin/node`), incompatible with this project's pnpm-installed packages. Always prefix commands with:
   ```bash
   export PATH="$HOME/.nvm/versions/node/v22.23.2/bin:$PATH"
   ```
2. **Package manager**: standardized on **pnpm** this session (see "Toolchain changes" below). Use `pnpm install`, not `npm install`. If it complains about a non-interactive purge confirmation, prefix with `CI=true`.
3. **`node_modules` / `.git` keep vanishing.** Root cause found: WordPress's own `wp-content/upgrade-temp-backup/themes/cb-identitygroup2026/` mechanism (WP 6.3+) is firing repeatedly on this install — something is triggering theme updates/reinstalls, which wipes the live directory (including `.git`, `node_modules`, file ownership) and replaces it with a fresh copy. **Recommend checking wp-admin → Updates and disabling theme auto-updates**, or finding what's triggering it. This is not caused by Claude Code sessions.
   - **Git recovery** (safe — `git reset` here never touches working-tree files, only the index/HEAD):
     ```bash
     git config --global --add safe.directory /var/www/coda/wp-content/themes/cb-identitygroup2026  # if "dubious ownership" error
     git init
     git remote add origin git@github.com:ChillibyteUK/cb-identitygroup2026.git
     git fetch origin main
     git symbolic-ref HEAD refs/heads/main
     git reset origin/main
     ```
   - **node_modules recovery**: `pnpm install` (with the Node 22 PATH export above).
4. **File permissions** sometimes revert to `www-data:www-data` (unclear if same root cause as #3). Claude has no sudo password — if writes start failing with `EACCES`, ask the user to run:
   ```bash
   sudo chmod -R g+w /var/www/coda/wp-content/themes/cb-identitygroup2026
   ```
5. **Browser tool**: `computer` (screenshot) and `get_page_text` currently require per-action approval on this site and are unavailable to Claude. Use `javascript_tool` (`getComputedStyle`, DOM queries, CSSOM rule matching) for verification instead — it works fine and has been the main verification method this session. Occasionally times out on first call; retry once.
6. **Always run `npm run css`** after any `.scss` edit (uses local `sass`/`postcss`/`cleancss` via `npm-run-all`) — nothing auto-compiles unless the user's own `npm run watch` happens to be running in another terminal.

## Toolchain changes made this session

- Removed `package-lock.json`; standardized on pnpm. `package.json` now has `"packageManager": "pnpm@11.24.0"`.
- Fixed `pnpm-workspace.yaml` — it kept self-corrupting/re-adding a stub every install. Root cause: this pnpm version (11.24.0) expects `allowBuilds: { '@parcel/watcher': true }`, **not** the older `onlyBuiltDependencies` key. Do not change this back.
- `theme.json`: added `settings.color.defaultPalette: false` (global, all sites — removes WP core's default color swatches from the picker) and a new base `--col-black` token (`#0d0d0c`, matches identity/coda/health's real per-site value, **not** pure `#000000` — pure black was visibly wrong specifically in the block editor iframe, which never gets the per-site `wp_head` override). Also updated `generate-theme-json.js` to keep emitting `defaultPalette: false` on regeneration.

## Established patterns for continuing this work

- **Dark/light toggle fields** (used on `cb-about-hero`, `cb-pushthrough`, `cb-plain-hero`): ACF `button_group` field named `style`/`overlay_style`, choices `dark`/`light`, hidden for every site except health via `acf/prepare_field/key=...` filters in `inc/cb-site-tokens.php` (reuse `cb_hide_non_health_field`). PHP adds a `{block}--dark`/`{block}--light` modifier class. In CSS, whichever choice already matches the existing base/unscoped rule needs **no override** — only the other choice needs one. Check each block's PHP for whether the toggle only applies conditionally (e.g. `cb-plain-hero` only applies it when a background image is set — no image = hardcoded dark-on-white regardless of toggle).
- **Editor field hiding**: `acf/prepare_field/key=field_xxx` filters in `inc/cb-site-tokens.php`. Reusable generic helpers already exist: `cb_hide_non_health_field` (hides for everyone except health) and `cb_hide_health_pretitle_fields` (hides for health only — name is historical, reused for several unrelated fields now, e.g. `cb-image-feature-overlay`'s Overlay Image field).
- **Line/border colors**:
  - Dark lines on a light/white background: `hsl(var(--hsl-neutral-400) / 0.5)`
  - Light lines on a dark background or **photo image**: use **near-solid** `hsl(var(--hsl-white) / 0.9)`, not the diluted `hsl(var(--hsl-neutral-050) / 0.5)` — the latter doesn't read reliably against variable-contrast photo backdrops (found on `cb-about-hero`/`cb-plain-hero`). Matching dark-on-photo value: `hsl(var(--hsl-neutral-800) / 0.9)`.
  - **Watch out for `<hr>` elements**: a sitewide `hr, hr.wp-block-separator { border-top: ... !important; }` rule beats any selector-based override regardless of specificity. If a block's "rule" divider is an `<hr>`, swap it to a `<div>` for health (see `cb-about-hero.php`'s `$rule_tag` pattern) rather than fighting the `!important`.
  - **Watch out for repurposed tokens**: `--hsl-lime-1000` / `--hsl-lime-900` are reused across many blocks as generic "accent border color" tokens, but health repurposes them to a dark red/maroon (`#7b0319`-ish), not an actual lime shade. Any block using `hsl(var(--hsl-lime-1000)...)` or the `has-lime-1000-border-top/-bottom` WP utility classes unconditionally will render a maroon line on health unless overridden. Already fixed: `cb-details`, `cb-service-page-header` (shared `.service-page-header` markup, no dedicated SCSS file — added rules to `_cb_service_page_header.scss`), `cb-work-index`, `cb-feature-list`, `cb-locations`, `cb-dept-email`. **Not yet checked**: `cb-contact-page` (checked live, currently clean, but source still has the pattern — re-verify if that page's content changes). `cb-about-page-header` was explicitly called "unused/deprecated" by the user — skip further work there unless asked (h1 font-size and `.about-overlay` color were still fixed at the user's explicit request).
- **Health accent colors**: `--col-blueberry-850`, `--col-strawberry-850` for dark-on-white accents (pretitles, hover states, etc.). `--col-button` = `strawberry-850`. `--col-gooseberry` (unnumbered = the scale's "500" equivalent) used for `cb-pushthrough` light-mode link hover.
- **`--col-secondary`** is literally `#2f13ba` (generic purple) on **every** site — not a real per-site color despite the name. If found driving a health element's color, it's very likely wrong; check what accent it should actually be (was blueberry-850 for `cb-about-page-header__content-wrapper .about-overlay`).
- **`--fs-800` doesn't exist for idtravel or health** (only identity/coda define it) — any base rule using `font-size: var(--fs-800)` with no fallback silently resolves to nothing for health. Check for a matching idtravel override to copy (pattern found on `cb-testimonial`).
- **h1 weight**: `.cb-site-health h1 { font-weight: var(--fw-semibold) !important; }` now lives in `_typography.scss` as a blanket fix (was a mix of `--fw-book`/`--fw-light` across many blocks). Note this does **not** cascade to descendant `span`s with their own explicit `font-weight` (had to fix `cb-plain-hero`'s `h1 span` separately).
- **When comparing two blocks' rendered spacing**: `getBoundingClientRect()` diffs only capture padding/margin that lives *outside* an element's own box. If one block puts padding on the outer section and another puts it on the inner heading, a naive top-position diff will look mismatched even when the *effective* text position is identical — add the element's own `computedStyle.paddingTop` to the outer gap to get the true comparable number. (Cost real back-and-forth this session on `cb-lined-title` vs `cb-signpost-header`.)
- **`cb_deprecated_block_notice()`** (in `inc/cb-utility.php`) only renders when ACF's `$is_preview` is true, which requires the block to be in ACF "preview" mode. All blocks here are registered with `'mode' => 'edit'` and `supports.mode => false`, so `$is_preview` is **never** true in normal use — this notice is effectively dead code everywhere it's called (including on `cb-lined-title`, which already had it). For a visible-in-practice deprecation notice, put the text directly in the block's ACF `message`-type field instead (its title/header field) — done for `cb-signpost-header`: `"message": "[deprecated - use CB Lined Title]"`.

## Work completed this session (all Health-scoped unless noted)

- **`cb-our-brands`**: hid pretitle field/output; inverted logo (`filter: invert(1)`); removed section border-bottom; `.brand-card__strap` line-height (`--lh-400`, global) + font-weight (`--fw-light`, health); `.brand-card__back` — constant (not hover-only) `white/.9` bg + `backdrop-filter: blur(3px)` + dark text at all times, hover/focus color explicitly re-pinned dark (base rule's own `:hover` selector was winning on specificity and flipping it back to white).
- **`cb-pushthrough`**: hid pretitle; swapped CTA arrow asset + smaller size; added Style toggle (default `dark`); removed border-bottom; removed Gutenberg color picker support for health (`inc/cb-blocks.php`, block registration conditional on site); light-mode line color (`--cb-pushthrough-line-color: hsl(white/.9)`); hover colors — light → `gooseberry`, dark → `strawberry-850`.
- **`cb-about-hero`**: `__content-wrap` → `white/.8` + `blur(3px)` + dark text; Overlay Style toggle replaces Overlay Image field (default `dark`); `__rule` swapped `<hr>`→`<div>` for health (see `<hr>` gotcha above); light/dark rule colors bumped to near-solid.
- **`cb-plain-hero`**: Style toggle (default `light`) — **only applies when a background image is set**; no-image case is unconditionally white-bg/dark-text/dark-lines regardless of toggle; colors bumped to near-solid; `h1 span` font-weight fixed to `--fw-semibold` + `text-wrap: balance` added.
- **News/single templates** (`index.php`, `single.php` via `_news.scss` + `_cb_recent_news.scss`): full white-bg/dark-text inversion (no dedicated health PHP fork exists for these); `insight-type-grid__date` darkened, needed `!important` to beat a `.grid-type-1`-scoped base rule and a `.has-purple-900-background-color` category variant; `news-insights-hero` h1 given full-bleed top+bottom border (DOM restructured to match `service-page-header__title`'s pattern — h1 unconstrained, inner `id-container` div for padding); `news-insights-hero a` → `blueberry-850`, underline only on hover.
- **Global (not health-scoped)**: `.cb-site-health h1 { font-weight: var(--fw-semibold) !important; }` typography fix; `theme.json` `defaultPalette`/`--col-black` changes (see Toolchain section).
- **Maroon-line fixes** (repurposed `--hsl-lime-1000`/`900`): `cb-details`, `cb-service-page-header`/`.service-page-header`, `cb-work-index`, `cb-feature-list`, `cb-locations`, `cb-dept-email`.
- **`cb-testimonial`**: `__author`/`__company` font-size fixed (missing `--fs-800` token, see pattern note above) to match idtravel's `--fs-700`/`--fw-book`; removed `text-transform: uppercase` on `__author`.
- **`cb-services-nav`**: `__item` border-bottom-color → `rgba(255,255,255,.8)`; `__header` background alpha `.75` → `.5`.
- **`cb-about-page-header`**: h1 font-size → `--fs-850`; `.about-overlay` background → `--col-blueberry-850` (was `--col-secondary`, a fake per-site color — see note above). Block itself called out as unused/deprecated by the user; don't do further unrequested work here.
- **`cb-image-feature-overlay`**: hid Overlay Image field; overlay panel → `white/.8` + `blur(3px)` + dark text (same pattern as about-hero).
- **`.id-button`**: `color: var(--col-white) !important` for health (guards against a WP-generated `has-*-color` utility class beating the base `color: #fff`).
- **`cb-lined-title` / `cb-signpost-header`**: aligned letter-spacing (`var(--ls--10)`) and padding (`1rem` both top/bottom, verified via `getBoundingClientRect` + own-padding math — see pattern note) so the two blocks render identically for health; added visible deprecation message to `cb-signpost-header`'s ACF field group pointing to `cb-lined-title`.

## State as of last message

Everything above is compiled (`npm run css` run clean) and verified via `javascript_tool` DOM/CSSOM inspection where possible. Last confirmed-clean check: `cb-lined-title` vs `cb-signpost-header` both measure 17px top and 17px bottom effective gap (equal). No outstanding/unfinished task — session ended here for the day.

Uncommitted changes exist in the working tree (never committed/pushed by Claude this session per the git safety rules — only the user commits/pushes, usually from a separate terminal). Run `git status`/`git diff` on resume to see the full list.
