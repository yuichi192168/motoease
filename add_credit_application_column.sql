-- Add credit application completion tracking to client_list table
ALTER TABLE `client_list` 
ADD COLUMN `credit_application_completed` TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Whether customer has completed Motorcentral Credit Application' 
AFTER `last_login`;

-- Add index for better performance
CREATE INDEX idx_credit_application ON client_list(credit_application_completed);

-- Update existing customers to have default value
UPDATE `client_list` SET `credit_application_completed` = 0 WHERE `credit_application_completed` IS NULL;




