# Design System: Factory ERP (Bespoke Hairpiece MES)
**Project ID:** `12760513028391886822`

## 1. Visual Theme & Atmosphere
The Factory ERP interface blends **Industrial High-Density Engineering** with **Bespoke Hair Atelier Craftsmanship**. Designed for professional operations (salon owners, clinic buyers, factory technicians, and executive accountants), the visual language emphasizes precision, dark slate durability, hairline mechanical dividers, high-visibility status telemetry, and warm amber gold focal points.

## 2. Color Palette & Roles
- **Canvas / Background** (`#090E17`) – Main deep midnight obsidian background
- **Surface Level 1 (Card/Container)** (`#131B2B`) – Primary panel & card background
- **Surface Level 2 (Elevated/Modals)** (`#1C263B`) – Interactive drawers, popovers, and elevated tables
- **Structural Borders** (`#28354D`) – 1px mechanical hairline borders and dividers
- **Primary Accent / Brand** (`#F59E0B`) – Polished Amber Gold for primary CTAs, active states, and highlighted KPIs
- **Primary Hover / Deep** (`#D97706`) – Burnished Gold for pressed states and active accents
- **Success / MES Verified** (`#10B981`) – Emerald Green for passed QC, scanned barcodes, and verified payments
- **Warning / Bottleneck** (`#F97316`) – Amber Orange for approaching delivery D-Days and line bottlenecks
- **Danger / Urgent** (`#EF4444`) – Crimson Red for rejected QC, payment overdue, and Rush alerts
- **Text Primary** (`#F8FAFC`) – Pure Titanium Off-White for headers and primary values
- **Text Secondary** (`#94A3B8`) – Cool Slate for labels, metadata, and helper text
- **Text Muted** (`#64748B`) – Low-contrast text for captions and disabled elements

## 3. Typography Rules
- **Display & Headlines:** `Outfit` (or `Inter` bold) – Clean, modern, confident geometric hierarchy.
  - Page Titles: `28px` / Semibold / `-0.02em` tracking
  - Section Headers: `20px` / Semibold / `-0.01em` tracking
  - Card Titles: `15px` / Medium
- **Body & Controls:** `Inter` – Hyper-legible humanist grotesque for forms, tables, and workflow texts.
  - Body Normal: `14px` / Regular
  - Body Small: `12px` / Regular
  - Form Labels: `11px` / Semibold / Uppercase with `0.08em` letter-spacing
- **Technical & Financial Readouts:** `JetBrains Mono` – Monospaced precision for order numbers (`#ORD-2026-9041`), QR codes, dimensions (`W: 7.5" x L: 9.0"`), hair densities (`90% Light-Med`), currency (`$1,480.00 AUD`), and timestamps.

## 4. Component Stylings
* **Buttons:**
  - *Primary:* Solid `#F59E0B` fill, `#090E17` bold typography, `6px` radius (`rounded-md`), flat with subtle hover shift to `#D97706`.
  - *Secondary / Spec:* `#131B2B` background with `1px solid #28354D` border, `#F8FAFC` text, illuminating to `#F59E0B` on hover.
  - *MES Scanner Action:* `#090E17` with `1px solid #10B981` border and emerald text/glow for barcode scans.
* **Cards & Containers:**
  - `bg-[#131B2B]` with `border border-[#28354D]`, `rounded-xl` (`12px`), internal padding `1.25rem` (`20px`).
* **Form Inputs & Selectors:**
  - Dark container `#090E17`, `1px solid #28354D`, text `#F8FAFC`, placeholder `#64748B`.
  - Focused state activates crisp `1px solid #F59E0B` with zero fuzzy shadows.
* **Badges & Chips:**
  - Compact `4px` radius, monospaced font, colored tinted background (`rgba(16, 185, 129, 0.15)`) with matching `1px` subtle border.

## 5. Layout Principles
- **Grid:** 12-column responsive layout, maximum container width `1440px`.
- **Spacing:** Compact 4px/8px engineering rhythm (`gap-4`, `p-6`).
- **Data Density:** High-information density with tabbed spec sheets, split pane views (list on left, inspect on right), and rapid-action toolbars.

## 6. Design System Notes for Stitch Generation
**Copy this block into every baton prompt:**

**DESIGN SYSTEM (REQUIRED):**
- Platform: Web Application, Desktop-optimized with responsive tablet support
- Theme: Dark Industrial Precision with Bespoke Atelier Gold
- Background: Deep Midnight Slate `#090E17`, Card Surfaces `#131B2B`, Borders `#28354D`
- Primary Accent: Polished Amber Gold `#F59E0B`, Burnished Gold `#D97706`
- Status Colors: Emerald Verified `#10B981`, Amber Warning `#F97316`, Crimson Rush `#EF4444`
- Typography: Headers in Outfit/Inter, Body in Inter, Technical Codes/Measurements/Prices in JetBrains Mono
- Shapes: Modern crisp corners (4px to 10px radius), hairline mechanical borders, zero gratuitous heavy drop-shadows
- Tone: High-density B2B Enterprise ERP & MES, Authoritative, Functional, Artisanal Quality

