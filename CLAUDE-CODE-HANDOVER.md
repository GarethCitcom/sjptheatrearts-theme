# SJP Theatre Arts - WordPress Theme Build Brief

This is the kickoff prompt for building the SJP Theatre Arts public website as a custom
WordPress block theme. Read this whole document before writing any code, then read the
source materials listed below in the order given. When this brief conflicts with an older
document, this brief wins.

Your first action after reading the sources: create a `CLAUDE.md` in the repo root that
distils the conventions in this brief (tokens, naming, build commands, review workflow),
then present a phased implementation plan for approval before scaffolding.

---

## 1. Project summary

SJP Theatre Arts (formerly Bromsgrove School of Dance) is a dance and theatre school in
Bromsgrove, Worcestershire. The site's job: take a parent from "my child wants to dance"
to an enrolment enquiry with as little doubt as possible. "Enrol now" is the single
primary action sitewide (there is no free trial; it was removed at the client's request).

Nine pages: Home, Classes, Class Detail (template), Timetable & Fees, About, Performances,
Join, Contact, plus legal/utility routes. A separate member portal exists on the client's
current WordPress + WooCommerce install; this site links out to it but does not contain it.

Two goals outrank everything else: **performance** (Core Web Vitals) and **SEO**.

## 2. Source materials and precedence

All of these live in the project working folder. Copy `design-src/` into the build repo
as `/design-reference/` (read-only; never import its code into the theme).

Read in this order. Where they conflict, earlier in this list wins:

1. **`design-src/README.md`** - the master handoff spec. Page-by-page section breakdowns,
   exact design tokens, interaction specs, asset notes. Treat its token tables as law.
2. **`design-src/screenshots/*.png`** - visual ground truth at 1440px, plus the mobile set.
3. **`design-src/*.dc.html`** - the design prototypes. Use them to read exact values
   (they are fully inline-styled) and copy text. They are design references, NOT
   production code. Do not port `support.js`. Two pieces of review scaffolding must not
   be built: the "Return to design options" pill and the sub-1024px purple blocker.
4. **`PRODUCT.md`** - audience, confirmed class facts, and the list of facts that are
   awaiting client confirmation (never invent these).
5. **`SJP-Website-Information-Architecture-and-Content-Strategy.md`** - IA and content strategy.
6. **`DESIGN.md`** - the earlier design-system doc. Superseded by `design-src/README.md`
   wherever they differ. Known differences: the approved build uses **Montserrat** for
   body/UI text (not DM Sans), page background `#F7F6F9` (not warm paper), and
   "Enrol now" (not "Book a trial").

Assets: `design-src/assets/` (prefer `assets/new-media/web/*.jpg`), brand SVGs in
`design-src/assets/` and the project `logo/` folder. All photography is the client's own.

## 3. Locked architecture decisions

These are decided. Do not relitigate them, but flag concerns if you see a real problem.

- **Block theme (FSE)**: `theme.json` v3+, HTML templates in `templates/`, template
  parts in `parts/`, patterns in `patterns/`. Target WordPress 6.8+ (build compatible
  with 7.x), PHP 8.1+.
- **ACF Pro is installed.** Bespoke, data-driven sections are ACF blocks registered via
  `block.json` with PHP render templates. ACF field groups are stored as JSON in the
  theme's `acf-json/` directory so they version in git.
- **Core blocks first.** Where a section is expressible with styled core blocks (group,
  columns, heading, paragraph, buttons, image, details) inside a locked pattern, do that.
  Reserve custom ACF blocks for genuinely dynamic or complex sections. Fewer, better
  blocks beats a custom block per design section.
- **Templates are locked down.** Use `templateLock` and pattern `contentOnly` editing so
  the client can edit text and images but cannot break layout.
- **No page builders, no CSS frameworks, no jQuery on the frontend.**
- **Tempo Book It integration**: the timetable is pulled over REST from the member
  portal install at `https://book.sjptheatrearts.co.uk/`, which runs the Tempo Book It
  plugin (repo: `https://github.com/GarethCitcom/tempo-book-it.git`, available locally -
  inspect it to confirm routes and response shapes before writing the client). Scope:
  **the Timetable & Fees page only**. Class detail pages keep manually entered schedule
  fields in ACF. Full spec in section 7.
- **Hosting: Unlimited Web Hosting, Agency Hosting 10** (cPanel). Redis object caching,
  AccelerateWP server-level caching, PHP X-Ray, MultiPHP, WP-CLI and SSH are available.
  Caching strategy in section 9.

## 4. Theme structure

```
sjp-theatre-arts/
├── style.css                 # theme header only
├── theme.json                # tokens: palette, type scale, spacing, layout
├── functions.php             # thin bootstrap; require files from inc/
├── inc/
│   ├── setup.php             # theme supports, menus, image sizes
│   ├── enqueue.php           # fonts, per-block styles, motion.js
│   ├── blocks.php            # ACF block registration (scan blocks/ dir)
│   ├── post-types.php        # class CPT, discipline + age-group taxonomies
│   ├── tempo/                # Tempo Book It REST client (section 7)
│   │   ├── class-tempo-client.php
│   │   └── class-tempo-cache.php
│   ├── forms.php             # enquiry form handler + routing
│   ├── seo.php               # JSON-LD schema output
│   └── performance.php       # head cleanup, resource hints, image handling
├── blocks/                   # one folder per ACF block: block.json, render.php, style.css, view.js (rare)
├── templates/                # index, front-page, page, single-sjp-class, archive-sjp-class, 404
├── parts/                    # header (utility bar + floating nav), footer, cta-band
├── patterns/                 # page-assembly patterns per README section breakdowns
├── assets/
│   ├── fonts/                # self-hosted woff2: Poppins 600/700, Montserrat 400/500/600/700
│   ├── images/               # theme-owned decorative assets only; photos go in the media library
│   └── js/motion.js          # idiomatic port of design-src/motion.js
└── acf-json/                 # ACF field group JSON (auto-synced)
```

## 5. Design tokens → theme.json

Map the token tables in `design-src/README.md` directly into `theme.json`:

- **Palette**: register every named colour from the README colour table (brand orange
  `#FE7300`, hover `#e56800`, brand purple `#381064`, deep purple `#2A0B4D`, darkest
  purple `#1D0B36`, magenta `#C5299B`, page background `#F7F6F9`, surfaces, borders,
  text tones, success green, and the four badge pairs). Slugs kebab-case, e.g.
  `brand-orange`, `deep-purple`, `text-muted`.
- **Typography**: self-hosted Poppins (600, 700) and Montserrat (400, 500, 600, 700),
  woff2 only, latin subset, `font-display: swap`, registered via theme.json `fontFace`.
  Build the type scale from the README table using `fluid` typography so the 76px
  homepage H1 and 52-58px page H1s scale down cleanly to the 390px mobile designs.
  Keep `text-wrap: balance` on headings and `text-wrap: pretty` on body copy.
- **Spacing**: spacing scale covering the 12/16/22/24/32/56-150px rhythm; content width
  1320px with 32px side padding via `layout.contentSize`.
- **Radii and shadows**: custom properties for the radius family (10/12/16/28/999) and
  the two shadow recipes, via `settings.custom` so blocks reference
  `var(--wp--custom--radius--card)` etc. Never hard-code a hex or radius in block CSS
  when a token exists.

## 6. Content model

**CPT `sjp-class`** (public, `show_in_rest`, `has_archive` → the Classes page).
ACF fields: intro, description, "what happens in a class", "where it can lead",
age range, days/times text (manual; Tempo does not drive this page), teachers
(repeater or relationship), kit list ("what to wear"), beginners-welcome flag,
hero image, gallery, related classes (relationship, falls back to same-discipline).
Taxonomies: `discipline`, `age-group` (drives the Classes filter UI).

**Class content source: `class-info-answers.json`** (working folder root; copy the
current version into the repo under `data/`). It is the export of the client's
class-information form and is a **living file**: the client is still completing it and
Gaz will refresh it, so always ask for the latest copy before importing. Shape: a top-level
object with `answers` keyed by class slug (15 slugs: `baby-yoga`, `parent-toddler`,
`first-steps`, `second-steps`, `ballet`, `jazz-commercial`, `tap`, `acro-cheer`,
`musical-theatre`, `drama`, `singing`, `troupes`, `ballroom-latin`, `adult-ballet`,
`wedding-dance`), each holding fields such as `desc`, `tags`, `days`, `dayTimes`,
`teachers`, `level`, `who`, and `steps` (title/text pairs for "what happens in a class"),
plus an `updatedAt` timestamp. Inspect the full file before designing the ACF mapping.
Write the WP-CLI import script as an **idempotent upsert keyed on slug** so re-running it
with a fresh export updates content without duplicating posts or destroying
manually-added editorial fields (images, related classes).

**Options pages** (ACF): Site settings (contact email, Instagram URL defaulting to
`https://www.instagram.com/sjp_theatre_arts/`, member portal URL defaulting to
`https://book.sjptheatrearts.co.uk/` - this replaces every `#member` placeholder,
address block with per-field "to confirm" state), CTA band copy per page context,
Tempo settings (section 7), fees table rows with an "awaiting confirmation" state.

Everything marked "To confirm before launch" in the designs must be an editable field
with an honest placeholder state, never hard-coded placeholder text.

## 7. Tempo Book It timetable integration

The live timetable is served by the Tempo Book It plugin's REST API on the member
portal install. **Confirmed endpoint**:

```
GET https://book.sjptheatrearts.co.uk/wp-json/dsbook/v1/timetable
```

The route is live and publicly readable (no auth observed) but the feed is still being
populated. It currently returns this skeleton, which defines the response envelope:

```json
{ "timezone": "Europe/London", "term": null, "half_term": null,
  "exclusions": [], "terms": [], "classes": [] }
```

Before coding, read the plugin source (local clone of `GarethCitcom/tempo-book-it`) to
confirm the populated shape of `classes`, `terms`, `half_term` and `exclusions`, and
document the schema in `inc/tempo/README.md`. Honour the feed's `timezone` and render
term/half-term/exclusion context where the design calls for it. An empty `classes`
array is a normal state (feed in progress): render the designed "to confirm" timetable
state, not an error. Then build:

- **Settings** (ACF options): base URL (default
  `https://book.sjptheatrearts.co.uk`), API key/auth field left empty unless the plugin
  source shows the endpoint gains auth later, cache TTL (default 15 minutes).
- **`Tempo_Client`**: `wp_remote_get` with a 5s timeout, response validation, typed
  mapping into a plain timetable array (day → sessions with class name, time, ages,
  teacher). Never trust or echo remote data unescaped.
- **Caching, two layers**:
  1. Transient (TTL from settings) for the normal path.
  2. A "last known good" copy in an autoload-off option, updated on every successful
     fetch. If the remote is down or returns garbage, render the last good data with a
     quiet "timetable correct as of {date}" note. If there has never been a good fetch,
     render the designed "to confirm" state with the contact prompt. The page must
     never white-screen or block on the remote.
- **Server-side rendering only.** The timetable block renders PHP so the content is in
  the HTML for SEO and works without JavaScript. No client-side fetching for primary
  content.
- **Cron prewarm**: a WP-Cron event refreshes the cache on the TTL so no visitor ever
  pays the remote round-trip.
- Where a class in the Tempo feed matches a `sjp-class` post (match by slug/name, with
  a manual override field), link the timetable row to the class detail page.

## 8. Forms

Two forms: enrolment enquiry (Join page, also embedded on class detail with the class
pre-selected) and general contact. Build as an ACF block + REST or admin-post handler:

- Server-side validation (name, email, child's age required), inline error styles per
  the design, success state confirming who replies (class enquiries route to Lottie;
  safeguarding contact routes directly to SJ, never the general inbox).
- Spam: honeypot + time-trap minimum; Cloudflare Turnstile optional behind a setting.
- Route by enquiry type to the right recipient; log submissions to a CPT as backup.
- No sensitive safeguarding/medical data collected through open marketing forms.

## 9. Performance requirements

Budgets (mobile, throttled): **LCP < 2.5s, CLS < 0.1, INP < 200ms**, Lighthouse
performance and SEO ≥ 95 on every template. Concrete rules:

- **CSS**: theme.json output + per-block stylesheets registered with
  `wp_enqueue_block_style` so a page only loads CSS for blocks it uses. No global
  utility framework. Total CSS per page target < 60KB uncompressed.
- **JS**: one deferred vanilla `motion.js` (< 15KB) porting the design's reveal/stagger/
  lift/count behaviours with IntersectionObserver, honouring `prefers-reduced-motion`,
  failing open (content never hidden if JS dies). Ticker is pure CSS. The homepage
  canvas ribbon is decoration; implement only if it costs nothing, otherwise drop it,
  per the design README. Accordions are native `<details>`. No jQuery.
- **Fonts**: self-hosted woff2, subset, swap; preload only the two files used above the
  fold on the current template.
- **Images**: convert photography to correctly sized WebP before media import; upload at
  sensible max dimensions; explicit width/height everywhere (CLS); `srcset`/`sizes`
  from WP; `loading="lazy"` below the fold; hero image `fetchpriority="high"` and
  preloaded; poster images for any video, video never autoloads.
- **Head hygiene**: remove emoji script, trim unneeded core styles, sensible resource
  hints. Do not blanket-dequeue things a plugin needs.
- **Caching (Unlimited Web Hosting, Agency Hosting 10)**: the account includes **Redis
  object caching** and **AccelerateWP** server-level caching - use both rather than
  adding a third-party page-cache plugin. Enable the Redis object-cache drop-in
  (via AccelerateWP or the Redis Object Cache plugin pointed at the account's Redis
  socket), and enable AccelerateWP's page caching for the site. With a persistent
  object cache in play, the Tempo transients live in Redis, which is ideal.
  `.htaccess` rules for long-lived immutable caching of versioned static assets,
  gzip/brotli if not already handled server-side. Give the Timetable page a shorter
  page-cache TTL aligned with the Tempo TTL. PHP X-Ray is available on the host for
  profiling if a template is slow in production.
- Verify, don't assume: run Lighthouse (or Playwright + web vitals) against each
  finished template during the build and record results.

## 10. SEO requirements

- Semantic HTML from the templates: one H1 per page, landmark elements, real `<nav>`,
  descriptive link text (no bare "click here"), correct heading order.
- Meta titles/descriptions, canonicals, sitemap, robots via **SiteSEO Pro** (decided;
  included with the hosting). The theme must not fight the plugin: no duplicate title
  tags, OG output or sitemaps from the theme. Audit which modules SiteSEO Pro enables
  by default and turn off anything that overlaps with the theme's hand-rolled JSON-LD
  (below) so schema is emitted exactly once.
- **Structured data**, hand-rolled in `inc/seo.php` as JSON-LD:
  - Sitewide: `LocalBusiness` (performing-arts school) with address (once confirmed),
    `sameAs` Instagram, logo.
  - Class pages: `Course` with `CourseInstance` schedule from the ACF fields.
  - Performances: `Event` where real dates exist (never fabricate dates).
  - `BreadcrumbList` matching visible breadcrumbs on inner pages.
- Timetable and all primary content server-rendered (already guaranteed by section 7).
- Alt text on all content images (the class JSON and media import should carry alts).
- Internal linking: age route cards → class archive filters, related-classes block,
  every page ending on the CTA band.
- **Launch checklist**: 301 map from the current `sjptheatrearts.co.uk` URLs to the new
  structure, verify in Search Console, submit sitemap.

## 11. Accessibility

WCAG 2.2 AA. Non-negotiables from the design spec: visible plum focus states offset
from element edges, minimum 44px touch targets, contrast-checked text on every surface
(watch orange-on-white; the design pairs orange with plum or white deliberately),
skip link, labelled form fields with error text not colour alone, `prefers-reduced-motion`
respected by every animation, logical mobile reading order, keyboard-operable nav and
accordions.

## 12. Build order

Work in phases; stop for review at the end of each phase with screenshots compared
against `design-reference/screenshots/`:

1. **Scaffold + tokens**: theme skeleton, theme.json palette/type/spacing, fonts,
   CLAUDE.md, dev environment (wp-env or wp-playground), lint (phpcs + WPCS).
2. **Shared chrome**: utility bar, floating pill nav (sticky + condensing behaviour),
   footer, CTA band part with per-page copy. Desktop and mobile menu overlay.
3. **Homepage**: all sections per README order; this proves out the block/pattern system.
4. **Classes + Class Detail + CPT**: post type, taxonomies, ACF groups, archive and
   single templates, related classes, class JSON import script.
5. **Timetable & Fees**: Tempo client, cache layers, timetable block, fees table with
   awaiting-confirmation state.
6. **Remaining pages**: About, Performances (leave the video slot), Join, Contact.
7. **Forms**: handlers, routing, validation, spam protection.
8. **Performance + SEO pass**: budgets verified per template, schema, meta, redirect map.
9. **QA**: checklist in section 13.

## 13. Acceptance checklist

- Every template visually matches its screenshot at 1440px and its mobile design at
  390px (the desktop prototypes are not responsive; the mobile set defines small-screen
  layouts; you own the in-between breakpoints).
- Site fully usable with JavaScript disabled.
- All "to confirm" items render their honest editable placeholder state.
- Client can edit every piece of copy and imagery without touching layout.
- Timetable survives the Tempo site being offline.
- Lighthouse ≥ 95 performance/SEO/accessibility on Home, Classes, a class detail,
  and Timetable (mobile emulation).
- `phpcs` clean against WordPress coding standards; no PHP notices at `WP_DEBUG`.
- Theme activates cleanly on a fresh install (no fatal without ACF: guard with an
  admin notice if ACF Pro is missing).

## 14. Explicitly out of scope / do not build

- The member portal (link out only; URL is an editable setting defaulting to
  `https://book.sjptheatrearts.co.uk/`, replacing the `#member` placeholders).
- The review-tool scaffolding in the prototypes (return pill, small-screen blocker).
- WooCommerce anything.
- The Noticeboard and "BORN TO BE" (outside website scope).
- Invented content: fees, times, addresses, testimonials, qualifications, dates,
  response-time promises. If a fact isn't in the sources, it's an editable field with
  a "to confirm" state.

## 15. Resolved decisions and remaining open items

Resolved (do not re-ask):

- **Tempo endpoint**: `https://book.sjptheatrearts.co.uk/wp-json/dsbook/v1/timetable`,
  live but feed still being populated (section 7). Confirm the populated schema from
  the plugin source.
- **SEO plugin**: SiteSEO Pro, included with hosting (section 10).
- **Hosting**: Unlimited Web Hosting, Agency Hosting 10 - Redis + AccelerateWP
  available (section 9).
- **Instagram**: `https://www.instagram.com/sjp_theatre_arts/`.
- **Member portal**: `https://book.sjptheatrearts.co.uk/`.

Still open - raise with Gaz before the relevant phase:

1. **Before phase 4**: ask for the latest `class-info-answers.json` export (the client
   is still completing the form; the file in the repo may be stale).
2. **Before phase 5**: confirm the Tempo feed has real data, or build against the
   plugin source and the empty-state envelope.
3. Address, parking, step-free access and waiting-area details (Contact/About).
4. Performance videos (still to be compressed; leave the template slot).
5. Fees, term dates, and final legal documents (privacy, cookies, safeguarding, terms).
