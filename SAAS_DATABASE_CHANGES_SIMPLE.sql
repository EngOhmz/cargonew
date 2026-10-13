-- =====================================================
-- SaaS Database Changes - Simple Direct SQL Script
-- =====================================================
-- This script contains all database changes for converting
-- the system into a multi-tenant SaaS application.
-- 
-- IMPORTANT: 
-- 1. BACKUP YOUR DATABASE BEFORE RUNNING THIS SCRIPT
-- 2. Run these commands in order
-- 3. If a column already exists, you'll get an error - just skip that command
-- 4. Test in a development environment first
-- =====================================================

-- =====================================================
-- STEP 1: Create Companies Table
-- =====================================================
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
ALTER TABLE `companies` ADD COLUMN `app_name` varchar(255) DEFAULT NULL AFTER `cname`;
ALTER TABLE `companies` ADD COLUMN `app_logo` varchar(255) DEFAULT NULL AFTER `app_name`;

-- =====================================================
-- STEP 2.1: Add Company Logo to Companies Table
-- =====================================================
ALTER TABLE `companies` ADD COLUMN `logo` varchar(255) DEFAULT NULL AFTER `app_logo`;

-- =====================================================
-- STEP 3: Add company_id to Users Table
-- =====================================================
ALTER TABLE `users` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX `users_company_id_index` (`company_id`);

-- =====================================================
-- STEP 4: Add company_id to Roles Table
-- =====================================================
ALTER TABLE `roles` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX `roles_company_id_index` (`company_id`);

-- =====================================================
-- STEP 5: Add company_id to Customers Table
-- =====================================================
ALTER TABLE `customers` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX `customers_company_id_index` (`company_id`);

-- =====================================================
-- STEP 6: Add company_id to Customer Pacels Table
-- =====================================================
ALTER TABLE `customer_pacels` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX `customer_pacels_company_id_index` (`company_id`);

-- =====================================================
-- STEP 7: Add company_id to Pacel Histories Table
-- =====================================================
ALTER TABLE `pacel_histories` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `added_by`,
ADD INDEX `pacel_histories_company_id_index` (`company_id`);

-- =====================================================
-- STEP 8: Add company_id to Cars Table
-- =====================================================
ALTER TABLE `cars` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `driver_id`,
ADD INDEX `cars_company_id_index` (`company_id`);

-- =====================================================
-- STEP 9: Add company_id to Drivers Table
-- =====================================================
ALTER TABLE `drivers` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `status`,
ADD INDEX `drivers_company_id_index` (`company_id`);

-- =====================================================
-- STEP 10: Add company_id to Driver Routes Table
-- =====================================================
ALTER TABLE `driver_routes` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `user_id`,
ADD INDEX `driver_routes_company_id_index` (`company_id`);

-- =====================================================
-- STEP 11: Add company_id to Package Loads Table
-- =====================================================
ALTER TABLE `package_loads` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `price`,
ADD INDEX `package_loads_company_id_index` (`company_id`);

-- =====================================================
-- STEP 12: Add company_id to Users Cars Selection Table
-- =====================================================
ALTER TABLE `users_cars_selection` 
ADD COLUMN `company_id` bigint(20) unsigned DEFAULT NULL AFTER `car_id`,
ADD INDEX `users_cars_selection_company_id_index` (`company_id`);

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================
-- Run these to verify all changes were applied

-- Check companies table
SELECT 'Companies table check' as check_type;
SELECT COUNT(*) as table_exists FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'companies';

-- Check all company_id columns
SELECT 'Company ID columns check' as check_type;
SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() AND COLUMN_NAME = 'company_id'
ORDER BY TABLE_NAME;

-- Check all company_id indexes
SELECT 'Company ID indexes check' as check_type;
SELECT TABLE_NAME, INDEX_NAME
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE() AND INDEX_NAME LIKE '%company_id%'
ORDER BY TABLE_NAME, INDEX_NAME;

-- Check mobile app settings
SELECT 'Mobile app settings check' as check_type;
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'companies' 
  AND COLUMN_NAME IN ('app_name', 'app_logo');

-- =====================================================
-- SUMMARY OF CHANGES
-- =====================================================
-- Tables Modified:
-- 1. companies (created) - Stores company/tenant information
-- 2. companies - Added app_name and app_logo for mobile settings
-- 3. users - Added company_id
-- 4. roles - Added company_id
-- 5. customers - Added company_id
-- 6. customer_pacels - Added company_id
-- 7. pacel_histories - Added company_id
-- 8. cars - Added company_id
-- 9. drivers - Added company_id
-- 10. driver_routes - Added company_id
-- 11. package_loads - Added company_id
-- 12. users_cars_selection - Added company_id
--
-- Total: 1 new table, 11 tables modified, 12 company_id columns added
-- =====================================================

