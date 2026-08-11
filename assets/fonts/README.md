# Self-hosted fonts

Latin subset only (`U+0000-00FF` + common punctuation), woff2 only, `font-display: swap`.
Registered through `theme.json` `settings.typography.fontFamilies[].fontFace` — never
via Google Fonts at runtime.

| File | Family | Weights | Bytes |
| --- | --- | --- | --- |
| `montserrat-variable.woff2` | Montserrat | **400–700 (variable)** | 37,956 |
| `poppins-600.woff2` | Poppins | 600 | 8,000 |
| `poppins-700.woff2` | Poppins | 700 | 7,816 |
| | | **Total** | **53,772 (~53KB)** |

## Why one Montserrat file

Google Fonts serves Montserrat as a **variable** font. Requesting weights 400, 500,
600 and 700 returns four byte-identical files (verified by md5) — the same variable
font, with only the `@font-face` `font-weight` descriptor differing. Shipping all four
would have cost ~114KB for nothing.

It is declared once with a weight *range*:

```json
{ "fontFamily": "Montserrat", "fontWeight": "400 700", "src": [ "...montserrat-variable.woff2" ] }
```

Poppins is **not** variable — 600 and 700 are genuinely different files, so both ship.

## Preloading

Budget rule: preload only the **two** files used above the fold on the current
template. In practice that is `montserrat-variable.woff2` plus whichever Poppins
weight the template's H1 uses (700 on nearly every page). Preload is emitted from
`inc/enqueue.php`, not hard-coded into a template part.

## Licensing

Both families are SIL Open Font License 1.1 — self-hosting is permitted, and the
licence text must travel with the fonts.

- Montserrat — Copyright 2024 The Montserrat.Git Project Authors. See `OFL-Montserrat.txt`.
- Poppins — Copyright 2020 The Poppins Project Authors. See `OFL-Poppins.txt`.

The two licence bodies are not byte-identical, so both files are kept.

## Refreshing

Fetch the CSS with a modern browser User-Agent (or Google serves TTF, not woff2), take
only the `@font-face` block whose `unicode-range` begins `U+0000-00FF`, and download
that URL:

```
https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Montserrat:wght@400;500;600;700&display=swap
```
