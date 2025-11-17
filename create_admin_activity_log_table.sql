-- Admin Activity Log Table
-- This table stores all admin actions for accountability and audit tracking

CREATE TABLE IF NOT EXISTS `admin_activity_log` (
  `log_id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL COMMENT 'ID of admin performing the action',
  `role` VARCHAR(50) NOT NULL DEFAULT 'admin' COMMENT 'Role of the user (should always be admin)',
  `action` TEXT NOT NULL COMMENT 'Detailed action description',
  `module` VARCHAR(100) DEFAULT NULL COMMENT 'Module where action occurred (e.g., Orders, Invoices, Customer Accounts, Inventory)',
  `reference_id` INT(11) DEFAULT NULL COMMENT 'Optional ID for related record (invoice, order, user, etc.)',
  `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of the action',
  PRIMARY KEY (`log_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_role` (`role`),
  KEY `idx_module` (`module`),
  KEY `idx_reference_id` (`reference_id`),
  KEY `idx_timestamp` (`timestamp`),
  CONSTRAINT `fk_admin_activity_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores all admin actions for audit tracking';

