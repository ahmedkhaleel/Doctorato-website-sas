#!/usr/bin/env node
/**
 * Performance budget gate for the Vite bundle.
 *
 * Reads public/build/manifest.json, sums up the on-disk size of every
 * file referenced from a top-level entry, and fails CI if the totals
 * cross the budgets defined below.
 *
 * Why we don't use a fancier tool (size-limit, bundlewatch, etc.):
 *   - No new dev dependency. The project's been disciplined about that
 *     because the production cPanel host has had repeated extension/
 *     dep-resolution issues.
 *   - Node's stdlib is enough — read manifest, fs.statSync, sum, exit.
 *
 * Budgets are set with ~25 % headroom over the current size so genuine
 * feature growth doesn't break CI on every PR. Tighten them after a
 * deliberate optimisation pass, not before.
 *
 * Usage:
 *   node scripts/check-bundle-size.mjs               # default budgets
 *   node scripts/check-bundle-size.mjs --report      # print sizes, no fail
 */
import { readFileSync, statSync, readdirSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const projectRoot = join(__dirname, '..');
const buildDir = join(projectRoot, 'public/build');
const manifestPath = join(buildDir, 'manifest.json');

/**
 * Budgets in bytes. We don't budget on "total JS across all chunks"
 * because Vite code-splits per-route — the browser only downloads
 * the entry + the chunks for the current page, never the lot.
 *
 * What we DO budget:
 *   - entry_js   = the main app.js (shipped on every page load)
 *   - single_js  = any non-entry chunk; catches a route page bloat
 *                  before it lands on customers
 *   - css_total  = entry CSS file (Tailwind output)
 *
 * Current as of Phase 21 (May 2026):
 *   app.js  ≈ 363 KB
 *   biggest non-entry chunk ≈ 110 KB
 *   app.css ≈ 280 KB
 */
const BUDGETS = {
    entry_js: 450 * 1024,   // app.js — 450 KB, ~25% headroom
    single_js: 200 * 1024,  // any non-entry chunk
    css_total: 320 * 1024,  // 320 KB
};

const reportOnly = process.argv.includes('--report');

let manifest;
try {
    manifest = JSON.parse(readFileSync(manifestPath, 'utf8'));
} catch (e) {
    console.error(`Cannot read ${manifestPath}.`);
    console.error('Run \`npm run build\` first.');
    process.exit(1);
}

const sizes = { js: 0, css: 0, perChunk: [], entryJs: 0, entryCssFile: null };

// Discover the entry chunk(s) from the manifest — anything marked
// `isEntry: true` is the file the browser loads first.
const entryFiles = new Set();
for (const entry of Object.values(manifest)) {
    if (entry.isEntry && entry.file) entryFiles.add(entry.file);
}

// Walk the manifest, statting every referenced file. We also walk the
// raw build dir for orphans (vendor chunks Vite splits out) so the
// total reflects what the browser actually downloads.
const seen = new Set();

function addFile(rel) {
    if (seen.has(rel)) return;
    seen.add(rel);
    const full = join(buildDir, rel);
    let size = 0;
    try { size = statSync(full).size; } catch { return; }

    if (rel.endsWith('.js')) {
        sizes.js += size;
        sizes.perChunk.push({ name: rel, size, isEntry: entryFiles.has(rel) });
        if (entryFiles.has(rel)) sizes.entryJs += size;
    } else if (rel.endsWith('.css')) {
        sizes.css += size;
    }
}

for (const entry of Object.values(manifest)) {
    if (entry.file) addFile(entry.file);
    for (const css of entry.css ?? []) addFile(css);
    for (const imp of entry.imports ?? []) {
        const ref = manifest[imp];
        if (ref?.file) addFile(ref.file);
    }
}

// Also include any orphan assets in the assets dir so a sneaky big
// vendor chunk doesn't slip past.
const assetsDir = join(buildDir, 'assets');
try {
    for (const f of readdirSync(assetsDir)) {
        if (f.endsWith('.js') || f.endsWith('.css')) {
            addFile(`assets/${f}`);
        }
    }
} catch {}

const kb = (n) => (n / 1024).toFixed(1) + ' KB';
const pct = (n, b) => ((n / b) * 100).toFixed(0) + '%';

const nonEntryJs = sizes.perChunk.filter((c) => !c.isEntry);
const biggestNonEntry = nonEntryJs.sort((a, b) => b.size - a.size)[0];

console.log('─── Bundle size ──────────────────────────────');
console.log(`  Entry JS    : ${kb(sizes.entryJs).padStart(10)}  (budget ${kb(BUDGETS.entry_js)}, ${pct(sizes.entryJs, BUDGETS.entry_js)})`);
console.log(`  CSS total   : ${kb(sizes.css).padStart(10)}  (budget ${kb(BUDGETS.css_total)}, ${pct(sizes.css, BUDGETS.css_total)})`);
if (biggestNonEntry) {
    console.log(`  Biggest split: ${kb(biggestNonEntry.size).padStart(10)}  ${biggestNonEntry.name}`);
    console.log(`                  (budget ${kb(BUDGETS.single_js)}, ${pct(biggestNonEntry.size, BUDGETS.single_js)})`);
}

const biggest = sizes.perChunk.sort((a, b) => b.size - a.size).slice(0, 5);
console.log('\n  Top 5 JS chunks:');
for (const c of biggest) {
    const flag = c.isEntry ? ' [entry]' : '';
    console.log(`    ${kb(c.size).padStart(10)}  ${c.name}${flag}`);
}
console.log('──────────────────────────────────────────────');

if (reportOnly) {
    process.exit(0);
}

const failures = [];
if (sizes.entryJs > BUDGETS.entry_js) {
    failures.push(`Entry JS ${kb(sizes.entryJs)} exceeds budget ${kb(BUDGETS.entry_js)}`);
}
if (sizes.css > BUDGETS.css_total) {
    failures.push(`CSS total ${kb(sizes.css)} exceeds budget ${kb(BUDGETS.css_total)}`);
}
const oversizedNonEntry = nonEntryJs.find((c) => c.size > BUDGETS.single_js);
if (oversizedNonEntry) {
    failures.push(
        `Chunk ${oversizedNonEntry.name} is ${kb(oversizedNonEntry.size)} ` +
        `(single non-entry chunk budget ${kb(BUDGETS.single_js)})`
    );
}

if (failures.length) {
    console.error('\n✗ Performance budget exceeded:');
    for (const f of failures) console.error(`  - ${f}`);
    console.error('\nFix options:');
    console.error('  - lazy-load the offending route via dynamic import()');
    console.error('  - check vite.config.js manualChunks for accidental bundling');
    console.error('  - revisit the budgets in scripts/check-bundle-size.mjs');
    process.exit(1);
}

console.log('✓ Bundle within budget');
