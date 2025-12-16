-- Security Database Schema
-- Created: 2024-09-14

-- Create the security database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `security` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the security database
USE `security`;

-- Enable foreign key constraints
SET FOREIGN_KEY_CHECKS = 1;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Users table (references the main ERP users table)
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Groups table
CREATE TABLE IF NOT EXISTS `groups` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text,
    `is_active` tinyint(1) NOT NULL DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles table
CREATE TABLE IF NOT EXISTS `roles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text,
    `is_system` tinyint(1) NOT NULL DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permissions table
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text,
    `action` varchar(50) NOT NULL,
    `resource` varchar(100) NOT NULL,
    `module` varchar(50) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permission_unique` (`action`, `resource`, `module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User-Group mapping
CREATE TABLE IF NOT EXISTS `user_groups` (
    `user_id` int(11) NOT NULL,
    `group_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `group_id`),
    KEY `group_id` (`group_id`),
    CONSTRAINT `user_groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `user_groups_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Group-Role mapping
CREATE TABLE IF NOT EXISTS `group_roles` (
    `group_id` int(11) NOT NULL,
    `role_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`group_id`, `role_id`),
    KEY `role_id` (`role_id`),
    CONSTRAINT `group_roles_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
    CONSTRAINT `group_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role-Permission mapping
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id` int(11) NOT NULL,
    `permission_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`role_id`, `permission_id`),
    KEY `permission_id` (`permission_id`),
    CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default system roles
INSERT IGNORE INTO `roles` (`id`, `name`, `description`, `is_system`) VALUES 
(1, 'super_admin', 'System administrator with full access', 1),
(2, 'admin', 'Administrator with most access', 1),
(3, 'editor', 'Can create and edit content', 1),
(4, 'viewer', 'Can only view content', 1);

-- Create a default admin group
INSERT IGNORE INTO `groups` (`id`, `name`, `description`) VALUES 
(1, 'System Administrators', 'System administrators with full access');

-- Assign super_admin role to System Administrators group
INSERT IGNORE INTO `group_roles` (`group_id`, `role_id`) VALUES (1, 1);

-- Create default admin user
-- Default password is 'admin123' (bcrypt hash)
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`, `is_active`) VALUES 
(1, 'admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Add admin user to System Administrators group
INSERT IGNORE INTO `user_groups` (`user_id`, `group_id`) VALUES (1, 1);

-- Create database user for the security system
-- Note: In production, use more secure credentials
CREATE USER IF NOT EXISTS 'security_user'@'localhost' IDENTIFIED BY 'secure_password_123';
GRANT SELECT, INSERT, UPDATE, DELETE ON `security`.* TO 'security_user'@'localhost';
FLUSH PRIVILEGES;

-- Output completion message
SELECT 'Security database and tables created successfully!' AS message;

COMMIT;