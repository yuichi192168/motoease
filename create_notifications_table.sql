-- Create notifications table for customer payment notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(30) NOT NULL,
  `type` enum('payment_upcoming','payment_missed','order_status','general') NOT NULL DEFAULT 'general',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reference_id` int(30) DEFAULT NULL COMMENT 'Reference to order_id or other related record',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_read` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `is_read` (`is_read`),
  KEY `date_created` (`date_created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add some sample notifications for testing
INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `reference_id`, `is_read`, `date_created`) VALUES
(2, 'payment_upcoming', 'Payment Due Soon', 'Payment reminder: Your order 202504-00001 (₱12,500.00) is due in 2 day(s).', 9, 0, NOW()),
(6, 'payment_missed', 'Payment Overdue', 'Overdue payment: Your order 202508-00001 (₱150,000.00) is 5 day(s) overdue.', 12, 0, NOW());



