-- SQL Migration: Add support for promo and customer images in service requests
-- This migration ensures the uploads directory structure is ready for service request images
-- Images are stored in request_meta table, so no schema changes are needed

-- Note: The request_meta table already exists and is used to store:
-- - promo_image: Path to promotional image for the service request
-- - customer_image: Path to customer image for the service request

-- Images will be stored in: uploads/service_requests/
-- File naming convention:
-- - Promo images: promo_{request_id}_{timestamp}.{ext}
-- - Customer images: customer_{request_id}_{timestamp}.{ext}

-- No database schema changes required as we use the existing request_meta table
-- The system will automatically create the uploads/service_requests/ directory when needed

-- Verification query to check existing images:
-- SELECT rm.request_id, rm.meta_field, rm.meta_value 
-- FROM request_meta rm 
-- WHERE rm.meta_field IN ('promo_image', 'customer_image')
-- ORDER BY rm.request_id DESC;



