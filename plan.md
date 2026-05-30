# High-Level Implementation Plan: Fix Stock Logic for Waste Transactions

## Goal

Fix waste stock behavior so every transaction changes `sampahs.stok` consistently:

- Setor sampah adds stock.
- Penjualan sampah reduces stock.
- Admin penjualan form rejects invalid weight before saving.

AI Agent must implement this in a new branch before editing code.

## Branch Instruction for AI Agent

Create a new branch first:

```bash
git checkout -b fix/stock-transaction-logic
```

If branch name already exists, use a close name such as:

```bash
git checkout -b fix/stock-transaction-logic-2
```

Do not implement on `main` or current working branch.

## Current Code Areas

Likely files to inspect and update:

- `app/Http/Controllers/Admin/SetoranController.php`
- `app/Http/Controllers/Admin/PenjualanSampahController.php`
- `app/Models/Sampah.php`
- `app/Models/Setoran.php`
- `app/Models/SetoranDetail.php`
- `app/Models/PenjualanSampah.php`
- `app/Models/DetailPenjualanSampah.php`
- `resources/views/admin/transaksi/penjualan-sampah/create.blade.php`
- `tests/Feature/Admin/SetoranControllerTest.php`
- `tests/Feature/Admin/PenjualanSampahControllerTest.php`

## Target Behavior

1. Setor sampah

- When admin creates a valid setoran, selected `Sampah` stock increases by submitted `berat`.
- Stock update happens in the same database transaction as `setorans`, `setoran_details`, and nasabah saldo update.
- If any part fails, stock must not change.

2. Penjualan sampah

- When admin creates a valid penjualan, each selected `Sampah` stock decreases by submitted `berat`.
- Stock update happens in the same database transaction as `penjualan_sampahs` and `detail_penjualan_sampahs`.
- If any part fails, stock must not change.
- Stock must never become negative.

3. Penjualan validation

- If any submitted `berat` is `0`, form must show validation error and not save transaction.
- If submitted `berat` is greater than available `stok`, form must show validation error and not save transaction.
- Important: user request says `jika request->berat <= stok tampilkan error`, but business rule for sales should be error when requested weight exceeds stock. Confirm wording if needed; implement as `berat > stok` invalid unless product owner explicitly confirms opposite rule.

## Implementation Steps

1. Verify schema and model relationships

- Confirm `sampahs` table has `stok` column from `database/migrations/2026_03_17_181111_add_stok_to_sampahs_table.php`.
- Confirm `Sampah` model allows reading/updating `stok`.
- Confirm relationships used by penjualan and setoran tests are correct.

2. Fix setoran stock update

- In `SetoranController::store`, keep validation requiring numeric positive weight.
- After creating `setoran_details`, increment selected `Sampah::stok` by submitted `berat`.
- Keep this inside `DB::beginTransaction()` / `DB::commit()`.
- Prefer locking selected row if concurrent transactions can happen, for example `Sampah::whereKey($id)->lockForUpdate()->firstOrFail()` inside transaction.

3. Fix penjualan stock source

- In `PenjualanSampahController::create`, display available stock from `sampahs.stok`, not recalculated stock from setoran details minus sales details, unless repository convention explicitly requires recalculation.
- Include `stok` in selected fields sent to Blade so form can display correct stock.
- Keep view data stable for existing UI JavaScript.

4. Fix penjualan server-side validation

- Keep base validation:
  - `pengepul_id` required and exists.
  - `sampah_id` required array.
  - each `sampah_id.*` exists.
  - `berat` required array.
  - each `berat.*` numeric and greater than zero.
- Add custom validation after base validation and before creating records:
  - Reject missing paired `berat` for each selected sampah.
  - Reject `berat <= 0` with field-specific error like `berat.0`.
  - Load each selected sampah from database and compare requested weight to current `stok`.
  - Reject `berat > stok` with clear field-specific error message.
- Return back with old input and validation errors so `admin/penjualan/create` shows errors.

5. Fix penjualan stock decrement

- Inside database transaction, for each detail row:
  - Lock selected `sampahs` row.
  - Re-check `berat > stok` after lock to prevent race condition.
  - Create `DetailPenjualanSampah` only after stock passes validation.
  - Decrement `stok` by `berat`.
- If any item fails, roll back entire penjualan.

6. Improve Blade error display if needed

- In `resources/views/admin/transaksi/penjualan-sampah/create.blade.php`, make sure validation errors for `berat`, `berat.*`, and general `error` messages are visible.
- Keep old input behavior so user does not lose form data after validation failure.
- If client-side JavaScript blocks zero weight, keep it as helper only; server-side validation remains source of truth.

7. Add or update automated tests

- Add/adjust setoran feature test:
  - Given sampah stock `10`, posting setoran with `berat = 2.5` results in stock `12.5`.
- Add/adjust penjualan feature tests:
  - Given sampah stock `10`, posting penjualan with `berat = 3` results in stock `7`.
  - Posting penjualan with `berat = 0` fails validation and stock remains unchanged.
  - Posting penjualan with `berat = 11` while stock is `10` fails validation and stock remains unchanged.
  - Multi-item penjualan rolls back all changes if one item exceeds stock.
- Run focused tests first, then broader suite if time allows.

## Suggested Validation Messages

Use Indonesian messages aligned with app style, for example:

- `Berat sampah harus lebih dari 0.`
- `Berat penjualan melebihi stok tersedia.`
- `Stok sampah tidak mencukupi.`

Keep messages tied to correct field keys so Blade can show them beside related input.

## Acceptance Criteria

- AI Agent works on a new branch.
- Setoran sampah increases `sampahs.stok` by submitted `berat`.
- Penjualan sampah decreases `sampahs.stok` by submitted `berat`.
- Penjualan with `berat = 0` fails validation and does not create records.
- Penjualan with `berat > stok` fails validation and does not create records.
- Failed penjualan does not change any stock values.
- Stock cannot become negative, including concurrent request path handled by transaction row lock or equivalent database-safe check.
- Tests cover stock increment, stock decrement, zero weight validation, insufficient stock validation, and rollback behavior.

## Manual Verification

1. Create or choose one sampah with known stock.
2. Create setoran for that sampah.
3. Confirm stock increases on admin stok sampah page or database.
4. Create penjualan for part of available stock.
5. Confirm stock decreases.
6. Try penjualan with `berat = 0`; confirm error appears and no transaction is saved.
7. Try penjualan with `berat` above stock; confirm error appears and no transaction is saved.

## Notes for AI Agent

- Keep change scoped to transaction stock logic and validation.
- Do not rewrite unrelated dashboard, report PDF, or layout code.
- Preserve existing route names and view names.
- Prefer Laravel validation and transaction patterns already used in controllers.
- If existing tests use factories, extend those tests instead of creating unrelated setup style.
