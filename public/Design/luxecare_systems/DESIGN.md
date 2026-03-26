# Design System Strategy: The Master Artisan

## 1. Overview & Creative North Star
The "Master Artisan" is the Creative North Star for this design system. We are moving away from the "generic SaaS dashboard" aesthetic and toward a high-end, editorial experience that mirrors the precision of luxury shoe restoration. 

While the industry is "cleaning," the digital experience must feel like "preservation." We achieve this through **Organic Professionalism**: a layout that breaks the rigid, boxed-in grid of traditional management software in favor of intentional asymmetry, layered surfaces, and authoritative typography. We don't just display data; we curate an artisan’s workflow. By utilizing high-contrast typography scales and breathing room (whitespace), we transform a utility tool into a premium workspace.

---

## 2. Colors & Surface Philosophy
This system uses a palette of deep forest greens (`primary`) and medicinal mints (`secondary`) to evoke a sense of cleanliness, chemistry, and high-tier service.

*   **Primary (`#001610`):** The "Ink" of the system. Used for high-contrast headlines and deep-moat backgrounds to anchor the eye.
*   **Secondary (`#3a6758`):** The "Action" color. Provides a professional, calming bridge between the deep primary and light backgrounds.
*   **Tertiary (`#051515`):** Reserved for ultra-high-definition accents or dark-mode-style "Artisan" cards.

### The "No-Line" Rule
To achieve a signature, premium feel, **1px solid borders are strictly prohibited for sectioning.** 
*   **Defining Boundaries:** Use background color shifts. A `surface-container-low` (`#f4f3f1`) section should sit directly on a `background` (`#faf9f7`) to create a soft, sophisticated edge.
*   **Surface Hierarchy:** Treat the UI as physical layers of fine paper. 
    *   *Layer 0 (Base):* `surface`
    *   *Layer 1 (Main Content Area):* `surface-container-low`
    *   *Layer 2 (Interactive Cards):* `surface-container-lowest` (#ffffff)
*   **The "Glass & Gradient" Rule:** Floating navigation or high-priority modals must use **Glassmorphism**. Apply `surface_variant` at 60% opacity with a `20px` backdrop blur. For primary CTAs, use a subtle linear gradient from `primary` to `primary_container` to add "soul" and depth.

---

## 3. Typography: Editorial Authority
We pair the geometric precision of **Manrope** for high-level branding with the utilitarian clarity of **Inter** for data management.

*   **Display & Headlines (Manrope):** Use `display-lg` (3.5rem) for dashboard welcomes and `headline-md` (1.75rem) for section headers. The goal is an "Editorial Look"—large, bold, and unapologetic.
*   **Body & Labels (Inter):** All data-heavy components (tables, forms) must use `body-md` (0.875rem) or `label-md` (0.75rem). The contrast between the massive Manrope headlines and the compact Inter data creates an "Architectural" hierarchy.
*   **Tracking:** Set `display` titles to `-0.02em` letter-spacing for a tighter, more "designed" appearance.

---

## 4. Elevation & Depth: Tonal Layering
Traditional drop shadows are too "cheap" for this brand. We utilize **Tonal Layering** to convey importance.

*   **The Layering Principle:** Depth is achieved by stacking. A `surface-container-lowest` card placed on a `surface-container` background creates a natural, soft lift.
*   **Ambient Shadows:** If a shadow is required for a floating element (like a status dropdown), use: `0px 12px 32px rgba(26, 28, 27, 0.06)`. Note the color: it is a tinted version of `on_surface`, never pure black.
*   **The "Ghost Border" Fallback:** For accessibility in forms, use the `outline_variant` (`#c1c8c4`) at **15% opacity**. It should feel like a suggestion of a line, not a barrier.

---

## 5. Components: The Artisan’s Toolkit

### Management Forms
*   **Input Fields:** Use `surface_container_low` as the field background. No borders. On focus, transition the background to `surface_container_lowest` and add a `2px` signature underline in `secondary`.
*   **Labels:** Always use `label-md` in `on_surface_variant` for a subtle, professional look.

### Data Tables (The "Fluid Table")
*   **Forbid Dividers:** Do not use horizontal lines between rows. Use the Spacing Scale `4` (0.9rem) to create vertical air.
*   **Row States:** On hover, change the row background to `primary_fixed` (`#c8eadd`) at 30% opacity. 
*   **Status Badges:** Use `secondary_container` backgrounds with `on_secondary_container` text. Apply the `full` (9999px) roundedness for a pill-like, modern feel.

### Buttons
*   **Primary:** Solid `primary` background, `on_primary` text. `xl` (0.75rem) roundedness. Use a subtle inner-glow gradient for a "pressed" effect.
*   **Secondary:** `surface_container_highest` background with `on_surface` text. No border.

### Signature Component: The "Order Track"
Instead of a standard list, use an asymmetrical timeline. Overlap the shoe image (`round-lg`) with a floating `surface_container_lowest` status badge that uses a backdrop blur.

---

## 6. Do’s and Don’ts

### Do:
*   **Embrace Whitespace:** Use the `16` (3.5rem) spacing token between major sections. If it feels like "too much" space, it’s probably just right.
*   **Asymmetric Layouts:** In a management dashboard, offset the primary data card by 1rem to the right of the headline to create a "custom" feel.
*   **Tonal Transitions:** Use `surface_dim` for "archived" or "disabled" states rather than grey-scale, keeping the color temperature consistent.

### Don’t:
*   **Don't use 1px Dividers:** This is the quickest way to make the system look like a generic template. Use color-blocking.
*   **Don't use High-Contrast Shadows:** Avoid "dirty" shadows. If the shadow is visible as a "dark smudge," it is too heavy.
*   **Don't use Default Inter Bold:** For headlines, stick to Manrope. Inter is for reading; Manrope is for feeling.