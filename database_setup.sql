-- ========================================================================
-- BATOM 1:1 CUSTOM WEARABLE ART STUDIO - DATABASE SETUP
-- Database Name: batom_studio
-- SQL Datetime: 2026-06-12
-- ========================================================================

-- ========================================================================
-- 1. CUSTOMER/USER MANAGEMENT TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `phone_number` VARCHAR(20),
  `password` VARCHAR(255) NOT NULL,
  `profile_picture` VARCHAR(255),
  `address` TEXT,
  `city` VARCHAR(50),
  `postal_code` VARCHAR(10),
  `country` VARCHAR(50) DEFAULT 'Indonesia',
  `account_status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  `verification_token` VARCHAR(255),
  `is_verified` BOOLEAN DEFAULT 0,
  `whatsapp_verified` BOOLEAN DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` TIMESTAMP NULL,
  INDEX `idx_email` (`email`),
  INDEX `idx_username` (`username`),
  INDEX `idx_phone` (`phone_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 2. ADMIN USERS TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `email` VARCHAR(100) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone_number` VARCHAR(20),
  `role` ENUM('super_admin', 'admin', 'curator', 'designer') DEFAULT 'admin',
  `permissions` JSON,
  `profile_picture` VARCHAR(255),
  `account_status` ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_login` TIMESTAMP NULL,
  INDEX `idx_email` (`email`),
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 3. CUSTOM ORDERS/COMMISSIONS TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_code` VARCHAR(50) UNIQUE NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `product_type` VARCHAR(50) NOT NULL,
  `garment_size` VARCHAR(10),
  `base_color` VARCHAR(50),
  `material_type` VARCHAR(50),
  `artwork_theme` VARCHAR(100),
  `placement_location` JSON,
  `design_description` TEXT,
  `budget_range` VARCHAR(50),
  `target_deadline` DATE,
  `total_price` DECIMAL(12, 2),
  `order_status` ENUM('pending', 'approved', 'in_progress', 'processing', 'delivering', 'finished', 'quality_check', 'ready_to_ship', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
  `payment_status` ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
  `payment_method` ENUM('whatsapp', 'bank_transfer', 'other') DEFAULT 'whatsapp',
  `notes` TEXT,
  `assigned_curator` INT UNSIGNED,
  `assigned_designer` INT UNSIGNED,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `shipped_at` TIMESTAMP NULL,
  `delivered_at` TIMESTAMP NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_curator`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`assigned_designer`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_order_code` (`order_code`),
  INDEX `idx_order_status` (`order_status`),
  INDEX `idx_payment_status` (`payment_status`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 4. ORDER ATTACHMENTS/REFERENCES TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `order_attachments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `file_type` ENUM('reference_image', 'sketch', 'design_draft') DEFAULT 'reference_image',
  `original_filename` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_size` INT,
  `mime_type` VARCHAR(50),
  `upload_description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  INDEX `idx_order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 5. ORDER STATUS TRACKING TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `order_status_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `status_type` ENUM('pending', 'approved', 'in_progress', 'processing', 'delivering', 'finished', 'quality_check', 'ready_to_ship', 'shipped', 'delivered', 'cancelled') NOT NULL,
  `status_description` TEXT,
  `updated_by` INT UNSIGNED,
  `progress_percentage` INT DEFAULT 0,
  `estimated_completion` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`updated_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 6. PAYMENT TRACKING TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `payment_tracking` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `total_amount` DECIMAL(12, 2) NOT NULL,
  `paid_amount` DECIMAL(12, 2) DEFAULT 0,
  `payment_status` ENUM('unpaid', 'partial', 'paid', 'pending_verification') DEFAULT 'unpaid',
  `payment_method` ENUM('whatsapp', 'bank_transfer', 'other') DEFAULT 'whatsapp',
  `payment_details` JSON,
  `whatsapp_message_sent` BOOLEAN DEFAULT 0,
  `whatsapp_message_timestamp` TIMESTAMP NULL,
  `payment_verified_by` INT UNSIGNED,
  `verified_at` TIMESTAMP NULL,
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`payment_verified_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_payment_status` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 7. INVOICES/STRUCK PEMBELANJAAN TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(50) UNIQUE NOT NULL,
  `order_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `subtotal` DECIMAL(12, 2),
  `custom_surcharge` DECIMAL(12, 2) DEFAULT 0,
  `shipping_cost` DECIMAL(12, 2) DEFAULT 0,
  `tax_percentage` INT DEFAULT 11,
  `tax_amount` DECIMAL(12, 2),
  `grand_total` DECIMAL(12, 2),
  `invoice_status` ENUM('draft', 'sent', 'viewed', 'paid', 'overdue') DEFAULT 'draft',
  `invoice_date` DATE,
  `due_date` DATE,
  `pdf_file_path` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_invoice_number` (`invoice_number`),
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_invoice_status` (`invoice_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 8. ACTIVITY LOGS TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED,
  `admin_id` INT UNSIGNED,
  `activity_type` VARCHAR(100),
  `description` TEXT,
  `related_order_id` INT UNSIGNED,
  `ip_address` VARCHAR(45),
  `user_agent` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`admin_id`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`related_order_id`) REFERENCES `orders`(`id`) ON DELETE SET NULL,
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 9. REPORTS/LAPORAN TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `admin_reports` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `report_type` ENUM('daily', 'weekly', 'monthly', 'yearly') NOT NULL,
  `report_date` DATE NOT NULL,
  `total_orders` INT DEFAULT 0,
  `completed_orders` INT DEFAULT 0,
  `pending_orders` INT DEFAULT 0,
  `total_revenue` DECIMAL(12, 2) DEFAULT 0,
  `unpaid_amount` DECIMAL(12, 2) DEFAULT 0,
  `total_customers` INT DEFAULT 0,
  `new_customers` INT DEFAULT 0,
  `custom_details` JSON,
  `generated_by` INT UNSIGNED,
  `pdf_file_path` VARCHAR(255),
  `excel_file_path` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`generated_by`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL,
  INDEX `idx_report_type` (`report_type`),
  INDEX `idx_report_date` (`report_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 10. DASHBOARD ANALYTICS SNAPSHOTS TABLE
-- ========================================================================
CREATE TABLE IF NOT EXISTS `analytics_snapshots` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `snapshot_date` DATE NOT NULL,
  `total_users` INT DEFAULT 0,
  `total_orders` INT DEFAULT 0,
  `completed_orders` INT DEFAULT 0,
  `cancelled_orders` INT DEFAULT 0,
  `total_revenue` DECIMAL(12, 2) DEFAULT 0,
  `average_order_value` DECIMAL(12, 2) DEFAULT 0,
  `top_product_type` VARCHAR(50),
  `top_theme` VARCHAR(100),
  `conversion_rate` DECIMAL(5, 2) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_snapshot_date` (`snapshot_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================================
-- 11. SAMPLE DATA - DEFAULT ADMIN USER
-- ========================================================================
-- Password: admin123 (hashed with bcrypt)
INSERT INTO `admin_users` 
(`full_name`, `username`, `email`, `password`, `phone_number`, `role`, `account_status`) 
VALUES 
('Admin Batom', 'admin_batom', 'admin@batom.studio', '$2y$10$X9..K/nR1z4zz0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0x0', '628123456789', 'super_admin', 'active');

-- ========================================================================
-- INDEXES FOR PERFORMANCE OPTIMIZATION
-- ========================================================================
CREATE INDEX `idx_orders_user_status` ON `orders`(`user_id`, `order_status`);
CREATE INDEX `idx_orders_payment_created` ON `orders`(`payment_status`, `created_at`);
CREATE INDEX `idx_payment_tracking_order` ON `payment_tracking`(`order_id`, `payment_status`);
CREATE INDEX `idx_invoices_date` ON `invoices`(`invoice_date`);
CREATE INDEX `idx_activity_logs_admin` ON `activity_logs`(`admin_id`, `created_at`);
