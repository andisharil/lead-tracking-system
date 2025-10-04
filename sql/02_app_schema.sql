SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

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
SET FOREIGN_KEY_CHECKS=1;