# SJP Theatre Arts — Studio Manager Design System

SJP Theatre Arts is a dance/theatre school ("studio") running classes for children and teens. This design system covers the frontend UX for **Studio Manager**, a booking/register web app layered on their existing WordPress + WooCommerce site. It was extracted from the Figma file **"Studio Manager — SJP Frontend UX"** (mounted, read-only; pages: Cover, Getting-Started, Foundations, Foundations-Components, Components, Button, Status-Badge, Components-Screens, Screens-Student-Parent-Mobile-View, Screens-Register, Screens-Student-Parent-Desktop-View, Utilities) plus four uploaded brand SVGs (`uploads/logo.svg`, `logo-white.svg`, `icon.svg`, `icon-white.svg`).

The file states its own scope: *"This file translates the approved SJP Theatre Arts direction into a reusable, tenant-aware interface system. It preserves existing WordPress, WooCommerce, security and capacity contracts; proposed dynamic behaviours are progressive enhancements."* Included: family booking, package review, checkout, account management, teacher day view and register. Admin concepts are intentionally deferred.

Direction: **"Guided Confidence"** — one clear decision at a time, persistent "booking for" context, plain-language warnings, side-by-side package comparison, generous teacher touch actions. (A second direction, "Connected Workspace" — searchable catalogues, split views, contextual drawers — is named on the Getting Started page as a considered alternative but has no built screens in the file.)

## Content fundamentals

- **Voice**: plain, reassuring, operational. Sentences are short and literal ("This date is full", "Your place remains held while you complete checkout"). No hype, no exclamation marks except in scope-notice callouts.
- **Person**: second person for the parent/teacher acting now ("Find the right class **for Ava**", "**Your** hold has expired"), third person when naming the participant being booked for ("Booking for Ava Williams").
- **Casing**: sentence case everywhere — buttons, headings, status labels. Section eyebrows are the one ALL-CAPS exception ("STEP 1 OF 4 • CHOOSE A CLASS").
- **Status language is never colour-only**: every status pairs a glyph + explicit word — "✓ Confirmed", "! Hold expiring", "× Unavailable", "i Transferred", "• Cancelled".
- **Numbers over adjectives**: "5 of 8 marked", "63%", "£8 saving", "3 students still need a status" — precise counts instead of vague reassurance.
- **No emoji.** The only glyphs used are ✓ ! × i • (status) and a couple of plain unicode arrows (‹ › ×).
- **Configurable vocabulary is a hard requirement, not copy polish**: "student" can become "participant"/"member", "class" can become "lesson"/"activity", "teacher" can become "instructor"/"tutor". Copy and layouts must survive labels at least 40% longer than the shipped defaults.

## Visual foundations

- **Palette**: brand orange `#FF7300` (primary actions, focus rings, accents) and deep purple `#330164` (headers, headings, secondary actions) are the two saturated brand colours; magenta `#C5299B` is a tertiary accent (used sparingly, e.g. cover art). Neutrals are a standard gray scale (white → `#F1F3F5` → `#E5E7EB` → `#6B7280` → `#3F3F46`). Five status pairs (success green / warning amber / danger red / info blue / neutral gray), each a light background + a darker, AA-legible foreground text colour — never the background alone.
- **Type**: Poppins (600/700) for all headings and prices — energetic, confident; Montserrat (400/600) for everything operational — body copy, labels, buttons, badges. Sizes are specific to the file, not a generic scale: page 28px/36 line-height, section 20/28, card 16/24, body 16/24, label 14/20, supporting 13/19, small 12/16.
- **Spacing**: 4px base unit, used non-linearly as needed (4, 8, 12, 16, 20, 24, 32, 40, 48, 64) — not a strict 8pt grid.
- **Corner radii**: small 8px (chips, inner rows), medium 12px (cards, badges' pill uses `full`), large 16px (panels, phone screens), full/999 (pills, avatars, filter chips).
- **Backgrounds**: flat colour only — no gradients, no photography, no patterns/textures. Full-bleed purple with two low-opacity (0.22–0.28) brand-colour circles is used once, on the Figma cover frame, purely as file decoration — not a reusable UI motif.
- **Elevation**: mostly flat with a 1px inset border (`boxShadow: inset 0 0 0 1px var(--color-border-default)`) standing in for a shadow on cards. One real drop shadow appears in the file, on the modal profile popup: `0px 8px 24px rgba(16,33,47,0.1)` — reused here as `--shadow-raised`; `--shadow-small` is an inferred lighter step in the same hue (no explicit "small" shadow value ships in the source).
- **Animation**: none specified in the file — no eased transitions, no motion tokens. Treat state changes as instant; add restrained fades/slides only if asked.
- **Hover/press/focus**: focus is the only state the file defines explicitly, as a `0 0 0 3px` ring in `--color-brand-focus` (orange) around the control. Disabled state is 72% opacity, not a colour swap. No hover style is defined in the source — infer a subtle opacity or shade shift if a build needs one, and flag it as an addition.
- **Touch targets**: 44px minimum control height everywhere; 48px specifically for primary mobile actions.
- **Overlays**: modal popups (e.g. protected student profile) sit on a `rgba(24,12,36,0.48)` scrim — the brand purple darkened, not plain black. The off-canvas filter drawer dims the page edge behind it to 0.28 opacity of the brand purple rather than a scrim.
- **Imagery**: no photography or illustration in the source beyond the single brand mark PNG (`assets/header-mark.png`) used in the mobile app header. Class "images" in the mobile cards are unfilled placeholders in the file itself.

## Iconography

No icon font, icon library, or SVG icon set ships in the file. Status and inline meaning are carried by **plain unicode glyphs** (✓ ! × i •) set in the same Montserrat weight as their label — never floating alone. The single true vector icon in the file is a warning/brake glyph (`assets/icon-warning-brake.svg`) used once, in the teacher register's completion banner. Emoji are not used anywhere. If a build needs a fuller icon set, match this restrained, single-colour, text-paired style — do not introduce a filled icon-font aesthetic.

## Brand assets

`assets/logo.svg` / `logo-white.svg` — full lockup (orange "SJP" monogram + "Theatre Arts" wordmark in purple/white). `assets/icon.svg` / `icon-white.svg` — monogram mark alone. `assets/header-mark.png` — the exact bitmap the Figma mobile header uses (copied verbatim, not redrawn). All four uploaded SVGs are the real brand assets — nothing here is a placeholder or an invented mark.

## Components

- **Button** (`components/button/`) — `size` compact(44px)/mobile(48px) · `style2` primary/secondary/outline · `state` default/focus/disabled. 18 variants total.
- **StatusBadge** (`components/status-badge/`) — `severity` success/warning/danger/info/neutral, each with paired bg/fg tokens. 5 variants total.

These are the file's **complete** component inventory (`METADATA.md` → Component families: 2 sets, 0 standalone) — no additional primitives were invented.

## Tokens

`components/fig-tokens.css` — all 89 Figma Variables (Color 25, Primitives 20, Typography 16, Spacing 10, Vocabulary 9, Size 5, Radius 4), generated verbatim from the file. `tokens/typography.css` adds semantic type-scale utility classes (`.text-page`, `.text-section`, `.text-card`, `.text-body`, `.text-body-strong`, `.text-supporting`, `.text-label`, `.text-small`) since the file defines a type scale in its Foundations page but no named Figma text styles. `tokens/shadows.css` adds the two elevation values described above. `tokens/fonts.css` loads Poppins + Montserrat from Google Fonts — the file specifies these exact families (not a substitution); no font files were provided to self-host, flagged below.

## UI kit

`ui_kits/studio-manager/` — interactive click-through of the family booking journey (discovery → package → review → checkout → account, plus the filter drawer) and the teacher register (plus the protected student-profile popup). See its README.md.

## Index

- `styles.css` — the global stylesheet entry point (imports everything below).
- `tokens/` — fonts, shadows, and the semantic type scale.
- `components/fig-tokens.css`, `components/fig-typography.css` — generated Figma Variables (typography.css is empty; the file has no named text styles).
- `components/button/`, `components/status-badge/` — the two components.
- `guidelines/` — 12 foundation specimen cards (colours, type, spacing, radius, elevation, touch targets, vocabulary, logo, iconography) shown in the Design System tab.
- `ui_kits/studio-manager/` — the interactive product recreation.
- `assets/` — logo/icon SVGs (light + dark), the header PNG, and the one warning vector icon.
- `SKILL.md` — Claude Code-compatible skill entry point.

## Intentional additions

- Semantic type-scale CSS classes (`tokens/typography.css`) — the file has no named Figma text styles, only documented pixel/weight combinations on its Foundations page; these classes make that scale usable.
- `--shadow-small` — inferred lighter companion to the one real shadow value in the file (`--shadow-raised`).

---

## Caveats — please help iterate

- **No font files were supplied.** `tokens/fonts.css` loads Poppins/Montserrat from Google Fonts, which are the file's exact named families (not a substitution) — but if you have your own licensed font files, send them and I'll self-host instead of relying on the CDN.
- **The desktop recomposition (`Screens-Student-Parent-Desktop-View`) was not rebuilt as a second UI kit.** It re-lays the same Guided Confidence content into a wider single-column page with a top nav instead of a sticky bottom bar — I read it for reference but didn't duplicate the build to keep scope tight. Say the word and I'll add it.
- **Hover states are inferred, not specified** — the file only defines default/focus/disabled for Button. Flag if you want a specific hover treatment documented instead.
- The Figma file is only 2 component families (Button, Status Badge) plus full screens — this is a smaller system than a typical multi-primitive kit. If there are more components elsewhere (e.g. a live codebase), attach it and I'll extend this system rather than inventing primitives the source doesn't define.

**Built 2 of 2 component families. Built 0 of 0 text styles (the file defines no named Figma text styles — see Tokens above).**
