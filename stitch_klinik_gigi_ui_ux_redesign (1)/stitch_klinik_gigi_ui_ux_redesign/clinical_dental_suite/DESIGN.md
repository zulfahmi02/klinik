---
name: Clinical Dental Suite
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#3d4947'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#6d7a77'
  outline-variant: '#bcc9c6'
  surface-tint: '#006a61'
  primary: '#00685f'
  on-primary: '#ffffff'
  primary-container: '#008378'
  on-primary-container: '#f4fffc'
  inverse-primary: '#6bd8cb'
  secondary: '#3b665f'
  on-secondary: '#ffffff'
  secondary-container: '#bdece2'
  on-secondary-container: '#416c65'
  tertiary: '#006194'
  on-tertiary: '#ffffff'
  tertiary-container: '#007bb9'
  on-tertiary-container: '#fdfcff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#89f5e7'
  primary-fixed-dim: '#6bd8cb'
  on-primary-fixed: '#00201d'
  on-primary-fixed-variant: '#005049'
  secondary-fixed: '#bdece2'
  secondary-fixed-dim: '#a2d0c6'
  on-secondary-fixed: '#00201c'
  on-secondary-fixed-variant: '#224e47'
  tertiary-fixed: '#cce5ff'
  tertiary-fixed-dim: '#93ccff'
  on-tertiary-fixed: '#001d31'
  on-tertiary-fixed-variant: '#004b73'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  display-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
    letterSpacing: -0.01em
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '600'
    lineHeight: 36px
    letterSpacing: -0.015em
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 30px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
    letterSpacing: 0em
  body-lg:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
    letterSpacing: 0em
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0em
  body-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: 0em
  label-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-xs:
    fontFamily: Plus Jakarta Sans
    fontSize: 11px
    fontWeight: '700'
    lineHeight: 14px
    letterSpacing: 0.04em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-mobile: 0.75rem
  margin: 2rem
  margin-mobile: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style

### Brand Personality & Core Atmosphere
The design system establishes a clinical, precise, yet deeply calming environment engineered specifically for specialized dental outpatient workflows (Poli Gigi). It rejects sterile coldness in favor of clinical reassurance: tranquil medical clarity, surgical hygiene, and effortless legibility. The interface prioritizes reducing anxiety for both practitioner and patient data intake, avoiding alarmist red alerts in routine states while preserving high clinical precision.

### Target Audience & Emotional Impact
Designed for dental surgeons, dental nurses, and clinical administrators managing electronic dental records (EDR), procedural documentation, and visual odontograms. The visual tone must elicit reassurance, operational competence, sterile hygiene, and absolute technical reliability without administrative friction.

### Visual Style: Modern Clinical Precision
A fusion of high-precision corporate minimalism and soft tactile clinical ergonomics:
- **Surfaces**: Crisp pure white cards against cool medical off-white backdrops, framed by whisper-soft slate borders rather than harsh drop shadows.
- **Micro-accents**: Calibrated surgical teal/emerald tones paired with soft cyan-mint washes, suggesting fresh airflow, clean surfaces, and medical safety.
- **Clarity First**: Dense clinical data and odontogram anatomical states maintain immediate optical distinction through distinct color-coded semantic keys without overwhelming visual noise.

## Colors

### Primary & Secondary Palette
- **Primary Teal**: `#0D9488` (Main brand color, active interactive controls, primary actions).
- **Primary Teal Deep**: `#0F766E` (Hover states, high-contrast active tabs, primary pressed states).
- **Secondary Mint Soft**: `#CCFBF1` (Secondary button backgrounds, selected row tints, badge containers).
- **Secondary Mint Ultralight**: `#F0FDFA` (Surface highlights, hover tints on white cards, odontogram active panel canvas).

### Neutrals & Surfaces
- **Canvas Base (`neutral_color_hex`)**: `#F8FAFC` (Cool off-white canvas, zero glare under clinical lighting).
- **Card & Container Fill**: `#FFFFFF` (Pure white surgical cards and modal sheets).
- **Subtle Border**: `#E2E8F0` (Dividers, card outlines, form input borders).
- **Surface Border Subtle**: `#F1F5F9` (Nested table borders and inactive component dividers).
- **Text Dominant (Charcoal Headings)**: `#0F172A` (Primary clinical data, tooth numbers, diagnoses).
- **Text Body (Slate)**: `#334155` (Clinical notes, form inputs, secondary labels).
- **Text Muted (Muted Slate)**: `#64748B` (Metadata, unselected tabs, secondary captions).

### Medical Status Indicators
- **Selesai / Sukses (Emerald)**: `#059669` (Background tint: `#ECFDF5`, Border: `#A7F3D0`).
- **Sedang Diperiksa / Dalam Proses (Amber)**: `#D97706` (Background tint: `#FFFBEB`, Border: `#FDE68A`).
- **Terdaftar / Info Pasien (Sky)**: `#0284C7` (Background tint: `#F0F9FF`, Border: `#BAE6FD`).
- **Batal / Peringatan Kritis (Rose)**: `#E11D48` (Background tint: `#FFF1F2`, Border: `#FECDD3`).

### Odontogram Diagnostic Semantic Tokens
Every tooth surface (buccal, lingual, occlusal, mesial, distal) and whole-tooth condition strictly follows standardized clinical color tokens:
- **Sehat (Healthy)**: Stroke `#059669` (1.5px outline), Fill `#FFFFFF`.
- **Karies (Caries / Decay)**: Fill `#E11D48`, Stroke `#BE123C`.
- **Tambalan Komposit (Composite Filling)**: Fill `#0284C7`, Stroke `#0369A1`.
- **Tambalan Sementara (Temporary Filling)**: Fill `#D97706`, Stroke `#B45309`.
- **Hilang / Ekstraksi (Missing Tooth)**: Slate Diagonal Cross `X` `#64748B` (stroke width 2px over `#F1F5F9` tooth base).
- **Sisa Akar (Retained Root)**: Downward solid triangle token in `#D97706` with `#E11D48` apex indicator.
- **Mahkota Tiruan / Crown**: Outer perimeter border `#9333EA` (Purple-600), Fill `#FAF5FF`.
- **Gigi Tiruan / Denture / Bridge**: Hatching lines pattern at 45-degree angle in `#0D9488`.
- **Impaksi (Impacted Tooth)**: Angled directional arrow slash `#DC2626`.

## Typography

### Structural Pairings
- **Headings & Badges**: **Plus Jakarta Sans** provides a welcoming, rounded, and contemporary medical character that removes anxiety from clinical terminology.
- **Body & Data Grid**: **Inter** ensures maximum legibility in multi-line clinical notes, diagnosis matrices, tooth numbers, and patient medical history.

### Indonesian Clinical Formatting Standards
- **Currency Format**: Always format numbers as integer Rupiah without space before figures: `Rp150.000`, `Rp1.250.000` (tabular numeral variant enabled for tabular alignment).
- **Date Format**: Standard Indonesian formal medical date string: `DD MMMM YYYY` (e.g., `03 November 2026`).
- **Time Format**: 24-hour notation followed by timezone: `HH.mm WIB` (e.g., `09.30 WIB`, `14.15 WIB`).
- **Tooth Numbering (FDI Two-Digit System)**: Quadrant digits and tooth positions (e.g., `11`, `16`, `24`, `36`, `48`) must always render in bold monospace or strict tabular figures using `Plus Jakarta Sans SemiBold`.

## Layout & Spacing

### Grid Architecture
- **Desktop (>= 1280px)**: 12-column fluid grid with `margin: 2rem` and `gutter: 1.5rem`. Max container width capped at `1440px` for optimal ergonomics during dual-panel medical entry (left: Odontogram interactive canvas, right: clinical diagnostic entry).
- **Tablet (768px - 1279px)**: 8-column layout. Split views collapse into stacked cards with sticky tab navigators.
- **Mobile (< 768px)**: 4-column layout with `margin-mobile: 1rem` and `gutter-mobile: 0.75rem`. Odontogram converts to quadrant-by-quadrant horizontal carousel view.

### Density & Vertical Rhythm
A baseline 4px/8px rhythm dictates all component spacings. Clinical forms maintain high compact utility (`space-sm` between input field label and control, `space-md` between fields in a group, and `space-xl` between logical medical sections).

## Elevation & Depth

### Low-Contrast Medical Layering
This design system avoids excessive 3D shadows to maintain an ultra-clean, clinical aesthetic. Depth is achieved primarily through pure white `#FFFFFF` cards elevated against the `#F8FAFC` canvas, bounded by crisp `#E2E8F0` micro-borders.

### Shadow Tokens
- **Surface Flat (Default Cards)**:
  `box-shadow: none; border: 1px solid #E2E8F0;`
- **Surface Raised (Interactive Cards on Hover, Odontogram Floating Toolbar)**:
  `box-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02); border: 1px solid #CBD5E1;`
- **Surface Overlay (Modals, Tooth Detail Popovers, Dropdowns)**:
  `box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.08), 0 4px 8px -2px rgba(15, 23, 42, 0.03); border: 1px solid #E2E8F0;`
- **Focus Rings**:
  `box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.20); border-color: #0D9488;`

## Shapes

### Geometric Strategy
The design system employs `roundedness: 2` (base radius of `0.5rem` / `8px`) to convey contemporary medical friendliness without losing structured administrative efficiency.

- **Base Radius (`0.5rem` / `8px`)**: Applied to text inputs, select dropdowns, action buttons, table row containers, and tooth selector buttons.
- **Card & Modal Radius (`rounded-lg: 1rem` / `16px`)**: Applied to main clinical cards, patient summary boards, and modal dialogue surfaces.
- **Interactive Pill (`rounded-full: 9999px`)**: Reserved exclusively for clinical status badges, tooth surface pill selectors, and action chips.
- **Odontogram Anatomical Geometry**: Individual tooth anatomical SVG components use organic, clean continuous arcs with soft internal quadrant corner radii (`2px`) for precise clicking.

## Components

### Buttons
- **Primary Action**: Background `#0D9488`, text `#FFFFFF`, border none, height 40px, padding horizontal 16px, `border-radius: 0.5rem`, font `Plus Jakarta Sans SemiBold 14px`. Hover: `#0F766E`. Active: `#115E59`.
- **Secondary Action**: Background `#F0FDFA`, text `#0F766E`, border `1px solid #CCFBF1`. Hover: Background `#CCFBF1`.
- **Outline Action**: Background `#FFFFFF`, text `#334155`, border `1px solid #E2E8F0`. Hover: Background `#F8FAFC`, border `#CBD5E1`.
- **Danger Action**: Background `#FFF1F2`, text `#E11D48`, border `1px solid #FECDD3`. Hover: Background `#FFE4E6`.

### Chips & Status Badges
- **General Badge Geometry**: Height 24px, padding horizontal 10px, `border-radius: 9999px`, font `Plus Jakarta Sans Bold 11px`, tracking `0.04em`.
- **Status Variants**:
  - *Selesai*: Background `#ECFDF5`, text `#059669`, border `1px solid #A7F3D0`.
  - *Sedang Diperiksa*: Background `#FFFBEB`, text `#D97706`, border `1px solid #FDE68A`.
  - *Terdaftar*: Background `#F0F9FF`, text `#0284C7`, border `1px solid #BAE6FD`.
  - *Batal*: Background `#FFF1F2`, text `#E11D48`, border `1px solid #FECDD3`.

### Odontogram Interactive Canvas
- **Adult (Permanent, Teeth 11-48) & Child (Deciduous, Teeth 51-85)**: Grouped cleanly into upper and lower jaw quadrants with crisp quadrant dividing markers.
- **Anatomical Part Representation**: 5 distinct clickable surfaces per tooth (Occlusal/Incisal center, Mesial, Distal, Vestibular/Buccal, Lingual/Palatal).
- **Interactive Feedback**: Hovering any surface yields a `#CCFBF1` tint with a `#0D9488` border hint. Clicking opens the surface diagnosis dropdown.

### Form Inputs & Select Controls
- **Height**: 40px base.
- **States**: Background `#FFFFFF`, border `1px solid #E2E8F0`, text `#0F172A`, placeholder `#94A3B8`.
- **Focus**: Border `#0D9488`, outer glow `box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2)`.
- **Labels**: Positioned strictly above fields in `Plus Jakarta Sans SemiBold 13px` with `#334155`.

### Checkboxes & Radio Buttons
- **Radio / Checkbox Box**: 18px × 18px, border `1.5px solid #CBD5E1`, background `#FFFFFF`, radius `4px` (checkbox) or `9999px` (radio).
- **Checked State**: Background `#0D9488`, border `#0D9488`, with crisp white inner check or radial dot.

### Clinical Cards & Data Lists
- **Cards**: Background `#FFFFFF`, border `1px solid #E2E8F0`, border radius `1rem`, padding `1.5rem`.
- **Patient Detail Strip**: Compact header ribbon in `#F8FAFC` with bold charcoal patient name (`Plus Jakarta Sans 18px Bold`), age, medical record number (Rekam Medis) highlighted in a soft sky-blue badge, and dental allergy tag highlighted in rose if active.