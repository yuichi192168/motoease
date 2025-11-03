# Product Stock Management Guide

## How to Change Product Stocks in the Database

### Method 1: Using Admin Interface (Recommended)
1. Navigate to: **Admin Panel → Inventory → Product Stocks**
2. Find your product in the list
3. Click **"Add Stock"** from the Actions dropdown
4. Enter quantity and submit

### Method 2: Direct SQL Updates

#### Understanding Stock Storage
- Stocks are stored in the `stock_list` table
- Each entry has: `product_id`, `quantity`, `type` (1=IN, 2=OUT), `date_created`
- Current stock = SUM of all entries where `type = 1` (IN entries)
- Available stock = Total IN stock - Ordered stock (from order_items)

#### SQL Queries for Stock Management

**1. Add Stock to a Product:**
```sql
-- Add 100 units to product ID 45
INSERT INTO stock_list (product_id, quantity, type, date_created) 
VALUES (45, 100, 1, NOW());
```

**2. Check Current Stock:**
```sql
-- Get current total stock for a product
SELECT 
    p.id,
    p.name,
    COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) as total_stock,
    COALESCE(SUM(CASE WHEN sl.type = 2 THEN sl.quantity ELSE 0 END), 0) as stock_out
FROM product_list p
LEFT JOIN stock_list sl ON p.id = sl.product_id
WHERE p.id = 45  -- Replace with your product ID
GROUP BY p.id;
```

**3. Set Stock to Specific Value (Adjustment):**
```sql
-- Get current stock first
SET @current_stock = (
    SELECT COALESCE(SUM(quantity), 0) 
    FROM stock_list 
    WHERE product_id = 45 AND type = 1
);

-- Set to 150 units (adjustment entry)
SET @new_stock = 150;
SET @adjustment = @new_stock - @current_stock;

-- Add adjustment entry
INSERT INTO stock_list (product_id, quantity, type, date_created) 
VALUES (45, @adjustment, 1, NOW());
```

**4. Reduce Stock:**
```sql
-- Remove 50 units from product ID 45
INSERT INTO stock_list (product_id, quantity, type, date_created) 
VALUES (45, 50, 2, NOW());  -- type 2 = OUT
```

**5. Update Multiple Products:**
```sql
-- Add 100 units to multiple products at once
INSERT INTO stock_list (product_id, quantity, type, date_created) 
VALUES 
    (45, 100, 1, NOW()),
    (46, 100, 1, NOW()),
    (47, 100, 1, NOW());
```

**6. Bulk Stock Update from CSV Import:**
```sql
-- First create a temporary table with your data
CREATE TEMPORARY TABLE temp_stocks (
    product_id INT,
    quantity INT
);

-- Insert your data (or import from CSV)
INSERT INTO temp_stocks VALUES
    (45, 100),
    (46, 200),
    (47, 150);

-- Insert into stock_list
INSERT INTO stock_list (product_id, quantity, type, date_created)
SELECT product_id, quantity, 1, NOW()
FROM temp_stocks;
```

**7. Reset Stock to Zero (if needed):**
```sql
-- Add negative adjustment to bring stock to zero
SET @current_stock = (
    SELECT COALESCE(SUM(quantity), 0) 
    FROM stock_list 
    WHERE product_id = 45 AND type = 1
);

-- If stock is positive, add negative entry
INSERT INTO stock_list (product_id, quantity, type, date_created) 
VALUES (45, -@current_stock, 2, NOW());
```

### Method 3: Using PHP/API Functions

**Using the save_stock function:**
```php
// Via AJAX call
POST to: classes/Master.php?f=save_stock
Parameters:
- product_id: (int) Product ID
- quantity: (float) Quantity to add
- id: (optional) Stock entry ID if editing
```

**Using the update_stock function:**
```php
// Via AJAX call
POST to: classes/Master.php?f=update_stock
Parameters:
- product_id: (int) Product ID
- quantity: (float) Quantity
- movement_type: 'IN' | 'OUT' | 'ADJUSTMENT'
- reason: (optional) Reason for adjustment
```

### Important Notes:

1. **Stock Calculation:**
   - Current Stock = SUM of all `type = 1` entries
   - Available Stock = Current Stock - Reserved Stock (from orders)

2. **Stock Movements:**
   - The system tracks movements in `stock_movements` table
   - This maintains audit trail of all stock changes

3. **Best Practices:**
   - Always add stock entries rather than updating existing ones
   - Use type 1 for adding stock (IN)
   - Use type 2 for removing stock (OUT)
   - Keep audit trail by inserting new records

4. **Viewing Stock:**
   - Admin → Inventory → Product Stocks: See all products with stock levels
   - Admin → Inventory → View Stock: See detailed stock history for a product

### Quick Reference Commands:

```sql
-- Find product ID by name
SELECT id, name FROM product_list WHERE name LIKE '%PCX%';

-- View all stock entries for a product
SELECT * FROM stock_list WHERE product_id = 45 ORDER BY date_created DESC;

-- View stock movements
SELECT * FROM stock_movements WHERE product_id = 45 ORDER BY date_created DESC;

-- Get available stock (considering orders)
SELECT 
    p.id,
    p.name,
    COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) as total_stock,
    COALESCE(SUM(oi.quantity), 0) as ordered,
    (COALESCE(SUM(CASE WHEN sl.type = 1 THEN sl.quantity ELSE 0 END), 0) - 
     COALESCE(SUM(oi.quantity), 0)) as available_stock
FROM product_list p
LEFT JOIN stock_list sl ON p.id = sl.product_id
LEFT JOIN order_items oi ON p.id = oi.product_id 
    AND oi.order_id IN (SELECT id FROM order_list WHERE status != 5)
WHERE p.id = 45
GROUP BY p.id;
```

