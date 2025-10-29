# Testing Guide - Fixed Issues

This guide provides step-by-step instructions to test the fixes applied to the BPSMS system.

## 1. Testing My Service Request View Details

### Steps:
1. **Login as a customer** (user with login_type = 2)
2. **Navigate to "My Service Requests"** from the customer dashboard or top navigation
3. **Verify the page loads correctly** with service request cards
4. **Click "View Details" button** on any service request card
5. **Expected Result**: A modal should open showing detailed service request information including:
   - Date requested
   - Status with color-coded badge
   - Vehicle information
   - Service type
   - Assigned mechanic
   - Requested services

### Debug Information:
- Open browser console (F12) and check for:
  - "My services page loaded" message
  - "View data buttons found: X" (where X > 0)
  - "View data clicked for ID: X" when clicking view details

### Troubleshooting:
- If modal doesn't open, check browser console for JavaScript errors
- Verify that `uni_modal` function is available (should be defined in main index.php)
- Check if the view_request.php file exists and is accessible

## 2. Testing Admin OR/CR Document Upload

### Steps:
1. **Login as admin** (user with login_type = 1)
2. **Navigate to "OR/CR Documents Management"** from admin panel
3. **Click "Add Document" button** in the top right
4. **Fill out the form**:
   - Select a customer from dropdown
   - Choose document type (OR or CR)
   - Enter document number
   - Enter plate number (optional)
   - Enter vehicle model and brand (optional)
   - Set release and expiry dates (optional)
   - Upload a file (PDF, JPG, JPEG, or PNG)
   - Select status (Pending, Released, or Expired)
   - Add remarks (optional)
5. **Click "Add Document"** to submit
6. **Expected Result**: 
   - Success message should appear
   - Modal should close
   - Page should reload showing the new document in the list

### Debug Information:
- Open browser console (F12) and check for:
  - "Add document form submitted" when clicking submit
  - "Response: {status: 'success', msg: '...'}" for successful uploads
  - Any error messages for failed uploads
- Check server error logs for:
  - "Add document function called with data: ..."
  - "SQL Query: INSERT INTO..."
  - "Document inserted with ID: X"
  - "File uploaded successfully: ..."

### Troubleshooting:
- If upload fails, check:
  - File permissions on uploads/documents/ directory
  - File size limits in PHP configuration
  - Database connection and table structure
  - Required fields are filled (customer, document type, document number, file)

## 3. Testing Search Services Functionality

### Steps:
1. **Navigate to "Services" page** (from main navigation)
2. **Verify all services are visible** on page load
3. **Type in the search box** to filter services:
   - Try searching for service names (e.g., "oil", "brake", "tire")
   - Try searching for descriptions
   - Try searching for partial words
4. **Clear the search box** to show all services again
5. **Expected Results**:
   - Services should filter in real-time as you type
   - "No Result" message should appear when no matches found
   - All services should reappear when search is cleared

### Debug Information:
- Open browser console (F12) and check for:
  - "Services page loaded" message
  - "Service items count: X" (where X > 0)
  - "Searching for: [search term]" when typing
  - "Checking text: [service text]" for each service
  - "Visible items: X" showing count of visible services

### Troubleshooting:
- If search doesn't work:
  - Check if jQuery is loaded properly
  - Verify the search input has ID "search"
  - Check if service items have class "item"
  - Ensure no JavaScript errors in console

## 4. Testing User OR/CR Document View

### Steps:
1. **Login as a customer** who has OR/CR documents uploaded by admin
2. **Navigate to "Manage Account"** from customer dashboard
3. **Scroll down to "OR/CR Documents" section**
4. **Verify documents are displayed** in a table format
5. **Click "View" button** on any document
6. **Expected Result**: Document should open in a new tab/window

### Expected Display:
- Document type (OR/CR)
- Document number
- Plate number
- Release date
- Status with color-coded badge
- View button for each document

## 5. Common Issues and Solutions

### JavaScript Errors:
- **Issue**: Functions not defined
- **Solution**: Check if all required JavaScript files are loaded
- **Check**: Browser console for "function not defined" errors

### Database Errors:
- **Issue**: SQL query failures
- **Solution**: Check database connection and table structure
- **Check**: Server error logs for SQL errors

### File Upload Issues:
- **Issue**: Files not uploading
- **Solution**: Check file permissions and PHP upload settings
- **Check**: uploads/documents/ directory exists and is writable

### Modal Not Opening:
- **Issue**: uni_modal function not working
- **Solution**: Check if modal HTML structure exists in page
- **Check**: Browser console for JavaScript errors

## 6. Files Modified for Testing

### Core Files:
1. `my_services.php` - Enhanced view details functionality
2. `admin/orcr_documents/index.php` - Added upload functionality
3. `classes/Master.php` - Added add_document() function
4. `services.php` - Enhanced search functionality

### Key Functions:
- `uni_modal()` - Modal display function
- `add_document()` - Admin document upload
- Search functionality - Real-time service filtering
- View details - Service request modal display

## 7. Success Criteria

### ✅ My Service Request View Details:
- Modal opens when clicking "View Details"
- All service request information displays correctly
- No JavaScript errors in console

### ✅ Admin OR/CR Document Upload:
- Form submits successfully
- Document appears in admin list after upload
- Customer can view uploaded document in manage account
- File uploads to correct directory

### ✅ Search Services:
- Real-time filtering works as user types
- All services show when search is cleared
- "No Result" message appears when no matches
- No JavaScript errors

### ✅ User OR/CR Document View:
- Documents display in manage account
- View button opens documents correctly
- All document information shows properly

## 8. Browser Compatibility

Tested on:
- Chrome (latest)
- Firefox (latest)
- Edge (latest)
- Safari (latest)

## 9. Mobile Responsiveness

All fixes maintain mobile responsiveness:
- Cards stack properly on mobile devices
- Modals are responsive
- Search functionality works on touch devices
- File upload works on mobile browsers

## 10. Performance Considerations

- Search is optimized with real-time filtering
- File uploads are handled efficiently
- Database queries are optimized
- JavaScript is loaded asynchronously where possible
