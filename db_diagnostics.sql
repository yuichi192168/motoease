-- Diagnostics for bpsms_db
-- Read-only checks to identify schema issues for appointments, cart, and service requests

-- 1) List presence of critical tables
SELECT 'service_requests' AS table_name, COUNT(*) AS exists_flag
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'service_requests'
UNION ALL
SELECT 'request_meta', COUNT(*)
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'request_meta'
UNION ALL
SELECT 'appointments', COUNT(*)
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'appointments'
UNION ALL
SELECT 'cart_list', COUNT(*)
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cart_list'
UNION ALL
SELECT 'service_list', COUNT(*)
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'service_list';

-- 2) Columns existence checks
SELECT TABLE_NAME, COLUMN_NAME
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND ( (TABLE_NAME = 'service_list' AND COLUMN_NAME IN ('estimated_hours','status','delete_flag'))
     OR (TABLE_NAME = 'service_requests' AND COLUMN_NAME IN ('client_id','mechanic_id','service_type'))
     OR (TABLE_NAME = 'appointments' AND COLUMN_NAME IN ('client_id','service_type','appointment_date','appointment_time','status'))
     OR (TABLE_NAME = 'cart_list' AND COLUMN_NAME IN ('client_id','product_id','color','quantity')) )
ORDER BY TABLE_NAME, COLUMN_NAME;

-- 3) Missing unique index for cart_list
SELECT 'cart_list uniq' AS check_name,
       (SELECT COUNT(1) FROM information_schema.STATISTICS s
         WHERE s.TABLE_SCHEMA = DATABASE()
           AND s.TABLE_NAME = 'cart_list'
           AND s.INDEX_NAME = 'uniq_cart_client_product_color') AS has_unique_index;

-- 4) FK checks
SELECT TABLE_NAME, CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('cart_list','service_requests','request_meta','appointments')
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, CONSTRAINT_NAME;

-- 5) Appointment slot uniqueness diagnostics
SELECT 'appointment_slot_dupes' AS issue,
       appointment_date, appointment_time, COUNT(*) as cnt
FROM appointments
GROUP BY appointment_date, appointment_time
HAVING COUNT(*) > 1;

-- 6) Orphan request_meta
SELECT 'orphan_request_meta' AS issue, COUNT(*) as orphans
FROM request_meta rm
LEFT JOIN service_requests sr ON sr.id = rm.request_id
WHERE sr.id IS NULL;
