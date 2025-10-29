-- Fix order_list primary key and simplify schema; adjust client_list
START TRANSACTION;

-- 1) ORDER LIST: make IDs unique (fix id = 0 rows), then add PK, then AUTO_INCREMENT
SET @max_order_id := (SELECT IFNULL(MAX(id),0) FROM `order_list`);
UPDATE `order_list`
SET id = (@max_order_id := @max_order_id + 1)
WHERE id = 0;

-- Optional: sanity check for duplicates (commented, for manual run)
-- SELECT id, COUNT(*) cnt FROM `order_list` GROUP BY id HAVING cnt > 1;

ALTER TABLE `order_list` ADD PRIMARY KEY (`id`);
ALTER TABLE `order_list` MODIFY `id` INT(30) NOT NULL AUTO_INCREMENT;

-- Ensure date_updated exists
ALTER TABLE `order_list` 
    ADD COLUMN IF NOT EXISTS `date_updated` DATETIME NULL DEFAULT NULL AFTER `date_created`;

-- Drop delivery-related columns if present
ALTER TABLE `order_list` 
    DROP COLUMN IF EXISTS `delivery_address`;

-- 2) CLIENT LIST: fix id = 0, then add PK, then AUTO_INCREMENT, drop address if present
SET @max_client_id := (SELECT IFNULL(MAX(id),0) FROM `client_list`);
UPDATE `client_list`
SET id = (@max_client_id := @max_client_id + 1)
WHERE id = 0;

-- Optional: sanity check for duplicates (commented, for manual run)
-- SELECT id, COUNT(*) cnt FROM `client_list` GROUP BY id HAVING cnt > 1;

ALTER TABLE `client_list` ADD PRIMARY KEY (`id`);
ALTER TABLE `client_list` MODIFY `id` INT(30) NOT NULL AUTO_INCREMENT;

ALTER TABLE `client_list`
    DROP COLUMN IF EXISTS `address`;

COMMIT;


