# PRODUCT.md — Sityos AI

> Product context for `/impeccable` skill. Complements `DESIGN.md` (visual system).

---

## Product

**Platform**: Sityos AI / Sityos Automate
**URL**: sityos.com
**Type**: Content platform for AI automation — tutorials and practical use cases

---

## Core Product: Tutorials & Use Cases

The primary product of the site is its **content library** — Tutorials and Use Cases about AI automation, productivity tools, and digital workflows.

This content is the business. Everything else (homepage, navigation, landing pages) exists to bring visitors in and keep them reading.

### Tutorials
- Step-by-step technical guides on AI tools, automation workflows, and productivity stacks
- Structured with clear headings, code snippets, and actionable steps
- Long-form content (1,500–4,000 words) optimized for organic search
- Badge color: green `#10b981`

### Use Cases
- Real-world examples of AI automation applied to specific problems or workflows
- More narrative than tutorials; shows outcomes, not just steps
- Medium-form content (800–2,000 words), high intent searches
- Badge color: blue `#2563eb`

---

## Business Model

**Primary monetization**: Google AdSense
- Display ads placed within content pages (articles, tutorials, use cases)
- Revenue driven by pageviews and session depth
- More content consumed = more ad impressions = more revenue

**Secondary**: Organic growth → brand awareness for Sityos Automate product offerings

---

## Growth Strategy

**Acquisition channel**: Organic search (SEO-first)
- Content targets long-tail keywords in AI automation, no-code/low-code, productivity
- Multilingual: English (primary), Spanish, Catalan
- XML Sitemap active, Pathauto canonical URLs

**Retention**: Internal linking between tutorials and use cases
- "Related tutorials" → extends session depth
- Use case → tutorial cross-links → funnel users into deeper content

---

## Target Audience

| Segment | Profile | Intent |
|---------|---------|--------|
| AI beginners | Professionals curious about AI tools | "How do I automate X?" |
| Automation practitioners | Developers, ops, freelancers using n8n, Make, Zapier, Claude | "What's the best way to do X?" |
| Content consumers | Tech-adjacent users via search | Informational, research |

**Key insight**: Most visitors arrive via Google with a specific question. They are not brand-aware. The design must earn trust and extend their stay within the first 3 seconds.

---

## Content Pages — Design Requirements

These requirements take priority on Tutorial and Use Case detail pages:

### Readability is non-negotiable
- Body text must be effortlessly readable: sufficient size, line-height, contrast
- Max content width: ~740px for prose — never full-width text on desktop
- Code blocks must be clearly distinct (monospace, surface background, subtle border)

### AdSense compatibility
- Layout must accommodate ad units without breaking reading flow:
  - **In-article ads**: between sections (after H2, mid-content)
  - **Sidebar (desktop)**: sticky column alongside content — 300×250 or 160×600
  - **Below-content**: rectangle unit after article body, before related content
- Reserve space in layout planning — ads are real content slots, not afterthoughts
- Never place ads where they obscure the reading path or overlap content

### Trust signals
- Author attribution, publish date, reading time — visible above the fold
- Category badge (green Tutorial / blue Use Case) — clear content-type signal
- Breadcrumb navigation — reinforces site structure and SEO

### Session extension
- "Related content" section at end of every article — minimum 3 cards
- In-content cross-links to related tutorials / use cases — natural, not forced
- "Browse all Tutorials" / "Browse all Use Cases" CTAs at bottom — low-friction next step

---

## Content Listing Pages

`/tutorials` and `/use-cases` are high-value landing pages:
- SEO landing pages for category-level keywords
- Entry point for users browsing by topic
- Filterable by tag / category
- Card grid layout with clear hierarchy: title, excerpt, date, reading time, badge

---

## Homepage Role

The homepage is a **traffic distribution layer**, not a destination:
- Capture attention → direct to Tutorials or Use Cases
- Social proof → build trust for first-time visitors
- Value props → explain what kind of content they'll find
- No conversion funnel (no product sign-up, no paywall)

---

## Content Types vs. Design Priorities

| Page type | #1 priority | #2 priority | #3 priority |
|-----------|------------|------------|------------|
| Tutorial / Use Case detail | Readability | Ad placement | Related content |
| `/tutorials`, `/use-cases` listing | Scannability | SEO structure | Filter/navigation |
| Homepage | Trust building | Content discovery | Performance |
| Tag archive `/tags` | Discovery | Breadth of topics | — |

---

## What Success Looks Like

- Visitors stay to read full articles (low bounce on content pages)
- Multiple pages viewed per session (internal linking works)
- Ad impressions per session > 3 (article length + related content working)
- Organic search traffic grows month over month (SEO + content quality)

---

## Using This with /impeccable

When auditing or crafting design decisions, this product context means:

- **Readability beats aesthetics** on content detail pages — if in doubt, choose the more readable option
- **Ad slots are product** — treat them as real UI elements with space requirements
- **Internal links are UX** — "Related tutorials" section is as important as the hero
- **Trust signals matter** — author, date, badge, breadcrumbs are not decorative; they are conversion elements
- **SEO structure is design** — H1 hierarchy, meta descriptions, and URL clarity are within design scope
