/**
 * Standing QA tool: compares every real published page against its local
 * test-clone equivalent, section by section. Not a one-off script - this
 * is the canonical verification tool for this project; re-run it after
 * any change instead of writing a new ad-hoc script.
 *
 * Usage:
 *   node compare-site.js <config.json> <output-report.json>
 *
 * config.json shape:
 *   {
 *     "playwrightPath": "/absolute/path/to/node_modules/playwright",
 *     "realBase": "https://example.com",
 *     "localBase": "http://example-test.local",
 *     "paths": ["about", "services/strategic-advisory", ...]
 *   }
 *
 * For each path, fetches both real and local, and reports:
 *   - HTTP status for each (a 404 on real for a local-only path is
 *     reported as "local-only", not a failure - test/scratch content is
 *     expected to 404 on real)
 *   - Section-level structural fingerprint (tag + class of every
 *     direct child of <main> or the main content area)
 *   - Per-section: text content (trimmed, whitespace-collapsed) and key
 *     computed styles (background-color, color, font-size, font-weight)
 *     on the section itself and its first heading/paragraph/div descendant
 *   - A verdict: "pass" (structure + text + styles identical), "flag"
 *     (lists every concrete difference), or "local-only"/"real-only"
 */

const path = require('path');

async function run() {
	const configPath = process.argv[2];
	const outputPath = process.argv[3];

	if (!configPath || !outputPath) {
		console.error('Usage: node compare-site.js <config.json> <output-report.json>');
		process.exit(1);
	}

	const config = require(path.resolve(configPath));
	const { chromium } = require(config.playwrightPath);

	const browser = await chromium.launch();
	const results = [];

	try {
		for (const p of config.paths) {
			const cleanPath = p.replace(/^\//, '');
			const realUrl = config.realBase.replace(/\/$/, '') + '/' + (cleanPath ? cleanPath + '/' : '');
			const localUrl = config.localBase.replace(/\/$/, '') + '/' + (cleanPath ? cleanPath + '/' : '');

			const [realData, localData] = await Promise.all([
				fetchPageData(browser, realUrl),
				fetchPageData(browser, localUrl),
			]);

			results.push(diffPages(p, realUrl, localUrl, realData, localData));
			console.error(`checked: ${p} -> ${results[results.length - 1].verdict}`);
		}
	} finally {
		await browser.close();
	}

	require('fs').writeFileSync(outputPath, JSON.stringify(results, null, 2));

	const summary = results.reduce((acc, r) => {
		acc[r.verdict] = (acc[r.verdict] || 0) + 1;
		return acc;
	}, {});
	console.log(JSON.stringify(summary, null, 2));
}

async function fetchPageData(browser, url) {
	const page = await browser.newPage({ viewport: { width: 1440, height: 1000 } });
	try {
		const resp = await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 }).catch(() => null);
		const status = resp ? resp.status() : 0;

		if (!resp || status >= 400) {
			return { status };
		}

		await page.waitForTimeout(600);

		const sections = await page.evaluate(() => {
			const main = document.querySelector('main') || document.body;
			const nodes = Array.from(main.children).filter(
				(el) => el.tagName !== 'SCRIPT' && el.tagName !== 'STYLE'
			);

			return nodes.map((el) => {
				// Widened selector list to catch more real title/date/excerpt
				// patterns. If NONE match, textEl stays null - do not fall back
				// to the section itself: comparing a container's own inherited
				// `color` produced false positives on sections with no direct
				// h1-h3/p descendant, where every actual visible text leaf sets
				// its own explicit colour and never uses the inherited value at
				// all (confirmed on cb-recent-news - every real title/date/
				// pre-title element had an identical explicit colour real vs
				// local, while the unused inherited section colour differed).
				const selector =
					'h1, h2, h3, p, .cb-testimonial__quote, [class*="title"], [class*="date"], [class*="excerpt"]';
				const textEl = el.querySelector(selector);
				const cs = getComputedStyle(el);
				const textCs = textEl ? getComputedStyle(textEl) : null;
				// All matches, not just the first: a style bug on the 2nd+
				// matching element (e.g. the Nth item title in a repeated nav/
				// list block) is invisible to a single-match check - confirmed
				// missed this way on cb-services-nav__item-title (identity's
				// h3 item titles past the first "SERVICES" h2 header were
				// never sampled, so a missing SCSS rule for them went
				// undetected through every previous sweep). Capped at 30 to
				// keep payloads sane on long lists.
				const allTextEls = Array.from(el.querySelectorAll(selector)).slice(0, 30).map((e) => {
					const ecs = getComputedStyle(e);
					return {
						classes: e.className,
						text: (e.innerText || '').trim().replace(/\s+/g, ' ').slice(0, 100),
						color: ecs.color,
						fontSize: ecs.fontSize,
						fontWeight: ecs.fontWeight,
					};
				});
				return {
					tag: el.tagName,
					classes: el.className,
					// innerText (not textContent) - respects rendering rules, so
					// hidden nodes (e.g. <style> legitimately nested inside an
					// inline <svg>, never visually rendered) don't produce
					// false-positive diffs.
					text: (el.innerText || '').trim().replace(/\s+/g, ' ').slice(0, 200),
					bg: cs.backgroundColor,
					borderTopColor: cs.borderTopColor,
					borderBottomColor: cs.borderBottomColor,
					color: textCs ? textCs.color : null,
					fontSize: textCs ? textCs.fontSize : null,
					fontWeight: textCs ? textCs.fontWeight : null,
					allTextEls,
				};
			});
		});

		return { status, sections };
	} finally {
		await page.close();
	}
}

// Whitespace and class ORDER are not meaningful in CSS (confirmed as a
// real false-positive source: trailing space in a class attribute string,
// and cb-pushthrough's intentionally-dual-named classes in either order).
// Renames/additions/removals of an actual class name still get caught.
function normalizeClasses(classes) {
	return (classes || '').split(/\s+/).filter(Boolean).sort().join(' ');
}

function diffPages(pathName, realUrl, localUrl, real, local) {
	if (real.status === 0 || real.status >= 400) {
		if (local.status && local.status < 400) {
			return { path: pathName, realUrl, localUrl, verdict: 'local-only', note: `real returned ${real.status}, local returned ${local.status}` };
		}
		return { path: pathName, realUrl, localUrl, verdict: 'both-missing', note: `real ${real.status}, local ${local.status}` };
	}
	if (local.status === 0 || local.status >= 400) {
		return { path: pathName, realUrl, localUrl, verdict: 'flag', diffs: [`local returned ${local.status} but real returned ${real.status}`] };
	}

	const diffs = [];
	const maxLen = Math.max(real.sections.length, local.sections.length);

	if (real.sections.length !== local.sections.length) {
		diffs.push(`section count differs: real=${real.sections.length} local=${local.sections.length}`);
	}

	for (let i = 0; i < maxLen; i++) {
		const r = real.sections[i];
		const l = local.sections[i];
		if (!r) {
			diffs.push(`section[${i}]: extra on local (${l.tag}.${l.classes})`);
			continue;
		}
		if (!l) {
			diffs.push(`section[${i}]: missing on local (real has ${r.tag}.${r.classes})`);
			continue;
		}
		if (normalizeClasses(r.classes) !== normalizeClasses(l.classes)) {
			diffs.push(`section[${i}] classes: real="${r.classes}" local="${l.classes}"`);
		}
		if (r.bg !== l.bg) {
			diffs.push(`section[${i}] (${r.classes}) background: real=${r.bg} local=${l.bg}`);
		}
		if (r.color !== l.color) {
			diffs.push(`section[${i}] (${r.classes}) text color: real=${r.color} local=${l.color}`);
		}
		if (r.fontSize !== l.fontSize) {
			diffs.push(`section[${i}] (${r.classes}) font-size: real=${r.fontSize} local=${l.fontSize}`);
		}
		if (r.fontWeight !== l.fontWeight) {
			diffs.push(`section[${i}] (${r.classes}) font-weight: real=${r.fontWeight} local=${l.fontWeight}`);
		}
		if (r.text !== l.text) {
			diffs.push(`section[${i}] (${r.classes}) text differs:\n    real:  ${r.text}\n    local: ${l.text}`);
		}

		const rEls = r.allTextEls || [];
		const lEls = l.allTextEls || [];
		const elMax = Math.max(rEls.length, lEls.length);
		for (let j = 0; j < elMax; j++) {
			const re = rEls[j];
			const le = lEls[j];
			if (!re || !le) continue; // count mismatch already implied by text differs above
			if (re.fontSize !== le.fontSize) {
				diffs.push(`section[${i}] textEl[${j}] (${re.classes}) font-size: real=${re.fontSize} local=${le.fontSize} ("${re.text}")`);
			}
			if (re.fontWeight !== le.fontWeight) {
				diffs.push(`section[${i}] textEl[${j}] (${re.classes}) font-weight: real=${re.fontWeight} local=${le.fontWeight} ("${re.text}")`);
			}
			if (re.color !== le.color) {
				diffs.push(`section[${i}] textEl[${j}] (${re.classes}) color: real=${re.color} local=${le.color} ("${re.text}")`);
			}
		}
	}

	return {
		path: pathName,
		realUrl,
		localUrl,
		verdict: diffs.length ? 'flag' : 'pass',
		diffs,
	};
}

run();
