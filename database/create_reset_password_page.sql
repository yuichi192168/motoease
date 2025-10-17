-- Ensure reset columns exist for token-based reset without email
ALTER TABLE `client_list`
  ADD COLUMN IF NOT EXISTS `reset_token` VARCHAR(128) NULL AFTER `last_login`,
  ADD COLUMN IF NOT EXISTS `reset_expires` DATETIME NULL AFTER `reset_token`;

CREATE INDEX IF NOT EXISTS idx_client_reset_token ON `client_list` (`reset_token`);

