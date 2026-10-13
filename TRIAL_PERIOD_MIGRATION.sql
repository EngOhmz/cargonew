-- =====================================================
-- Trial Period and Subscription Management SQL Script
-- =====================================================
-- This script adds trial period and subscription management
-- functionality to the companies table.
-- 
-- IMPORTANT: 
-- 1. Backup your database before running this script
-- 2. Run these commands in order
-- 3. Test in a development environment first
-- =====================================================

-- =====================================================
-- Add Trial and Subscription Fields to Companies Table
-- =====================================================
-- Adds fields to track trial periods and subscriptions
-- Only users with company_id will use trial/subscription periods

ALTER TABLE `companies` 
ADD COLUMN IF NOT EXISTS `trial_start_at` datetime DEFAULT NULL AFTER `personalPhone`,
ADD COLUMN IF NOT EXISTS `trial_end_at` datetime DEFAULT NULL AFTER `trial_start_at`,
ADD COLUMN IF NOT EXISTS `subscription_start_at` datetime DEFAULT NULL AFTER `trial_end_at`,
ADD COLUMN IF NOT EXISTS `subscription_end_at` datetime DEFAULT NULL AFTER `subscription_start_at`,
ADD COLUMN IF NOT EXISTS `is_disabled` tinyint(1) DEFAULT 0 AFTER `subscription_end_at`;

-- =====================================================
-- Update Existing Companies (Optional)
-- =====================================================
-- If you want to set a default trial period for existing companies
-- that don't have one, uncomment and modify the following:

-- UPDATE `companies` 
-- SET `trial_start_at` = NOW(),
--     `trial_end_at` = DATE_ADD(NOW(), INTERVAL 7 DAY),
--     `is_disabled` = 0
-- WHERE `trial_start_at` IS NULL 
--   AND `trial_end_at` IS NULL;

-- =====================================================
-- Notes:
-- =====================================================
-- 1. trial_start_at: When the trial period starts
-- 2. trial_end_at: When the trial period ends (7 days from start by default)
-- 3. subscription_start_at: When the paid subscription starts (after trial)
-- 4. subscription_end_at: When the paid subscription ends
-- 5. is_disabled: Whether the company is disabled (0 = enabled, 1 = disabled)
-- 
-- Only users with company_id will be checked for trial/subscription periods.
-- Users without company_id (like admin@gmail.com) will not be affected.

