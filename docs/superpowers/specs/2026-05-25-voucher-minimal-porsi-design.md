# Design Specification: Voucher Minimum Portions (Minimal Porsi)

This document details the system design for adding a new portion count requirement (`minimal_porsi`) to vouchers. This constraint will work in conjunction with the existing minimum purchase amount (`minimal_pembelian`) using an **AND** condition.

## Requirements

1. **Database Schema Update**:
   * Add a `minimal_porsi` column to the `voucher` table: `INT NOT NULL DEFAULT 0`.
2. **Admin UI**:
   * Add a "Minimal Jumlah Porsi" numeric field to both "Tambah Voucher" and "Edit Voucher" panels.
   - Display portion requirements in the admin list table.
3. **Admin Actions**:
   - Store and update the new `minimal_porsi` value.
4. **Checkout UI & Logic (Customer)**:
   - Calculate total portions in the cart.
   - Restrict voucher usage if subtotal is less than `minimal_pembelian` OR total portions are less than `minimal_porsi`.
   - Update locking visuals to display portion shortfalls dynamically.
5. **Checkout Completion Action**:
   - Enforce the constraints on the server side prior to inserting the order.

## Implementation Details

### Database Schema
```sql
ALTER TABLE `voucher` ADD COLUMN `minimal_porsi` INT NOT NULL DEFAULT 0 AFTER `minimal_pembelian`;
```

### Components

#### Admin Form Input
* File: `admin/diskon_tambah.php`, `admin/diskon_edit.php`
* Field: `minimal_porsi` (number type, minimum 0, default 0).

#### Checkout Validation Logic
* File: `pelanggan/keranjang_checkout.php`
* Verification code blocks will calculate:
  ```php
  $totalPortions = 0;
  foreach ($cart as $item) {
      $totalPortions += (int)$item['qty'];
  }
  ```
* Voucher validation:
  ```php
  $isLocked = ($subtotal < $minPurchase) || ($totalPortions < $minPortions);
  ```

#### Server Checkout Process
* File: `actions/pesanan/checkout.php`
* Verify portions count before submitting the transaction.
