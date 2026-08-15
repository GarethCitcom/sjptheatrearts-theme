# CLAUDE.md, SJP Theatre Arts block theme

Working conventions for this repo. The build brief is
[CLAUDE-CODE-HANDOVER.md](CLAUDE-CODE-HANDOVER.md) and **it wins over every other
document, including this one.** This file distils; it does not re-decide.

## Source precedence

When two sources disagree, the earlier one wins:

1. [CLAUDE-CODE-HANDOVER.md](CLAUDE-CODE-HANDOVER.md), the build brief
2. [design_handoff_sjp_website/README.md](design-reference/design_handoff_sjp_website/README.md), master design spec; its token tables are law
3. [design_handoff_sjp_website/screenshots/](design-reference/design_handoff_sjp_website/screenshots/), visual ground truth (1440px + the 390px mobile set)
4. [design-reference/*.dc.html](design-reference/), read exact values and copy from these; **never production code**
5. [design-reference/CLAUDE.md](design-reference/CLAUDE.md), the writing-style rule: **no em dashes anywhere in copy**, plain UK English, no puffery

6. [docs/PRODUCT.md](docs/PRODUCT.md), audience and confirmed facts
7. [docs/SJP-Website-Information-Architecture-and-Content-Strategy.md](docs/SJP-Website-Information-Architecture-and-Content-Strategy.md)
8. [docs/DESIGN.md](docs/DESIGN.md), superseded design system

The design bundle was replaced wholesale in phase 3c, not added to: the previous
one is now nested under `design_handoff_sjp_website/`, which is why entries 2 and
3 point inside it. Two things the master README gets wrong, because it predates
the prototypes: it never mentions Born To Be, and it still documents a five-item
nav with a 24px gap. All eight prototypes show six links (seven with Born To Be)
at a 20px gap. **Prototypes win.** There is no Born To Be screenshot, so that page
is compared against a Playwright render of its `.dc.html`.

Known supersessions to hold in mind, because the stale docs read convincingly:

| Stale claim | Where | Correct |
| --- | --- | --- |
| Body/UI font is DM Sans | DESIGN.md, PRODUCT.md | **Montserrat** |
| Warm paper page background | DESIGN.md | **`#F7F6F9`** |
| "Book a trial" is the primary action; trials are offered | PRODUCT.md | **"Enrol now"**. There is no trial, removed at the client's request |
| Timetable is derived from class data | design-reference/README.md | Timetable comes from the **Tempo feed**; class detail keeps manual ACF schedule fields |

Never port `design-reference/support.js`. Never build the "Return to design
options" pill or the sub-1024px purple blocker, both are review scaffolding.

## Non-negotiables

Four constraints outrank convenience on every ticket:

1. **Lighthouse ≥ 95** performance / SEO / accessibility on every template (mobile emulation).
2. **Fully usable with JavaScript off.** JS is enhancement only; nothing load-bearing.
3. **Never invent a fact.** Fees, times, addresses, dates, testimonials, qualifications,
   response times: if it is not in the sources, it is an editable field rendering an
   honest "to confirm" placeholder. See *Placeholder policy*.
4. **The client can edit all copy and imagery, and cannot break layout.** `templateLock`
   plus pattern `contentOnly` editing.

## Design tokens

Every value below comes from the design README token tables. **Never hard-code a hex,
radius, or shadow in block CSS when a token exists.** Slugs are kebab-case.

### Palette → `theme.json` `settings.color.palette`

| Slug | Hex | Role |
| --- | --- | --- |
| `brand-orange` | `#FE7300` | Primary action |
| `orange-hover` | `#e56800` | Orange hover |
| `orange-light` | `#FF9A3D` | Orange accent on dark |
| `brand-purple` | `#381064` | Headings, dark buttons |
| `deep-purple` | `#2A0B4D` | CTA panels |
| `darkest-purple` | `#1D0B36` | Utility bar, mobile menu |
| `magenta` | `#C5299B` | Tertiary accent, "to confirm" |
| `page-background` | `#F7F6F9` | Page background |
| `surface` | `#FFFFFF` | Card / surface |
| `card-border` | `#EAE3F0` | Card border |
| `inner-divider` | `#F0EBF5` | Inner divider |
| `section-rule` | `#E9E4F0` | Section rule |
| `nav-divider` | `#EEE7F4` | Nav divider |
| `text-body` | `#211B27` | Body text |
| `text-secondary` | `#5A5265` | Secondary text |
| `text-muted` | `#8A8194` | Muted / meta |
| `text-on-purple` | `#D9C8EC` | On purple |
| `text-on-purple-dim` | `#B49BD3` | On purple, dimmer |
| `success-green` | `#26CC8C` | Success |

Badge pairs (background / foreground), age badges, discipline pills, eyebrows:

| Slug pair | Background | Foreground |
| --- | --- | --- |
| `badge-purple-bg` / `badge-purple-fg` | `#EFE7F7` | `#6A3AA0` |
| `badge-orange-bg` / `badge-orange-fg` | `#FFE9D6` | `#B85400` |
| `badge-magenta-bg` / `badge-magenta-fg` | `#FAE6F4` | `#9C1F7A` |
| `badge-green-bg` / `badge-green-fg` | `#DFF7EC` | `#12855A` |

**Contrast, resolved (Gaz, phase 1).** Run `npm run contrast` before relying on any
pairing; it fails the build on an unwaived breach. Decisions taken:

| Token | Was | Now | Why |
| --- | --- | --- | --- |
| `text-muted` | `#8A8194` | **`#736C7E`** | 3.72 → 5.03 at 12px |
| `badge-orange-fg` | `#B85400` | **`#A34A00`** | 4.15 → 5.05 on its badge |
| `badge-green-fg` | `#12855A` | **`#0F7350`** | 4.12 → 5.20 on its badge |
| `orange-text` | *(new)* | **`#B85400`** | Orange link/text on light: 4.88. Replaces `brand-orange` for text, which was 2.74 |

`brand-orange` `#FE7300` is **unchanged** and remains the button/surface colour.
`success-green` `#26CC8C` is a decorative and state accent **only, never a text
colour**, at 1.85:1 on the green badge it fails even the large-text floor, so the
Join step-3 numeral uses `badge-green-fg`.

**Signed-off exceptions.** The "Enrol now" button ships white-on-orange exactly as
designed, at 2.74:1 (3.33:1 on hover). Gaz accepted this knowingly for brand fidelity
on the primary sitewide action.

Born To Be red `#F63631` is the second, on the same reasoning: white on it is
3.82:1, and the red on white or on the page background is 3.82:1 and 3.55:1. The
design's own hover red `#d92b26` passes at 4.86:1 if the waiver is ever revisited.

Measured consequence: templates carrying the button score **95** on Lighthouse
accessibility, the floor is met, but `color-contrast` itself audits as a hard fail
and the WCAG breach is real. The margin is zero, so any *other* accessibility defect
introduced later will push the category under 95. `npm run audit` therefore reports
the waived audit on every route even while the category passes.

The waiver lives in `WAIVED` in `tools/contrast.mjs` and in `EXPECTED` in
`tools/audit.mjs`; do not widen either without asking.

### Typography

Self-hosted **Poppins** 600/700 (headings, numerals, prices) and **Montserrat**
400/500/600/700 (everything else). woff2 only, latin subset, `font-display: swap`,
registered via `theme.json` `fontFace`. Never load Google Fonts.

| Use | Size / line-height / weight | Tracking |
| --- | --- | --- |
| Homepage H1 | 76px / 1.04 / Poppins 700 | `-.025em` |
| Page H1 (inner) | 54px / 1.05 / Poppins 700 | `-.025em` |
| Section H2, homepage (`section-lg`) | 46px / 1.12 / Poppins 700 | `-.02em` |
| Section H2, inner pages (`section-md`) | 40px / 1.12 / Poppins 700 | `-.02em` |
| Section H2 small (`section-sm`) | 25–36px / 1.15–1.2 / Poppins 700 | |
| Card H3 (`card-title`) | 17–20px / Poppins 600 | |
| Card H3 large, class cards (`card-lg`) | 24px / Poppins 600 | |
| Hero body | 18px / 1.65 / Montserrat 400 | |
| Page intro | 17px / 1.65 | |
| Body | 15–16px / 1.7–1.75 | |
| Card body | 14px / 1.6 | |
| Small print | 13px / 1.6–1.65 | |
| Meta / label | 12px / 600–700 | |
| Eyebrow (uppercase) | 11–12px / 700 | `.08–.12em` |

**The README's "38 to 46px" for section headings is not a viewport range, it is
the gap between the homepage and the inner pages at the same width.** Reading it
as a fluid range put every Born To Be heading 6px oversized, and the class card
title 12px oversized, before anyone compared the two renders side by side. The
scale gained `section-md` and `card-lg` to fix it. Before treating a range in
that table as responsive, check a prototype: it may be contextual.

Use `fluid` typography so the large sizes scale down to the 390px designs. Keep
`text-wrap: balance` on headings and `text-wrap: pretty` on body copy.

### Spacing, radii, elevation → `settings.custom`

- `layout.contentSize` **1320px**, side padding **32px**.
- Spacing scale covering the 12 / 16 / 22 / 24 / 32 / 56–150px rhythm. Section padding
  56–150px vertical; 150px only on the homepage hero (nav overlap).
- Gaps: 22px card grids · 16px sidebar stack · 12–14px pills, buttons, form fields.
- Card padding: 10px image cards (image inside the border) · 24–46px text cards.
- Radii → `var(--wp--custom--radius--*)`: `image` 10px · `small` 12px · `card` 16px ·
  `band` 28px · `pill` 999px.
- Shadows → `var(--wp--custom--shadow--*)`:
  - `nav` `0 14px 34px -18px rgba(33,11,60,.35)`
  - `frame` `0 18px 44px -22px rgba(29,11,54,.4)`
  - `header-rest` `0 4px 24px -12px rgba(33,27,39,.18)`
  - `header-stuck` `0 10px 30px -14px rgba(33,27,39,.32)`
- Borders: `1px solid #EAE3F0` cards · `1px solid #F0EBF5` inner dividers.
- **Control heights: 44px standard, 46px sidebar CTA, 52px hero buttons. Never below 44px**,
  this is both the design spec and the WCAG 2.2 touch-target floor.

### Motion constants (`assets/js/motion.js`)

Port idiomatically from `design-reference/motion.js`; do not copy it.

| Behaviour | Spec |
| --- | --- |
| Reveal | fade + rise 28px, `0.72s cubic-bezier(.22,.61,.36,1)`, IO `threshold: 0.04`, `rootMargin: 0px 0px -8% 0px`, 4s safety timer forces visible |
| Grid stagger | 70ms per child, max 12; sibling blocks 90ms, max 3 steps |
| Card lift | `translateY(-6px)` over `0.32s`, same easing |
| Count up | 0 → value over 900ms, cubic ease-out, at 60% visibility, `en-GB`, timeout guarantee |
| Condensing header | 88px → 72px past 24px scroll; shadow `header-rest` → `header-stuck` |
| Pulse | 2.6s expanding white ring; hover stops it and scales 1.06 |
| Ticker | **pure CSS** `@keyframes` translating `-50%`, 36s linear infinite, duplicated content |
| Accordion | native `<details>`; `[data-chevron]` rotates 180° when open |

Every animation respects `prefers-reduced-motion`. Motion **fails open**, content is
never left invisible if JS dies. Budget < 15KB, deferred, vanilla, no jQuery.

## Client decisions (Gaz)

Recorded so later phases don't relitigate them.

- **The homepage canvas ribbon is retained.** It is design-critical. This overrides
  brief §9 ("implement only if it costs nothing, otherwise drop it") and the design
  README's permission to drop it. Implementation constraints in *Hero ribbon* below,
  it must not cost us the mobile Lighthouse budget.
- **Musical Theatre stays a class**, and the "Born To Be" wording in its
  `class-info-answers.json` description is kept for now. Brief §14 lists "BORN TO BE"
  as out of scope; that applies to building it a *section of its own*, not to removing
  it from the class copy. A dedicated page may be added later, do not design the
  content model so that becomes hard.
- **Class count is 15** (Gaz, phase 4). `class-info-answers.json` holds fifteen
  classes and none is flagged `removed`. Still **do not hard-code the number**:
  derive it from published `sjp-class` posts, so the homepage stat and the
  "All N classes" button follow the data if the client drops or adds one.
- **There are two troupes, not three** (Gaz, phase 6). Blaze has gone; only
  Notorious and Shockwave remain, which is what the live timetable feed shows.
  The design prototypes, `data/class-info-answers.json` and `docs/PRODUCT.md` all
  named three, and every copy of it has been corrected. **The design reference
  still says Blaze**, so do not reinstate it when following those files.
- **Stats are derived wherever the build knows the answer** (Gaz, phase 6): the
  About page's "class styles taught" counts published classes, and the awarding
  bodies stat counts the logos actually shown. The prototype's 13 and 12 were
  written before either was settled. A stat nobody can derive, such as the number
  of troupes, stays an editable field.
- **"Madi" and "Madison Copson" are the same person** (Gaz, phase 4). The class
  data and the homepage team section say Madi; the Born To Be page says Madison
  Copson. Both are correct in their own place, so neither is "fixed" to match the
  other. Never guess a person's name onto a live page: this one was asked.

## Hero ribbon

Pointer-trailing spring ribbon on the homepage hero. Source to port:
`design-reference/SJP Homepage Alt.dc.html`, `componentDidMount` (~line 560).

Mechanics: 18-point spring chain, `segLen` 19, `maxW` 40, tapered both ends, filled
with a linear gradient `rgba(254,115,0,.55)` → `rgba(197,41,155,.42)` →
`rgba(106,58,160,.28)`. Follows the pointer; with no pointer it drifts on a lissajous
path. `pointer-events: none`, absolutely positioned, `inset: 0`.

Rules that keep it inside the performance budget:

| Context | Behaviour |
| --- | --- |
| Desktop, `(pointer: fine)` | Full animated ribbon |
| `prefers-reduced-motion: reduce` | The design's **own static pose**, the `if (reduced)` branch draws a fixed curve and returns. Do not invent a different still state |
| Touch / `(pointer: coarse)` | Static form only. No canvas, no rAF. The ribbon follows a pointer that does not exist, so animating it buys nothing and costs the mobile budget |
| No JS | Static form only |

- Render the static form as **inline SVG** so the reduced-motion, touch and no-JS
  paths cost zero JavaScript. The canvas upgrades over it.
- Run the rAF loop **only while the hero is in the viewport** (IntersectionObserver);
  cancel on exit. The prototype runs it forever, do not copy that.
- Ship as its own deferred script enqueued on the front page only. It is **not** part
  of `motion.js` and must not eat into its 15KB budget.
- Cap DPR at 2 (as the prototype does) and re-measure on `ResizeObserver`.

## Mobile menu: the summary *is* the close button

The overlay is a native `<details>`. The `<summary>` is the hamburger when closed
and, when open, is repositioned over the overlay as the design's circular X
(`top: 19px; right: 19px; 44×44`). Closing is therefore native browser behaviour.

**Do not add a separate close control.** A `<button>` or `<span>` in the panel is
inert without JavaScript, and the panel is `position: fixed; inset: 0`, which covers
the summary in its resting position, so a no-JS or keyboard user would be trapped
in the menu with no way out. That bug shipped once; the shape above is the fix.

When testing any disclosure, test that it **closes**, not just that it opens, with
JavaScript both on and off.

## ACF block editing model

Verified against ACF Pro 6.8.7 source. Three facts govern everything:

1. `pro/blocks.php:761`: `$form = ( 'edit' === $mode && $is_preview )`, only
   `mode: "edit"` renders the form; **"auto" is silently treated as preview.**
2. The editor JS hides the mode toggle for any block whose `mode` attribute is
   `"auto"` (`("auto"===c||s)&&(p=!1)`).
3. The same code locks **every** v2 block to preview and hides the toggle
   whenever the canvas is iframed:
   `I(){return document.querySelectorAll('iframe[name="editor-canvas"]').length>0}`.
   WordPress iframes the canvas when every registered block declares
   `apiVersion` 3, so an ACF v2 block declared as `apiVersion: 3` can NEVER
   show its edit form. This is why ACF registers its own v2 blocks as
   apiVersion 2.

**Consequence: every ACF block's block.json must declare `"apiVersion": 2`.**
That keeps the page editor non-iframed, which is what makes the toolbar
**Switch to Edit** toggle appear and the full-canvas-width form work. The
chrome blocks (site-header, site-footer, cta-band) carry no ACF form and stay
apiVersion 3. The site editor is always iframed regardless, there, ACF blocks
are preview + sidebar by design, and `editor-ui.css` widens the sidebar to
480px whenever it contains ACF fields. There is no ACF modal API; do not build
one by relocating ACF's React DOM.

Block previews in the canvas must be inert: `editor.css` sets
`pointer-events: none` on links/buttons/summaries inside previews (scoped to
`.acf-block-preview` for ACF blocks so edit-mode forms stay interactive).
Without it, clicking a card navigated the canvas to the live front end.

Two rules that keep editors working:

1. Seed real `data` (values + `_field` key shadows) into every programmatic
   block, empty `data` shows blank inputs against a populated front end *and*
   trips a PHP 8.3 deprecation inside ACF (`key()` on an empty array).
2. The `block` location rule matches the **registered block name**
   (`sjptheatrearts/hero`), never an `acf/…`-prefixed slug, the wrong name
   fails silently and every block reports "no editable fields".

## Stacking and motion rules (hard-won)

- The hero decor layer's `z-index: -1` must resolve against the **root**
  stacking context. Never give `.sjpta-hero` `isolation` or a z-index: trapping
  the -1 inside the hero paints the ribbon *over* the static age cards, and a
  hover transform then flips the paint order per card.
- The reveal rule's `transition` list must include `transform` (at the lift
  duration). Transitions are all-or-nothing per element, and the reveal
  selector out-specifies component rules, omitting transform there kills every
  hover lift's easing on the front end while the editor (no motion.js) looks
  fine. Test hovers on the front end, not the editor.

## Enquiry form placement (Gaz, phase 3b)

The homepage carries a **real inline form**, in the join teaser, using the same
renderer and handler as every other form on the site. A visitor who simply
scrolls the homepage can ask without navigating anywhere.

**Enquire and enrol are two different things, and every form says which it is**
(Gaz, 2026-08). The homepage panel and the class pages carry an *enquiry* form
(type `enquiry`, panel title "Enquire now"), with a "Ready to enrol? Enrol now"
button under it that goes to the Join page and carries across whatever has been
typed (`?pf_<field>=` in the URL, read by `sjpta_enquiry_prefill()`; a class
page passes `pf_class_want`). The Join page carries the *enrolment* form (type
`enrolment`, button "Send my enrolment") with the reverse under it: "Want to
talk to us first? Contact us". The nav's "Enrol now" and a class page's hero
"Enrol now" both go to the Join page; a class page's "Ask a question" scrolls
to its own enquiry form. Never send someone to a form called one thing to do
the other.

## CSS pipeline and the page budget

Stylesheets are authored to be read; `npm run css` writes a `.min.css` beside
each source, and `inc/enqueue.php` swaps it in at request time, both for
`<link>`s (`style_loader_src`) and for the copies WordPress inlines
(`wp_maybe_inline_styles` reads `path`, rewritten on `wp_head` priority 0,
because block styles are not all registered during `wp_enqueue_scripts`).
A checkout that never runs the build still works, just heavier.
**Run `npm run css` after editing any stylesheet, and commit the `.min.css`**,
the theme must deploy without a build step.

**Colours: palette vs custom.** Every `settings.color.palette` entry costs three
generated utility classes (`.has-X-color` and friends) that we never use, 8KB on
every page for 28 colours. So the palette holds only the nine an editor might
legitimately pick, and the other nineteen live in `settings.custom.color`, which
emits the variable without the classes. Consequence for new CSS:
**`--wp--preset--color--*` for those nine, `--wp--custom--color--*` for the rest.**
Adding an internal colour means adding it to `settings.custom.color`, not the
palette.

Totals after phase 3c: homepage **57KB**, Born To Be **56KB**, both inside the
60KB target. Born To Be carries twelve sections and would have been about 70KB
without the shared section furniture below. The residue is core's own output,
`global-styles` 13.8KB and `wp-block-library` 3.6KB.

Local Lighthouse carries a 350 to 600ms TTFB with no opcache, object cache or
page cache; production has Redis and AccelerateWP, so re-measure there in phase 8
before chasing anything further. Expect local scores to swing a point or two
between runs, and never diagnose a regression from a single sample.

**Shared section furniture.** `.sjpta-inner`, `.sjpta-eyebrow`, `.sjpta-h2` and
`.sjpta-sectionhead` live in `primitives.css`; a block adds the shared class in
the markup and keeps its BEM class only for what it genuinely overrides. On a
twelve-section page the duplicated copies were roughly 4KB.

If you ever automate that extraction, note the trap it set: a block's `__inner`
rule is often also its grid container, and a naive "delete the whole rule" pass
silently collapsed four two-column layouts into one. Pixel-compare after any
CSS refactor; the stylesheet still minified and `phpcs` still passed.

**A block rule can lose to `primitives.css` at equal specificity.** The two
entries in `"style": [ "file:./style.css", "sjpta-primitives" ]` are siblings,
not dependencies, so WordPress prints them in that order: the block's own
stylesheet first, primitives after. Most block styles are small enough to be
inlined further down the head, which puts them after primitives and hides the
problem, but the larger ones stay as linked files and print before it.
`site-header` is the one that does, and four of its declarations were dead:
`.sjpta-pill__cta { display: none }` lost to `.sjpta-btn { display: inline-flex }`
so the mobile header kept showing a button meant to be hidden, and
`.sjpta-menu__cta` lost `display`, `min-height` and `font-size`, rendering at the
44px default instead of the designed 52px.

**So: when overriding anything from `primitives.css`, give the block rule more
weight** (scope it to a parent) rather than trusting source order. Reordering the
arrays globally would fix it at the root but flips every equal-specificity
collision on the site at once, which is not a change to make casually.
`build/cascade-audit.mjs` walks the live cascade on every page at both widths and
reports any theme rule that loses to a primitive; run it after touching either.

**Hero decoration fades with a painted gradient, not a `mask`.** Inner-page
heroes hang 500 to 560px glows off the top edge and clip them with
`overflow: hidden` (needed: the right-hand glow is inset -200px and would
otherwise cause horizontal scroll). On a 350 to 480px hero that clip leaves a
hard horizontal line. The fix is a `::after` wash of the page background over the
bottom 45%, not a `mask-image` on the decor layer: the mask reads better in
source but forces the full-width decoration into an offscreen compositing pass on
every paint, which cost Born To Be about five Lighthouse points (90 against 97).
The content container needs `z-index: 1`, since `::after` is generated last and
otherwise paints over the heading.

## Accent roles and the Born To Be page

Colour is named by **role**, not by brand. `assets/css/primitives.css` declares
seven custom properties whose defaults alias the SJP tokens, so nothing renders
differently until a page opts in:

| Token | SJP | Born To Be | Used for |
| --- | --- | --- | --- |
| `--sjpta-accent` | `brand-orange` | `bt-red` | Buttons, dots, icon tiles |
| `--sjpta-accent-rgb` | `254 115 0` | `246 54 49` | Glows and dot grids |
| `--sjpta-accent-hover` | `orange-hover` | `bt-red-hover` | Button hover |
| `--sjpta-accent-text` | `orange-text` | `bt-red` | Accent **text**: links, eyebrows |
| `--sjpta-accent-light` | `orange-light` | `bt-red-light` | Accent on dark |
| `--sjpta-accent-2` | `magenta` | `bt-violet` | Link hover, second badge tone |
| `--sjpta-accent-3` | `orange-light` | `bt-lilac` | Third decorative colour. **Never text** |

**Write `var(--sjpta-accent)`, not a brand token, in any block that could appear
on either brand's page.** A block styled by role renders orange when it is moved
back to an SJP page; one styled by brand does not.

The override is six declarations on `.sjpta-theme-btb` in
`assets/css/accent-born-to-be.css`, enqueued only by `is_page( 'born-to-be' )`.
The class comes from `templates/page-born-to-be.html`, which also selects the
Born To Be header part and omits the closing CTA band. **Both the template and
the stylesheet key off the same slug, so renaming the page silently reverts it to
orange with a CTA band appended.** That is the known trade for not adding a field
group for one page.

**The class goes on the header as well as `<main>`.** The floating nav, the
"Enquire" button, the utility dot and the padlock are all red in the design, and
the header is a sibling of `<main>` rather than a child, so scoping to `<main>`
alone left the entire top of the page orange while everything below it was red.
The footer deliberately stays SJP orange, which is why this is two class
attributes in the template rather than one on `<body>`.

That sibling relationship bites twice. `--sjpta-nav-band` (the height of the band
the floating pill sits in) is declared on `:root` rather than on the header, for
the same reason: custom properties inherit downwards only, and the hero is not
inside the header. The header pulls itself up by that amount on any page whose
hero carries **`.sjpta-underlap`**, and the hero adds it back as top padding, so
the hero's tint runs behind the nav instead of leaving a band of bare page
background that reads as a white gap. The design's own prototype has that seam;
it looks like a mistake, so it is fixed rather than reproduced.

**A new hero opts in with the marker class and the matching top padding**, both,
never one alone. The rule was originally keyed on `.sjpta-pagehero`, so the class
page's own hero took the padding without the pull-up and reopened exactly the gap
the mechanism exists to close. Keying on a shared marker rather than on one
block's name is what stops the next hero repeating it.

Born To Be's Instagram and Facebook are **site settings** (`btb_instagram_url`,
`btb_facebook_url`), not values typed into the template part, because the same
account is linked from three places on that page.

`assets/css/primitives.css` holds everything applied by a helper or shared across
blocks: `.sjpta-accent`, `.sjpta-btn` and its five variants, `.sjpta-badge`, the
photo collage, and the four-tone map (`.sjpta-tone--red/plum/amber/mint`, which
expose `--tone-bg`, `--tone-fg`, `--tone-solid`). Every `block.json` lists
`"style": [ "file:./style.css", "sjpta-primitives" ]`, so per-block loading still
applies. **Never define a shared class inside one block's stylesheet**, that bug
shipped once and only stayed invisible because every affected block happened to
render on the homepage.

`.sjpta-daypill` is in `primitives.css` for the same reason, and it got there the
hard way twice over: it was written inside the timetable's stylesheet, which
loads only on that page, so the gallery's category filters rendered as bare links
on Performances. **Before styling a control, check whether a second block already
wants it.**

Sections opt into scroll reveal with a `data-reveal` attribute (and `data-stagger`
on a child grid). The homepage's own sections are still named individually in
`motion.js`; new blocks should use the attribute.

Blocks render their own outer `<section>` rather than calling
`get_block_wrapper_attributes()`, so core's anchor support has nothing to attach
to. Use `sjpta_anchor_attr( $block )` to emit the `id`, or in-page links such as
`#classes` have no target.

## Image sizes and the LCP

`sjpta-480` and `sjpta-640` exist purely to give `srcset` finer steps: core jumps
300 → 768 → 1024, so a photo drawn at ~380 or ~520 CSS pixels downloaded the 768
variant either way. Worth 55KB above the fold on Born To Be.

`sjpta_preload_hero_image()` preloads the page hero's large photograph, **scoped
to `(min-width: 1024px)`**. Below that the hero stacks, the photograph falls below
the fold, and the LCP element is the intro paragraph, so an unconditional preload
does not help the LCP, it competes with the font that text is waiting on. Measure
which element is actually the LCP before preloading anything.

`sjpta_webp_quality()` re-encodes generated WebP sizes at 75 rather than core's
82. `npm run media` already writes WebP at 78, and re-encoding an
already-compressed image at a *higher* quality cannot restore detail; it only
spends bytes describing the first encoder's artefacts. Worth 35KB above the fold
on Born To Be. Uploaded JPEGs keep core's default, because those may be camera
originals with real detail to preserve. **After changing image sizes or quality,
run `$WP media regenerate`** or existing uploads keep the old derivatives.

## Galleries and the lightbox

`assets/js/lightbox.js` and `assets/css/lightbox.css`, registered as the handle
`sjpta-lightbox` and referenced from a gallery block's `block.json` (`style` and
`viewScript`), so they load only on a page that renders one. A new gallery block
opts in with `data-lightbox` on the grid and `sjpta_gallery_link()` per tile.

**Every thumbnail is a real link to its full-size file.** The script intercepts
the click; with it blocked, failed or disabled the link still opens the
photograph. A lightbox built on a click handler alone would simply do nothing
there, which fails the no-JavaScript rule.

Keyboard: Escape closes, left and right arrows move, Tab is trapped inside the
dialog, and focus returns to the thumbnail that opened it. The caption and the
`aria-label` come from the attachment's alt text, so **a gallery is only as
accessible as the alt text in the media library**.

A trap worth remembering: the backdrop and the close button both carry
`data-close`, so `querySelector('[data-close]')` returns the backdrop, and
calling `focus()` on a plain div silently does nothing. Focus stayed on the page
behind and the first Tab walked out of the dialog. Focus the button by its own
class.

**The video lightbox is the same shape for film.** `assets/js/video-lightbox.js`
and `assets/css/video-lightbox.css`, registered as `sjpta-video-lightbox` and
referenced from the experience block's `block.json`. The play button is a real
link to the first video file; the script intercepts it and opens a pop-up
player that starts the first video, runs each on into the next when it ends,
and offers a thumbnail rail to switch. The playlist travels as JSON in the
button's `data-video-lightbox` attribute, built from the block's `videos`
repeater (file, title, thumbnail). A block that grows a playlist later
references the same handles rather than shipping a second player.

**The homepage photo strip is deliberately not a lightbox gallery.** It is a
decorative marquee: `role="presentation"`, empty alt text, a duplicated run of
images to make the loop seamless, and the whole strip is one link to Instagram.
Making it a gallery would break that link and put each photograph in the viewer
twice.

## The enquiry form

One implementation: `inc/enquiry.php` (types, settings, processing, storage,
email, retention), `inc/enquiry-admin.php` (the Enquiries screens),
`inc/enquiry-form.php` (rendering), `inc/newsletter.php` (the footer sign-up,
same plumbing) and `assets/js/enquiry-form.js` (sending without a page load).
Any block calls `sjpta_enquiry_form()` with its own **type**, class list, copy
and anchor. Every form on the site goes through it. **Never write a second form.**

**Every form has a type, and the type alone decides who is emailed.** The types
are `sjpta_enquiry_types()`: `enquiry` (homepage panel, class pages),
`enrolment` (Join), `contact` (Contact), `born-to-be`, `newsletter` (footer).
The type travels as a hidden `sjpta_type`; the addresses live under
**Enquiries → Settings** (`sjpta_enquiry_settings`, one comma-separated list
per type, plus a "who replies" name and the retention period), resolved on the
server by `sjpta_enquiry_recipients()`. Nothing about routing is in the page,
so there is no open-relay field to defend. A type with no address set falls
back to the SJP settings field it used to read (`class_enquiry_email`,
`btb_enquiry_email`, `contact_email`) and then to the contact inbox; the
newsletter falls back to nobody, because a sign-up is a notification, not a
message. The enquiry panel block names its type in `form_type`; panels saved
before that field existed still carry the old `recipient` route key, which
`sjpta_enquiry_type_from_route()` maps, so nothing needs re-saving.

**Two ways in, one path through.** `sjpta_enquiry_process()` validates, stores
and sends. The script posts the form to `POST /wp-json/sjptheatrearts/v1/enquiry`
(newsletter: `/newsletter`) and gets `{ok, errors}` back; with scripting off,
or if the endpoint cannot be reached, the same form POSTs to its own page and
`sjpta_handle_enquiry()` on `template_redirect` calls the same function, then
redirects to `?enquiry=sent#anchor`. Both endpoints are public and nonce-free
on purpose (see below). The thank-you is printed **inside the form in a
`<template data-sjpta-sent>`** by the same `sjpta_enquiry_sent_markup()` the
redirect path prints, so the two cannot look different; the script swaps it in,
scrolls it into view and focuses it. While sending, the form is `is-submitting`:
button disabled, spinner shown, label "Sending…", `aria-live` status updated.
Server errors are written under the fields and into the summary exactly as PHP
renders them. **The script never validates on its own**; the server's answer is
the only source of truth, so the two paths cannot disagree.

**Storage and the Enquiries screen.** Every submission is a private
`sjpta-enquiry` post with `_sjpta_type`, `_sjpta_status` (`new` / `done`),
`_sjpta_sent_to`, `_sjpta_source` (the page it came from) and one `_sjpta_<field>`
per value. The list filters by form and by status, has "Mark as dealt with"
row and bulk actions, and the menu shows a count of new ones. **Retention** is a
setting (default 365 days, 0 keeps forever); `sjpta_enquiry_prune()` runs daily
on the `sjpta_enquiry_prune` cron and deletes outright, not to trash, because a
retention period that quietly kept the data another thirty days would break its
own promise. **"Send test"** on the settings screen mails the type's addresses
and reports the mail server's actual answer (`wp_mail_failed`): the first thing
to press when "forms are not working", because it tells email delivery apart
from everything else. On this dev box it reports "Could not instantiate mail
function", which is correct: Laragon's web PHP has no mail transport.

**The four designs draw four different forms**, which is what `variant` is for.
`sjpta_enquiry_layout()` holds one entry per design: which fields appear, how the
rows are divided, what each field is called in that context, and whether consent
is a checkbox or a line of small print. Born To Be picks a class from a menu and
offers a message box; a class page already knows the class, so it states it with
a "Change" link back to the list, drops the message box and adds the consent
checkbox. Same fields, same handler, same validation underneath.

Rows, not one grid with per-field widths: the class page's second row is
`0.6fr 1fr 1fr`, an age box beside two full-width ones, which a two-column grid
cannot express. A class page says "Student's name" where Born To Be says
"Child's name" (an adult tap class has no child in it), through `overrides`
rather than a forked field list, so both still post `child_name`.

**Choice fields are validated against the list the form offered.** Interests,
experience, days and the contact topic are all checked on the way in and blanked
if they do not match; anyone can post anything to a public endpoint and this text
lands in an email a person reads. The class menu is the exception and is
deliberately not held to a list: Born To Be names its own sessions and a class
page posts its own title, so there is no canonical set to check against.

**Pills are real radios and checkboxes** behind styled labels, clipped rather
than `display: none` so they keep their place in the tab order, with the focus
ring drawn on the pill. A group of them is a `fieldset` with a `legend`, or a
screen reader announces five unrelated buttons and never says what question they
answer.

**Contact asks a different consent question**: permission to reply, not
permission to market. Half those messages are about a wedding dance or a party.

Only a form that shows the consent checkbox records an answer to it. An unticked
box means the visitor declined; a form that never asked must not be stored as
"No", or whoever reads the enquiry is told a parent refused something they were
never offered. The asking form says so with a hidden `sjpta_asked_*` field.

Consent is **not required to send**. It is permission to follow up about classes
later, and answering the question someone has just typed does not depend on it.

Field errors render **under** the input. Above, the message pushed its own field
down and the inputs either side of it stopped lining up, so one mistake made the
whole row look broken.

Built in phase 3c rather than phase 7 at Gaz's request, because Born To Be
enquiries go to Madison rather than SJ.

**The confirmation names who replies** where the site knows, via
`sjpta_enquiry_responder( $type )`: the "Who replies" column on the settings
screen first, then for `enquiry` and `enrolment` the SJP settings name
(`class_enquiry_name`, default "Lottie"). Pass the default explicitly when
reading an ACF option: ACF returns an empty string for a field nobody has saved,
not the field's default, so the sentence silently fell back to "we" until it did.
The same trap is why `sjpta_enquiry_contact_email()` passes the footer's
explicit fallback: without it the chain reached `admin_email`, which on this
install is the booking provider's support desk.

**Every submission is stored as well as emailed**, as a private `sjpta-enquiry`
post that is not public, not queryable and not in REST. Email is the part that
fails quietly; the stored copy means a parent's message is never simply gone.

**No WordPress nonce, deliberately.** A nonce printed into a page a host caches
goes stale, and the failure lands on a parent who typed everything correctly.
There is no authenticated action to protect. Spam is handled by a honeypot and a
signed timestamp with a **minimum** age only, never a maximum, because on a cached
page the timestamp is as old as the cache entry.

**From stays on this domain**, with the visitor's address in `Reply-To`. Sending
as the visitor fails SPF and DMARC at most hosts.

Accessibility: real labels, an error summary first with links to each field,
`aria-describedby` and `aria-invalid` on the field itself, and messages that are
sentences rather than colour. "Typed nothing" and "typed something unusable" are
different mistakes with different advice: `sanitize_email()` empties an invalid
address, so the handler compares against the raw input or a mistyped address gets
reported as a blank one.

## The footer

Built from `design-reference/archive/SJPFooter.dc.html`, **not** the later
`SJPAltFooter.dc.html`. Gaz pivoted to the archive's four-column footer in phase
5, keeping the **white background** rather than the archive's deep purple. So the
design's colour *roles* are translated, never its colours copied: white headings
become brand purple, the pale lilac body text becomes the secondary tone, and the
white-at-8%-opacity panels become the page background over the surface. The
archive's orange emphasis (`#FE7300`, 2.7:1 on white) takes `--sjpta-accent-text`
under the standing rule for accent-coloured text.

Every link resolves to something real:

- The four age groups link to the **filtered** class list (`?age=…#all`) rather
  than the design's flat link to the whole list, using `sjpta_age_routes()` so a
  new age group appears without editing the footer.
- "Private lessons" has no page; it points at the class list filtered to the
  one-to-one tag (`?tag=private-one-to-one#all`), which lists the classes
  taught that way (Gaz, phase 9). It pointed at the timetable before the list
  could filter by tag.
- The About column's anchors (`#teachers`, `#safeguarding`, `#exams`, `#gallery`)
  need those sections to carry matching ids when phase 6 builds those pages.
- Legal links are an editable repeater, **hidden while empty**, because none of
  those documents exist yet.
- Facebook and TikTok render only when their setting is filled. Never invent a
  social account.

**The newsletter goes through the site, then to the mailing tool**
(`inc/newsletter.php`). The address is stored under Enquiries as type
`newsletter`, emailed to whoever the settings screen names (nobody by default),
and forwarded from the server with `wp_remote_post()` to `newsletter_action`
using `newsletter_field` as the field name, which is exactly what the tool's own
embed code posts from a browser. Mailchimp, Brevo and MailerLite all accept it,
so the provider still owns the list, the double opt-in and the unsubscribe; the
difference is that the visitor stays on the page and a sign-up the tool refuses
is not simply lost (the forwarding result is on the stored record). Same
honeypot and signed timestamp, same script, same no-JS fallback
(`?newsletter=sent#signup`). **Note that on the dev site this forwards to the
real Mailchimp list**, so test with addresses you are happy to see there.
**With no endpoint set there is no field at all**, only a link to Contact: a
box that swallowed an address and did nothing with it would be worse than not
asking.

## The timetable

`inc/tempo/` holds the client (`client.php`), the two cache layers (`cache.php`),
the session-to-class mapping (`match.php`) and `wp sjp timetable`, which exists
because every failure here is deliberately invisible on the front end.

Two rules the code depends on, both learned the hard way:

- **Key the week off `day_of_week`, the ISO integer, never `day_name`.** The name
  is localised on the portal.
- **Times and dates are wall clock readings in the feed's zone, not instants.**
  `strtotime( '10:00:00' )` builds a timestamp in the server's zone, which
  `wp_date()` then shifts into Europe/London and moves every class an hour later.
  Build the time in the feed's zone with `DateTimeImmutable::createFromFormat()`
  and format it back into that same zone. The portal's own `time_range` and
  `*_display` strings are ignored for the same family of reasons: they carry the
  portal's `date_format` and `time_format`, which nobody here controls.

**Three states, named in the returned array rather than inferred.** `live`,
`stale` (portal unreachable, showing the last known good copy, dated on screen)
and `unknown` (unreachable and never cached, so the designed "to confirm" state).
A 402 licence lapse and a 403 disabled feed are treated exactly like an
unreachable host: either would otherwise take the timetable down silently.

**Matching is explicit and many-to-one.** The portal names sessions by level
("Grade 3 Ballet", "Jazz (Yr4-Yr6)") and this site's pages are the discipline
("Ballet", "Jazz & Commercial"): of twenty sessions, one matched by name. Each
class page lists the names it goes by, in `timetable_names`. Keyword matching was
rejected because two real sessions name two classes at once
("Rosette Tap & First Steps"), and a keyword rule sends those to the wrong page
silently. An unmapped row renders with no "Details" link, which is honest.

**Fees never come from the feed**, which carries no pricing at all. They are ACF
fields with the standard empty state.

**Every bookable session carries a Book link**, built by
`sjpta_tempo_booking_url()` as `{feed base}/?dsb_schedule={schedule_id}`. It uses
`tempo_base_url`, not `portal_url`, even though they are the same host today: a
`schedule_id` only means anything on the site that issued it. The portal sends a
signed-out visitor to its sign-in page carrying the session in `redirect_to`, so
**Book is an action for existing families**; new families enrol instead. Sessions
with `online_open: false` get no Book link, because offering to book a place
nobody can book is worse than offering nothing.

## Structured data

`inc/seo.php` only. **The theme emits no titles, descriptions, canonicals, Open
Graph or sitemap** — those are SiteSEO Pro's, and two sources fighting produces
duplicates. One `@graph` per page: the organisation once, then a `Course` on a
class page and a `BreadcrumbList` matching the visible crumbs.

Rules that keep it honest:

- **A property nobody has confirmed is left out**, never emitted empty. The brief
  asks for `LocalBusiness`; that type promises a place you can go to, and the
  street address is still outstanding, so it stays an `Organization` with
  `areaServed` until the address exists. `offers` is absent from every Course
  because no fee has been set.
- **Class schedules come from the live timetable**, not the class fields: only one
  of the fifteen classes has a time typed into it, while the portal has twenty
  real sessions. A class taught twice a week emits two `CourseInstance` nodes.
- **Run text through `sjpta_schema_text()`.** JSON-LD is data, not markup, and
  WordPress's title filters turn "Acro & Cheer" into "Acro &#038; Cheer", which is
  then exactly what a search engine prints.

## Placeholder policy

A "to confirm" fact is an ACF field with an empty value, rendered through one shared
helper that outputs the designed placeholder (magenta `#C5299B` per the Contact
design). Rules:

- Never hard-code the words "To confirm before launch" into a template or pattern.
- Never invent a plausible-looking value to fill a gap.
- The placeholder is editable: the client fills the field and the placeholder vanishes
  with no code change.
- **The wording of a placeholder can itself be editable.** A class with no times set
  shows "Ask us for times" in magenta, but `times_note` lets the client write what
  should appear instead, per class ("See timetable"). That text renders as ordinary
  copy, not in the magenta "to confirm" tone: it is a decision, not an outstanding
  fact, and the magenta state stops meaning anything if deliberate answers wear it.
- Fees, term dates, address, parking, step-free access, waiting area, response times
  and performance dates are all in this category today.

## Naming

- Text domain **`sjptheatrearts`**; PHP prefix **`sjpta_`**; class prefix **`SJPTA_`**
  (or namespace `SJPTA\`). WPCS rejects prefixes under 4 characters, so plain `sjp_`
  is not usable and clean `phpcs` is an acceptance criterion.
- Block namespace **`sjp/`**, e.g. `sjp/timetable`, `sjp/class-card`.
- CSS custom properties come from theme.json, do not invent parallel `--sjp-*` names
  where a `--wp--` token exists.
- Block CSS lives in `blocks/<block>/style.css`, registered with
  `wp_enqueue_block_style()` so it only loads on pages using the block.
- Files: `kebab-case.php`. ACF field groups export to `acf-json/` and are committed.
- CPT `sjp-class`; taxonomies `discipline`, `age-group`.

## Performance budgets

| Metric | Budget |
| --- | --- |
| LCP (mobile, throttled) | < 2.5s |
| CLS | < 0.1 |
| INP | < 200ms |
| Lighthouse perf / SEO / a11y | ≥ 95 every template |
| CSS per page | < 60KB uncompressed |
| `motion.js` | < 15KB, deferred |

Rules: per-block stylesheets only; no utility framework; no jQuery on the frontend;
explicit `width`/`height` on every image; hero `fetchpriority="high"` + preload;
`loading="lazy"` below the fold; preload only the two font files used above the fold
on the current template; video never autoloads and always has a poster.

## Two traps in the seed and editor plumbing

**Never write `post_content` without `wp_slash()`.** WordPress unslashes on the
way in and eats every backslash, and block comments are full of them: `\n` for a
newline and `&` for every ampersand. This has now bitten twice. SJ's
biography shipped with a stray `n` between each paragraph; later a one-line
`wp_update_post()` to remove a troupe name stripped the backslash from all
fourteen ampersands on the homepage, so it read "jazz u0026 drama" everywhere.

Use `sjpta_seed_write()` for seeded pages, which slashes for you. For a one-off
edit to existing content, slash it by hand:

```php
wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $new ) ) );
```

Nothing warns you: `phpcs` passes, the page renders, and the damage only shows
in the rendered text.

**Never call `get_field()` unguarded.** The theme must not fatal without ACF Pro,
and a class page did exactly that the moment ACF was deactivated. Blocks that read
*block* data already gate on `$sjpta_has_acf`; anything reading a **post's** fields
must go through `sjpta_get_field()`, which returns null when ACF is absent.
Deliberately not a `get_field()` polyfill: defining that name would shadow the
real function if ACF loaded later and silently break every field on the site.

**A plain dynamic block placed in page content must also be registered in the
editor.** Registering it in PHP renders it on the front end but leaves the
editor's own registry empty, so it shows "Your site doesn't include support for
this block" and cannot be edited. Most of this theme's dynamic blocks live in
templates or template parts, where it never arises; the closing CTA band is
seeded into every page's content, and was broken on all of them from phase 3a
until `assets/js/editor-blocks.js` was added. ACF blocks are unaffected, because
ACF registers those itself. Any new plain dynamic block that goes in content
needs an entry in that file.

## Links that leave the site

**Every link to another site opens in a new tab**, and no internal link does.
This started as a portal-only rule (Gaz, phase 5) and became universal in phase 9
(Gaz), after an audit found every social link opening in place: a parent tapping
Instagram in the footer lost the site.

Use `sjpta_external_attr( $url )` for the attributes and `sjpta_new_tab_note( $url )`
for the screen-reader warning, never a hand-written `target`. Both decide from
the URL's **host** against `home_url()`, via `sjpta_is_external_url()`, so a link
added later cannot quietly miss the rule, and an editor-supplied URL in a generic
block (`link-cards`, `gallery-mosaic`) gets it right without anyone thinking about
it. A URL with no host is a path or a fragment, so it is ours.
`rel="noopener"` comes with it, because a page opened this way can otherwise
reach back through `window.opener`.

The note matters: a new tab with no warning leaves a screen reader user with a
back button that suddenly does nothing. **Where the link is icon-only and carries
an `aria-label`, put the warning in the label instead** (the footer's social
buttons do): an `aria-label` replaces the element's content as its accessible
name, so a nested `screen-reader-text` span inside it is never announced.

`build/links.mjs` crawls every page and lists every outbound link with its
`target` and `rel`. Run it after adding any link off-site.

## Accessibility

WCAG 2.2 AA. One H1 per page, correct heading order, landmarks, real `<nav>`, skip
link, descriptive link text.

**A section whose visible label is not a heading still needs one.** The timetable
and fees section bars set their labels as spans, beside a note and filter pills,
which left their `h3` children hanging directly off the page `h1` and failed
`heading-order`. Both now emit a `screen-reader-text` `h2` above the bar. Any new
block whose contents are `h3` needs the same. Visible plum focus ring offset from the element edge.
44px minimum targets. Errors carry text, never colour alone. Keyboard-operable nav and
accordions. Logical mobile reading order.

## Commands

PHP is not on `PATH` in this shell. Use the pinned Laragon build (8.3.16, the 8.4.x
builds ship no `php.ini`, so they lack `mysqli` and WP-CLI cannot boot):

```bash
export PHP="/c/laragon/bin/php/php-8.3.16-Win32-vs16-x64/php.exe"
export WP="$PHP /c/laragon/bin/wp-cli/wp-cli.phar"
```

| Task | Command |
| --- | --- |
| Install lint tooling | `$PHP /c/laragon/bin/composer/composer.phar install` |
| Lint | `composer lint` → `phpcs` |
| Auto-fix | `composer lint:fix` → `phpcbf` |
| WP-CLI (run from `c:/laragon/www/sjp-main`) | `$WP theme list` |
| Import classes | `$WP sjp import-classes --file=data/class-info-answers.json` (phase 4) |
| **Minify CSS, run after editing any stylesheet** | `npm run css` |
| Prepare photography (WebP) | `npm run media`, then `wp media import` |
| Add new image sizes to existing uploads | `$WP media regenerate --only-missing --yes` |
| Seed the homepage | `$WP eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-home.php` |
| Seed Born To Be | `$WP eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-born-to-be.php` |
| Seed Timetable & fees | `$WP eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-timetable.php` |
| Map portal names to classes | `$WP eval-file wp-content/themes/sjptheatrearts-theme/tools/seed-timetable-names.php` |
| **Timetable feed state and unmapped names** | `$WP sjp timetable` · `--refresh` to bypass the cache |
| Review screenshots | `npm run shots` (Playwright, 1440px + 390px) |
| Lighthouse gate | `npm run audit` (mobile) · `npm run audit -- home desktop` |
| Contrast gate | `npm run contrast`, **the authoritative contrast check** |

Dev site: **https://sjp-main.test** (Laragon, WP 7.0.3, ACF Pro 6.8.7 active).
`WP_DEBUG` must be **on** while building, the acceptance checklist requires no PHP
notices at `WP_DEBUG`.

**Compression is switched on in the site root `.htaccess`**, outside the WordPress
markers, in a block labelled `BEGIN SJP dev compression` (the previous file is
kept as `.htaccess.bak`). Laragon loads `mod_deflate` but configures nothing, so
the dev site served 120KB of uncompressed HTML and every Lighthouse run measured
a first paint no real host would produce. Turning it on moved both flagship pages
from 94 to **98** without a single change to the theme. Any production host
compresses text, so this is the honest measurement. **If a page suddenly scores
in the low 90s, check this block still exists before optimising anything.**

## Review workflow

**One phase at a time.** At the end of each phase:

1. Capture the rendered template at **1440px** and **390px** (`npm run shots`).
2. Put it side by side with the matching file in `design-reference/screenshots/`.
3. Report Lighthouse numbers for any template finished in that phase.
4. Confirm `phpcs` is clean and there are no `WP_DEBUG` notices.
5. **Stop and wait for Gaz's sign-off before starting the next phase.**

Flag rather than silently resolve: any conflict between sources, any fact that would
otherwise have to be invented, any design detail the screenshots and README disagree on.

## Environment gotchas

- **No Docker** on this machine, so `wp-env` is not available. The Laragon install at
  `c:/laragon/www/sjp-main` *is* the dev environment.
- The git repo root is the **theme directory**, not the WordPress root.
- The Tempo feed currently returns an empty envelope because the portal has no active
  term. That is a normal state to design for, not a bug to fix, and the feed went
  live on 2026-08-10 with Autumn Term 2026. Schema, verified against the plugin
  source rather than guessed, is in [inc/tempo/README.md](inc/tempo/README.md).
