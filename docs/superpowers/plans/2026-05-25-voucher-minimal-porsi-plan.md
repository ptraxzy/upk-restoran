# Implementation Plan: Voucher Minimum Portions (Minimal Porsi)

This plan outlines the steps required to implement the portional restriction feature for vouchers.

## Steps

### Phase 1: Database Migration
1. Run direct SQL migration to alter the `voucher` table:
   ```sql
   ALTER TABLE `voucher` ADD COLUMN `minimal_porsi` INT NOT NULL DEFAULT 0 AFTER `minimal_pembelian`;
   ```
2. Update `database/sql/001-init.sql` schema at lines 110-120 to include the `minimal_porsi` column definition so new container instances start with the correct schema.

### Phase 2: Backend Admin Actions
1. **Store Action (`actions/diskon/store.php`)**:
   * Retrieve `minimal_porsi` from POST parameters.
   - Cast it to `int`.
   - Update database query to insert the new column value.
2. **Update Action (`actions/diskon/update.php`)**:
   * Retrieve `minimal_porsi` from POST parameters.
   - Cast it to `int`.
   - Update database query to save the updated column value.

### Phase 3: Admin UI Enhancements
1. **Create Form (`admin/diskon_tambah.php`)**:
   * Add number input field for `minimal_porsi`.
2. **Edit Form (`admin/diskon_edit.php`)**:
   * Add number input field for `minimal_porsi` pre-populated with database value.
3. **Admin Voucher List (`admin/diskon.php`)**:
   * Display Portion requirements alongside Minimum Purchase details.

### Phase 4: Customer Checkout Logic & UI (`pelanggan/keranjang_checkout.php`)
1. Add portion aggregation to the cart loop:
   ```php
   $totalPortions = 0;
   foreach ($cart as $item) {
       $totalPortions += (int)$item['qty'];
   }
   ```
2. Fetch `minimal_porsi` in the active vouchers query.
3. Update `apply_voucher` POST handler to check both subtotal and portions:
   ```php
   $minPortions = (int)$voucher['minimal_porsi'];
   if ($cartSubtotal < $minPurchase || $cartTotalPortions < $minPortions) { ... }
   ```
4. Update the active session voucher verification loop during subtotal calculation to clear the voucher if subtotal or portions become invalid.
5. Enhance the voucher list layout to render a lock badge and disabled styles if either condition is not met. Show exact shortfall details dynamically.

### Phase 5: Server-side Order Checkout Action (`actions/pesanan/checkout.php`)
1. Fetch `minimal_porsi` from database.
2. Calculate total portions from the user's active cart.
3. Reject order submission and redirect back with an error flash message if portion/minimum purchase requirements are violated.
