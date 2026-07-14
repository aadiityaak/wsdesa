---
name: "Pinterest-design-analysis"
description: |
  Pinterest-style photography-first design system with masonry grid, warm-cream chrome, Pin Sans typography, and single red CTA accent. Invoke when building UI that follows Pinterest aesthetics — masonry pin grids, cream-neutral palettes, rounded cards, or content-discovery layouts.
---

# Pinterest Design Analysis

A photography-first discovery system organized around the Pinterest Red CTA, the masonry pin grid, and a soft warm-cream chrome that gets out of the imagery's way. Pin imagery is the load-bearing visual element. The chrome is quiet: warm grays, true whites, and a single saturated red.

## Colors

| Token | Value |
|---|---|
| primary | #e60023 |
| primary-pressed | #cc001f |
| ink | #000000 |
| ink-soft | #211922 |
| body | #33332e |
| charcoal | #262622 |
| mute | #62625b |
| ash | #91918c |
| stone | #c8c8c1 |
| hairline | #dadad3 |
| hairline-soft | #e5e5e0 |
| canvas | #ffffff |
| surface-soft | #fbfbf9 |
| surface-card | #f6f6f3 |
| secondary-bg | #e5e5e0 |
| secondary-pressed | #c8c8c1 |
| surface-dark | #262622 |
| on-primary | #ffffff |
| on-secondary | #000000 |
| on-dark | #ffffff |
| on-dark-mute | rgba(255,255,255,0.7) |
| focus-outer | #435ee5 |
| focus-inner | #ffffff |
| error | #9e0a0a |
| error-deep | #cc001f |
| success-deep | #103c25 |
| success-pale | #c7f0da |
| accent-pressed-blue | #617bff |
| accent-purple | #7e238b |
| accent-purple-deep | #6845ab |

## Typography

Font: Pin Sans (proprietary). Substitutes: Inter (400/500/600/700) for body, Manrope for display.

| Token | Size | Weight | Line Height | Letter Spacing |
|---|---|---|---|---|
| display-xl | 70px | 600 | 1.1 | -1.2px |
| display-lg | 44px | 700 | 1.15 | -0.8px |
| heading-xl | 28px | 700 | 1.2 | -1.2px |
| heading-lg | 22px | 600 | 1.25 | 0 |
| heading-md | 18px | 600 | 1.3 | 0 |
| body-md | 16px | 400 | 1.4 | 0 |
| body-strong | 16px | 600 | 1.4 | 0 |
| body-sm | 14px | 400 | 1.4 | 0 |
| body-sm-strong | 14px | 700 | 1.4 | 0 |
| caption-md | 12px | 500 | 1.5 | 0 |
| caption-sm | 12px | 400 | 1.4 | 0 |
| link-md | 16px | 600 | 1.4 | 0 |
| button-md | 14px | 700 | 1 | 0 |
| button-sm | 12px | 700 | 1 | 0 |

## Border Radius

| Token | Value | Use |
|---|---|---|
| rounded-none | 0px | Footer, nav, page sections |
| rounded-sm | 8px | Rare medium-radius |
| rounded-md | 16px | Buttons, inputs, pin cards, feature cards, category tiles |
| rounded-lg | 32px | Large pin cards, modal cards |
| rounded-full | 9999px | Search bar, filter chips, pills, avatars |

## Spacing

| Token | Value |
|---|---|
| xxs | 4px |
| xs | 6px |
| sm | 8px |
| md | 12px |
| lg | 16px |
| xl | 24px |
| xxl | 32px |
| section | 64px |

## Components

### Buttons
- button-primary: bg #e60023, white text, rounded-md (16px), button-md typography, padding 6px 14px, height 40px. Pressed: #cc001f.
- button-secondary: bg #e5e5e0, black text, rounded-md, padding 6px 14px, height 40px. Pressed: #c8c8c1.
- button-tertiary: transparent bg, black text, rounded-md.
- button-icon-circular: bg #f6f6f3, rounded-full, 40x40px.
- button-pill-on-image: bg white, black text, rounded-full, padding 8px 14px.
- button-disabled: bg #f6f6f3, text #91918c.

### Inputs
- text-input: bg white, body-md, rounded-md, padding 11px 15px, height 44px. Focus: 2px #000 border + 4px #435ee5 outline.
- search-bar: bg #f6f6f3, body-md, rounded-full, padding 11px 15px, height 48px. Focus: bg white + 1px #91918c border.

### Cards
- pin-card: bg #f6f6f3, rounded-md (16px), zero internal padding. Image is full-bleed.
- pin-card-large: same but rounded-lg (32px).
- pin-overlay-pill: bg white, button-sm, rounded-full, padding 6px 12px. Floats over pin corner.
- category-tile: bg #f6f6f3, rounded-md, padding 16px.
- feature-card: bg white, rounded-md, padding 32px. 4:5 portrait + heading-xl headline + body + red CTA.
- feature-card-soft: same but bg #f6f6f3.
- modal-card: bg white, rounded-lg (32px), padding 32px. 50% scrim + 16px ambient shadow.

### Filter Chips
- filter-chip: bg #f6f6f3, black text, rounded-full, padding 8px 16px.
- filter-chip-active: bg #000, white text, rounded-full.

### Navigation and Footer
- primary-nav: bg white, height 64px, body-strong. Red CTA always visible.
- hero-cta-strip: bg #262622, white text, heading-xl, padding 48px 32px.
- footer-section: bg white, text #62625b, body-sm, padding 32px 24px.

## Layout Rules
- Max width: 1280px with 24px gutters.
- Section rhythm: 64px vertical gap between major blocks.
- Pin grid gutters: 8px horizontal and vertical.
- Pin masonry columns: 5-6 at 1920px, 4 at 1280px, 3 at 1024px, 2 at 768px, 1 at 480px.
- Hero feature row: asymmetric 2-column alternating left/right.
- Footer: 4-col desktop, 2-up tablet, 1-up mobile.

## Core Principles

### Do
- Reserve #e60023 (Pinterest Red) for primary CTAs, active-tab indicator, brand wordmark. Never decorative.
- Use rounded-md (16px) on every interactive element and standard card.
- Stage every pin image inside pin-card with zero internal padding. The photograph IS the card.
- Stack sections at 64px rhythm; tighten pin grids to 8px gutters.
- Use pin-overlay-pill to anchor search-term tags on category tile imagery.
- Build hierarchy from font weight (400/600/700) and size, not color tinting.
- Apply -1.2px letter-spacing on display-xl and heading-xl.

### Do Not
- No sharp-cornered buttons or cards. No rounded-none interactive elements.
- No drop shadows on cards. Only shadow is 16px ambient under modal-card.
- No internal padding on pin-card. Image is full-bleed.
- Do not replace #e60023 with another red.
- No radius between 16px and 32px. Jump directly from md to lg.
- No ink-soft outside inline body anchor links.

## Elevation
- Level 0 Flat: Default. Pin cards, feature cards, footer.
- Level 1 Hairline: 1px solid #dadad3. Inputs, dividers.
- Level 2 Modal scrim + shadow: Dark scrim + 16px ambient. Login/signup modal.

## Responsive Breakpoints
- ultrawide 1920px+: 5-6 col grid
- desktop-large 1440px: 4-col grid, full nav
- desktop 1280px: Same, narrower gutters
- desktop-small 1024px: 3-col grid
- tablet 768px: 2-col grid, hamburger nav
- mobile 480px: 1-col grid, hero 70px to 44px
- mobile-narrow 320px: Hero to 36px, section padding 32px
