# Adding and styling blocks in cb-identitygroup2026

This is the ongoing-maintenance companion to `CB-THEME-CONSOLIDATION-PLAN.md`
(that document is the historical one-time migration plan; this one is the
process to follow for every block added or touched *after* consolidation).

It exists because the single most common bug class found across this entire
project — by a wide margin — is **a block built from one site's real design,
then either wrongly applied to all three sites, or never checked against the
other two at all.** Every rule below exists to close a specific, real,
previously-shipped instance of that bug. Where useful, the actual bug is
named so the reasoning doesn't get lost.

## 1. How a block is put together here

This theme uses classic ACF block registration, not `block.json`. A block has
up to four parts:

| Part | Location | Required? |
|---|---|---|
| Registration | `inc/cb-blocks.php`, one `acf_register_block_type()` call | Always |
| Render template | `blocks/cb-{name}.php` | Always |
| ACF field group | `acf-json/group_cb_{name}.json` | If it takes editable fields |
| Styles | `src/sass/theme/blocks/_cb_{name}.scss`, imported from `blocks/_blocks.scss` | Almost always |

Block names in markup are `acf/cb-{name}` (hyphenated), matching the
`render_template` path and the ACF field group's `location` rule
(`"value": "acf/cb-{name}"`).

## 2. The one rule that matters most

**Before writing a single line of PHP or SCSS for a block that could plausibly
appear on more than one site, open all three real sources and compare them
side by side:**

- `/var/www/id/wp-content/themes/cb-identity2025/blocks/cb-{name}/` (or
  `.php` directly, some are flat)
- `/var/www/coda/wp-content/themes/cb-coda2026/blocks/cb-{name}.php`
- `/var/www/idtravel/wp-content/themes/cb-idtravel2026/blocks/cb-{name}.php`

Not all three will exist — that's fine, it's information. What matters is:
do the ones that *do* exist actually match each other, or do they differ in
markup, colour scheme, typography, or field structure? If you skip this step
and build from whichever one is open in your editor, you will silently bake
that one site's assumptions into a "shared" block. This happened repeatedly
this session — `cb-pushthrough`, `cb-faq`, `cb-contact-page`,
`cb-image-feature-overlay`, `cb-services-nav`'s item typography, and the
`--col-lime-*`/`--col-brand-dark` colour tokens were all built or fixed once
from a single site's source and quietly wrong on the others until a page-by-page
comparison against real production caught it.

## 3. Site detection

```php
$site        = cb_site_template_suffix(); // 'identity' | 'coda' | 'idtravel'
$is_identity = 'identity' === $site;
$is_coda     = 'coda' === $site;
$is_idtravel = 'idtravel' === $site;
```

Declare these **before** any code that needs them — several bugs this session
were caused by a `$is_identity` variable declared halfway through a file,
after the code that should have branched on it.

Body class `.cb-site-{identity|coda|idtravel}` is added automatically
(`cb_add_site_body_class()` in `inc/cb-site-tokens.php`) — use it for CSS-only
structural differences that don't need a PHP branch.

## 4. Two different kinds of per-site difference — use the right mechanism

**A colour/size swap** (same structure, different token value): give the
token a per-site value in `cb_get_site_tokens_table()` in
`inc/cb-site-tokens.php`. Don't add a `.cb-site-X` SCSS override for this —
that's how the `--col-brand-dark`/`--col-lime-*` rounding bugs happened
(duplicating a value in SCSS that should have lived in the token table).

**A structural difference** (extra/missing wrapper, different classes,
dark-vs-light theme, a field that only exists for one site): scope it with
`.cb-site-{slug} { ... }` in `_header-site-overrides.scss` (see the existing
`cb-pushthrough`, `cb-faq`, `cb-contact-page` overrides there for the
pattern) or with a `$is_identity`/`$is_coda`/`$is_idtravel` branch in the PHP
template. Don't try to force this through the token table — tokens are
values, not markup.

If a real source has a field/section another site's real source completely
lacks (e.g. identity's "New business USA"/"Middle East" contact fields,
absent from idtravel and coda), wrap it in the matching `$is_x` check. Do not
render it unconditionally "to be safe" — that's the opposite mistake, and it
happened too (an earlier fix restored a missing identity field but forgot to
scope it, so it leaked onto the other two sites).

## 5. Six specific traps, all previously shipped as real bugs

1. **A token existing in the shared table doesn't mean this component should
   resolve it.** Some real sources genuinely never define a given custom
   property (e.g. identity's real `_tokens.scss` has no `--col-ink` at all) —
   on real production, `var(--col-ink)` there is invalid and falls through to
   the property's initial value (`transparent` for `background-color`,
   `inherit` for `color`). Because this shared theme's token table gives
   *every* site a real value for commonly-needed tokens like `--col-ink`, a
   component that was supposed to stay visually inert on one site instead
   gets an unwanted colour. If you find a background/colour that's "just a
   bit too present" compared to real, check whether that site's real
   `_tokens.scss` defines the property at all before assuming the token table
   is wrong. Fix by overriding *that one component* back to `transparent`/
   `inherit` under `.cb-site-{slug}`, not by removing the token everyone else
   needs.

2. **WordPress core silently re-declares `--wp--preset--*` tokens and can win
   the cascade regardless of source order.** `cb_output_site_token_overrides()`
   appends `!important` to every declaration specifically because of this —
   removing it (or adding a new per-site `--wp--preset--*` override without
   it) will silently do nothing on the frontend even though the value looks
   correct in the page source. If a `has-{slug}-color` or
   `has-{slug}-font-size` utility class isn't picking up a per-site override
   you just added, this is almost certainly why — check with a headless
   browser's `getComputedStyle`, not by reading the compiled CSS text.

3. **Gutenberg's `has-{slug}-font-size` classes need a per-site
   `--wp--preset--font-size--{slug}` override too, the same as colours do.**
   `theme.json`'s `fontSizes` array is one static scale (byte-identical to
   idtravel's own, since the base theme was forked from idtravel) — identity
   and coda's own real font-size scales differ from it and never got an
   override until this was found on coda's `/work/` page. If you use a
   Gutenberg-picker font size anywhere, confirm `--wp--preset--font-size--{slug}`
   is overridden for every site that needs a different value.

4. **A bare `h1`/`h2`/`h3` type-selector override outranks a single utility
   class.** `.cb-site-x h1 {...}` is a class+element compound (specificity
   `0,1,1`), which beats a lone class like `.fs-850` (`0,1,0`) *regardless of
   which one appears later in the compiled CSS.* If you need a per-site
   heading override, scope it to the actual component
   (`.cb-site-x .content-grid h1`), not to the bare tag — a bare-tag override
   will silently clobber every other place on the page that sizes its own
   heading via a utility class instead of relying on the generic rule.

5. **A class name can be a false friend.** `.fw-semi` and `.fw-semibold` are
   two *different* tokens (`--fw-semi` vs `--fw-semibold`) on identity's and
   coda's real sources, but the same token on idtravel's. Don't assume a
   utility class means the same thing on every site just because the name is
   generic-sounding — check the real source's own typography file.

6. **`menu_order` and other WP data fields can be the actual bug, not the
   code.** `cb-services-nav`'s item ordering looked like a component bug for
   a while; the actual cause was every services child page having
   `menu_order = 0` in the local database, an artifact of the content
   import/sync, not the shared theme. If a component's *logic* matches real
   byte-for-byte but the *output* still differs, check the underlying data
   before touching code.

## 6. Verifying a block against real production

`qa/compare-site.js` is the standing tool for this — not a one-off script,
re-run it after every change:

```
node qa/compare-site.js <config.json> <output-report.json>
```

Config shape:
```json
{
  "playwrightPath": "/absolute/path/to/node_modules/playwright",
  "realBase": "https://identityglobal.com",
  "localBase": "http://idglobal-test.local",
  "paths": ["about", "services/strategic-advisory"]
}
```

It fetches both real and local for each path, and for every direct child of
`<main>` reports: classes (normalised — order/whitespace don't matter),
`innerText`, and computed `background-color`/`border-top-color`/
`border-bottom-color`/`color`/`font-size`/`font-weight` — **on every element
matching `h1, h2, h3, p, .cb-testimonial__quote, [class*="title"],
[class*="date"], [class*="excerpt"]` within that section, not just the
first one.** That last point matters: an earlier version of this tool only
sampled the first match per section, which is exactly how the
`cb-services-nav__item-title` bug (every item title past the page's own "SERVICES"
header, silently wrong) went undetected through several full sweeps. If
you're adding a block with more than one repeated text element (a list, a
grid of cards, a nav), the widened check is what will actually catch a
per-item styling bug — don't shrink the selector back down.

A `pass` verdict means structure, text, and those specific style properties
matched. It does **not** mean pixel-perfect, and does not cover: mobile/
tablet breakpoints (the tool runs one fixed desktop viewport, 1440×1000),
hover/focus/interactive states, animations, or non-`page` post types. Say so
explicitly if you haven't checked those, rather than letting a `pass` verdict
imply more than it does — this got called out directly earlier in this
project and the correction stands as the standard going forward.

## 7. Adding a genuinely new block, step by step

1. Check the real sources for all 3 sites. Does an equivalent block already
   exist on one, two, or all three? If yes, this may be a "standardise onto
   an existing block" case (see `CB-THEME-CONSOLIDATION-PLAN.md` §2 for the
   pattern already used for `cb-details`, `cb-stats`, `cb-logo-slider`, etc.)
   rather than a genuinely new one.
2. Register it in `inc/cb-blocks.php` (`acf_register_block_type()`), following
   the existing entries' shape.
3. Write `blocks/cb-{name}.php`. If any site's real source differs from
   another's — even slightly — declare `$is_identity`/`$is_coda`/`$is_idtravel`
   near the top of the file (before they're needed) and branch explicitly.
   Don't merge two sites' designs into one "compromise" markup shape unless
   they're actually identical.
4. Add the ACF field group under `acf-json/group_cb_{name}.json` if it takes
   fields (export from the ACF admin UI, or hand-write following an existing
   group's shape — `location` must be `[[{"param":"block","operator":"==","value":"acf/cb-{name}"}]]`).
5. Write `src/sass/theme/blocks/_cb_{name}.scss` and add
   `@import "cb_{name}";` (underscore, no leading `_`, no `.scss`) to
   `src/sass/theme/blocks/_blocks.scss`. Use tokens (`var(--col-*)`,
   `var(--fs-*)`, `var(--fw-*)`) throughout — never hardcode a hex value or an
   asset path that's specific to one site's brand unless the block is
   genuinely one-site-only by design (see §8).
6. If any token the block needs doesn't exist yet for one of the sites that
   needs it, add it to that site's array in `cb_get_site_tokens_table()`
   (`inc/cb-site-tokens.php`) — check whether it needs the `--wp--preset--*`
   sibling too (see trap #3 above) if it's consumed via a
   `has-{slug}-color`/`has-{slug}-font-size` utility class rather than a
   direct `var()` reference in your own SCSS.
7. `npm run css`, then `rsync` the changed PHP/SCSS/JSON files plus the `css/`
   folder to all three test sites' theme directories.
8. Build a config for `qa/compare-site.js` covering every real page that uses
   this block (or a temporary draft page if it's genuinely new content with
   no real equivalent yet), and run it. Fix anything it flags, re-run until
   clean, then re-run once more after a short pause to make sure nothing
   regressed on pages you weren't actively changing.
9. Log what you found and fixed in `SESSION-HANDOFF.md`, following its
   existing dated-entry format — this project's history shows that skipping
   this step means the same bug class gets rediscovered from scratch later.

## 8. One-off, genuinely site-exclusive blocks

Not every block needs to work on all three sites — plenty are legitimately
site-exclusive (idtravel's `cb-business-travel-nav`/`cb-specialist-travel-nav`,
coda's `cb-dept-email`/`cb-locations`, identity's `cb-region-page-header`, and
others — see `CB-THEME-CONSOLIDATION-PLAN.md` §2's "one-off" classifications).
For these, it's fine to hardcode that site's own values and skip the
`$is_x` branching entirely — there's no "correct" cross-site behaviour to
match because the concept doesn't exist on the other sites.

The risk is specifically when a block *could* plausibly be inserted on
another site via the block picker (every registered block is available in
every site's editor, regardless of which site's real content actually uses
it) but was never verified there. As of this writing, **35 of the theme's 64
registered blocks are live in only one site's real content**, and every one
of them was checked and found to have zero site-conditional logic (8 more
are used in zero sites' current content — genuinely dead, not just
single-site). Two
concrete failure modes were confirmed by actually inserting blocks on a
foreign site and rendering them (not just reading the code):

- Blocks whose SCSS is 100% token-based (no hardcoded hex, no hardcoded
  asset paths) render with a *reasonably coherent* result on a foreign site,
  because the same token names resolve to that site's own colours/sizes
  automatically — confirmed by test-rendering `cb-stats` on coda.
- Blocks that depend on a specific page existing (e.g.
  `cb-business-travel-nav` requires a page literally slugged `business-travel`)
  render **nothing at all** on a site without that page — a safe failure
  (no visual breakage) but not a styled one, and worth knowing before an
  editor inserts one expecting output.

Neither test found a case of actually *broken* (wrong-looking, half-styled)
rendering, but neither test covers every block, and structural differences
(the kind that need `.cb-site-x` scoping, not just a token swap) wouldn't be
caught by inserting a block on a site whose real design was never compared
against it in the first place. Don't take "it's never been reported as
looking wrong" as equivalent to "it's been checked" — it hasn't, beyond
what's described above. If you're about to make a single-site block
available for editorial use on another site, actually insert it on a draft
page there and look at it before telling anyone it's ready.
