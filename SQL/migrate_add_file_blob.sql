-- Add file_blob and original_name columns to files_table if they don't exist
-- This migration adds support for storing files in the database as LONGBLOB

ALTER TABLE `files_table` 
  ADD COLUMN `file_blob` LONGBLOB DEFAULT NULL,
  ADD COLUMN `original_name` VARCHAR(255) DEFAULT NULL;
