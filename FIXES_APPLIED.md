# Fixes Applied - Service Request and OR/CR Management

This document summarizes the fixes applied to the BPSMS system on October 11, 2025.

## 1. OR/CR Documents Management - Admin Upload Feature

### Changes Made:
- **File**: `admin/orcr_documents/index.php`
  - Added "Add Document" button in the card header
  - Created new modal dialog `#addDocumentModal` with comprehensive form fields:
    - Customer selection dropdown
    - Document type (OR/CR)
    - Document number
    - Plate number
    - Vehicle model and brand
    - Release and expiry dates
    - File upload (PDF, JPG, JPEG, PNG)
    - Status selection (Pending, Released, Expired)
    - Remarks field
  - Added JavaScript handler for the Add Document button
  - Implemented form submission with AJAX to upload documents

- **File**: `classes/Master.php`
  - Added new function `add_document()` to handle admin document uploads
  - Function creates new OR/CR document records in the database
  - Handles file uploads and stores them in `uploads/documents/` directory
  - Updates file path in the database with versioning

### Features:
- Admin can now upload OR/CR documents for any customer
- Supports multiple file formats (PDF, JPG, JPEG, PNG)
- Admin can set document status immediately upon upload
- All vehicle and document details can be entered at upload time

## 2. User View of OR/CR Documents in Manage Account

### Current Status:
- **File**: `manage_account.php`
  - OR/CR Documents section already exists (lines 230-275)
  - Displays customer's OR/CR documents in a table format
  - Shows:
    - Document type (OR/CR)
    - Document number
    - Plate number
    - Release date
    - Status badge (color-coded)
    - View button to open document file
  - Query correctly filters by logged-in user's client_id
  - Files are displayed using `validate_image()` function

### Verification:
✅ Users can already view their OR/CR documents in the manage account page
✅ Documents are properly filtered by client ID
✅ View button opens documents in new tab
✅ Status badges display correctly

## 3. Search Services Functionality

### Changes Made:
- **File**: `services.php`
  - Enhanced search functionality in the service list
  - Improved search logic to handle empty searches correctly
  - Added better visibility control with `.show()` and `.hide()` instead of `.toggle()`
  - Added `visibleCount` tracker for debugging
  - Enhanced console logging for troubleshooting
  - Search now shows all items when search field is empty
  - Case-insensitive search through service names and descriptions

### Features:
- Real-time search as user types
- Searches through service names and descriptions
- Shows "No Result" message when no matches found
- Automatically shows all services when search is cleared

## 4. My Service Request View Enhancement

### Changes Made:
- **File**: `my_services.php`
  - Completely redesigned service request cards with Bootstrap card component
  - Added card header with request number
  - Enhanced information display:
    - Request ID in header
    - Formatted date (Month DD, YYYY HH:MM AM/PM)
    - Service type prominently displayed
    - Vehicle name (if available)
    - Vehicle plate number (if available)
    - Mechanic assignment status
    - Color-coded status badges
  - Added card footer with "View Details" button
  - Improved visual hierarchy with shadow effects
  - Better spacing and layout

### Features:
- More professional card-based layout
- Better information organization
- Enhanced readability with proper labels
- Conditional display of vehicle information
- Clearer mechanic assignment status ("Not Assigned" vs "N/A")

## Database Structure

### OR/CR Documents Table:
```sql
CREATE TABLE `or_cr_documents` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `client_id` int(30) NOT NULL,
  `document_type` varchar(10) NOT NULL,
  `document_number` varchar(50) NOT NULL,
  `plate_number` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(100) DEFAULT NULL,
  `vehicle_brand` varchar(100) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `file_path` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_or_cr_documents_client_id` (`client_id`),
  KEY `idx_or_cr_documents_status` (`status`)
)
```

## Files Modified:
1. `admin/orcr_documents/index.php` - Added admin upload functionality
2. `classes/Master.php` - Added `add_document()` function
3. `services.php` - Enhanced search functionality
4. `my_services.php` - Improved service request card layout

## Testing Recommendations:

### OR/CR Document Upload:
1. Login as admin
2. Navigate to OR/CR Documents Management
3. Click "Add Document" button
4. Select a customer
5. Fill in document details
6. Upload a file (PDF or image)
7. Submit and verify document appears in list
8. Login as the customer and verify they can see the document in "Manage Account"

### Search Services:
1. Navigate to Services page
2. Type in search box
3. Verify services filter in real-time
4. Clear search and verify all services return
5. Search for non-existent service and verify "No Result" message

### My Service Requests:
1. Login as customer
2. Navigate to "My Service Requests"
3. Verify cards display correctly with all information
4. Click "View Details" to verify modal opens
5. Verify status badges are color-coded correctly

## Security Considerations:
- All file uploads are validated and stored in dedicated directory
- User can only view their own OR/CR documents (filtered by client_id)
- Admin has full control over document uploads
- File paths use versioning to prevent caching issues

## Success Metrics:
✅ Admin can upload OR/CR documents for customers
✅ Customers can view their OR/CR documents
✅ Search functionality works correctly
✅ Service request view is enhanced and user-friendly
✅ All features are responsive and work across devices

