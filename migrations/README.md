# Migration runbook

Six one-off content migrations, run in order (001 → 006). Every script is
safe to re-run (idempotent — once a post no longer contains the old block
name, the script skips it) and every write path uses `$wpdb->update()`
directly, never `wp_update_post()`/`wp_insert_post()` (those call
`wp_unslash()` on the way in; since `post_content` read from the DB is
already unslashed, running it through `wp_update_post()` would double-unslash
and corrupt any backslash-containing content — this corrupted a post once
already during an earlier by-hand migration attempt, before these scripts
existed).

## Migrations 002/003/004 are identity-only — an error on coda/idtravel is expected

These three (`cb-latest-insights-expo`, `cb-related-work-expo`,
`cb-related-work-sports`) look up an "Expo" or "Sports" term in the `theme`
taxonomy before doing anything else, because the content they migrate
(Expo/Sports-categorised case studies and insights) only exists on identity.
Run against coda's or idtravel's production site, they correctly print:

```
Error: Could not find the 'expo' term in the 'theme' taxonomy on this site - aborting.
```

and touch nothing. **This is the intended safety guard working correctly,
not a broken migration** — confirmed by running all three directly against
coda's and idtravel's test sites, where they abort the same way. Only run
002/003/004 against identity's site.

## Current status (verified 2026-07-13)

All 6 have been run and confirmed **fully applied on all three local test
sites** (idglobal-test, idcoda-test, idtravel-test) — a dry-run of every
script against every site it applies to returns zero pending changes (see
the note above for 002/003/004 specifically on coda/idtravel). The only
remaining `wp:cb/…`-namespaced content on identity's site is
`cb-content-grid-v2` and `cb-styled-text-image`, which are **deliberately**
left on the old namespace and renamed at render time instead, by
`cb_rename_legacy_block_names()` in
`inc/cb-utility.php` — not a gap, see that function's docblock.

**This status has only been confirmed against the local test database
clones, not production directly** — this project's hosting has no WP-CLI/SSH
access, so there is no way to query production's database from this
environment. Run a dry run of every migration against production first
(steps below) and read its output before assuming the same "already applied"
status holds there. If production's content was set up independently of (or
before) these test clones were last synced, it may still need some or all of
these to run for real.

## How to run on production (no WP-CLI available)

1. Log in to `wp-admin`, go to **Tools → CB Migrations**.
2. For each script, click **Dry Run** first. Read the output. A dry run
   never writes to the database — it's always safe to run.
3. **Take a full database backup** (via your host's backup/export tool)
   before running anything in write mode. This cannot be undone
   automatically.
4. Only if the dry run reports changes to make, tick "I've backed up the
   database" and click **Run (write)**.
5. Re-run the same script's dry run afterward — it should now report zero
   pending changes. If it doesn't, stop and investigate before moving to the
   next script.
6. Run 001 → 006 in numeric order. Some scripts (005) explicitly document
   which block names other scripts are responsible for — running out of
   order risks a script reporting a false "nothing to do" for content
   another script hasn't reached yet, not actual corruption, but it wastes a
   dry-run/write cycle sorting out why.

If WP-CLI access becomes available at some point instead, every script also
runs identically via:

```
wp eval-file wp-content/themes/cb-identitygroup2026/migrations/00X-name.php          # dry run
wp eval-file wp-content/themes/cb-identitygroup2026/migrations/00X-name.php write     # apply
```

(The admin-page path and the WP-CLI path share the exact same script files —
`inc/cb-migrations-admin.php` only provides a `WP_CLI` class polyfill so the
scripts' own `WP_CLI::log()`/`::success()`/etc. calls work unmodified in
both contexts.)

## What each one does

| # | Migration | Converts |
|---|---|---|
| 001 | `cb-awards-slider-to-logo-slider` | `cb/cb-awards-slider`, `cb/cb-sport-logos` → `acf/cb-logo-slider` (specific mode) |
| 002 | `cb-latest-insights-expo` | `cb/cb-latest-insights-expo` → `acf/cb-latest-insights` (+ taxonomy_filter) |
| 003 | `cb-related-work-expo` | `cb/cb-related-work-expo` → `acf/cb-related-work` (Expo theme_filter) |
| 004 | `cb-related-work-sports` | `cb/cb-related-work-sports` → `acf/cb-related-work` (Sports theme_filter) |
| 005 | `identity-block-namespace` | Identity's remaining `cb/cb-{name}` blocks (31 types) → `acf/cb-{name}`, plus 2 field-key renames (`cb-full-image`'s `top_gradient`→`top_border`, `cb-service-page-header`'s `service_title`/`service_intro_text`→`title`/`content`) |
| 006 | `cb-content-grid` | `cb/cb-content-grid` → `acf/cb-content-grid` |

Deliberately **not** migrated at the database level: `cb-content-grid-v2`
and `cb-styled-text-image` (see above — render-time filter instead).
