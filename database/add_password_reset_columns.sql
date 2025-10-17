-- Add password reset columns to client_list if they don't exist
ALTER TABLE `client_list`
  ADD COLUMN IF NOT EXISTS `reset_token` VARCHAR(128) NULL AFTER `last_login`,
  ADD COLUMN IF NOT EXISTS `reset_expires` DATETIME NULL AFTER `reset_token`;

-- Optional: index the token for quick lookup
CREATE INDEX IF NOT EXISTS idx_client_reset_token ON `client_list` (`reset_token`);

