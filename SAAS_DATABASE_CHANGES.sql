-- =====================================================
-- SaaS Database Changes - Complete SQL Script
-- =====================================================
-- This script contains all database changes for converting
-- the system into a multi-tenant SaaS application.
-- 
-- IMPORTANT: 
-- 1. Backup your database before running this script
-- 2. Run these commands in order
-- 3. Check if columns exist before adding them (optional safety check)
-- =====================================================

-- =====================================================
-- STEP 1: Create Companies Table
-- =====================================================
-- This table stores company/tenant information
CREATE TABLE IF NOT EXISTS `companies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cname` varchar(255) NOT NULL,
  `mandatory_id` bigint(20) unsigned DEFAULT NULL,
  `companyDeclarartion_id` bigint(20) unsigned DEFAULT NULL,
  `organization` varchar(255) DEFAULT NULL,
  `regDate` date DEFAULT NULL,
  `regNo` varchar(255) DEFAULT NULL,
  `employeeNo` varchar(255) DEFAULT NULL,
  `regHead` varchar(255) DEFAULT NULL,
  `employeeBox` varchar(255) DEFAULT NULL,
  `tinNo` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `contactName` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `personalPhone` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `companies_mandatory_id_index` (`mandatory_id`),
  KEY `companies_companydeclarartion_id_index` (`companyDeclarartion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- STEP 2: Add Mobile App Settings to Companies Table
-- =====================================================
-- Add app_name and app_logo columns for mobile app customization
ALTER TABLE `companies` 
ADD COLUMN IF NOT EXISTS `app_name` varchar(255) DEFAULT NULL AFTER `cname`,
ADD COLUMN IF NOT EXISTS `app_logo` varchar(255) DEFAULT NULL AFTER `app_name`;

-- =====================================================
-- STEP 3: Add company_id to Users Table
-- =====================================================
-- Links users to their company/tenant
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX IF NOT EXISTS `users_company_id_index` (`company_id`);

-- =====================================================
-- STEP 4: Add company_id to Roles Table
-- =====================================================
-- Ensures roles are company-specific
ALTER TABLE `roles` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX IF NOT EXISTS `roles_company_id_index` (`company_id`);

-- =====================================================
-- STEP 5: Add company_id to Customers Table
-- =====================================================
-- Isolates customer data by company
ALTER TABLE `customers` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX IF NOT EXISTS `customers_company_id_index` (`company_id`);

-- =====================================================
-- STEP 6: Add company_id to Customer Pacels Table
-- =====================================================
-- Isolates parcel data by company
ALTER TABLE `customer_pacels` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX IF NOT EXISTS `customer_pacels_company_id_index` (`company_id`);

-- =====================================================
-- STEP 7: Add company_id to Pacel Histories Table
-- =====================================================
-- Isolates parcel history data by company
ALTER TABLE `pacel_histories` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX IF NOT EXISTS `pacel_histories_company_id_index` (`company_id`);

-- =====================================================
-- STEP 8: Add company_id to Cars Table
-- =====================================================
-- Isolates car/vehicle data by company
ALTER TABLE `cars` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `driver_id`,
ADD INDEX IF NOT EXISTS `cars_company_id_index` (`company_id`);

-- =====================================================
-- STEP 9: Add company_id to Drivers Table
-- =====================================================
-- Isolates driver data by company
ALTER TABLE `drivers` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `status`,
ADD INDEX IF NOT EXISTS `drivers_company_id_index` (`company_id`);

-- =====================================================
-- STEP 10: Add company_id to Driver Routes Table
-- =====================================================
-- Isolates driver route data by company
ALTER TABLE `driver_routes` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `user_id`,
ADD INDEX IF NOT EXISTS `driver_routes_company_id_index` (`company_id`);

-- =====================================================
-- STEP 11: Add company_id to Package Loads Table
-- =====================================================
-- Isolates package load data by company
ALTER TABLE `package_loads` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `price`,
ADD INDEX IF NOT EXISTS `package_loads_company_id_index` (`company_id`);

-- =====================================================
-- STEP 12: Add company_id to Users Cars Selection Table
-- =====================================================
-- Isolates user car selection data by company
ALTER TABLE `users_cars_selection` 
ADD COLUMN IF NOT EXISTS `company_id` bigint(20) unsigned DEFAULT NULL AFTER `car_id`,
ADD INDEX IF NOT EXISTS `users_cars_selection_company_id_index` (`company_id`);

-- =====================================================
-- OPTIONAL: Add Foreign Key Constraints (Recommended)
-- =====================================================
-- Uncomment these if you want to enforce referential integrity
-- Note: This will prevent deletion of companies that have related data

-- ALTER TABLE `users` 
-- ADD CONSTRAINT `users_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `roles` 
-- ADD CONSTRAINT `roles_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `customers` 
-- ADD CONSTRAINT `customers_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `customer_pacels` 
-- ADD CONSTRAINT `customer_pacels_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `pacel_histories` 
-- ADD CONSTRAINT `pacel_histories_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `cars` 
-- ADD CONSTRAINT `cars_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `drivers` 
-- ADD CONSTRAINT `drivers_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `driver_routes` 
-- ADD CONSTRAINT `driver_routes_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `package_loads` 
-- ADD CONSTRAINT `package_loads_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- ALTER TABLE `users_cars_selection` 
-- ADD CONSTRAINT `users_cars_selection_company_id_foreign` 
-- FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================
-- Run these queries to verify all changes were applied successfully

-- Check if companies table exists
-- SELECT COUNT(*) as companies_table_exists 
-- FROM information_schema.tables 
-- WHERE table_schema = DATABASE() AND table_name = 'companies';

-- Check if all company_id columns exist
-- SELECT 
--     TABLE_NAME,
--     COLUMN_NAME,
--     DATA_TYPE,
--     IS_NULLABLE
-- FROM information_schema.COLUMNS
-- WHERE TABLE_SCHEMA = DATABASE()
--     AND COLUMN_NAME = 'company_id'
-- ORDER BY TABLE_NAME;

-- Check if all company_id indexes exist
-- SELECT 
--     TABLE_NAME,
--     INDEX_NAME
-- FROM information_schema.STATISTICS
-- WHERE TABLE_SCHEMA = DATABASE()
--     AND INDEX_NAME LIKE '%company_id%'
-- ORDER BY TABLE_NAME, INDEX_NAME;

-- =====================================================
-- ROLLBACK SCRIPT (If needed)
-- =====================================================
-- Uncomment and run these if you need to rollback the changes

-- ALTER TABLE `users_cars_selection` DROP INDEX IF EXISTS `users_cars_selection_company_id_index`;
-- ALTER TABLE `users_cars_selection` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `package_loads` DROP INDEX IF EXISTS `package_loads_company_id_index`;
-- ALTER TABLE `package_loads` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `driver_routes` DROP INDEX IF EXISTS `driver_routes_company_id_index`;
-- ALTER TABLE `driver_routes` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `drivers` DROP INDEX IF EXISTS `drivers_company_id_index`;
-- ALTER TABLE `drivers` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `cars` DROP INDEX IF EXISTS `cars_company_id_index`;
-- ALTER TABLE `cars` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `pacel_histories` DROP INDEX IF EXISTS `pacel_histories_company_id_index`;
-- ALTER TABLE `pacel_histories` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `customer_pacels` DROP INDEX IF EXISTS `customer_pacels_company_id_index`;
-- ALTER TABLE `customer_pacels` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `customers` DROP INDEX IF EXISTS `customers_company_id_index`;
-- ALTER TABLE `customers` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `roles` DROP INDEX IF EXISTS `roles_company_id_index`;
-- ALTER TABLE `roles` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `users` DROP INDEX IF EXISTS `users_company_id_index`;
-- ALTER TABLE `users` DROP COLUMN IF EXISTS `company_id`;

-- ALTER TABLE `companies` DROP COLUMN IF EXISTS `app_logo`;
-- ALTER TABLE `companies` DROP COLUMN IF EXISTS `app_name`;

-- DROP TABLE IF EXISTS `companies`;

-- =====================================================
-- END OF SCRIPT
-- =====================================================

