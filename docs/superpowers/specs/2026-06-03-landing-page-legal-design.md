# Design Spec: Lumière Dine-in Landing Page & Legal Pages

- **Date**: 2026-06-03
- **Feature**: Landing Page, Privacy Policy, and Terms of Service for Lumière Dine-in digital ordering portal.

## Background & Objectives
Currently, accessing the root domain redirects immediately to `login.php`. We want to introduce a beautiful, premium Dine-in Landing Page ("Smart Order Portal") that fits the restaurant's dark luxury theme, allows customers to start ordering, showcases signature menu items, and provides quick navigation for staff members. We also need to add standard Privacy Policy and Terms of Service pages to complete the application.

## Proposed Components

### 1. Landing Page (`index.php`)
Replaces the redirect script with a full-bleed luxury interface:
- **Hero Area**:
  - Background: Full-width dark ambiance photo of the restaurant.
  - Slogan: *"Fine Dining, Delivered to Your Table."*
  - CTA Button: *"Mulai Memesan"* (links to `login.php` or `pelanggan/dashboard.php` if logged in).
- **Staff Access Link**: A subtle button/link in the top navigation bar to go to the employee login panel.
- **Teaser Menu Catalog**: Shows 3 featured dishes dynamically retrieved from the `menu` table in the database:
  - *Wagyu Ribeye A5*
  - *Hokkaido Scallop*
  - *Dark Matter* (dessert)
- **Instruksi Pemesanan (How it Works)**:
  - Explains the 4 simple steps to order from the table.

### 2. Privacy Policy Page (`privacy.php`)
A standalone page using the public shell:
- Explains collection of name, email, table number, order history, and payment method details.
- Clarifies that data is used strictly for cooking preparation, billing, and serving.
- Confirms password encryption (bcrypt).

### 3. Terms of Service Page (`terms.php`)
A standalone page using the public shell:
- Explains dine-in ordering rules (choosing correct table number is user's responsibility).
- Payment rules (QRIS automated or Tunai cash at cashier before serving).
- Cancellation policy (no cancellation or refund once chef begins preparing food).
- Fair use of vouchers (one-time use per account).

### 4. Global Footer Link Updates (`includes/ui.php`)
- Updates the footer inside `render_public_shell` to include clickable links to `privacy.php` and `terms.php`.

## Verification Plan
- Access root domain (`/`) and verify the landing page renders without redirects.
- Check menu items are displayed correctly with image, price, and descriptions from the database.
- Click the "Mulai Memesan" and "Portal Staf" links to verify routing.
- Click the Privacy Policy and Terms of Service links in the footer to verify correct legal page rendering.
