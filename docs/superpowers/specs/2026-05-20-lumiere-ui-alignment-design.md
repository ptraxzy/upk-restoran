# Lumière Premium UI Alignment Design Spec

**Date**: 2026-05-20  
**Status**: Approved  
**Author**: Antigravity

---

## 1. Objective

To rebrand the "UPK Restoran" system to **Lumière** and redesign the administration pages to match the dark luxury Figma design specifications. The visual styling will be achieved using high-fidelity responsive CSS rules built on top of the existing Bootstrap 5 layout, maintaining 100% database compatibility, logic, and functional integrity.

---

## 2. Rebranding & UI Shell (`Lumière`)

* **App Name & Brand**:
  * Set `APP_NAME=Lumière` in `.env`.
  * Update default brand logo text in `includes/ui.php` to `Lumière`.
  * Replace `'brand' => 'NOCTRA'` with `'brand' => 'Lumière'` in PHP controllers/views.
* **Layout Enhancements**:
  * Sidebar background: `#000000` with gold right border (`border-right: 1px solid rgba(197, 160, 89, 0.2)`).
  * Brand Header: Elegant serif font for **ADMINISTRATION** with **EST. 2024** subtitle in gold-secondary.
  * Active Navigation Links: Highlighted via left-border `border-left: 4px solid var(--gold)` and refined styling.

---

## 3. Menu Forms Design (`menu_tambah.php` & `menu_edit.php`)

* **Title Area**: 
  * "Tambah Menu Baru" / "Ubah Menu" titles rendered in large elegant Cormorant Garamond gold serif.
  * Uppercase letter-spaced backlink: `KEMBALI KE INVENTARIS` with a gold arrow pointing left.
* **Form Grid (2 Columns)**:
  * **Left Column (4:5 Aspect Ratio Photo Container)**:
    * A sleek vertical container with a dashed border (`border: 1px dashed rgba(200, 198, 197, 0.3)`).
    * When empty: Shows a gold camera icon, title *"Unggah foto/pilih url gambar"*, and description *"Rasio 4:5 direkomendasikan"*.
    * Beneath the panel: A clean URL input field. Pasting a URL auto-renders the image preview in the 4:5 box in real-time.
  * **Right Column (Form Fields)**:
    * **Nama Hidangan**: Transparent input field with giant elegant placeholder.
    * **Kategori**: Horizontal selection chips (APPETIZER, MAIN COURSE, DESSERT, BEVERAGE) built using semantic HTML radio buttons styled as chips.
      * *Active State*: Gold border, gold text, dark grey background (`#2a2a2a`).
      * *Inactive State*: Muted thin borders, light grey text.
    * **Deskripsi**: Textarea styled with elegant placeholder.
    * **Harga**: Sleek horizontal alignment featuring **IDR** gold badge prefix and the numeric price input field.
    * **Status Ketersediaan**: Premium custom gold switch toggle on the right side.
    * **Jumlah Porsi**: Minimalist portion counter.

---

## 4. Staff Forms Design (`karyawan_tambah.php` & `karyawan_edit.php`)

* **Visual Sections**: 
  * Dividers made of thin, gold-translucent horizontal rules (`border-bottom: 1px solid rgba(197, 160, 89, 0.1)`).
* **Section 1: Data Diri**:
  * Minimalist, border-bottom input fields for `username` and `password`.
* **Section 2: Peran & Penugasan**:
  * Custom position cards mapping to system `level` (Admin vs Kasir).
  * Styled as premium interactive cards (like Chef, Waiter, Manager from Figma).
  * Active card is highlighted with a gold border and subtle translucent gold glow (`rgba(233, 193, 118, 0.05)`).
* **Actions Block**:
  * Buttons "BATAL" and "SIMPAN KARYAWAN" positioned on the bottom right.

---

## 5. Functional Safeguards

* Maintain standard form methods, action targets (`actions/menu/store.php`, etc.), validation error flash messaging, and database interactions perfectly untouched.
* Ensure all elements remain completely responsive for mobile/tablet screen sizes.
