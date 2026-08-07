---
name: "SJP Theatre Arts"
description: "Backstage warmth, centre-stage energy for a welcoming performing-arts school."
colors:
  backstage-plum: "#381064"
  spotlight-orange: "#FE7300"
  rehearsal-peach: "#DDB18F"
  warm-paper: "#FFF9F4"
  soft-lilac: "#F4EEF8"
  stage-ink: "#211B27"
  clean-white: "#FFFFFF"
typography:
  display:
    fontFamily: "Poppins, Arial, sans-serif"
    fontSize: "72px"
    fontWeight: 700
    lineHeight: "80px"
    letterSpacing: "normal"
  headline:
    fontFamily: "Poppins, Arial, sans-serif"
    fontSize: "56px"
    fontWeight: 700
    lineHeight: "64px"
    letterSpacing: "normal"
  section-heading:
    fontFamily: "Poppins, Arial, sans-serif"
    fontSize: "40px"
    fontWeight: 700
    lineHeight: "48px"
    letterSpacing: "normal"
  title:
    fontFamily: "Poppins, Arial, sans-serif"
    fontSize: "30px"
    fontWeight: 600
    lineHeight: "38px"
    letterSpacing: "normal"
  subtitle:
    fontFamily: "Poppins, Arial, sans-serif"
    fontSize: "22px"
    fontWeight: 600
    lineHeight: "30px"
    letterSpacing: "normal"
  body-large:
    fontFamily: "DM Sans, Arial, sans-serif"
    fontSize: "18px"
    fontWeight: 400
    lineHeight: "28px"
    letterSpacing: "normal"
  body:
    fontFamily: "DM Sans, Arial, sans-serif"
    fontSize: "16px"
    fontWeight: 400
    lineHeight: "24px"
    letterSpacing: "normal"
  body-small:
    fontFamily: "DM Sans, Arial, sans-serif"
    fontSize: "14px"
    fontWeight: 400
    lineHeight: "20px"
    letterSpacing: "normal"
  label:
    fontFamily: "DM Sans, Arial, sans-serif"
    fontSize: "15px"
    fontWeight: 600
    lineHeight: "20px"
    letterSpacing: "normal"
  label-small:
    fontFamily: "DM Sans, Arial, sans-serif"
    fontSize: "12px"
    fontWeight: 600
    lineHeight: "16px"
    letterSpacing: "0.02em"
  nav:
    fontFamily: "DM Sans, Arial, sans-serif"
    fontSize: "14px"
    fontWeight: 600
    lineHeight: "20px"
    letterSpacing: "normal"
rounded:
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
  full: "999px"
spacing:
  4: "4px"
  8: "8px"
  12: "12px"
  16: "16px"
  24: "24px"
  32: "32px"
  48: "48px"
  64: "64px"
  80: "80px"
  96: "96px"
components:
  button-primary:
    backgroundColor: "{colors.spotlight-orange}"
    textColor: "{colors.backstage-plum}"
    typography: "{typography.label}"
    rounded: "{rounded.full}"
    padding: "14px 24px"
    height: "52px"
  button-secondary:
    backgroundColor: "{colors.clean-white}"
    textColor: "{colors.backstage-plum}"
    typography: "{typography.label}"
    rounded: "{rounded.full}"
    padding: "14px 24px"
    height: "52px"
  age-route-card:
    backgroundColor: "{colors.rehearsal-peach}"
    textColor: "{colors.backstage-plum}"
    rounded: "{rounded.lg}"
    padding: "32px"
  class-card:
    backgroundColor: "{colors.clean-white}"
    textColor: "{colors.stage-ink}"
    rounded: "{rounded.lg}"
    padding: "0 0 24px"
  trust-chip:
    backgroundColor: "{colors.soft-lilac}"
    textColor: "{colors.backstage-plum}"
    typography: "{typography.label-small}"
    rounded: "{rounded.full}"
    padding: "8px 12px"
  form-field:
    backgroundColor: "{colors.clean-white}"
    textColor: "{colors.stage-ink}"
    typography: "{typography.body}"
    rounded: "{rounded.md}"
    padding: "14px 16px"
    height: "52px"
---

# Design System: SJP Theatre Arts

## Overview

**Creative North Star: "Backstage warmth, centre-stage energy"**

The system pairs the reassurance of a welcoming rehearsal space with the confidence and excitement of live performance. Warm supporting surfaces make first-time families feel comfortable, while strong plum fields, energetic orange actions and expressive authentic photography give teenagers and ambitious students something to aspire to.

The visual language is warm, confident, inclusive and uncluttered. It feels professional without becoming corporate, family-friendly without becoming childish, and theatrical without relying on decorative clichés. Information should remain calm and easy to scan even when the photography carries movement and emotion.

**Key Characteristics:**

- Warm, welcoming surfaces anchored by confident theatre plum.
- Clear Poppins headlines supported by highly readable DM Sans.
- Authentic SJP photography with expressive faces, movement and belonging.
- Rounded, tactile components with direct actions and generous spacing.
- Orange used selectively to create energy and clarify the next step.

## Colors

The palette moves from deep theatrical confidence to warm, softly lit supporting surfaces, with one high-energy action accent.

### Primary

- **Backstage Plum** (`#381064`): Anchors hero areas, footers, major calls to attention and high-confidence brand moments. White or warm-paper text sits on it.

### Secondary

- **Spotlight Orange** (`#FE7300`): Reserved for primary actions, small labels, quotation marks and moments that need immediate energy.

### Tertiary

- **Rehearsal Peach** (`#DDB18F`): Creates warm route cards, conversion bands and human, approachable section backgrounds.
- **Soft Lilac** (`#F4EEF8`): Supports trust strips, quiet information panels and low-emphasis grouped content.

### Neutral

- **Warm Paper** (`#FFF9F4`): The preferred page canvas when a warmer, less clinical background is appropriate.
- **Stage Ink** (`#211B27`): The default text colour on pale surfaces; it keeps typography softer than pure black while preserving contrast.
- **Clean White** (`#FFFFFF`): Used for navigation, cards, form fields and clean pauses between coloured sections.

### Named Rules

**The Spotlight Rule.** Orange is an action and energy colour, not a general-purpose background; its scarcity is what gives it impact.

**The Plum Anchor Rule.** Every major journey should include a confident plum moment, but long reading passages stay on pale surfaces.

## Typography

**Display Font:** Poppins (with Arial and sans-serif fallbacks)  
**Body Font:** DM Sans (with Arial and sans-serif fallbacks)

**Character:** Poppins gives the school a recognisable, rounded confidence without losing clarity. DM Sans keeps navigation, practical details, forms and longer copy friendly and effortless to read.

### Hierarchy

- **Display** (Bold, `72px`, `80px` line height): Campaign-level or homepage hero statements on wide screens.
- **Headline** (Bold, `56px`, `64px` line height): Page-level titles and large hero messaging.
- **Section Heading** (Bold, `40px`, `48px` line height): Major content sections and conversion moments.
- **Title** (SemiBold, `30px`, `38px` line height): Card groups and strong subsection introductions.
- **Subtitle** (SemiBold, `22px`, `30px` line height): Card titles, FAQ prompts and supporting headings.
- **Body Large** (Regular, `18px`, `28px` line height): Introductory copy and reassuring lead paragraphs; keep readable lines close to 60–70 characters.
- **Body** (Regular, `16px`, `24px` line height): Default content, descriptions and form guidance.
- **Body Small** (Regular, `14px`, `20px` line height): Supporting details, metadata and footer content.
- **Label** (SemiBold, `15px`, `20px` line height): Buttons and prominent interface labels.
- **Label Small** (SemiBold, `12px`, `16px` line height): Age ranges, times and compact metadata; uppercase is permitted for short labels only.
- **Navigation** (SemiBold, `14px`, `20px` line height): Desktop navigation and utility routes.

### Named Rules

**The Two-Voice Rule.** Poppins owns headlines; DM Sans owns navigation, body copy, labels, metadata and forms.

**The Confident Sentence Rule.** Headlines use natural sentence case and strong weight; avoid all-caps display copy or ornamental theatre fonts.

## Layout

Layouts use generous section spacing, strong horizontal bands and a clear reading order. Desktop compositions favour balanced two-column heroes, three-card discovery grids and centred content containers. Mobile compositions collapse to a single column, keep the most important action close to the relevant decision, and preserve image impact without crowding the copy.

The spacing system follows a four-pixel base with deliberate jumps from compact component spacing to generous section spacing. Use `8–24px` inside small components, `24–48px` inside cards and grouped panels, and `64–96px` between major desktop sections. Mobile sections normally use `48–64px` vertically. Content should not become dense simply to keep a desktop arrangement intact.

Responsive decisions are content-led: three cards become one scrolling column, complex timetable rows become stacked records, desktop navigation becomes a compact mobile header, and side-by-side form or story layouts become linear. The approved reference canvases are `1440px` desktop and `390px` mobile.

### Named Rules

**The Decision-First Rule.** Keep age, class fit, practical information and the next action together; visual drama must never separate a family from the information needed to decide.

**The Breathing-Room Rule.** Preserve warm empty space around headlines, cards and forms; do not compress the design into a dashboard-like density.

## Elevation & Depth

The system is layered rather than heavily lifted. Plum, peach, lilac, cream and white create most of the hierarchy through tonal contrast. Soft ambient shadows are reserved for cards that need separation from a similarly light background and for genuinely floating interface moments.

### Shadow Vocabulary

- **Card** (`0 8px 24px -8px rgba(33, 27, 39, 0.10)`): A restrained ambient shadow for white cards on pale surfaces.
- **Floating** (`0 16px 36px -10px rgba(33, 27, 39, 0.16)`): A stronger but still diffuse shadow for menus, overlays or deliberately raised conversion elements.

### Named Rules

**The Tonal-First Rule.** Establish depth with surface colour and spacing before adding a shadow.

**The Ambient-Only Rule.** Shadows remain broad and quiet; never use hard black drop shadows or stacked ornamental effects.

## Shapes

The form language is warmly rounded and confident. Small controls use gently curved corners (`8–16px`), cards and image frames use generous corners (`24–32px`), and primary actions use a full pill silhouette. Photography is clipped cleanly into the same radius family so imagery and interface feel part of one system.

Borders are quiet and functional. Use them where fields or interactive states need definition, not as decoration around every container. Avoid mixing sharp rectangles, random organic blobs and unrelated radius values on the same page.

## Components

Components feel tactile, rounded and direct. They should make the next step obvious while remaining calm enough for parents reviewing practical information.

### Buttons

- **Shape:** Full pill silhouette (`999px` radius) with a `52px` standard height.
- **Primary:** Spotlight-orange fill, backstage-plum label and `14px 24px` padding; use for the single preferred action in a local decision area.
- **Hover / Focus:** Deepen the orange slightly or lift by no more than `2px`; use a visible plum focus outline with clear separation from the button edge.
- **Secondary:** Clean-white fill with backstage-plum label. On pale surfaces, add a subtle plum-tinted border so the outline remains visible.

### Chips

- **Style:** Soft-lilac fill, backstage-plum text, full-pill shape and compact `8px 12px` padding.
- **State:** Chips communicate trust points, ages or compact metadata. Selected interactive chips may use backstage plum with white text; static chips must not imitate primary buttons. Interactive chips use a minimum `44px` touch target on mobile.

### Cards / Containers

- **Corner Style:** Generous corners (`24px`), increasing to `32px` for large feature panels.
- **Background:** Clean white for content cards, rehearsal peach for age routes and conversion panels, and soft lilac for quiet trust or information groups.
- **Shadow Strategy:** Flat by default; use the Card shadow only when tonal separation is insufficient.
- **Border:** Usually none. If required for accessibility or interaction, use a quiet plum-tinted stroke.
- **Internal Padding:** `24–32px` for standard cards and up to `48px` for large editorial panels.

### Inputs / Fields

- **Style:** Clean-white field, stage-ink text, a visible low-contrast boundary and a medium rounded corner (`16px`).
- **Focus:** Shift the boundary to backstage plum and add a clear offset focus ring; never rely on colour alone if an error message is present.
- **Error / Disabled:** Pair colour with concise DM Sans text and a state icon where helpful. Disabled fields stay legible and visibly inactive.

### Navigation

Desktop navigation uses a clean-white header, compact DM Sans labels and one spotlight-orange trial action. The current page may be indicated with plum text and a restrained underline or weight change. Mobile navigation keeps the logo, trial route and menu control easy to reach; expanded links appear in a simple vertical list rather than reproducing the desktop row.

Footer navigation keeps the trial route, Member Login, Privacy, Cookies, Safeguarding and Terms available on both desktop and mobile.

### Age Route Cards

Age route cards are a signature discovery pattern. They use rehearsal peach, backstage-plum copy, a short age label and a direct class-exploration link. Keep each route concise and parallel so families can compare options quickly.

### Class Cards

Class cards lead with authentic photography, then present age or timing metadata, the class name, a one-sentence description and a direct detail link. Preserve consistent image proportions and card heights within a row. Missing facts are labelled honestly rather than filled with invented detail.

### FAQ and Timetable Rows

FAQ rows and timetable records prioritise scanning. Questions use clear Poppins titles with simple disclosure controls; timetable entries align class, age, day and next action on desktop, then stack those fields into labelled mobile records.

### Trial Enquiry

The enquiry journey carries a selected class into the form when a visitor arrives from a class page. It asks only for the practical details needed to recommend a suitable trial, marks preferred days as optional, explains how the information will be used, and shows what happens after submission. Unknown schedules and fees are phrased as a prompt to ask the school, never as internal production notes.

## Do's and Don'ts

### Do:

- **Do** use authentic SJP photography that shows expressive faces, movement, friendship, teaching and the real studio or stage.
- **Do** use Poppins for every headline and DM Sans for supporting interface and reading text.
- **Do** reserve spotlight orange for actions and small energetic details.
- **Do** organise content into warm horizontal bands with generous space and clear next steps.
- **Do** maintain readable contrast, visible focus states and a logical mobile reading order.
- **Do** label unconfirmed schedules, fees or teacher details honestly instead of fabricating certainty.

### Don't:

- **Don't** introduce Bricolage Grotesque, ornamental theatre fonts or unrelated typefaces.
- **Don't** make the experience corporate, intimidating, overly childlike or visually noisy.
- **Don't** use rainbow category coding, large fields of orange or competing primary actions.
- **Don't** use generic stock photography when authentic SJP imagery is available.
- **Don't** overuse shadows, borders, gradients or decorative stage motifs.
- **Don't** shrink a desktop grid to fit mobile; reflow it into a deliberate single-column journey.
