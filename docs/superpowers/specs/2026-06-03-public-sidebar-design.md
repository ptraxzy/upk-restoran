# Design Spec: Public Slide-Out Mobile Sidebar Layout

This document details the design specifications for adding a luxurious slide-out sidebar (drawer menu) for mobile and tablet views in the customer section of the `upk-restoran` application.

## 1. Problem Statement
Currently, the HTML structure for the custom mobile drawer (`.public-sidebar`) is rendered in the public shell (`includes/ui.php`) but lacks styling in `assets/css/style.css`. Because of this, it displays as an unstyled block element in normal document flow below the navigation bar, cluttering both desktop and mobile layouts.

## 2. Goals
- Hide the mobile drawer from normal document flow and desktop screens entirely.
- Implement a premium slide-out drawer from the right side of the screen when mobile users tap the hamburger menu toggle.
- Add modern, luxury aesthetics matching the dark luxury theme of the restaurant (using backdrop blurs, subtle gold accents, and fluid transitions).

## 3. Specification

### 3.1 CSS Rules to add in `assets/css/style.css`

#### Backdrop Overlay (`.public-sidebar-overlay`)
- Position: Fixed (`top: 0`, `left: 0`, `width: 100vw`, `height: 100vh`)
- Background: Semi-transparent dark overlay (`rgba(0, 0, 0, 0.6)`)
- Backdrop Blur: `backdrop-filter: blur(8px)` for depth
- Z-Index: `1040` (behind sidebar drawer, above content and default navbar)
- Hide state: `opacity: 0`, `visibility: hidden`
- Show state: `.show` adds `opacity: 1`, `visibility: visible`
- Transition: `opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.4s`

#### Sidebar Drawer Container (`.public-sidebar`)
- Position: Fixed (`top: 0`, `right: 0`, `width: 320px`, `max-width: 85vw`, `height: 100vh`)
- Z-Index: `1050`
- Background: Semi-transparent deep dark container (`rgba(10, 10, 10, 0.85)`)
- Backdrop Blur: High blur (`backdrop-filter: blur(25px)`)
- Border: Elegantly demarcated with a left gold border (`1px solid rgba(201, 168, 76, 0.15)`)
- Shadows: Soft shadows stretching left (`box-shadow: -10px 0 30px rgba(0, 0, 0, 0.5)`)
- Hide state: `transform: translateX(100%)` (shifted off-screen to the right)
- Show state: `.show` sets `transform: translateX(0)`
- Transition: Smooth hardware-accelerated transform (`transform 0.4s cubic-bezier(0.16, 1, 0.3, 1)`)

#### Sidebar Header
- Spacing: Margins and paddings to align with overall page content grid.
- Title Brand: Uses display font (`'Libre Baskerville'`) in gold.
- Close Button (`.btn-close-sidebar`):
  - Gold text, borderless, transparent background.
  - Hover animation: Rotate by 90 degrees (`transform: rotate(90deg)`) and transition color.

#### Sidebar Menu Links
- Vertical stack (`display: flex; flex-direction: column; gap: 20px`).
- Sidebar links (`.sidebar-menu-link`):
  - Text color: Subtle secondary grey.
  - Transition: smooth changes on hover for text-color and padding (`transition: all 0.25s ease`).
  - Hover / Active state: Shifts slightly to the right (`padding-left: 8px`) and turns gold (`var(--gold)`).

### 3.2 Desktop & Mobile Navigation Sync
The navbar-toggler's custom hamburger handles state updates. By keeping the sidebar overlay and drawer off-screen/fixed, desktop layouts will remain unaffected by the presence of these DOM nodes.

## 4. Verification Plan
- **Mobile Emulation**: Use Chrome/Firefox Developer Tools to test responsiveness. Verify drawer toggles seamlessly when tapping the hamburger and overlay.
- **Desktop Check**: Ensure no layout shifts or phantom margins appear on desktop.
- **UX Smoothness**: Confirm backdrop blur is hardware-accelerated and transitions are fluid without stutter.
