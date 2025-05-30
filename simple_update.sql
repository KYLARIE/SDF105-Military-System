-- Use the military database
USE military;

-- Add profile_photo column
ALTER TABLE `admins` ADD COLUMN `profile_photo` VARCHAR(255) DEFAULT NULL;

-- Add role column
ALTER TABLE `admins` ADD COLUMN `role` VARCHAR(100) DEFAULT 'Administrator';

-- Add email column
ALTER TABLE `admins` ADD COLUMN `email` VARCHAR(255) DEFAULT NULL;

-- Add phone column
ALTER TABLE `admins` ADD COLUMN `phone` VARCHAR(50) DEFAULT NULL;

-- Add department column
ALTER TABLE `admins` ADD COLUMN `department` VARCHAR(100) DEFAULT 'IT Department'; 