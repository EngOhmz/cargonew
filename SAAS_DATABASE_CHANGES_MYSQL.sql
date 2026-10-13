-- =====================================================
-- SaaS Database Changes - MySQL Compatible SQL Script
-- =====================================================
-- This script contains all database changes for converting
-- the system into a multi-tenant SaaS application.
-- 
-- IMPORTANT: 
-- 1. Backup your database before running this script
-- 2. Run these commands in order
-- 3. This version uses stored procedures to check if columns exist
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
SET @dbname = DATABASE();
SET @tablename = 'companies';
SET @columnname = 'app_name';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column app_name already exists in companies.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " varchar(255) DEFAULT NULL AFTER `cname`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @columnname = 'app_logo';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column app_logo already exists in companies.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " varchar(255) DEFAULT NULL AFTER `app_name`")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 3: Add company_id to Users Table
-- =====================================================
-- Links users to their company/tenant
SET @tablename = 'users';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in users.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `added_by`, ADD INDEX `users_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 4: Add company_id to Roles Table
-- =====================================================
-- Ensures roles are company-specific
SET @tablename = 'roles';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in roles.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `added_by`, ADD INDEX `roles_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 5: Add company_id to Customers Table
-- =====================================================
-- Isolates customer data by company
SET @tablename = 'customers';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in customers.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `added_by`, ADD INDEX `customers_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 6: Add company_id to Customer Pacels Table
-- =====================================================
-- Isolates parcel data by company
SET @tablename = 'customer_pacels';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in customer_pacels.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `added_by`, ADD INDEX `customer_pacels_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 7: Add company_id to Pacel Histories Table
-- =====================================================
-- Isolates parcel history data by company
SET @tablename = 'pacel_histories';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in pacel_histories.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `added_by`, ADD INDEX `pacel_histories_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 8: Add company_id to Cars Table
-- =====================================================
-- Isolates car/vehicle data by company
SET @tablename = 'cars';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in cars.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `driver_id`, ADD INDEX `cars_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 9: Add company_id to Drivers Table
-- =====================================================
-- Isolates driver data by company
SET @tablename = 'drivers';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in drivers.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `status`, ADD INDEX `drivers_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 10: Add company_id to Driver Routes Table
-- =====================================================
-- Isolates driver route data by company
SET @tablename = 'driver_routes';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in driver_routes.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `user_id`, ADD INDEX `driver_routes_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 11: Add company_id to Package Loads Table
-- =====================================================
-- Isolates package load data by company
SET @tablename = 'package_loads';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in package_loads.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `price`, ADD INDEX `package_loads_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- STEP 12: Add company_id to Users Cars Selection Table
-- =====================================================
-- Isolates user car selection data by company
SET @tablename = 'users_cars_selection';
SET @columnname = 'company_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 'Column company_id already exists in users_cars_selection.'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " bigint(20) unsigned DEFAULT NULL AFTER `car_id`, ADD INDEX `users_cars_selection_company_id_index` (`", @columnname, "`)")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================
-- Run these queries to verify all changes were applied successfully

-- Check if companies table exists
SELECT COUNT(*) as companies_table_exists 
FROM information_schema.tables 
WHERE table_schema = DATABASE() AND table_name = 'companies';

-- Check if all company_id columns exist
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
    AND COLUMN_NAME = 'company_id'
ORDER BY TABLE_NAME;

-- Check if all company_id indexes exist
SELECT 
    TABLE_NAME,
    INDEX_NAME
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
    AND INDEX_NAME LIKE '%company_id%'
ORDER BY TABLE_NAME, INDEX_NAME;

-- Check mobile app settings columns in companies table
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'companies'
    AND COLUMN_NAME IN ('app_name', 'app_logo');

-- =====================================================
-- END OF SCRIPT
-- =====================================================

