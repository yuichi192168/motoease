-- =====================================================
-- SQL Script to Reset All Product Stocks to Zero
-- =====================================================
-- WARNING: This will reset ALL product stocks to zero!
-- Make sure you have a database backup before running this script.
-- =====================================================

-- Step 1: Preview what will be reset (run this first to see what will happen)
-- =====================================================
SELECT 
    p.id as product_id,
    p.name as product_name,
    COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) as current_stock,
    COALESCE(SUM(CASE WHEN sl.type = 2 THEN sl.quantity ELSE 0 END), 0) as stock_out
FROM product_list p
LEFT JOIN stock_list sl ON p.id = sl.product_id
WHERE p.delete_flag = 0
GROUP BY p.id, p.name
HAVING current_stock > 0
ORDER BY p.id;

-- =====================================================
-- Step 2: Reset all stocks to zero
-- =====================================================
-- This creates OUT entries to bring all stocks to zero

-- Start transaction for safety
START TRANSACTION;

-- Insert OUT entries to reset all stocks to zero
INSERT INTO stock_list (product_id, quantity, type, date_created)
SELECT 
    product_id,
    -current_stock as quantity,  -- Negative quantity to reduce to zero
    2 as type,                  -- 2 = OUT
    NOW() as date_created
FROM (
    SELECT 
        p.id as product_id,
        COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) as current_stock
    FROM product_list p
    LEFT JOIN stock_list sl ON p.id = sl.product_id
    WHERE p.delete_flag = 0
    GROUP BY p.id
    HAVING current_stock > 0
) as stock_summary;

-- Optional: Record in stock_movements table (if it exists)
-- Uncomment the following if you want to track this in stock_movements

/*
INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, reference_id, reference_type, date_created, created_by)
SELECT 
    product_id,
    'OUT' as movement_type,
    -current_stock as quantity,
    current_stock as previous_stock,
    0 as new_stock,
    'Bulk stock reset to zero' as reason,
    CONCAT('BULK_RESET-', DATE_FORMAT(NOW(), '%Y%m%d')) as reference_id,
    'ADJUSTMENT' as reference_type,
    NOW() as date_created,
    NULL as created_by
FROM (
    SELECT 
        p.id as product_id,
        COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) as current_stock
    FROM product_list p
    LEFT JOIN stock_list sl ON p.id = sl.product_id
    WHERE p.delete_flag = 0
    GROUP BY p.id
    HAVING current_stock > 0
) as stock_summary;
*/

-- Review what was inserted before committing
SELECT 
    COUNT(*) as total_reset_entries,
    SUM(ABS(quantity)) as total_quantity_reset
FROM stock_list 
WHERE type = 2 
AND date_created >= DATE_SUB(NOW(), INTERVAL 1 MINUTE);

-- If everything looks good, commit the transaction
-- If not, run: ROLLBACK;
COMMIT;

-- =====================================================
-- Step 3: Verify all stocks are now zero
-- =====================================================
SELECT 
    p.id as product_id,
    p.name as product_name,
    COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) as total_stock_in,
    COALESCE(SUM(CASE WHEN sl.type = 2 THEN sl.quantity ELSE 0 END), 0) as total_stock_out,
    (COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) - 
     COALESCE(SUM(CASE WHEN sl.type = 2 THEN sl.quantity ELSE 0 END), 0)) as current_stock
FROM product_list p
LEFT JOIN stock_list sl ON p.id = sl.product_id
WHERE p.delete_flag = 0
GROUP BY p.id, p.name
ORDER BY p.id;

-- =====================================================
-- Alternative: Reset specific products only
-- =====================================================
-- If you want to reset only specific products, use this instead:

/*
START TRANSACTION;

INSERT INTO stock_list (product_id, quantity, type, date_created)
SELECT 
    product_id,
    -current_stock as quantity,
    2 as type,
    NOW() as date_created
FROM (
    SELECT 
        p.id as product_id,
        COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) as current_stock
    FROM product_list p
    LEFT JOIN stock_list sl ON p.id = sl.product_id
    WHERE p.delete_flag = 0
    AND p.id IN (1, 2, 3, 4, 5)  -- Replace with your product IDs
    GROUP BY p.id
    HAVING current_stock > 0
) as stock_summary;

COMMIT;
*/

-- =====================================================
-- Alternative: Reset by category
-- =====================================================
-- If you want to reset stocks for products in a specific category:

/*
START TRANSACTION;

INSERT INTO stock_list (product_id, quantity, type, date_created)
SELECT 
    product_id,
    -current_stock as quantity,
    2 as type,
    NOW() as date_created
FROM (
    SELECT 
        p.id as product_id,
        COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) as current_stock
    FROM product_list p
    LEFT JOIN stock_list sl ON p.id = sl.product_id
    WHERE p.delete_flag = 0
    AND p.category_id = 1  -- Replace with your category ID
    GROUP BY p.id
    HAVING current_stock > 0
) as stock_summary;

COMMIT;
*/

