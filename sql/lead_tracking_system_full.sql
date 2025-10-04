-- Lead Tracking System - Full Database Schema and Seed
-- MySQL 8.0+ | InnoDB | utf8mb4
-- Import this file in phpMyAdmin (cPanel) to create all tables and initial data.
-- NOTE: Create the database in cPanel first, then select it in phpMyAdmin before importing.

-- Optional: Uncomment and set your database name if needed when using CLI
-- USE your_database_name;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

/* ==========================
   CORE SCHEMA
   ========================== */

CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) NULL,
  `phone` varchar(255) NULL,
  `company` varchar(255) NULL,
  `position` varchar(255) NULL,
  `created_by` bigint unsigned NULL,
  `updated_by` bigint unsigned NULL,
  `last_login_at` timestamp NULL,
  `deleted_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_created_by_index` (`created_by`),
  KEY `users_updated_by_index` (`updated_by`),
  CONSTRAINT `fk_users_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NULL,
  `created_by` bigint unsigned NULL,
  `updated_by` bigint unsigned NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  KEY `roles_created_by_index` (`created_by`),
  KEY `roles_updated_by_index` (`updated_by`),
  CONSTRAINT `fk_roles_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_roles_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_user_role_id_user_id_unique` (`role_id`,`user_id`),
  KEY `role_user_user_id_index` (`user_id`),
  CONSTRAINT `fk_role_user_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_user_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permission_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_role_permission_id_role_id_unique` (`permission_id`,`role_id`),
  KEY `permission_role_role_id_index` (`role_id`),
  CONSTRAINT `fk_permission_role_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_permission_role_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permission_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `permission_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_user_permission_id_user_id_unique` (`permission_id`,`user_id`),
  KEY `permission_user_user_id_index` (`user_id`),
  CONSTRAINT `fk_permission_user_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_permission_user_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `city` varchar(255) NULL,
  `state` varchar(255) NULL,
  `country` varchar(255) NULL,
  `postal_code` varchar(255) NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `description` text NULL,
  `contact_person` varchar(255) NULL,
  `contact_email` varchar(255) NULL,
  `contact_phone` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locations_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sources` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NULL,
  `status` enum('active','inactive','paused') NOT NULL DEFAULT 'active',
  `description` text NULL,
  `cost_per_lead` decimal(10,2) NULL,
  `monthly_budget` decimal(10,2) NULL,
  `contact_person` varchar(255) NULL,
  `contact_email` varchar(255) NULL,
  `contact_phone` varchar(50) NULL,
  `configuration` json NULL,
  `last_active_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sources_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text NULL,
  `category` varchar(100) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* ==========================
   APPLICATION SCHEMA
   ========================== */

CREATE TABLE IF NOT EXISTS `campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NULL,
  `status` enum('active','paused','completed','draft') NOT NULL DEFAULT 'draft',
  `type` enum('email','social','ppc','display','content','other') NOT NULL DEFAULT 'other',
  `source_id` bigint unsigned NOT NULL,
  `budget` decimal(10,2) NULL,
  `spent` decimal(10,2) NOT NULL DEFAULT 0,
  `start_date` date NULL,
  `end_date` date NULL,
  `targeting` json NULL,
  `settings` json NULL,
  `utm_source` varchar(255) NULL,
  `utm_medium` varchar(255) NULL,
  `utm_campaign` varchar(255) NULL,
  `utm_term` varchar(255) NULL,
  `utm_content` varchar(255) NULL,
  `impressions` int NOT NULL DEFAULT 0,
  `clicks` int NOT NULL DEFAULT 0,
  `ctr` decimal(5,2) NOT NULL DEFAULT 0,
  `cpc` decimal(8,2) NOT NULL DEFAULT 0,
  `cpm` decimal(8,2) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `idx_campaigns_status_source` (`status`,`source_id`),
  KEY `idx_campaigns_dates` (`start_date`,`end_date`),
  CONSTRAINT `fk_campaigns_source` FOREIGN KEY (`source_id`) REFERENCES `sources`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `leads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `location_id` bigint unsigned NOT NULL,
  `source_id` bigint unsigned NOT NULL,
  `status` enum('new','successful','lost') NOT NULL DEFAULT 'new',
  `notes` text NULL,
  `value` decimal(10,2) NULL,
  `campaign_id` bigint unsigned NULL,
  `closed_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_leads_location` FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`),
  CONSTRAINT `fk_leads_source` FOREIGN KEY (`source_id`) REFERENCES `sources`(`id`),
  CONSTRAINT `fk_leads_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE SET NULL,
  KEY `idx_leads_campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `ad_spend` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `month` varchar(255) NOT NULL,
  `spend_date` date NULL,
  `platform` varchar(255) NULL,
  `ad_type` varchar(255) NULL,
  `impressions` int NOT NULL DEFAULT 0,
  `clicks` int NOT NULL DEFAULT 0,
  `conversions` int NOT NULL DEFAULT 0,
  `description` text NULL,
  `source_id` bigint unsigned NOT NULL,
  `campaign_id` bigint unsigned NULL,
  `amount_spent` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ad_spend_month_source_unique` (`month`,`source_id`),
  CONSTRAINT `fk_ad_spend_source` FOREIGN KEY (`source_id`) REFERENCES `sources`(`id`),
  CONSTRAINT `fk_ad_spend_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns`(`id`) ON DELETE SET NULL,
  KEY `idx_ad_spend_campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `webhook_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NULL,
  `endpoint` varchar(255) NOT NULL,
  `method` varchar(10) NOT NULL DEFAULT 'POST',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `status_code` int NULL,
  `processing_time_ms` int NULL,
  `error_message` text NULL,
  `payload` longtext NULL,
  `headers` longtext NULL,
  `response` longtext NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `idx_webhook_logs_status_created` (`status`,`created_at`),
  KEY `idx_webhook_logs_source` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `webhook_retries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `webhook_log_id` bigint unsigned NOT NULL,
  `status` varchar(20) NOT NULL,
  `status_code` int NOT NULL DEFAULT 0,
  `response` longtext NULL,
  `attempted_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `idx_webhook_retries_log_attempt` (`webhook_log_id`,`attempted_at`),
  CONSTRAINT `fk_webhook_retries_log` FOREIGN KEY (`webhook_log_id`) REFERENCES `webhook_logs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* ==========================
   FRAMEWORK / SUPPORT SCHEMA
   ========================== */

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint unsigned NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext NULL,
  `cancelled_at` int NULL,
  `created_at` int NOT NULL,
  `finished_at` int NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* ==========================
   SEED DATA (SAFE DEFAULTS)
   ========================== */

INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'manage_users', 'Create, edit, and delete users', NOW(), NOW()),
(2, 'manage_roles', 'Create, edit, and delete roles and assign permissions', NOW(), NOW()),
(3, 'view_reports', 'View reporting dashboards and exports', NOW(), NOW()),
(4, 'edit_campaigns', 'Create and edit campaigns', NOW(), NOW()),
(5, 'view_leads', 'View leads and basic details', NOW(), NOW()),
(6, 'edit_leads', 'Create, edit, and delete leads', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Admin role', NOW(), NOW()),
(2, 'Manager', 'Manager role', NOW(), NOW()),
(3, 'Staff', 'Staff role', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

INSERT INTO `permission_role` (`permission_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, NOW(), NOW()), (2, 1, NOW(), NOW()), (3, 1, NOW(), NOW()), (4, 1, NOW(), NOW()), (5, 1, NOW(), NOW()), (6, 1, NOW(), NOW()),
(3, 2, NOW(), NOW()), (4, 2, NOW(), NOW()), (5, 2, NOW(), NOW()), (6, 2, NOW(), NOW()),
(5, 3, NOW(), NOW()), (6, 3, NOW(), NOW())
ON DUPLICATE KEY UPDATE `permission_id`=VALUES(`permission_id`), `role_id`=VALUES(`role_id`);

INSERT INTO `locations` (`name`, `created_at`, `updated_at`) VALUES
('KL/Klang/Sg Buloh/Shah Alam', NOW(), NOW()),
('Bangi/Kajang', NOW(), NOW()),
('Kuantan', NOW(), NOW()),
('Johor Bahru', NOW(), NOW()),
('Seremban', NOW(), NOW()),
('Kota Bharu', NOW(), NOW()),
('Ipoh', NOW(), NOW()),
('Kuala Terengganu', NOW(), NOW()),
('Melaka', NOW(), NOW()),
('Batu Pahat', NOW(), NOW()),
('Perai', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

INSERT INTO `sources` (`name`, `type`, `status`, `created_at`, `updated_at`) VALUES
('TikTok', 'other', 'active', NOW(), NOW()),
('Facebook', 'other', 'active', NOW(), NOW()),
('Instagram', 'other', 'active', NOW(), NOW()),
('WhatsApp', 'other', 'active', NOW(), NOW()),
('Website', 'other', 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

INSERT INTO `settings` (`key`, `value`, `category`, `created_at`, `updated_at`) VALUES
('app_name', 'Lead Tracking System', 'general', NOW(), NOW()),
('timezone', 'Asia/Kuala_Lumpur', 'general', NOW(), NOW()),
('app_locale', 'en', 'general', NOW(), NOW()),
('app_debug', 'false', 'general', NOW(), NOW()),
('session_lifetime', '120', 'general', NOW(), NOW())
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NULL,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text NULL,
  `target_user_id` bigint unsigned NULL,
  `ip_address` varchar(45) NULL,
  `user_agent` text NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `idx_activity_logs_module_action` (`module`,`action`),
  KEY `idx_activity_logs_user_created` (`user_id`,`created_at`),
  CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;