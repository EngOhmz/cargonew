# Database Migrations Summary for SaaS Multi-Tenancy

## Overview
This document lists all migrations created to add `company_id` column to database tables for SaaS multi-tenancy support.

## Migrations Created

### 1. ✅ `pacel_histories` Table
**File:** `2026_01_21_124656_add_company_id_to_pacel_histories_table.php`
- **Status:** Created and configured
- **Column:** `company_id` (unsignedBigInteger, nullable)
- **Position:** After `added_by`
- **Index:** Yes
- **Used By:** 
  - `POST /packing/save` endpoint
  - `POST /store_pacel/save` endpoint

### 2. ✅ `drivers` Table
**File:** `2026_01_21_131345_add_company_id_to_drivers_table.php`
- **Status:** Created and configured
- **Column:** `company_id` (unsignedBigInteger, nullable)
- **Position:** After `status`
- **Index:** Yes
- **Used By:**
  - `POST /driver/save` endpoint

### 3. ✅ `driver_routes` Table
**File:** `2026_01_21_131437_add_company_id_to_driver_routes_table.php`
- **Status:** Created and configured
- **Column:** `company_id` (unsignedBigInteger, nullable)
- **Position:** After `user_id`
- **Index:** Yes
- **Used By:**
  - `POST /car_selection/save` endpoint (creates DriverRoute records)

### 4. ✅ `package_loads` Table
**File:** `2026_01_21_131501_add_company_id_to_package_loads_table.php`
- **Status:** Created and configured
- **Column:** `company_id` (unsignedBigInteger, nullable)
- **Position:** After `user_id`
- **Index:** Yes
- **Used By:**
  - `POST /packing/save` endpoint (creates PackageLoad records)

### 5. ✅ `users_cars_selection` Table
**File:** `2026_01_21_131525_add_company_id_to_users_cars_selection_table.php`
- **Status:** Created and configured
- **Column:** `company_id` (unsignedBigInteger, nullable)
- **Position:** After `user_id`
- **Index:** Yes
- **Used By:**
  - Car selection functionality (if used)

## Already Existing Migrations

The following tables already have `company_id` migrations from previous updates:

1. ✅ `customers` - `2024_01_15_000001_add_company_id_to_customers_table.php`
2. ✅ `customer_pacels` - `2024_01_15_000003_add_company_id_to_customer_pacels_table.php`
3. ✅ `users` - `2024_01_15_000004_add_company_id_to_users_table.php`
4. ✅ `roles` - `2024_01_15_000002_add_company_id_to_roles_table.php`
5. ✅ `cars` - `2024_01_15_000005_add_company_id_to_cars_table.php`

## Running Migrations

To apply all migrations, run:

```bash
php artisan migrate
```

This will add `company_id` columns to:
- `pacel_histories`
- `drivers`
- `driver_routes`
- `package_loads`
- `users_cars_selection`

## Controller Updates

The following controller methods have been updated to set `company_id`:

1. ✅ `ManagementController::driver_store()` - Sets `company_id` when creating Driver
2. ✅ `ManagementController::car_store()` - Sets `company_id` when creating Car
3. ✅ `ManagementController::car_selection()` - Sets `company_id` when creating DriverRoute
4. ✅ `ManagementController::packing_store()` - Sets `company_id` when creating PackageLoad and PacelHistory
5. ✅ `ManagementController::store_pacel()` - Sets `company_id` when creating Customer, CustomerPacel, and PacelHistory

## Model Updates

The following models have been updated to include `company_id` in fillable:

1. ✅ `Car` - Added `company_id` to fillable
2. ✅ `Driver` - Added `company_id` to fillable
3. ✅ `PackageLoad` - Added `company_id` to fillable
4. ✅ `UserCarSelection` - Added `company_id` to fillable
5. ✅ `DriverRoute` - Uses `guarded`, so `company_id` is automatically fillable

## Verification

After running migrations, verify the columns exist:

```sql
-- Check pacel_histories
DESCRIBE pacel_histories;

-- Check drivers
DESCRIBE drivers;

-- Check driver_routes
DESCRIBE driver_routes;

-- Check package_loads
DESCRIBE package_loads;

-- Check users_cars_selection
DESCRIBE users_cars_selection;
```

All tables should have a `company_id` column of type `bigint unsigned` (nullable).

## Notes

- All `company_id` columns are nullable to support existing data
- All columns have indexes for better query performance
- The backend automatically sets `company_id` from the user's `company_id` when `user_id` is provided in API requests

