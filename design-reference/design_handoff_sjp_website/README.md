# Handoff: SJP Theatre Arts public website

## Overview

SJP Theatre Arts (formerly Bromsgrove School of Dance) is a dance and theatre school in
Bromsgrove, Worcestershire, running classes from baby yoga through to adult ballet and
ballroom. This bundle is the approved design for their new public marketing website: nine
desktop pages plus a mobile screen set.

The site's job is to get a parent from "my child wants to dance" to an enrolment enquiry with
as little doubt as possible. Every page ends on the same purple call-to-action band, and
"Enrol now" is the single primary action throughout (there is no free trial; that was removed
at the client's request).

The school also runs a member portal (registers, invoices, bookings) built on their existing
WordPress + WooCommerce install. This site links out to it but does not contain it. The
separate **Studio Manager** design system in `_ds/` covers that portal; this marketing site
reuses its brand foundations.

## About the design files

The `.dc.html` files in this bundle are **design references written in HTML**. They are
prototypes of the intended look, copy and behaviour. They are **not production code and should
not be pasted into the site**.

Concretely, they are authored in a design tool's own component runtime (`support.js`,
`<x-dc>`, `<helmet>`), everything is inline-styled by design, and the pages are hard-coded with
no CMS behind them. The task is to **rebuild these designs in the target environment** using its
established patterns.

**Target environment: WordPress.** The client already runs WordPress with WooCommerce, so the
expected implementation is a block theme (or a classic theme, if that is what the site is on)
with real templates, patterns and editable content, not static HTML. Class data will arrive
separately as JSON from a class-information form the client is filling in; it should populate a
`class` custom post type that drives the Classes index, the Class Detail template and the
Timetable page. If you are implementing somewhere other than WordPress, the same rule applies:
use that stack's conventions and treat these files as the visual and copy spec.

Two things in the prototypes are review-tool scaffolding and must **not** be built:

- the fixed "Return to design options" pill, bottom-left on every page;
- the full-screen "This screen is too small" purple blocker under 1024px. The real site must be
  responsive. Use `SJP Mobile Alt.dc.html` for the mobile layouts.

## Fidelity

**High fidelity.** Colours, type, spacing, radii, copy and imagery are final and should be
matched closely. Exact values are listed under Design tokens below, and every value is also
readable inline in the HTML.

Two areas are deliberately unresolved and marked in the designs themselves:

- **Timetable and fees** — day, time and price cells read "To confirm before launch". The
  layout is final; the data is not.
- **Contact** — full address, parking, step-free access and waiting-area notes read "To confirm
  before launch", in magenta `#C5299B`.

Build these as real, editable fields; do not hard-code the placeholder text.

## Screens / views

All desktop pages share the same frame: content is capped at **1320px** with **32px** side
padding, on a `#F7F6F9` page background.

### Shared chrome (every page)

**Utility bar** — full-bleed, background `#1D0B36`, height 40px, 12px/500 Montserrat, text
`#D9C8EC`. Left: a 6px orange dot then "Bromsgrove, Worcestershire". Centre: "Dance · Sing ·
Perform, classes from babies to adults, all abilities welcome" in `#B49BD3`. Right: "Instagram",
a 1px divider, then "Member login" in white/700 with a small orange padlock SVG.

**Floating nav** — `position: sticky; top: 14px`, centred, `z-index: 80`. A white pill:
`border: 1px solid #EAE3F0`, `border-radius: 999px`, padding `8px 8px 8px 22px`,
`box-shadow: 0 14px 34px -18px rgba(33,11,60,.35)`. Contents left to right: `assets/logo.svg` at
36px height, a 1px `#EEE7F4` divider, then nav links at 14px/600 in `#381064` with 24px gap
(Classes, Timetable & fees, About, Performances, Contact), then the primary "Enrol now" button:
44px tall, 22px horizontal padding, `background: #FE7300`, white 14px/700, pill radius, with a
14px white arrow SVG. Hover darkens to `#e56800`.

On the homepage only, the nav overlaps the hero via `margin-bottom: -88px` on the sticky
wrapper, and the hero compensates with `padding-top: 150px`.

**Closing CTA band** (every page, just above the footer) — a `#2A0B4D` panel, `border-radius:
28px`, centred text, with two decorative circles absolutely positioned and clipped: orange
`rgba(254,115,0,.22)` top-left-ish and magenta `rgba(197,41,155,.24)` bottom-right. Heading in
Poppins 700 at 38–42px white, body 16px `#D9C8EC` capped at 520–560px, then two pill buttons
(orange primary, transparent-with-white-border secondary). Copy differs per page: "Not sure
where to begin?" (home), "Ready to join?" (classes), "Come and see the place" (about), "Ask for
this term's sheet" (timetable), "Be in the next one" (performances), "Still deciding?" (join),
"Ready when you are" (contact).

**Footer** — `SJPAltFooter.dc.html`. Shared across all pages; build once as a template part.

---

### 1. Homepage — `SJP Homepage Alt.dc.html`

Purpose: orient a new parent by age, prove the school is credible, and push to enrol.

Sections in order:

1. **Hero** — centred, max 860px. Small pill eyebrow ("Bromsgrove · all ages · all abilities",
   `#EFE7F7` bg, `#6A3AA0` text, 12px/700, `.12em` tracking, uppercase, with an orange sparkle
   SVG). H1 in Poppins 700 **76px / 1.04 / -.025em** in `#381064`, with "shine." in `#FE7300`.
   Sub-paragraph 18px/1.65 `#5A5265`, max 560px. Two buttons: "Enrol now" (52px tall, `#381064`,
   white) and "Find the right class" (52px, white, `1px solid #E4DCEC`, `#381064` text, arrow).
   Background layers: a dot grid (`radial-gradient(rgba(56,16,100,.08) 1px, transparent 1.4px)`
   at `24px 24px`), two large soft radial glows (orange top-left, magenta top-right), and a
   full-bleed `<canvas>` drawing an animated ribbon. **The canvas is decoration; if it is
   awkward to port, drop it. Do not let it block content.**
2. **Three age cards** — 3-column grid, 22px gap. Each card: white, `1px solid #EAE3F0`,
   `border-radius: 16px`, 10px padding; a 250px image with `border-radius: 10px` and an
   age-badge pill bottom-left; then title (Poppins 600, 20px, `#381064`), body 14px/1.6, and a
   footer row divided by `1px solid #F0EBF5` with a meta label (12px/600 `#8A8194`) and a 34px
   circular arrow button. Cards: "First steps into the studio" (Ages 2–4), "Ballet, tap, jazz &
   drama" (Ages 4–10), "Teens: train your way" (Ages 11–18).
3. **Discipline ticker** — full-bleed white strip, 1px `#E9E4F0` borders top and bottom,
   infinite left-scrolling row of coloured pills (Ballet, Tap, Jazz & Commercial, Acro & Cheer,
   Musical Theatre, Drama, Singing, Lyrical, Troupe…) using the four pastel badge pairs. CSS
   `@keyframes sjp-ticker` translating `-50%` over 36s linear, with the content duplicated.
4. **"A school where every child…"** — two-up: copy card and imagery, telling SJ's story (Laine
   Theatre Arts, scholarship from Betty Laine) alongside the "beginners genuinely welcome"
   promise.
5. **Teens band** — dark purple full-width panel, "Train your **way.**" with "way." in `#FF9A3D`,
   46px Poppins 700.
6. **"Joining is three steps"** — numbered list plus the enquiry teaser.
7. **Adult strip** — "Adult ballet, ballroom & wedding dance", 27px heading.
8. **Closing CTA + footer.**

### 2. Classes — `SJP Classes Alt.dc.html`

Purpose: let a parent find the right class. H1 56px, "Find the right **class**". Sub-copy:
thirteen class styles across four age routes. Filterable/browsable card grid of class styles,
each linking to the class detail template. Ends with "Ready to join?".

### 3. Class detail — `SJP Class Detail Alt.dc.html`

The template for a single class; the prototype shows **Jazz & Commercial**. This is the page the
client's class-information JSON will populate.

- Hero: status pills (including a green "Beginners welcome" badge, `#DFF7EC` / `#12855A`), H1 at
  58px with the second word in orange, intro paragraph, then meta chips.
- Main column: description, what happens in a class, where it can lead.
- **Sticky sidebar** (`position: sticky; top: 96px`, 16px gap):
  - "What to wear" card on `#EFE7F7`, 16px radius, bulleted with orange `·` markers;
  - "Your teacher" card, white with border; teacher avatar is a 56px `#C5299B` rounded square
    with white Poppins 700 initials;
  - "Want to watch first?" card on `#2A0B4D` with a decorative orange circle and an orange
    46px CTA.
- Enquiry block: "Join Jazz & Commercial", 34px heading, routed to the named teacher.
- "You might also like" — 3 related class cards (Ballet, Acro & Cheer, Troupe) with 190px images.

Everything class-specific here (name, ages, teacher, kit list, related classes) must be data,
not markup.

### 4. Timetable & fees — `SJP Timetable and Fees Alt.dc.html`

H1 56px, "Timetable & **fees**". A week grid plus a fee table. Unconfirmed cells render as "To
confirm before launch". Closes with "Ask for this term's sheet".

### 5. About — `SJP About Alt.dc.html`

H1 52px. Sections: "A welcome from SJ" (36px heading, portrait `assets/sj-headshot.jpg` plus a
140px show photo beneath); the teaching team (`assets/team.jpg`, 460px min-height); "Additional
needs and one-to-one teaching" (28px); "How we keep students safe" (28px) — a list of the 12
safeguarding commitments, closing on a `#2A0B4D` panel with `assets/safeguarding-white.png` at
56px and a note that the full policy is available on request; awarding-body logos (IDTA, LAMDA,
UKA, ADFP) shown at `filter: grayscale(1); opacity: .55` with 44px gaps. Ends with "Come and see
the place".

### 6. Performances — `SJP Performances Alt.dc.html`

H1 56px, "Nobody sits out". Hero band: "The annual Christmas show" (40px, white on dark) with a
three-stat grid. Then three cards: "Performing troupes" (Notorious, Blaze, Shockwave — note the
softened language: troupes, not competition teams), "Summer and Easter schools", "Medal and exam
days". Then a photo gallery. Ends with "Be in the next one".

**Pending:** three video clips are intended for this page and have not been compressed yet. Leave
a video slot in the template.

### 7. Join — `SJP Join Alt.dc.html`

H1 56px, "Joining is three **steps**". Three coloured step cards, each with a 52px circled
number: step 1 on `#FFE9D6` with an `#FE7300` numeral, step 2 on `#FAE6F4` with `#C5299B`, step 3
on `#DFF7EC` with `#26CC8C`. Body text in these cards is `#5A4B52`, with a coloured closing line
(`#8A6A4F` / `#9C5A88` / `#3F7A62`). Then a four-card reassurance row (What to wear, What to
bring, If they are nervous, Afterwards). Then the **enrolment form** on `#F7F6F9` with a 16px
radius and 38px/40px padding, grouped under uppercase 11px `#8A8194` section labels ("About
you"…), with a note that optional fields can be left blank. Ends with "Still deciding?".

### 8. Contact — `SJP Contact Alt.dc.html`

H1 56px, "Come and **visit**". Three contact cards on the pastel backgrounds, each with a 48px
solid circular icon: Email (`#FFE9D6` / `#FE7300`, mailto `sjptheatrearts@yahoo.com`), Social
(`#FAE6F4` / `#C5299B`, `@sjptheatrearts`), Already with us (`#EFE7F7` / `#381064`, member
login). Then a location card with a 2×2 detail grid (Full address, Parking, Step-free access,
Waiting area) — all currently "To confirm before launch". Then the general message form. Then
three notes: "When we reply", "When we are teaching" (Monday, Tuesday, Wednesday, Thursday and
Saturday), "Safeguarding contact" (goes directly to SJ, not the general inbox). Ends with "Ready
when you are".

### 9. Mobile — `SJP Mobile Alt.dc.html`

Nine 390px-wide screens laid out side by side for review, each in a 28px-radius device frame:
01 Home, 02 Menu open, 03 Classes, 04 Class detail, 05 Timetable & fees, 06 Join, 07 About,
08 Performances, 09 Contact. Screen 02 shows the full-screen `#1D0B36` menu overlay (844px tall).

These are the responsive targets for the same pages, not separate routes. The desktop pages are
not currently responsive — that is the build's job, and these screens define the intended small
-screen result.

## Interactions & behaviour

All motion lives in `motion.js`, applied from JavaScript so the markup stays plain. It honours
`prefers-reduced-motion` and fails open — content is never left invisible. Behaviours:

- **Reveal on scroll** — sections fade and rise 28px, `opacity`/`transform` over **0.72s
  `cubic-bezier(.22,.61,.36,1)`**, triggered by IntersectionObserver at `threshold: 0.04`,
  `rootMargin: 0px 0px -8% 0px`. A 4-second safety timer forces anything still hidden to show.
- **Grid stagger** — inside a grid (or `[data-stagger]`), children animate individually at
  **70ms** intervals, up to 12 children. Otherwise sibling blocks step at 90ms, capped at 3 steps.
- **Card lift** — `article` and `[data-lift]` elements translate `-6px` on hover over
  **0.32s**, same easing.
- **Counting numbers** — `[data-count]` elements count from 0 to their value over **900ms**
  with a cubic ease-out, fired at 60% visibility, formatted `en-GB`, with a timeout guarantee.
- **Condensing header** — the sticky header's bar goes 88px → 72px past 24px of scroll, with the
  shadow deepening from `0 4px 24px -12px rgba(33,27,39,.18)` to `0 10px 30px -14px rgba(33,27,39,.32)`.
- **Pulsing play button** — `[data-pulse]` runs a 2.6s expanding white ring; hover stops the
  pulse and scales to 1.06.
- **Floating shapes** — decorative absolutely-positioned rotated divs bob ±12px over 6–8s.
- **Accordions** — `<details>` with a `[data-chevron]` child rotate the chevron 180° when open.
- **Tickers** — the discipline pill strip and marquee use CSS `@keyframes` translating `-50%`
  (36s linear, infinite) with duplicated content.
- **Smooth scrolling** — `html { scroll-behavior: smooth }` for in-page anchors.

Ports of these should be re-implemented idiomatically (CSS animations, or the theme's existing
scroll library). Nothing here is load-bearing — the site must be fully usable with JavaScript off.

Forms in the prototype are visual only: no validation, no submit handling, no success or error
states. Those need designing into the build. At minimum: required-field validation on name,
email and child's age; an inline error style; and a success state that confirms who will reply
(Lottie for class enquiries, SJ for safeguarding).

## State management

The prototype pages are static. The real build needs:

- **Class data** — a `class` custom post type: name, discipline tags, age range, days, times,
  teacher(s), fee, "where it can lead" destinations, kit list, related classes, image. Source:
  the JSON export from the client's class-information form (15 classes including Baby Yoga and
  the three troupes: Notorious, Blaze, Shockwave). Drives the Classes index, the Class Detail
  template and the Timetable page.
- **Timetable** — derived from class data rather than entered twice.
- **Fees** — editable, with an "awaiting confirmation" state per row.
- **Enquiry forms** — a submission handler routing to the right recipient by enquiry type.
- **Member portal links** — currently `#member` placeholders; point them at the real portal.

## Design tokens

### Colour

| Role | Hex |
| --- | --- |
| Brand orange (primary action) | `#FE7300` |
| Orange, hover | `#e56800` |
| Orange, light accent on dark | `#FF9A3D` |
| Brand purple (headings, dark buttons) | `#381064` |
| Deep purple (CTA panels) | `#2A0B4D` |
| Darkest purple (utility bar, mobile menu) | `#1D0B36` |
| Magenta (tertiary accent, "to confirm") | `#C5299B` |
| Page background | `#F7F6F9` |
| Card / surface | `#FFFFFF` |
| Card border | `#EAE3F0` |
| Inner divider | `#F0EBF5` |
| Section rule | `#E9E4F0` |
| Nav divider | `#EEE7F4` |
| Body text | `#211B27` |
| Secondary text | `#5A5265` |
| Muted / meta text | `#8A8194` |
| Text on purple | `#D9C8EC` |
| Text on purple, dimmer | `#B49BD3` |
| Success green | `#26CC8C` |

Badge pairs (background / foreground), used for age badges, discipline pills and eyebrows:

| Pair | Background | Foreground |
| --- | --- | --- |
| Purple | `#EFE7F7` | `#6A3AA0` |
| Orange | `#FFE9D6` | `#B85400` |
| Magenta | `#FAE6F4` | `#9C1F7A` |
| Green | `#DFF7EC` | `#12855A` |

### Typography

**Poppins** 600/700 for headings, numerals and prices. **Montserrat** 400/500/600/700 for
everything else. Both currently load from Google Fonts via
`_ds/.../tokens/fonts.css`; self-host them in the build.

| Use | Size / line-height / weight | Tracking |
| --- | --- | --- |
| Homepage H1 | 76px / 1.04 / Poppins 700 | -.025em |
| Page H1 (inner pages) | 52–58px / 1.06 / Poppins 700 | -.025em |
| Section H2, large | 38–46px / 1.08–1.15 / Poppins 700 | -.02em |
| Section H2, small | 25–36px / 1.15–1.2 / Poppins 700 | — |
| Card H3 | 17–20px / Poppins 600 | — |
| Hero body | 18px / 1.65 / Montserrat 400 | — |
| Page intro | 17px / 1.65 | — |
| Body | 15–16px / 1.7–1.75 | — |
| Card body | 14px / 1.6 | — |
| Small print | 13px / 1.6–1.65 | — |
| Meta / label | 12px / 600–700 | — |
| Eyebrow (uppercase) | 11–12px / 700 | .08–.12em |

Headings use `text-wrap: balance`; body paragraphs use `text-wrap: pretty`. Keep both.

### Spacing, radii, elevation

- Container: max-width **1320px**, side padding **32px**.
- Section padding: **56–150px** vertical; 150px only on the homepage hero (nav overlap).
- Grid gaps: 22px (card grids), 16px (sidebar stack), 12–14px (pills, buttons, form fields).
- Card padding: 10px for image cards (image sits inside the border), 24–46px for text cards.
- Radii: **10px** inner images · **12px** small cards and avatars · **16px** cards and panels ·
  **28px** CTA bands and mobile device frames · **999px** pills, buttons, badges.
- Borders: `1px solid #EAE3F0` on cards, `1px solid #F0EBF5` on inner dividers.
- Shadows: nav `0 14px 34px -18px rgba(33,11,60,.35)` · mobile frames
  `0 18px 44px -22px rgba(29,11,54,.4)` · header stuck `0 10px 30px -14px rgba(33,27,39,.32)`.
- Control heights: 44px standard, 46px sidebar CTA, 52px hero buttons. Never below 44px.

## Assets

Everything referenced by the designs is included under `assets/`.

- **Brand** — `logo.svg`, `logo-white.svg`, `icon.svg`, `icon-white.svg`. These are the real
  brand files, not placeholders.
- **Accreditation** — `idta.png`, `lamda.png`, `uka.png`, `adfp.jpg`, `safeguarding-white.png`
  (Dance School Safeguarding Services).
- **Photography, compressed for web** — `assets/new-media/web/*.jpg` (17 files). These are the
  client's own 2025 photos, already sized for the site. Use these.
- **Photography, earlier set** — the loose `.jpg`/`.png` files at the root of `assets/`
  (class shots, SJ's headshot, the team photo, performance shots). Some are lower resolution;
  swap for `new-media/web` equivalents where one exists.
- **Uncompressed originals** are in the main project at `assets/new-media/` (not in this bundle,
  to keep it small). Ask if you need higher resolution for retina or hero crops.

All photography is the client's own. No stock, no licensing to clear.

## Files in this bundle

```
SJP Homepage Alt.dc.html          desktop home
SJP Classes Alt.dc.html           class finder
SJP Class Detail Alt.dc.html      single-class template (shows Jazz & Commercial)
SJP Timetable and Fees Alt.dc.html
SJP About Alt.dc.html
SJP Performances Alt.dc.html
SJP Join Alt.dc.html
SJP Contact Alt.dc.html
SJP Mobile Alt.dc.html            nine 390px mobile screens
SJPAltFooter.dc.html              shared footer
screenshots/                      full-page renders of all nine screens, 1440px wide
motion.js                         the scroll/hover motion layer described above
support.js                        design-tool runtime (do not port)
_ds/                              Studio Manager design system: tokens, type scale, shadows
assets/                           all imagery, logos and accreditation marks
```

Open any `.dc.html` in a browser to view it. They are meant for a desktop window at 1024px or
wider; below that the review blocker takes over.

## Open items before build

1. Timetable days, times and fees.
2. Studio address, parking, step-free access and waiting-area details.
3. Three performance videos, still to be compressed.
4. Class content JSON from the client's form (15 classes).
5. Member portal URL to replace the `#member` placeholders.
6. Instagram handle link target.
