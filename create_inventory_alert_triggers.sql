-- Automatically maintain inventory_alerts when inventory/stock changes
-- Run this script in MySQL after deploying code changes.

-- Safety: use the current database
SELECT DATABASE() INTO @cur_db;

-- Ensure inventory_alerts table exists (schema aligned with code expectations)
CREATE TABLE IF NOT EXISTS `inventory_alerts` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`product_id` int(11) NOT NULL,
	`alert_type` varchar(50) NOT NULL,
	`current_stock` decimal(10,2) NOT NULL,
	`threshold_value` decimal(10,2) NOT NULL,
	`message` text NOT NULL,
	`is_resolved` tinyint(1) NOT NULL DEFAULT 0,
	`resolved_by` varchar(100) DEFAULT NULL,
	`resolved_date` datetime DEFAULT NULL,
	`date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	KEY `idx_product_id` (`product_id`),
	KEY `idx_alert_type` (`alert_type`),
	KEY `idx_is_resolved` (`is_resolved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Drop and recreate helper procedure
DROP PROCEDURE IF EXISTS sp_refresh_inventory_alerts;
DELIMITER $$
CREATE PROCEDURE sp_refresh_inventory_alerts(IN in_product_id INT)
proc_main: BEGIN
	DECLARE v_reorder_point DECIMAL(10,2) DEFAULT 0.0;
	DECLARE v_max_stock DECIMAL(10,2) DEFAULT 0.0;
	DECLARE v_name VARCHAR(255) DEFAULT NULL;
	DECLARE v_stock_in DECIMAL(18,2) DEFAULT 0.0;
	DECLARE v_stock_out DECIMAL(18,2) DEFAULT 0.0;
	DECLARE v_ordered DECIMAL(18,2) DEFAULT 0.0;
	DECLARE v_current DECIMAL(18,2) DEFAULT 0.0;
	DECLARE v_available DECIMAL(18,2) DEFAULT 0.0;

	-- Guard: product exists
	SELECT name, COALESCE(reorder_point,0), COALESCE(max_stock,0)
	INTO v_name, v_reorder_point, v_max_stock
	FROM product_list WHERE id = in_product_id LIMIT 1;
	IF v_name IS NULL THEN
		LEAVE proc_main;
	END IF;

	-- Compute stock components
	SELECT COALESCE(SUM(quantity),0) INTO v_stock_in
	FROM stock_list WHERE product_id = in_product_id AND type = 1;

	SELECT COALESCE(SUM(quantity),0) INTO v_stock_out
	FROM stock_list WHERE product_id = in_product_id AND type = 2;

	SELECT COALESCE(SUM(oi.quantity),0) INTO v_ordered
	FROM order_items oi
	JOIN order_list ol ON ol.id = oi.order_id
	WHERE oi.product_id = in_product_id AND ol.status != 5;

	SET v_current = GREATEST(0, v_stock_in - v_stock_out);
	SET v_available = GREATEST(0, v_current - v_ordered);

	-- Resolve helpers
	UPDATE inventory_alerts
	SET is_resolved = 1, resolved_date = NOW()
	WHERE product_id = in_product_id AND alert_type IN ('LOW_STOCK','OVERSTOCK') AND is_resolved = 0;

	-- LOW_STOCK
	IF v_available <= v_reorder_point THEN
		IF EXISTS (
			SELECT 1 FROM inventory_alerts 
			WHERE product_id = in_product_id AND alert_type = 'LOW_STOCK' AND is_resolved = 0
		) THEN
			UPDATE inventory_alerts
			SET current_stock = v_available,
				threshold_value = v_reorder_point,
				message = CONCAT('Low stock alert: ', v_name, ' has ', v_available, ' units remaining (Reorder point: ', v_reorder_point, ')')
			WHERE product_id = in_product_id AND alert_type = 'LOW_STOCK' AND is_resolved = 0;
		ELSE
			INSERT INTO inventory_alerts (product_id, alert_type, current_stock, threshold_value, message, is_resolved, resolved_by, resolved_date, date_created)
			VALUES (in_product_id, 'LOW_STOCK', v_available, v_reorder_point,
					CONCAT('Low stock alert: ', v_name, ' has ', v_available, ' units remaining (Reorder point: ', v_reorder_point, ')'),
					0, NULL, NULL, NOW());
		END IF;
	ELSE
		-- already resolved above in the bulk resolve
	END IF;

	-- OVERSTOCK
	IF v_max_stock > 0 AND v_available >= v_max_stock THEN
		IF EXISTS (
			SELECT 1 FROM inventory_alerts 
			WHERE product_id = in_product_id AND alert_type = 'OVERSTOCK' AND is_resolved = 0
		) THEN
			UPDATE inventory_alerts
			SET current_stock = v_available,
				threshold_value = v_max_stock,
				message = CONCAT('Overstock alert: ', v_name, ' has ', v_available, ' units (Max stock: ', v_max_stock, ')')
			WHERE product_id = in_product_id AND alert_type = 'OVERSTOCK' AND is_resolved = 0;
		ELSE
			INSERT INTO inventory_alerts (product_id, alert_type, current_stock, threshold_value, message, is_resolved, resolved_by, resolved_date, date_created)
			VALUES (in_product_id, 'OVERSTOCK', v_available, v_max_stock,
					CONCAT('Overstock alert: ', v_name, ' has ', v_available, ' units (Max stock: ', v_max_stock, ')'),
					0, NULL, NULL, NOW());
		END IF;
	ELSE
		-- already resolved above in the bulk resolve
	END IF;

END $$
DELIMITER ;

-- Helper: drop trigger if exists function (MySQL doesn’t support IF NOT EXISTS for triggers)
SET @sql := NULL;

-- Triggers on stock_list
SET @sql = (SELECT IFNULL(CONCAT('DROP TRIGGER IF EXISTS `tr_stock_list_ai_refresh_alerts`;'), 'SELECT 1'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = (SELECT IFNULL(CONCAT('DROP TRIGGER IF EXISTS `tr_stock_list_au_refresh_alerts`;'), 'SELECT 1'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = (SELECT IFNULL(CONCAT('DROP TRIGGER IF EXISTS `tr_stock_list_ad_refresh_alerts`;'), 'SELECT 1'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

DELIMITER $$
CREATE TRIGGER tr_stock_list_ai_refresh_alerts
AFTER INSERT ON stock_list
FOR EACH ROW
BEGIN
	CALL sp_refresh_inventory_alerts(NEW.product_id);
END $$

CREATE TRIGGER tr_stock_list_au_refresh_alerts
AFTER UPDATE ON stock_list
FOR EACH ROW
BEGIN
	CALL sp_refresh_inventory_alerts(NEW.product_id);
END $$

CREATE TRIGGER tr_stock_list_ad_refresh_alerts
AFTER DELETE ON stock_list
FOR EACH ROW
BEGIN
	CALL sp_refresh_inventory_alerts(OLD.product_id);
END $$
DELIMITER ;

-- Triggers on order_items (affects available stock via committed quantity)
SET @sql = (SELECT IFNULL(CONCAT('DROP TRIGGER IF EXISTS `tr_order_items_ai_refresh_alerts`;'), 'SELECT 1'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = (SELECT IFNULL(CONCAT('DROP TRIGGER IF EXISTS `tr_order_items_au_refresh_alerts`;'), 'SELECT 1'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = (SELECT IFNULL(CONCAT('DROP TRIGGER IF EXISTS `tr_order_items_ad_refresh_alerts`;'), 'SELECT 1'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

DELIMITER $$
CREATE TRIGGER tr_order_items_ai_refresh_alerts
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
	CALL sp_refresh_inventory_alerts(NEW.product_id);
END $$

CREATE TRIGGER tr_order_items_au_refresh_alerts
AFTER UPDATE ON order_items
FOR EACH ROW
BEGIN
	CALL sp_refresh_inventory_alerts(NEW.product_id);
	IF OLD.product_id <> NEW.product_id THEN
		CALL sp_refresh_inventory_alerts(OLD.product_id);
	END IF;
END $$

CREATE TRIGGER tr_order_items_ad_refresh_alerts
AFTER DELETE ON order_items
FOR EACH ROW
BEGIN
	CALL sp_refresh_inventory_alerts(OLD.product_id);
END $$
DELIMITER ;

-- Trigger on order_list status changes (to capture cancellations / completions)
SET @sql = (SELECT IFNULL(CONCAT('DROP TRIGGER IF EXISTS `tr_order_list_au_refresh_alerts`;'), 'SELECT 1'));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

DELIMITER $$
CREATE TRIGGER tr_order_list_au_refresh_alerts
AFTER UPDATE ON order_list
FOR EACH ROW
BEGIN
	IF OLD.status <> NEW.status THEN
		-- refresh for all products in this order
		DECLARE done INT DEFAULT 0;
		DECLARE v_pid INT;
		DECLARE cur CURSOR FOR SELECT product_id FROM order_items WHERE order_id = NEW.id;
		DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

		OPEN cur;
		read_loop: LOOP
			FETCH cur INTO v_pid;
			IF done = 1 THEN LEAVE read_loop; END IF;
			CALL sp_refresh_inventory_alerts(v_pid);
		END LOOP;
		CLOSE cur;
	END IF;
END $$
DELIMITER ;
