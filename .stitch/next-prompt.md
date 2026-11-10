---
page: admin-dashboard
---
An executive operations and manufacturing control dashboard for "Factory ERP: Custom Hairpiece Manufacturing & Order Management System".

**DESIGN SYSTEM (REQUIRED):**
- Platform: Web Application, Desktop-optimized with responsive tablet support
- Theme: Dark Industrial Precision with Bespoke Atelier Gold
- Background: Deep Midnight Slate `#090E17`, Card Surfaces `#131B2B`, Borders `#28354D`
- Primary Accent: Polished Amber Gold `#F59E0B`, Burnished Gold `#D97706`
- Status Colors: Emerald Verified `#10B981`, Amber Warning `#F97316`, Crimson Rush `#EF4444`
- Typography: Headers in Outfit/Inter, Body in Inter, Technical Codes/Measurements/Prices in JetBrains Mono
- Shapes: Modern crisp corners (4px to 10px radius), hairline mechanical borders, zero gratuitous heavy drop-shadows
- Tone: High-density B2B Enterprise ERP & MES, Authoritative, Functional, Artisanal Quality

**Page Structure & Core Requirements:**
1. **Top Header & Telemetry Navigation:**
   - Brand: "Factory ERP // OPERATIONS CONSOLE".
   - Status indicators: Active MES Nodes, D-Day Urgent orders counter, Shift Leader on duty, Live UTC clock.
   - Quick search bar: Order #, Client Name, Lot ID, Hair Shade lookup.
2. **Executive KPI Metric Tiles (4-Column HUD):**
   - Tile 1: "Today's New Orders" (e.g. 14 Orders / +3 from yesterday) with "Acceptance Pending: 4".
   - Tile 2: "D-Day Bottleneck Alert" (e.g. 7 Orders ≤ 48h) in Amber/Crimson warning styling.
   - Tile 3: "WIP Line Status" (e.g. 142 Active Units across Knotting, Ventilation, Autoclave, QC).
   - Tile 4: "Monthly Billing & A/R" (e.g. $184,520 AUD / Uncollected: $24,190 AUD) with collection progress bar.
3. **Main Content Grid (Split 8:4 or 7:5):**
   - **Left / Wide Area:**
     * **Interactive MES Bottleneck Radar:** Visual funnel showing hairpiece stages:
       1. Specs Review (8) → 2. Base Foundation (14) → 3. Single-Hair Hand-Tying / Knotting (64, Bottleneck!) → 4. Silicone Autoclave Bake (18) → 5. Final QC & Trichology Styling (12) → 6. Pack & Dispatch (8).
     * **Urgent Orders Queue Table:** Order ID, Client Salon, Item Type (Full Wig, Toupee, Top-piece), Base Spec, Rush Level (Standard vs 48h Expedited), Current Station, Action button ("Inspect & Fast-Track").
   - **Right / Secondary Area:**
     * **Raw Hair Inventory Gauges:** European Virgin Remy 60# (Blonde), Asian Virgin 1B#, Synthetic Heat-Resistant Blend stock meters with reorder triggers.
     * **Recent MES Station Scan Activity Feed:** Live timestamps of barcode scans from factory workstations (e.g. "Operator Kang scanned LOT-0891A @ Station 03 (Knotting complete - QC passed)").
4. **Action Dock & Quick Commands:**
   - "Batch Print Job Orders (QR)", "New Urgent Override", "Download Daily MES Audit PDF".
