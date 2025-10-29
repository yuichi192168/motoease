-- Fix cart database issues
-- This script addresses cart items with ID 0 and other cart-related issues

-- 1. Remove any cart items with ID 0 or invalid product_id
DELETE FROM cart_list WHERE id = 0 OR product_id = 0 OR product_id IS NULL;

-- 2. Remove cart items that reference non-existent products
DELETE c FROM cart_list c 
LEFT JOIN product_list p ON c.product_id = p.id 
WHERE p.id IS NULL;

-- 3. Remove cart items with invalid client_id
DELETE c FROM cart_list c 
LEFT JOIN client_list cl ON c.client_id = cl.id 
WHERE cl.id IS NULL;

-- 4. Update any cart items with quantity 0 or negative
UPDATE cart_list SET quantity = 1 WHERE quantity <= 0;

-- 5. Ensure cart_list table has proper AUTO_INCREMENT
-- First, let's check if the table needs to be altered
-- This will be handled by the application code

-- 6. Clean up any orphaned cart items
DELETE FROM cart_list WHERE client_id NOT IN (SELECT id FROM client_list);

-- 7. Clean up any cart items with invalid product references
DELETE FROM cart_list WHERE product_id NOT IN (SELECT id FROM product_list WHERE delete_flag = 0 AND status = 1);

-- 8. Set proper default values for any NULL quantities
UPDATE cart_list SET quantity = 1 WHERE quantity IS NULL;

-- 9. Ensure date_added is set for any items without it
UPDATE cart_list SET date_added = NOW() WHERE date_added IS NULL;

-- 10. Remove duplicate cart items (same client, product, and color)
-- Keep the most recent one
DELETE c1 FROM cart_list c1
INNER JOIN cart_list c2 
WHERE c1.id < c2.id 
AND c1.client_id = c2.client_id 
AND c1.product_id = c2.product_id 
AND (c1.color = c2.color OR (c1.color IS NULL AND c2.color IS NULL));
