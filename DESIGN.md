# DESIGN.md — Sityos AI Design System

> Design reference for `/impeccable` skill. Reflects state after Redesign v2 (June 2026).

---

## Product Identity

**Product**: Sityos AI (platform name) / Sityos Automate (product name)
**Type**: SaaS content platform + AI automation hub
**Purpose**: Central hub for AI workflow automation, tutorials, and use-case documentation
**Audience**: Tech professionals, automation teams, IA early adopters, digital productivity users
**Tone**: Professional-tech, premium, forward-thinking — confident without being arrogant
**Tagline direction**: Automation intelligence, accessible and practical

---

## Design Principles

1. **Dark-first** — Default dark mode with indigo-tinted blacks for visual depth (not flat black)
2. **Gold as brand soul** — `#C9A227` is the primary brand accent; never substitute with other yellows
3. **Blue for action** — `#3F7EFF` exclusively for interactive elements (links, CTAs, focus rings)
4. **Typography leads** — Large, bold headings are the primary visual element; copy is secondary
5. **Premium whitespace** — Generous section padding creates a high-end, uncluttered feel
6. **Motion with purpose** — Animations serve onboarding and state feedback; never purely decorative
7. **Glassmorphism, not frosted** — Subtle backdrop-blur + semi-transparent surfaces, not heavy frosting
8. **WCAG AA everywhere** — Every text/bg combination meets AA contrast minimum

---

## Color System

### Brand Layer (flat tokens)

| Token | Value | Usage |
|-------|-------|-------|
| `--gold` | `#C9A227` | Primary brand accent — borders, icons, highlights |
| `--gold-dim` | `#9B7A1A` | WCAG AA on light backgrounds |
| `--gold-pale` | `rgba(201,162,39, 0.10)` | Icon backgrounds, subtle fills |
| `--gold-line` | `rgba(201,162,39, 0.22)` | Thin decorative borders |
| `--gold-border` | `rgba(201,162,39, 0.40)` | Hover borders, visible borders |
| `--blue` | `#3F7EFF` | Interactive: links, CTAs, focus |
| `--blue-dim` | `#2B62D6` | WCAG AA on light backgrounds |
| `--blue-pale` | `rgba(63,126,255, 0.10)` | Alternate icon backgrounds |
| `--blue-border` | `rgba(63,126,255, 0.35)` | Interactive borders |

### Dark Surfaces (always use in dark mode)

| Token | Value | Description |
|-------|-------|-------------|
| `--dark` | `#080810` | Page base — deepest |
| `--dark-2` | `#0E0E1A` | Elevated surfaces (cards, footer) |
| `--dark-3` | `#141428` | Surface layer (social proof, code blocks) |
| `--dark-4` | `#1E1E34` | Borders |
| `--dark-5` | `#24243A` | Hover states |

> All darks are **indigo-tinted** (not pure black) for visual richness.

### Light Surfaces

| Token | Value |
|-------|-------|
| `--light` | `#F8F8F8` |
| `--light-2` | `#EFEFEF` |
| `--light-3` | `#E2E2E2` |

### Semantic Tokens (mode-aware via CSS custom properties)

| Token | Dark value | Light value |
|-------|-----------|-------------|
| `--sao-bg-base` | `#080810` | `#F8F8F8` |
| `--sao-bg-elevated` | `#0E0E1A` | `#EFEFEF` |
| `--sao-bg-surface` | `#141428` | `#E2E2E2` |
| `--sao-text-primary` | `#F8F8F8` | `#0B0B0F` |
| `--sao-text-secondary` | `rgba(248,248,248,0.72)` | `rgba(11,11,15,0.70)` |
| `--sao-text-muted` | `rgba(248,248,248,0.45)` | `rgba(11,11,15,0.45)` |
| `--sao-accent` | `#C9A227` | `#9B7A1A` (dimmed for WCAG AA) |
| `--sao-accent-interactive` | `#3F7EFF` | `#2B62D6` (dimmed for WCAG AA) |

### Badge Colors (semantic, never change to gold)

| Content type | Color | Token |
|---|---|---|
| Article | Purple `#7c3aed` | `--sao-badge-article` |
| Tutorial | Green `#10b981` | `--sao-badge-tutorial` |
| Use Case | Blue `#2563eb` | `--sao-badge-usecase` |

### Semantic States

| State | Color |
|-------|-------|
| Success | `#34C878` |
| Warning | `#F0A028` |
| Error | `#E05555` |
| Info | `#5090DC` |

---

## Typography

**Primary font**: Sora (Google Fonts) — used for all UI text
**Mono font**: JetBrains Mono — code blocks, technical labels

### Type Scale (post Redesign v2 — June 2026)

| Token | Value | Max size | Usage |
|-------|-------|----------|-------|
| `--sao-type-hero` | `clamp(3rem, 7vw + 1rem, 5.5rem)` | ~88px | Hero headlines only |
| `--sao-type-display` | `clamp(2.25rem, 4.5vw + 0.75rem, 3.25rem)` | ~52px | Section headlines, CTA headlines |
| `--sao-type-headline` | `clamp(1.5rem, 2.5vw + 0.5rem, 1.875rem)` | ~30px | Card titles, subheadings |
| `--sao-type-title` | `clamp(1.125rem, 1.5vw + 0.25rem, 1.375rem)` | ~22px | Feature grid titles, small headings |
| `--sao-type-body-lg` | `1.25rem` | — | Subtitles, lead paragraphs |
| `--sao-type-body` | `0.9375rem` | — | Body copy |
| `--sao-type-label` | `0.875rem` | — | UI labels, metadata |
| `--sao-type-meta` | `0.75rem` | — | Timestamps, badges, eyebrows |

### Typography Rules

- Hero headline: `font-weight: 800`, `letter-spacing: -0.04em`, `line-height: 1.0`
- Section headings: `font-weight: 700`, `letter-spacing: -0.04em`
- Eyebrows: `font-size: meta`, `letter-spacing: 0.30em`, `text-transform: uppercase`, gold color
- Body: `line-height: 1.6`, secondary text color
- `<em>` in headlines: gradient text treatment with `--sao-gradient-text`

---

## Spacing System (4px base grid)

| Token | Value | Usage |
|-------|-------|-------|
| `--sao-space-2` | `0.5rem / 8px` | Tight UI gaps |
| `--sao-space-4` | `1rem / 16px` | Default gap |
| `--sao-space-6` | `1.5rem / 24px` | Card body padding (standard) |
| `--sao-space-8` | `2rem / 32px` | Section internal padding, card body (new) |
| `--sao-space-12` | `3rem / 48px` | Header spacing |
| `--sao-space-16` | `4rem / 64px` | Large gaps |
| `--sao-space-24` | `6rem / 96px` | Hero padding |
| `--sao-section-padding` | `clamp(5rem, 10vw, 9rem)` | Section vertical padding (homepage) |

---

## Motion

| Token | Value | Usage |
|-------|-------|-------|
| `--sao-duration-fast` | `100ms` | Micro-interactions |
| `--sao-duration-base` | `180ms` | Default transitions |
| `--sao-duration-medium` | `280ms` | Image zoom, card hover |
| `--sao-duration-slow` | `420ms` | Page entries, large components |
| `--sao-easing-standard` | `cubic-bezier(0.4, 0, 0.2, 1)` | Default |
| `--sao-easing-spring` | `cubic-bezier(0.34, 1.56, 0.64, 1)` | Bouncy interactions |
| `--sao-easing-enter` | `cubic-bezier(0, 0, 0.2, 1)` | Entry animations |

---

## Border Radius

| Token | Value | Usage |
|-------|-------|-------|
| `--sao-radius-sm` | `4px` | Tags, pills |
| `--sao-radius-md` | `8px` | Buttons, inputs |
| `--sao-radius-lg` | `14px` | Feature grid items, old cards |
| `--sao-radius-xl` | `20px` | Cards (post redesign v2) |
| `--sao-radius-full` | `100px` | Pills, badges |

---

## Shadows & Glows

| Token | Value | Usage |
|-------|-------|-------|
| `--sao-shadow-sm` | `0 1px 2px rgba(0,0,0,0.08)` | Subtle elevation |
| `--sao-shadow-md` | `0 4px 12px rgba(0,0,0,0.12)` | Cards at rest |
| `--sao-shadow-lg` | `0 12px 40px rgba(0,0,0,0.18)` | Modals, dropdowns |
| `--sao-shadow-glow` | `0 0 28px rgba(201,162,39,0.32)` | Gold glow |
| `--sao-shadow-glow-strong` | `0 0 48px rgba(201,162,39,0.45)` | Hover states |
| `--sao-shadow-glow-blue` | `0 0 24px rgba(63,126,255,0.30)` | Interactive glow |

---

## Gradients

```css
--sao-gradient-primary:  linear-gradient(135deg, #C9A227 0%, #9B7A1A 100%)
--sao-gradient-text:     linear-gradient(135deg, #D4B44A 0%, #C9A227 50%, #F0CC60 100%)
--sao-gradient-mesh:     [4-layer radial gradients — gold + blue depth]
--sao-divider-gradient:  linear-gradient(90deg, transparent 0%, rgba(201,162,39,0.25) 50%, transparent 100%)
```

---

## Component Inventory (SDC)

All components live in `docroot/themes/custom/sityos_automate_olivero/components/`.

| Component | Variants | Key props | State |
|-----------|----------|-----------|-------|
| `hero` | default, centered, split | eyebrow, headline (with `<em>`), subtitle, cta_primary/secondary, picture_sources | ✅ Active |
| `card` | standard, featured, horizontal | title, url, description, picture_sources, label, date | ✅ Active |
| `feature-grid` | cards, minimal, icon-left | heading, subtitle, items[], columns | ✅ Active |
| `cta-central` | — | headline (with `<em>`), body, cta_primary/secondary, microcopy | ✅ Active |
| `testimonials` | carousel, grid, single | heading, items[] (quote, author, role, avatar) | ✅ Active (placeholder data) |
| `social-proof` | — | intro_text, logos[], metrics[] | ⚠️ Placeholder data |
| `cta-button` | primary, ghost, gradient | label, url, variant, size (sm/md/lg) | ✅ Active |
| `stats-counter` | — | items[], animate | Available, unused |
| `newsletter-signup` | — | heading, form_action | Available, unused |

### Component Design Patterns

- **Card**: `border-radius: 20px`, hover `translateY(-3px)` + gold glow, image zoom 1.06×
- **Feature Grid items**: 5rem × 5rem icons with `gold-pale` background, `border-radius: 14px`
- **Section headings**: `::before` gold line 40px × 2px, `letter-spacing: -0.04em`
- **CTA buttons**: gold shimmer animation on primary, ghost with animated border on gradient variant
- **Nav links**: gold underline sweeps from 0% to 100% on hover

---

## Homepage Layout

| Region | Component | Notes |
|--------|-----------|-------|
| `hero` | `sityos_hero_home` | Gradient mesh, centered variant, `min-height: clamp(680px, 92vh, 1000px)` |
| `highlighted` | `sityos_social_proof` | **[PLACEHOLDER DATA]** — metrics strip |
| `content_above` | `sityos_value_props` | 3-pillar feature-grid, trilingual inline |
| `content` | Views `use_cases` | Slick carousel, 2-col layout at 584px+ |
| `content` | `sityos_use_cases_cta` | "View All Use Cases →" |
| `content` | Views `tutorials_homepage` | **[CT PENDING]** |
| `content` | `sityos_tutorials_cta` | "Browse All Tutorials →" |
| `content_below` | `sityos_cta_central` | Blue gradient + gold, CTA to /subscribe |
| `footer_top` | `sityos_testimonials_home` | **[PLACEHOLDER DATA]** |

---

## Breakpoints

| Name | Value | Grid |
|------|-------|------|
| micro | 500px | compact header |
| tablet | 768px | 8 columns |
| desktop | 1024px | 12 columns |
| wide | 1440px | 12 columns + auto margin |
| olivero-nav | 75rem (1200px) | desktop nav threshold |

---

## Accessibility Requirements

- **Minimum contrast**: WCAG AA (4.5:1 for body text, 3:1 for large text)
- **Gold on dark**: `#C9A227` on `#080810` → ~6.8:1 ✅
- **Gold-dim on light**: `#9B7A1A` on `#F8F8F8` → ~4.5:1 ✅
- **Blue on dark**: `#3F7EFF` on `#080810` → ~5.2:1 ✅
- **Blue-dim on light**: `#2B62D6` on `#F8F8F8` → ~4.7:1 ✅
- **Focus rings**: 2px gold `outline-offset: 3px` on all interactive elements
- **Reduced motion**: `@media (prefers-reduced-motion: reduce)` implemented in all animated components
- **Logo**: uses `role="img"` + `<title>` + `<desc>` in SVG
- **Skip link**: provided by Olivero base theme

---

## Known Design Debt

| Issue | Priority | Notes |
|-------|----------|-------|
| Social Proof placeholder data | HIGH | Logos[] and metrics[] need real data before launch |
| Testimonials placeholder data | HIGH | Quotes and authors are fake |
| Light mode underworked | MEDIUM | Homepage sections need more design attention in light mode |
| Footer layout | MEDIUM | Bug fix applied (light mode text), layout structure could improve |
| Hero mobile padding | LOW | Verify `<375px` — padding may be insufficient |
| Tutorial CT missing | LOW | `tutorials_homepage` View blocked until Tutorial CT created |

---

## Using /impeccable with This File

| Command | What it does |
|---------|-------------|
| `/impeccable audit` | Full interface review against these design principles |
| `/impeccable bolder` | Push components toward higher visual impact |
| `/impeccable polish` | Fine-tune spacing, typography, micro-details |
| `/impeccable shape` | Define new design direction for a section |
| `/impeccable craft` | Implement specific design improvements |
| `/impeccable animate` | Add motion design to components |
| `/impeccable clarify` | Remove visual noise, simplify |

### Context to provide with /impeccable commands:
- Specify which component or section: `hero`, `cards`, `feature-grid`, `homepage`, etc.
- Specify mode: `dark`, `light`, or `both`
- Specify constraint: `mobile-first`, `desktop`, `responsive`
