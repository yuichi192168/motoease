-- Create appointments table for booking appointments
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `client_id` int(30) NOT NULL,
  `service_type` int(30) NOT NULL,
  `mechanic_id` int(30) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `vehicle_info` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `service_type` (`service_type`),
  KEY `mechanic_id` (`mechanic_id`),
  KEY `appointment_date` (`appointment_date`),
  KEY `status` (`status`),
  CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`service_type`) REFERENCES `service_list` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics_list` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

