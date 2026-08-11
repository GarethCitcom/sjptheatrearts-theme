# "New web design guide.fig" (Anity charity template) — extracted design language

Parsed from the .fig binary. Re-renderable: `scraps/fig-render.js` + `scraps/guide-nodes.json`
+ `scraps/guide-blobs.bin` + `scraps/guide-blobidx.json`. Rendered pages: `scraps/pg-*.html`.

## Palette
| Role | Hex |
|---|---|
| Primary orange | `#FF5528` (also `#FF733B`) |
| Near-black surface | `#343434` (footers/CTA), `#121212` |
| Body grey | `#727272`, `#616670` |
| Cream | `#F2F0EC` |
| Mint | `#26CC8C` / `#20B86D` |
| Amber | `#FFA415` / `#FFB840` |
| Coral | `#F84D42` |
| Tints | `#CEF9E8`, `#FFDED5`, `#FFEFD7` |

## Type
- Display: **Fredoka One** — 50/60, 24/34, 20/34, 18/30
- Alt display: **Nunito ExtraBold** 50/65, 20/24
- Body: **DM Sans Regular 16/34** (dominant), Nunito Regular 20/30
- Buttons: **DM Sans Bold 14**, +1.4px tracking
- Script accent: **Pacifico** 24 (eyebrows / handwritten marks)

## Geometry
Radii: **20px** cards (dominant), 10px small, **999px** pills, 70px blobs, 50%.
Shadows: very soft — `0 4 12 rgba(0,0,0,.04)`, `0 6 24 -4 rgba(0,0,0,.08)`, `0 0 60 rgba(0,0,0,.05)`.

## Section patterns (the "elements" the client loves)
1. **Header** — white sticky bar: logo left · nav centre (DM Sans 16) · phone block with circular icon · orange pill CTA right.
2. **Hero** — full-bleed image/dark field with organic blob + rotated-square graphic shapes; Fredoka 50px headline; 16/34 body; pill CTA.
3. **Section head** — orange/script eyebrow → Fredoka heading where ONE word carries a hand-drawn brush underline.
4. **3-up tinted feature cards** — 20px radius, pale tint (peach / mint / lilac), circular icon top-left, title + 2 lines.
5. **Cause/class cards** — image top, white 20px-radius body, title, 2-line desc, coloured progress bar with circular knob, two metric labels, orange pill.
6. **Stat cards** — big Fredoka number + small label in pale tinted 20px cards.
7. **Approach grid** — 2×2 icon-circle + title + body, faint line-art illustration behind.
8. **Video block** — large 20px-radius image with centred circular play button.
9. **Team cards** — rounded portrait, name/role, small square arrow button.
10. **Testimonials** — big quote glyph, body copy, avatar + name/role.
11. **Dark CTA band** — centred Fredoka headline + orange pill on `#343434`.
12. **Dark footer** — big headline + pill left, 3 link columns, contact rows with icons, circular socials, thin legal bar.
13. Brush-stroke / organic image masks and faint outline line-art as decoration.

## SJP brand (from SJP Media/DESIGN.md)
Plum `#381064` · Orange `#FE7300` · Peach `#DDB18F` · Cream `#FFF9F4` · Lilac `#F4EEF8` · Ink `#211B27`.
Approved type there: Poppins + DM Sans. Client wants BRIGHTER than the ChatGPT concept.

## Source docs in the mounted folder
- `SJP-Website-Information-Architecture-and-Content-Strategy.md` — full IA, sitemap, page blueprints, copy lines
- `PRODUCT.md` — confirmed class facts / undecided list
- `DESIGN.md` — the ChatGPT design system
