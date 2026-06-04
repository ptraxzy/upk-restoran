# Real-time Order Tracking & UI Improvements

## Problem
Currently, the customer's order page (`pesanan.php`) only shows a single global "Lacak Status" button, which tracks the latest order. Customers cannot easily track specific active orders when they have more than one. Furthermore, the tracking page (`pesanan_status.php`) is static and requires manual refreshing to see progress changes made by kitchen staff or cashiers.

## Chosen Solution (Option A)

### 1. UI Refactoring for Specific Order Tracking
- **Remove Global Tracking Button:** The global "Lacak Status" button at the top of `pelanggan/pesanan.php` will be removed to avoid confusion.
- **Add Per-Order Tracking Button:** Each active order listed under "Pesanan Aktif" will have its own small, specific "Lacak" button next to its current status badge.
- **Dynamic Parameter:** Clicking the "Lacak" button will pass the order ID via URL parameter (`pesanan_status.php?id=...`).
- **Update Tracking Page:** `pesanan_status.php` will read `$_GET['id']` to fetch and display the status of that exact order, instead of hardcoding `ORDER BY p.tanggal_pesanan DESC LIMIT 1`.

### 2. Real-time Status via AJAX Polling
- **Backend API:** Create a lightweight JSON endpoint (`pelanggan/ajax_status_pesanan.php` or inside `pesanan_status.php` using headers) that receives an order ID and returns the current status and payment details.
- **Frontend Polling:** Inject a JavaScript block in `pesanan_status.php` using `setInterval` to ping the API every 3-5 seconds.
- **Dynamic DOM Updates:** When the status string changes (e.g. from "Diproses" to "Sedang Disiapkan"), the JavaScript will automatically update the DOM elements (progress numbers, highlighting, badges, and text) with CSS transitions without a full page reload.

## Trade-offs
- AJAX polling is lightweight to build but adds minor network traffic per active tracking session (a small JSON payload every 5s). For a single restaurant scale, this overhead is perfectly negligible and avoids the infrastructural complexity of WebSockets.
