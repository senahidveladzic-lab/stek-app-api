# Brand Guidelines

Shared design system for the web app (Laravel/React) and Expo mobile app.

---

## App Name: Štek App

**Brand name:** Štek App
**Meaning:** "Štek" (also "šteka") is Balkan slang for a hidden money stash — "imam štek" means "I have a stash." The name captures the essence of saving and tracking your money.

### App Store Naming

Diacritics (Š) hurt discoverability on both Apple App Store and Google Play — search indexing ignores or mishandles special characters. Both stores recommend against them.

**Use "Stek" (without diacritic) as the store listing name.** Users in the Balkans will read "Stek" as "Štek" — it's the same word.

| Platform | Name | Subtitle / Short Description |
|----------|------|------------------------------|
| **Apple App Store** | Stek | Prati troškove i štedi pametno |
| **Google Play** | Stek - Praćenje troškova | Praćenje troškova i budžeta za domaćinstvo |
| **Web app (internal)** | Štek App | Full diacritic, no ASO constraints |
| **Expo app display name** | Stek | Shown under the app icon on home screen |
| **Expo `app.json` slug** | stek | Lowercase, no diacritics |

### Usage Rules

- In UI text, marketing, and branding materials: always use **Štek** (with diacritic)
- In technical contexts (URLs, slugs, bundle IDs, store listings): use **stek** (without diacritic)
- Bundle ID suggestion: `com.stek.app` (iOS) / `com.stek.app` (Android)

---

## Color Palette

### Primary — Teal

The brand primary is teal, conveying trust (like blue) with growth/money associations (like green). Inspired by Copilot Money's clean aesthetic and Spendee's energy.

| Token | Light Mode | Dark Mode | Hex (approx) |
|-------|-----------|-----------|---------------|
| **Primary** | `oklch(0.6 0.135 175)` | `oklch(0.7 0.14 175)` | `#0D9488` / `#14B8A6` |
| **Primary foreground** | `oklch(0.985 0 0)` | `oklch(0.16 0.015 260)` | White / Dark |

### Semantic Colors

| Purpose | Color | Hex (approx) | Usage |
|---------|-------|---------------|-------|
| **Success** | Emerald-500 | `#10B981` | Under budget, savings, income |
| **Warning** | Amber-500 | `#F59E0B` | Approaching limit, caution |
| **Danger** | Red-500 | `#EF4444` | Over budget, overspending |
| **Info** | Primary (teal) | `#0D9488` | Links, accents, focus rings |

### Neutrals (warm-tinted)

Backgrounds and text use a subtle blue-gray warmth rather than pure gray.

| Token | Light Mode | Dark Mode |
|-------|-----------|-----------|
| **Background** | `oklch(0.985 0.002 250)` — near-white with cool tint | `oklch(0.16 0.015 260)` — deep blue-charcoal |
| **Card** | `oklch(1 0 0)` — pure white | `oklch(0.2 0.015 260)` — elevated dark |
| **Muted** | `oklch(0.96 0.008 250)` | `oklch(0.26 0.015 260)` |
| **Muted text** | `oklch(0.55 0.02 260)` | `oklch(0.7 0.015 260)` |
| **Border** | `oklch(0.91 0.008 250)` | `oklch(0.28 0.015 260)` |
| **Foreground** | `oklch(0.17 0.02 260)` — near-black | `oklch(0.96 0.005 250)` — near-white |

### Chart / Category Colors

Used for data visualization, member split bars, and category accents.

| Slot | Light Mode | Dark Mode | Description |
|------|-----------|-----------|-------------|
| Chart 1 | `oklch(0.6 0.135 175)` | `oklch(0.7 0.14 175)` | Teal (primary) |
| Chart 2 | `oklch(0.65 0.18 280)` | `oklch(0.7 0.17 280)` | Indigo |
| Chart 3 | `oklch(0.7 0.16 50)` | `oklch(0.75 0.16 50)` | Orange |
| Chart 4 | `oklch(0.65 0.2 330)` | `oklch(0.7 0.2 330)` | Pink |
| Chart 5 | `oklch(0.7 0.14 140)` | `oklch(0.75 0.14 140)` | Lime |

---

## Typography

| Element | Font | Weight | Size | Tracking |
|---------|------|--------|------|----------|
| **Hero amount** | Instrument Sans | 700 (bold) | 30–40px (`text-3xl` to `text-4xl`) | `tracking-tight` |
| **Card title** | Instrument Sans | 600 (semibold) | 16px | Normal |
| **Body** | Instrument Sans | 400 (regular) | 14px (`text-sm`) | Normal |
| **Labels / captions** | Instrument Sans | 500 (medium) | 11–12px (`text-xs`) | `tracking-wide` uppercase for labels |
| **Amounts (tabular)** | Instrument Sans | 600–700 | Varies | `tabular-nums` (monospaced digits) |

**Mobile (Expo):** Use the system font (San Francisco on iOS, Roboto on Android) for native feel. Match weight/size ratios from above.

---

## Spacing & Layout

| Token | Value | Usage |
|-------|-------|-------|
| **Border radius** | `0.625rem` (10px) | Cards, buttons, inputs |
| **Card padding** | 20–24px | Internal card spacing |
| **Section gap** | 24px (`gap-6`) | Between dashboard cards |
| **Grid** | 3-column on desktop, 1-column on mobile | Top stats row |

---

## Component Patterns

### Cards
- White surface on light, elevated dark surface on dark
- Subtle border (no heavy shadows)
- `rounded-xl` (10px radius)
- Content uses consistent vertical rhythm (`space-y-3` to `space-y-4`)

### Progress Bars
- Height: `h-1.5` to `h-2` (6–8px)
- Background: `bg-muted` (light) / `bg-primary/10` (hero)
- Fill: semantic color based on status (emerald/amber/red)
- Always `rounded-full`

### Badges
- Small (`text-xs`), rounded (`rounded-md`)
- `secondary` variant for neutral info
- `destructive` variant for alerts/over-budget

### Expense List Items
- Category emoji in tinted background square (use category color at 10% opacity)
- Merchant/description as primary text
- Category name + user as secondary muted text
- Amount right-aligned, bold, tabular-nums

---

## Status Color Logic

### Budget Progress
| Condition | Color | Meaning |
|-----------|-------|---------|
| < 75% used | Emerald | On track |
| 75–99% used | Amber | Approaching limit |
| >= 100% used | Red | Over budget |

### Month-over-Month Comparison (no budget)
| Condition | Color | Meaning |
|-----------|-------|---------|
| Spending decreased | Emerald | Improving |
| Spending increased < 20% | Amber | Slight increase |
| Spending increased > 20% | Red | Significant increase |
| No previous data | Primary (teal) | Neutral |

---

## Dark Mode

- System-automatic + manual toggle (Light / Dark / System)
- Dark backgrounds use deep blue-charcoal (`oklch(0.16 0.015 260)`), not pure black
- Cards are slightly elevated (`oklch(0.2 0.015 260)`)
- Primary teal is brightened in dark mode for contrast
- Semantic colors (emerald/amber/red) remain consistent across modes
- Borders and muted surfaces use the same blue-gray warmth

---

## Expo Mobile App Notes

### Adapting for React Native
- Use `react-native` `StyleSheet` with the same OKLCH values (convert to hex for RN compatibility)
- System font for text, match weight ratios
- `borderRadius: 10` for cards
- Bottom tab navigation (not sidebar)
- Hero stat as the first visible element on dashboard
- Category progress bars use the same semantic color logic
- Pull-to-refresh on dashboard for data reload

### Approximate Hex Values for RN

```
Primary Light: #0D9488    Primary Dark: #14B8A6
Background Light: #F8FAFC Background Dark: #1A1F2E
Card Light: #FFFFFF        Card Dark: #232A3B
Border Light: #E2E8F0      Border Dark: #2D3548
Foreground Light: #1A202E  Foreground Dark: #F0F2F5
Muted Light: #F1F4F8       Muted Dark: #2D3548
Muted Text Light: #6B7A8D  Muted Text Dark: #9BA4B2
Emerald: #10B981
Amber: #F59E0B
Red: #EF4444
Chart Indigo: #6366F1
Chart Orange: #F97316
Chart Pink: #EC4899
Chart Lime: #84CC16
```
