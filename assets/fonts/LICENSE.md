# Bundled font licenses

All bundled fonts are licensed under the SIL Open Font License 1.1
(https://openfontlicense.org/) and are GPL-compatible. Latin-subset
variable-font builds sourced from the Fontsource project
(https://fontsource.org/), which packages the upstream releases unmodified.

| Files | Typeface | Copyright |
|---|---|---|
| inter-latin-wght-{normal,italic}.woff2 | Inter | © The Inter Project Authors (https://github.com/rsms/inter) |
| lora-latin-wght-{normal,italic}.woff2 | Lora | © The Lora Project Authors (https://github.com/cyrealtype/Lora-Cyrillic) |
| jetbrains-mono-latin-wght-normal.woff2 | JetBrains Mono | © 2020 The JetBrains Mono Project Authors (https://github.com/JetBrains/JetBrainsMono) |

These families are optional presets: the theme's default stacks are system
fonts, and a bundled family downloads only when content actually uses it
(declared via theme.json `fontFace`, loaded lazily by the browser).
