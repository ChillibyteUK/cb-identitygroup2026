# Strategic Implementation Plan: ACF Blocks Theme Consolidation (Fact-Checked Revision)

> **Revision note:** This is a corrected version of the original consolidation
> plan. Every block-inventory and codebase claim below was verified directly
> against the live `cb-identity2025`, `cb-coda2026`, and `cb-idtravel2026`
> repositories. Corrections from the original are marked **[CORRECTED]**; new
> findings not present in the original are marked **[NEW]**. Everything else
> was independently re-verified and confirmed accurate.

## Executive Summary

Three Understrap child themes, built consecutively. Consolidate all ACF block
functionality into a single **shared child theme** (`cb-identitygroup2026`)
with per-site CSS tokenisation — no rewrites of working code except where
noted below, and genuine standardisation where blocks duplicate each other.

The three sites are **separate WordPress installations**, each running its
own copy of the shared child theme. No child-theme nesting is required.

**[CORRECTED] Effort estimate:** the original plan assumed "a single focused
session." That undercounts two real conversion costs surfaced during
verification (registration-system conversion for all of identity's blocks,
and coda's i18n being built from near-zero) — see §0 and §6. Recommend
running Phase A, B, C, and D as separate, independently reviewable
sessions/PRs rather than one sitting.

**[RESOLVED] Repo location:** `ChillibyteUK/cb-identitygroup2026` has now been
created (empty, as of this revision) as the standalone shared-theme repo.
Phase A's fork-idtravel-as-base step targets this repo.

---

## 0. [NEW] Block Registration Architecture — Must Be Resolved Before Phase A

Verification surfaced an architectural mismatch the original plan did not
account for:

| Theme | Mechanism | Evidence |
|---|---|---|
| **identity** | Native `block.json` + `register_block_type()`, namespace `cb/cb-{name}` | `inc/cb-blocks-enhanced.php` — 42 block.json files registered this way |
| **coda** | Flat `.php` files + `acf_register_block_type()` | 26 blocks, no block.json anywhere |
| **idtravel** | Flat `.php` files + `acf_register_block_type()`, but **inconsistent namespace** — mixes `acf/cb_name` (underscore, ~17 blocks) and `acf/cb-name` (hyphen, ~9 blocks) in the same file | `inc/cb-blocks.php`, function `acf_blocks()` |

The original plan's §10 ("Naming/Collisions") treated this as a simple
rename — "normalise all to `acf/cb_{name}` via `acf_register_block_type()`."
In reality it's two different registration *systems*, not just two naming
conventions: identity's blocks use block.json's `edit.js`/`render.php`
convention, while coda/idtravel use ACF's classic template-callback pattern.

**Decision: standardise on classic ACF (`acf_register_block_type`).** This
means, in Phase A, every one of identity's 20 blocks being moved into the
shared theme must be **converted**, not just copied — block.json + JS edit
config replaced with an ACF field-group-driven template file. This is real
engineering work per block, not a find-and-replace. Budget it as such in
Phase A.

---

## 1. The Architecture

```
Understrap (parent theme, unchanged on all 3 sites)
  └── cb-identitygroup2026 (shared child theme, repo root = theme root)
        ├── style.css            WP discovers theme here
        ├── blocks/              ALL shared blocks, one-offs (flat PHP, classic ACF registration)
        ├── inc/                 ALL utilities merged
        ├── acf-json/            ALL field groups consolidated
        ├── src/sass/            ALL SCSS + per-site token overrides
        ├── css/                 Built output — committed for deploy
        └── sites/               Build-time per-site token overrides
```

Per-site differentiation is unchanged from the original plan: build-time SCSS
token compilation, `get_field('cb_site', 'option')` runtime branching,
per-site `theme.json` generation.

---

## 2. Standardisation Analysis: Eliminating Redundant Blocks

### 2.1 Identity-only blocks that can be replaced by shared blocks

*(Confirmed unchanged from original — all 20 identity blocks named below were
verified to exist as `block.json` directories under `cb-identity2025/blocks/`.)*

| Identity block | Replace with | Config field(s) to add | Notes |
|---|---|---|---|
| **cb-about-detail** | **cb-details** (coda) | Add `pre_title` text field to cb-details field group | About detail rows = title + content pairs. |
| **cb-service-detail** | **cb-details** (coda) | Same — already covered | |
| **cb-awards-slider** | **cb-logo-slider** (idtravel) | Add `logo_source` radio: Site-Wide / Specific | Both infinite CSS marquees of logos. |
| **cb-sport-logos** | **cb-logo-slider** (idtravel) | Same field | |
| **cb-case-study-key-stats** | **cb-stats** (idtravel) | None | |
| **cb-content-grid-v2** | **cb-content-grid** (idtravel) | None | |
| **cb-latest-insights-expo** | **cb-latest-insights** (coda) | Add `taxonomy_filter` field | |
| **cb-related-work** (3 variants: `cb-related-work`, `cb-related-work-expo`, `cb-related-work-sports`) | **cb-featured-work** (coda) | Add `taxonomy_filter` field | Confirmed all 3 variant directories exist. |
| **cb-work-by-region** | **cb-featured-work** (coda) | Same taxonomy_filter field | |
| **cb-styled-text-image** | **cb-content-grid** (idtravel) | None | |

**Result: 12 identity-only blocks eliminated**, unchanged from original.

### 2.2 Identity-only blocks that stay as shared blocks (moved, not standardised)

Unchanged from original — all 8 confirmed present in `cb-identity2025/blocks/`:
cb-case-study-hero, cb-brand-title-text, cb-culture-page-header,
cb-innovation-header, cb-policies-page, cb-region-page-header, cb-file-block,
cb-work-carousel.

**[CORRECTED — cb-case-study-hero note]** The original Decisions table (§12)
frames `cb_find_hero_subtitle()` as duplicated "inline in 3 blocks across 2
themes," used to parse `cb-case-study-hero`'s field data. Verified: it is
duplicated **10 times, entirely within identity**, and is **not present in
`cb-case-study-hero` itself** — it's called by the *listing* blocks that
display a case study's subtitle as a card field:
`cb-work-carousel`, `cb-work-by-region`, `cb-featured-work`, and all
variants of `cb-work-index` (3) and `cb-related-work` (3). Extraction to
`inc/cb-utility.php` is still correct and arguably more valuable given the
higher duplication count — just correct the "2 themes" framing.

### 2.3 Coda-only blocks — standardisation opportunities

| Coda block | Replace with | Config field(s) to add | Notes |
|---|---|---|---|
| **cb-full-case-study** | **cb-featured-work** (coda, shared) | Add `hero_mode` checkbox | Confirmed exists in coda. |
| **cb-what-we-delivered** | **cb-details** (shared) | Add `render_style` radio: Details / Bullet list | Confirmed exists in coda. |
| **cb-lined-title** | **cb-signpost-header** | None | **[CORRECTED]** `cb-signpost-header` is confirmed to exist in idtravel and identity, but **not in coda** — it must be copied into coda before this drop-in works, it isn't already a coda block. |
| **cb-gradient-intro** | One-off | — | Keep as-is. |
| **cb-dept-email** | One-off | — | Keep as-is. |
| **cb-locations** | One-off | — | Keep as-is. |
| **cb-feature-list** | One-off | — | Keep as-is. |

**[NEW]** `cb-hero-prop-cta` exists in **both coda and idtravel** but was not
catalogued anywhere in the original plan. It needs a classification (shared
as-is, or one-off) added before the inventory is complete — not yet
resolved, flagged as an open item (§7).

### 2.4 Idtravel-only blocks — standardisation opportunities

| Idtravel block | Replace with | Config field(s) to add | Notes |
|---|---|---|---|
| **cb-video-hero** | **cb-full-video** (shared) | Add `full_width`, `hero_mode` checkboxes | |
| **cb-plain-hero** | **cb-image-feature-overlay** (shared) | Already has Inline + Hero modes | |
| **cb-cta-hero** | **cb-image-feature-overlay** (shared) | Already covered by Hero mode | |
| **cb-stats** / **cb-stat-hero** | **cb-stats** (shared) | Add `show_hero` checkbox + hero fields | |
| **cb-business-travel-nav** | **cb-child-page-nav** (new shared) | Add `parent_slug` text field | |
| **cb-solutions-nav** | **cb-child-page-nav** (new shared) | Same | |
| **cb-specialist-travel-nav** | **cb-child-page-nav** (new shared) | Same | |
| **cb-about-hero** | One-off | — | Keep as-is. |
| **cb-vimeo-event** | One-off | — | Keep as-is. |
| **cb-slido-embed** | One-off | — | Keep as-is. |
| **cb-decision-gate** | One-off | — | Keep as-is. |
| **cb-tmc-post-index** | One-off | — | Keep as-is. |

**[NEW]** `cb-service-grid` exists in **both idtravel and identity** but was
not catalogued in the original plan. Not confirmed in coda. Needs
classification before the inventory is complete — flagged as an open item
(§7).

---

## 3. Revised Block Inventory After Standardisation

**[CORRECTED]** The original §3 table claimed 10 blocks are shared "all 3
themes, kept as-is," including `cb-contact-page` and `cb-careers-page`.
Verified: both exist in idtravel and identity, but **not in coda** — they are
not currently shared across all three, they need to be copied into coda
first (same treatment as the other Phase A "copy from coda"/"copy to coda"
items). `cb-about-detail` and `cb-full-case-study` were also wrongly listed
in the original's Phase A step 2 as things to copy *from* coda — neither
exists in coda, and both are slated for elimination anyway (§2.1, §2.3).
Those two are removed from the copy list entirely.

| Category | Count | What |
|---|---|---|
| Shared blocks confirmed in all 3 themes as-is | 8 | content-grid, cta, faq, testimonial, our-brands, pushthrough, recent-news, contact-form |
| Shared blocks needing a copy into coda before they're truly "all 3" | 2 | contact-page, careers-page |
| Shared blocks confirmed in 2 themes, kept as-is | 9 | latest-insights, featured-work, full-image, full-video, about-page-header, service-page-header, services-nav, work-index, leadership |
| Shared via standardisation (replacing one-offs) | 6 | cb-details (+pre_title, render_style), cb-logo-slider (+logo_source), cb-stats (+show_hero), cb-latest-insights (+taxonomy_filter), cb-featured-work (+taxonomy_filter, hero_mode), cb-content-grid (already covers v2 + styled-text-image) |
| New shared block (consolidated) | 1 | cb-child-page-nav (replaces 3 nav blocks) |
| Inline deprecations | 3 | Drop cb-lined-title (→ signpost-header, needs copying to coda first), cb-plain-hero, cb-cta-hero (→ image-feature-overlay) |
| One-off blocks (site-specific, conditionally registered) | 18 | case-study-hero, brand-title-text, culture-page-header, innovation-header, policies-page, region-page-header, file-block, work-carousel (identity); gradient-intro, dept-email, locations, feature-list (coda); about-hero, vimeo-event, slido-embed, decision-gate, tmc-post-index, full-case-study (idtravel) |
| **[NEW] Uncatalogued — needs classification** | 2 | cb-hero-prop-cta (coda + idtravel), cb-service-grid (idtravel + identity) |
| Dead | 1 | content-grid-links (delete) — confirmed present in idtravel as `content-grid-links.php`, no `cb-` prefix |

Net reduction is broadly consistent with the original's "~48 blocks" estimate,
plus the 2 uncatalogued blocks above to resolve and the identity
block-registration conversion cost from §0.

---

## 4. Site-Wide Settings Consolidation

**Confirmed accurate, no changes.** All field claims in the original §4 were
verified directly against `acf-json/group_site_wide_settings.json` in all
three repos:

- The 10 "identical across all 3 themes" General-tab fields are confirmed
  identical.
- Identity's `insight_cta`/`press_cta` are confirmed as 9 static choices,
  width 50 — matches the original's claim exactly.
- Coda and idtravel's `insight_cta`/`press_cta` carry a static JSON
  placeholder (`{"CTA 1": "CTA 1"}`) but are dynamically rebuilt at runtime
  via `acf/load_field` filters in `inc/cb-theme.php` — confirmed in both.
- `people_cta` exists only in idtravel currently, and — **[NEW]** — it does
  **not** yet have the dynamic `acf/load_field` filter that `insight_cta`/
  `press_cta` have; it stays on static choices today. The plan's "same
  treatment" for `people_cta` (§4) is the right call but is genuinely new
  work, not an existing pattern to copy.
- `cb_site` field: confirmed absent in all three themes, as expected (it's
  new).

No changes to the original's proposed consolidated field group (§4) beyond
noting the `people_cta` dynamic-filter work above.

---

## 5. Design Token Standardisation

**Confirmed accurate — this is the most solid section of the original plan.**
Every token bug claimed was independently re-verified in all three
repositories:

- `--col-accent-400`, `--col-secondary-100/400/900`, `--col-primary` (bare),
  `--col-black`, `--col-neutral-50`/`-050` — all confirmed referenced via
  `var(--x)` in `_wp.scss`/`_buttons.scss`/`_typography.scss`/etc. and
  **never defined** in any of the three themes' `_tokens.scss`. This is a
  real, cross-theme, silently-broken focus-outline and list-styling bug.
- Brand colour naming divergence (`--col-green-*` / `--col-lime-*` /
  `--col-raspberry-*`) confirmed in identity/coda/idtravel respectively.
- **[NEW]** idtravel's `--col-primary-black` is referenced in 5 places
  (`_text-page.scss`, `_buttons.scss`, `_cb_contact_form.scss`) but never
  defined — confirmed, matching the original's §5.1 claim, and confirmed
  that identity and coda *do* define it, so this bug is idtravel-specific.
- **[NEW]** idtravel's undefined `--col-lime-*` references are broader than
  the original's "gradient only" framing — also referenced (and undefined)
  in `_child_theme.scss` and `_text-page.scss`, not just the gradient in
  `_tokens.scss`.

The generic token naming convention (§5.2) and per-site mapping tables are
unchanged and confirmed accurate — no corrections needed there.

---

## 6. [CORRECTED] Implementation Task Sequence — Revised Effort Notes

The original's numbered task sequence (Phase A–D) is structurally sound.
Corrections to specific steps:

- **Phase A, step 1 (fork idtravel as base):** unchanged, still the right
  base given idtravel and coda share the same registration mechanism.
- **Phase A, step 2 (copy blocks from coda):** remove `cb-about-detail` and
  `cb-full-case-study` — neither exists in coda (§3). Add `cb-contact-page`
  and `cb-careers-page` to the copy-into-coda list, and `cb-signpost-header`
  needs copying into coda as well (currently idtravel/identity only).
- **Phase A, step 4 (copy identity's one-off blocks):** each of these 8, plus
  all 12 blocks from §2.1, plus any of identity's other blocks pulled into
  the shared theme, must go through the **block.json → classic ACF
  conversion** from §0. This did not exist as a line item in the original
  plan — add it explicitly as its own step with its own review, given it's
  effectively a rewrite of up to 20 block templates.
- **Phase B, step 9 (i18n):** for coda specifically, this is closer to
  wrapping strings from scratch (only 1 of ~26 block files has any `__()`
  call today, and that one uses the wrong domain) rather than "fixing
  stragglers." Budget accordingly.
- **Overall effort:** given the above, treat Phase A/B/C/D as separate
  sessions/PRs rather than "a single focused session."

---

## 7. Open Items Before Migration Can Start

1. ~~Where does `cb-identitygroup2026` live?~~ **Resolved** —
   `ChillibyteUK/cb-identitygroup2026` created as a standalone repo.
2. **Classify `cb-hero-prop-cta`** (exists in coda + idtravel) — shared
   as-is, or one-off per site?
3. **Classify `cb-service-grid`** (exists in idtravel + identity, not
   confirmed in coda) — shared as-is, or one-off per site?
4. **Confirm registration-conversion effort** for identity's 20 blocks is
   acceptable as a dedicated Phase A sub-step before starting real
   implementation.

---

## 8. What We're NOT Doing

Unchanged from original — component extraction beyond directly-modified
blocks, perfect SCSS deduplication, ACF field-key migration, and full
template rewrites are all explicitly out of scope.

---

## Appendix: Verification Method

Every block-inventory, token, ACF-field, and text-domain claim in this
document was checked directly against the three live repositories
(`cb-identity2025` — correct owner `ChillibyteUK`, not the initially-added
`ChillibyteDan` fork which is empty; `cb-coda2026`; `cb-idtravel2026`) via
file-path-level grep/read, not inferred from the original document's
prose. Findings are cited with file paths above; see session history for
line numbers on each specific claim if needed.
