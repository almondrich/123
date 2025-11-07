-- ============================================================================
-- FIX INJURY COORDINATE PRECISION
-- ============================================================================
-- This script updates the injuries table to store decimal coordinates
-- instead of integers for better precision
--
-- BEFORE: coordinate_x INT (stores: 49, 8, 52)
-- AFTER:  coordinate_x DECIMAL(5,2) (stores: 49.27, 8.05, 52.13)
--
-- Run this in MySQL/phpMyAdmin or via command line:
-- mysql -u root -p pre_hospital_db < fix_injury_coordinates.sql
-- ============================================================================

USE pre_hospital_db;

-- Backup notice
SELECT '⚠️  IMPORTANT: Make sure you have a backup before running this!' AS 'WARNING';
SELECT 'Run: mysqldump -u root pre_hospital_db > backup.sql' AS 'Backup Command';

-- Show current schema
SELECT 'Current Schema:' AS 'Info';
DESCRIBE injuries;

-- Update coordinate columns to use DECIMAL for precision
ALTER TABLE injuries
MODIFY COLUMN coordinate_x DECIMAL(5,2) NOT NULL COMMENT 'X coordinate as percentage (0-100 with 2 decimals)',
MODIFY COLUMN coordinate_y DECIMAL(5,2) NOT NULL COMMENT 'Y coordinate as percentage (0-100 with 2 decimals)';

-- Show updated schema
SELECT 'Updated Schema:' AS 'Info';
DESCRIBE injuries;

-- Show sample data to verify
SELECT 'Sample Data (first 5 records):' AS 'Info';
SELECT
    id,
    form_id,
    injury_number,
    injury_type,
    coordinate_x,
    coordinate_y,
    body_view
FROM injuries
ORDER BY id DESC
LIMIT 5;

SELECT '✅ Schema update complete!' AS 'Success';
SELECT 'Existing records will keep their integer values (e.g., 49.00)' AS 'Note 1';
SELECT 'New records will store decimal precision (e.g., 49.27)' AS 'Note 2';
