-- Create or_cr_documents table if it doesn't exist
CREATE TABLE IF NOT EXISTS `or_cr_documents` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `client_id` int(30) NOT NULL,
  `document_type` enum('or','cr') NOT NULL,
  `document_number` varchar(100) NOT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL,
  `vehicle_brand` varchar(100) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('pending','released','expired') NOT NULL DEFAULT 'pending',
  `file_path` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_or_cr_documents_client_id` (`client_id`),
  KEY `idx_or_cr_documents_status` (`status`),
  CONSTRAINT `fk_or_cr_documents_client` FOREIGN KEY (`client_id`) REFERENCES `client_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
