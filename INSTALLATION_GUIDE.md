# Promo & Customer Images - Installation Guide

## Quick Installation

1. **Create Database Tables:**
   Run this SQL in your database:

```sql
-- Create tables for promo images and customer purchase images
CREATE TABLE IF NOT EXISTS `promo_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `image_path` varchar(500) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `customer_purchase_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) NOT NULL,
  `motorcycle_model` varchar(255) NOT NULL,
  `purchase_date` date,
  `image_path` varchar(500) NOT NULL,
  `testimonial` text,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `date_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default system settings
INSERT INTO `system_info` (`meta_field`, `meta_value`) VALUES
('promo_display_enabled', '1'),
('customer_images_enabled', '1'),
('promo_section_title', 'Special Promotions'),
('customer_section_title', 'Happy Customers'),
('max_promo_images', '5'),
('max_customer_images', '8')
ON DUPLICATE KEY UPDATE `meta_value` = VALUES(`meta_value`);
```

2. **Create Upload Directories:**
   The directories `uploads/promos/` and `uploads/customers/` should be created automatically, but if not, create them manually.

3. **Set Permissions:**
   Make sure the upload directories have write permissions (777).

## Features Added

✅ **Promo Images Section** - Display promotional images on home page
✅ **Customer Images Section** - Show customers with their motorcycles
✅ **Admin Upload Interface** - Upload images through System Settings
✅ **Management Panel** - View, activate/deactivate, and delete images
✅ **Responsive Design** - Works on all devices
✅ **Automatic Cleanup** - Old images are deleted when replaced

## How to Use

### For Administrators:

1. **Upload Images:**
   - Go to Admin Panel → System Information
   - Scroll to "Promo Images" or "Customer Purchase Images"
   - Select multiple images and fill in details
   - Click "Update"

2. **Manage Images:**
   - Go to Admin Panel → Promo & Customer Images
   - View all uploaded images
   - Toggle active/inactive status
   - Delete unwanted images

### For Customers:
- Promo images appear in "Special Promotions" section
- Customer images appear in "Happy Customers" section
- Both sections auto-hide if no active images

## File Changes Made

- `admin/system_info/index.php` - Added upload sections
- `admin/promo_management/index.php` - New management page
- `admin/inc/navigation.php` - Added navigation link
- `classes/SystemSettings.php` - Added upload handlers
- `classes/Master.php` - Added management functions
- `home.php` - Added display sections

## Troubleshooting

**Images not showing?**
- Check if tables were created
- Verify images are marked as active
- Check upload directory permissions

**Upload errors?**
- Ensure directories exist: `uploads/promos/` and `uploads/customers/`
- Check PHP upload limits
- Verify file types are images

**Database errors?**
- Run the SQL commands manually
- Check database connection
- Verify table creation

## Support

If you encounter any issues, check the detailed README file: `PROMO_CUSTOMER_FEATURES_README.md`

