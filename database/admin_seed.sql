-- Create database and admin_users table for TokoBajuCustom
CREATE DATABASE IF NOT EXISTS `toko_baju_custom` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `toko_baju_custom`;

CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(150) NOT NULL,
  `username` VARCHAR(100) DEFAULT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(30) DEFAULT NULL,
  `role` VARCHAR(50) DEFAULT 'admin',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: Use the CLI seeder at tools/create_admin_user.php to insert an admin user (it will hash the password properly).
