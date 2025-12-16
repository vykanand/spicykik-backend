-- Create groups table
CREATE TABLE IF NOT EXISTS `groups` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `external_id` VARCHAR(255) NULL,
    `type` ENUM('static', 'dynamic') NOT NULL DEFAULT 'static',
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_groups_name` (`name`),
    UNIQUE KEY `idx_groups_external_id` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create roles table
CREATE TABLE IF NOT EXISTS `roles` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `scope` ENUM('global', 'module', 'app', 'tenant') NOT NULL DEFAULT 'global',
    `is_custom` BOOLEAN NOT NULL DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_roles_name_scope` (`name`, `scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create permissions table
CREATE TABLE IF NOT EXISTS `permissions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `action` VARCHAR(100) NOT NULL,
    `resource` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_permissions_action_resource` (`action`, `resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create user_group_memberships table
CREATE TABLE IF NOT EXISTS `user_group_memberships` (
    `user_id` INT UNSIGNED NOT NULL,
    `group_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `group_id`),
    KEY `idx_ugm_group_id` (`group_id`),
    CONSTRAINT `fk_ugm_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ugm_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create group_role_assignments table
CREATE TABLE IF NOT EXISTS `group_role_assignments` (
    `group_id` INT UNSIGNED NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`group_id`, `role_id`),
    KEY `idx_gra_role_id` (`role_id`),
    CONSTRAINT `fk_gra_group` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_gra_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create user_role_assignments table
CREATE TABLE IF NOT EXISTS `user_role_assignments` (
    `user_id` INT UNSIGNED NOT NULL,
    `role_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`, `role_id`),
    KEY `idx_ura_role_id` (`role_id`),
    CONSTRAINT `fk_ura_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ura_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create role_permissions table
CREATE TABLE IF NOT EXISTS `role_permissions` (
    `role_id` INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`role_id`, `permission_id`),
    KEY `idx_rp_permission_id` (`permission_id`),
    CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default roles
INSERT IGNORE INTO `roles` (`name`, `description`, `scope`, `is_custom`) VALUES
    ('admin', 'Full system administrator with all permissions', 'global', 0),
    ('editor', 'Can create and edit content', 'global', 0),
    ('viewer', 'Can only view content', 'global', 0),
    ('user', 'Basic user with limited permissions', 'global', 0);

-- Insert default permissions
INSERT IGNORE INTO `permissions` (`action`, `resource`, `description`) VALUES
    -- User management
    ('create', 'users', 'Create new users'),
    ('read', 'users', 'View user information'),
    ('update', 'users', 'Update user information'),
    ('delete', 'users', 'Delete users'),
    
    -- Group management
    ('create', 'groups', 'Create new groups'),
    ('read', 'groups', 'View group information'),
    ('update', 'groups', 'Update group information'),
    ('delete', 'groups', 'Delete groups'),
    
    -- Role management
    ('create', 'roles', 'Create new roles'),
    ('read', 'roles', 'View role information'),
    ('update', 'roles', 'Update role information'),
    ('delete', 'roles', 'Delete roles'),
    
    -- Module permissions (examples)
    ('access', 'dashboard', 'Access the dashboard'),
    ('access', 'inventory', 'Access inventory module'),
    ('access', 'reports', 'Access reports module'),
    ('access', 'settings', 'Access system settings');

-- Assign all permissions to admin role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT r.id, p.id
FROM `roles` r
CROSS JOIN `permissions` p
WHERE r.name = 'admin';

-- Update users table structure if needed
ALTER TABLE `users` 
    MODIFY COLUMN `role` VARCHAR(50) NULL,
    ADD COLUMN `is_active` BOOLEAN DEFAULT TRUE AFTER `email`,
    ADD COLUMN `display_name` VARCHAR(255) NULL AFTER `name`,
    ADD COLUMN `external_id` VARCHAR(255) NULL AFTER `display_name`,
    ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
    ADD UNIQUE KEY `idx_users_external_id` (`external_id`);

-- Create default admin group if not exists
INSERT IGNORE INTO `groups` (`name`, `description`, `type`) 
VALUES ('Administrators', 'System administrators with full access', 'static');

-- Assign admin users to Administrators group
INSERT IGNORE INTO `user_group_memberships` (`user_id`, `group_id`)
SELECT u.id, g.id 
FROM `users` u
CROSS JOIN `groups` g 
WHERE u.role = 'admin' AND g.name = 'Administrators';

-- Assign admin role to Administrators group
INSERT IGNORE INTO `group_role_assignments` (`group_id`, `role_id`)
SELECT g.id, r.id
FROM `groups` g
CROSS JOIN `roles` r
WHERE g.name = 'Administrators' AND r.name = 'admin';
