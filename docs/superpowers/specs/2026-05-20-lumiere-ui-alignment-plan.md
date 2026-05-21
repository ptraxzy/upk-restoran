# Lumière Premium UI Alignment Implementation Plan

This document details the step-by-step implementation plan for transitioning UPK Restoran to **Lumière** and aligning the menu/staff forms with Figma's dark luxury designs.

---

## Phase 1: Global Rebranding & Layout (`Lumière`)

### Step 1.1: Environment Variable
- Update `.env` to:
  ```env
  APP_NAME=Lumière
  ```

### Step 1.2: Shell Brand Default
- Edit `includes/ui.php` to use `'Lumière'` as the default brand instead of `"L'Art Culinaire"`.
- Replace instances of `'brand' => 'NOCTRA'` with `'brand' => 'Lumière'` or make them dynamic.

### Step 1.3: Premium Theme CSS
- Append custom css rules to `/home/putra/upk-restoran/assets/css/style.css` to build the required premium style tokens:
  - Gold radial glow: `.gold-radial-glow`
  - 4:5 vertical photo upload box: `.premium-photo-box`
  - Large luxurious input fields: `.premium-input-large`, `.premium-textarea`
  - Interactive category selection chips: `.premium-category-chips`
  - Interactive role selector cards: `.premium-role-card`
  - Gold custom toggles: `.premium-toggle`

---

## Phase 2: Menu Forms Realignment

### Step 2.1: Tambah Menu Baru (`admin/menu_tambah.php`)
- Rebuild the page contents inside the PHP output buffer to feature:
  - Backlink: `KEMBALI KE INVENTARIS` styled with spacing and gold icon.
  - Title: elegant gold serif heading.
  - Left column: Aspect-ratio 4:5 preview panel with dynamic JavaScript auto-loading from the image URL input field.
  - Right column: Name input, category chips, textarea description, IDR group price input, status switch, portion input.
- Match all input names exactly (`nama_menu`, `id_kategori`, `deskripsi`, `harga`, `gambar`, `status`, `porsi`) to prevent any breaking changes to the database actions.

### Step 2.2: Ubah Menu (`admin/menu_edit.php`)
- Mirror the `menu_tambah.php` design.
- Correctly load and pre-fill all dynamic values fetched from the `menu` database table.

---

## Phase 3: Staff Forms Realignment

### Step 3.1: Tambah Karyawan (`admin/karyawan_tambah.php`)
- Rebuild layout to utilize the Figma-inspired section groups:
  - Data Diri: Elegant inputs for `username` and `password`.
  - Peran & Penugasan: Level selection cards (Admin and Kasir) mimicking the luxury Chef, Waiter, Manager role cards.
- Ensure the active selection uses gold highlights.
- Keep all validation and submission structures matching the original logic.

### Step 3.2: Ubah Karyawan (`admin/karyawan_edit.php`)
- Apply the same layout to the edit employee page, pre-populating the database fields for the chosen employee.

---

## Phase 4: Quality & Validation

- Review all changed pages.
- Ensure no broken tags or broken PHP sessions.
- Keep the database schema and storage operations 100% intact.
