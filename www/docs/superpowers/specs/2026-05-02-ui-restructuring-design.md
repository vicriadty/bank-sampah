# Design Doc: Bank Sampah UI Restructuring & Standardization (Issue #4)

## 1. Introduction
This project aims to improve the maintainability and user experience of the Bank Sampah application by organizing view files into role-based directories and standardizing the UI of core administrative forms.

## 2. Goals
- Separate Admin and Nasabah views clearly in `resources/views`.
- Fix broken dynamic dropdowns caused by previous routing changes.
- Standardize "Create" forms using a consistent Bootstrap Card layout.

## 3. Architecture
### 3.1 Directory Structure
We will move from a flat `pages/` structure to a role-based structure:
- `resources/views/admin/`: All admin-only views (management of nasabah, pengepul, transactions).
- `resources/views/nasabah/`: All nasabah-only views (dashboard, profile, requests).
- `resources/views/auth/`: Authentication views.
- `resources/views/landing.blade.php`: The root landing page.

### 3.2 Data Flow (AJAX Fix)
The AJAX call in the Setoran form will be updated to point to the correct prefixed route:
`GET /admin/get-sampah-by-jenis/{id}`

## 4. Components & UI Design
### 4.1 Standard Form Layout
Every "Create" form will follow this pattern:
- Page Header (H1)
- Grid Column (`col-lg-8` for single forms, `col-lg-12` for complex tables)
- Card Component (`.card.shadow`)
  - Card Header (`.card-header.py-3` with `.text-primary`)
  - Card Body (`.card-body`)

## 5. Implementation Plan (High Level)
1. **Migration**: Move directories and update all controller references.
2. **Bug Fix**: Update script in `admin/transaksi/setor-sampah/create.blade.php`.
3. **Styling**: Refactor each target file to use the new card layout.

## 6. Testing
- Verify all admin links in the sidebar still work.
- Verify the "Nama Sampah" dropdown loads correctly in the Setoran form.
- Visual inspection of all "Create" forms.
