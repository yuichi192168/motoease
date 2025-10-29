# Promo and Customer Image Features

This update adds promotional image display and customer purchase image features to the BP SMS system.

## Features Added

### 1. Promo Images
- Upload and display promotional images on the home page
- Admin can manage promo images through system settings
- Images are displayed in a dedicated "Special Promotions" section

### 2. Customer Purchase Images
- Upload and display images of customers with their purchased motorcycles
- Admin can manage customer images through system settings
- Images are displayed in a "Happy Customers" section with testimonials

### 3. Admin Management
- New management page for viewing and controlling all uploaded images
- Toggle active/inactive status for images
- Delete images with automatic file cleanup

## Installation

1. **Run the installation script:**
   ```
   http://your-domain/bpsms/install_promo_features.php
   ```

2. **Or manually run the SQL:**
   - Execute the SQL commands in `create_promo_customer_tables.sql`

## Usage

### For Administrators

1. **Upload Images:**
   - Go to Admin Panel > System Information
   - Scroll down to "Promo Images" and "Customer Purchase Images" sections
   - Select multiple images and fill in the required information
   - Click "Update" to save

2. **Manage Images:**
   - Go to Admin Panel > Promo Management
   - View all uploaded images in organized tables
   - Toggle active/inactive status
   - Delete unwanted images

### For Customers

- Promo images appear in the "Special Promotions" section on the home page
- Customer images appear in the "Happy Customers" section on the home page
- Both sections are automatically hidden if no active images are available

## File Structure

```
bpsms/
├── admin/
│   ├── system_info/index.php (updated with upload sections)
│   └── promo_management/index.php (new management page)
├── classes/
│   ├── SystemSettings.php (updated with upload handlers)
│   └── Master.php (updated with management functions)
├── uploads/
│   ├── promos/ (promo image storage)
│   └── customers/ (customer image storage)
├── home.php (updated with display sections)
├── create_promo_customer_tables.sql (database schema)
└── install_promo_features.php (installation script)
```

## Database Tables

### promo_images
- `id` - Primary key
- `title` - Promo title
- `description` - Promo description
- `image_path` - Path to uploaded image
- `is_active` - Display status (1=active, 0=inactive)
- `display_order` - Order for display
- `date_created` - Creation timestamp
- `date_updated` - Last update timestamp

### customer_purchase_images
- `id` - Primary key
- `customer_name` - Customer's name
- `motorcycle_model` - Model of purchased motorcycle
- `purchase_date` - Date of purchase
- `image_path` - Path to uploaded image
- `testimonial` - Customer testimonial
- `is_active` - Display status (1=active, 0=inactive)
- `display_order` - Order for display
- `date_created` - Creation timestamp
- `date_updated` - Last update timestamp

## System Settings

New system settings added:
- `promo_display_enabled` - Enable/disable promo display
- `customer_images_enabled` - Enable/disable customer images
- `promo_section_title` - Title for promo section
- `customer_section_title` - Title for customer section
- `max_promo_images` - Maximum promo images to display
- `max_customer_images` - Maximum customer images to display

## Customization

### Styling
The sections use Bootstrap classes and can be customized by modifying the CSS in `home.php`.

### Display Limits
Modify the LIMIT values in the SQL queries in `home.php` to change how many images are displayed.

### Image Sizes
Adjust the `height` and `width` values in the CSS to change image display sizes.

## Security Notes

- Images are validated for type (only image files accepted)
- File names are timestamped to prevent conflicts
- Old images are automatically deleted when replaced
- All user inputs are properly escaped to prevent XSS

## Troubleshooting

1. **Images not displaying:**
   - Check if upload directories exist and have proper permissions
   - Verify database tables were created successfully
   - Check if images are marked as active in the management panel

2. **Upload errors:**
   - Ensure upload directories have write permissions (777)
   - Check PHP upload limits in php.ini
   - Verify file types are supported images

3. **Database errors:**
   - Run the installation script again
   - Check database connection
   - Verify SQL syntax in the create script

