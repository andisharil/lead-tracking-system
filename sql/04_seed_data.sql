SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

-- Permissions
INSERT INTO `permissions` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'manage_users', 'Create, edit, and delete users', NOW(), NOW()),
(2, 'manage_roles', 'Create, edit, and delete roles and assign permissions', NOW(), NOW()),
(3, 'view_reports', 'View reporting dashboards and exports', NOW(), NOW()),
(4, 'edit_campaigns', 'Create and edit campaigns', NOW(), NOW()),
(5, 'view_leads', 'View leads and basic details', NOW(), NOW()),
(6, 'edit_leads', 'Create, edit, and delete leads', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Roles
INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Admin role', NOW(), NOW()),
(2, 'Manager', 'Manager role', NOW(), NOW()),
(3, 'Staff', 'Staff role', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Map role to permissions
INSERT INTO `permission_role` (`permission_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, NOW(), NOW()), (2, 1, NOW(), NOW()), (3, 1, NOW(), NOW()), (4, 1, NOW(), NOW()), (5, 1, NOW(), NOW()), (6, 1, NOW(), NOW()),
(3, 2, NOW(), NOW()), (4, 2, NOW(), NOW()), (5, 2, NOW(), NOW()), (6, 2, NOW(), NOW()),
(5, 3, NOW(), NOW()), (6, 3, NOW(), NOW())
ON DUPLICATE KEY UPDATE `permission_id`=VALUES(`permission_id`), `role_id`=VALUES(`role_id`);

-- Locations
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

-- Sources
INSERT INTO `sources` (`name`, `type`, `status`, `created_at`, `updated_at`) VALUES
('TikTok', 'other', 'active', NOW(), NOW()),
('Facebook', 'other', 'active', NOW(), NOW()),
('Instagram', 'other', 'active', NOW(), NOW()),
('WhatsApp', 'other', 'active', NOW(), NOW()),
('Website', 'other', 'active', NOW(), NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);

-- Default Settings (safe placeholders; change in app after deploy)
INSERT INTO `settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
('app_name', 'Lead Tracking System', NOW(), NOW()),
('timezone', 'Asia/Kuala_Lumpur', NOW(), NOW()),
('app_locale', 'en', NOW(), NOW()),
('app_debug', 'false', NOW(), NOW()),
('session_lifetime', '120', NOW(), NOW())
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

SET FOREIGN_KEY_CHECKS=1;