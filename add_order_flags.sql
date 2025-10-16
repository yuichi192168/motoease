-- Add requires_credit and agreed_to_terms flags to order_list
ALTER TABLE `order_list`
    ADD COLUMN IF NOT EXISTS `requires_credit` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
    ADD COLUMN IF NOT EXISTS `agreed_to_terms` TINYINT(1) NOT NULL DEFAULT 0 AFTER `requires_credit`;


