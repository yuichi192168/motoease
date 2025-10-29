# MotoEase

**A Client-Focused Web-Based System for Motorcycle Sales and Service**

MotoEase is a comprehensive web-based management system designed for Star Honda Calamba, providing end-to-end solutions for motorcycle dealership operations including sales, service management, customer accounts, and administration.

## 📋 Description

MotoEase is a complete business management solution specifically tailored for motorcycle dealerships. The system enables dealers to manage their motorcycle inventory, handle customer service requests, track appointments, process orders, manage invoices, and maintain comprehensive customer records. With an intuitive interface for both customers and administrators, MotoEase streamlines day-to-day operations while providing excellent customer experience.

### Key Highlights
- **Motorcycle Sales Management**: Complete catalog of Honda motorcycles with detailed specifications
- **Service Management**: Appointment scheduling and service request tracking
- **Customer Portal**: Account management, order tracking, and invoice viewing
- **Inventory Management**: ABC category classification with stock alerts
- **Document Management**: OR/CR document handling and verification
- **Admin Dashboard**: Comprehensive reporting and analytics

## ✨ Features

### Customer Features
- **Account Management**: Create account, manage profile, view account balance
- **Product Browsing**: Browse motorcycles by category with detailed specifications
- **Shopping Cart**: Add to cart with quantity validation and stock checks
- **Order Placement**: Secure checkout with order tracking
- **Service Requests**: Submit service appointments and track status
- **Invoice Access**: View and download invoices
- **Document Upload**: Upload OR/CR documents for verification
- **Wishlist**: Save favorite motorcycles for later

### Administrator Features
- **User Management**: Complete user control with role-based access (Admin, Branch Supervisor, Staff)
- **Product Management**: Add, edit, and manage motorcycle listings with specifications
- **Inventory Control**: ABC category classification with low stock alerts
- **Order Processing**: View and process customer orders
- **Service Management**: Approve/reject service appointments, track progress
- **Report Generation**: Sales reports, inventory reports, customer analytics
- **System Settings**: Configure site information, logo, payment methods
- **Promo Management**: Upload and manage promotional images
- **Customer Image Gallery**: Showcase customer purchases

### System Features
- **Role-Based Access Control**: Multi-level permission system
- **Security**: Account locking mechanism after failed login attempts
- **Responsive Design**: Mobile-friendly interface
- **Email Integration**: PHPMailer for notifications
- **File Upload Management**: Secure document and image handling
- **Database Synchronization**: Maintains data consistency

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (jQuery, Bootstrap 4)
- **Libraries**: PHPMailer, jQuery, Bootstrap, Font Awesome, DataTables
- **Server**: XAMPP / Apache + PHP + MySQL
- **Time Zone**: Asia/Manila

## 📦 Requirements

### Server Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB equivalent)
- **Apache**: 2.4+ (or Nginx)
- **Extensions**: mysqli, gd, mbstring, curl, zip

### Browser Support
- Chrome (latest)
- Firefox (latest)
- Edge (latest)
- Safari (latest)
- Mobile browsers

## 🚀 Installation Guide

### Step 1: Install XAMPP

1. Download and install [XAMPP](https://www.apachefriends.org/) for Windows
2. Ensure PHP and MySQL are installed and running
3. Start Apache and MySQL services from XAMPP Control Panel

### Step 2: Clone/Download Project

```bash
# Clone the repository or extract the project
cd C:\xampp\htdocs
# Extract or clone MotoEase project here
# Project should be accessible at: http://localhost/bpsms/
```

### Step 3: Database Setup

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `motoease` (or your preferred name)
3. Import the database file:
   - Import `motoease-5.2.sql` or `bpsms_db.sql`
   - This will create all required tables and initial data

Alternatively, run the SQL files in order:
```sql
-- Import database schema
SOURCE motoease-5.2.sql;

-- Additional updates (if needed)
SOURCE add_motorcycle_specifications.sql;
SOURCE add_abc_category_column.sql;
SOURCE add_address_column_to_client_list.sql;
```

### Step 4: Configure Database Connection

Edit `initialize.php` file:

```php
<?php
// Update these database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');  // Your MySQL password
define('DB_NAME', 'motoease');  // Your database name
define('DB_PORT', '3306');  // Usually 3306 for MySQL

// Update base URL (important!)
define('base_url', 'http://localhost/bpsms/');
?>
```

### Step 5: Configure Email Settings (Optional)

Edit `config.php` to configure email settings for notifications:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_SECURE', 'tls');
define('MAIL_FROM', 'noreply@motoease.com');
```

### Step 6: Set Permissions

Create necessary directories and set write permissions:

```bash
# On Windows, ensure these directories exist and are writable:
- uploads/
- uploads/products/
- uploads/brands/
- uploads/promos/
- uploads/customers/
- uploads/avatars/
```

### Step 7: Install Dependencies

Install PHP dependencies using Composer:

```bash
# Navigate to project directory
cd C:\xampp\htdocs\bpsms

# Install dependencies
composer install
```

If you don't have Composer installed:
- Download from: https://getcomposer.org/
- Or use the included `vendor/` folder if present

### Step 8: Access the Application

1. **Customer Interface**: `http://localhost/bpsms/`
2. **Admin Panel**: `http://localhost/bpsms/admin/`
3. **Default Admin Credentials**:
   - Username: `admin`
   - Password: `admin123` (Change immediately after first login)

### Step 9: Initial Configuration

1. Log in to the admin panel
2. Go to **System Settings** → **System Information**
3. Update:
   - Business name and address
   - Contact information
   - Email settings
   - Logo upload
   - Other site settings

## 📱 Usage

### Customer Portal

1. **Register an Account**: Click "Register" to create a new customer account
2. **Browse Products**: Explore motorcycles by category
3. **View Specifications**: Click on any motorcycle to see detailed specifications
4. **Add to Cart**: Add motorcycles to shopping cart
5. **Checkout**: Place orders through secure checkout process
6. **Track Orders**: View order status in "My Orders"
7. **Book Services**: Schedule service appointments
8. **Manage Account**: Update profile and view balance

### Admin Portal

1. **Dashboard**: View sales overview and statistics
2. **Manage Products**: Add, edit, or delete motorcycles
3. **Process Orders**: Review and process customer orders
4. **Manage Users**: Create and manage staff accounts
5. **View Reports**: Generate sales and inventory reports
6. **Configure Settings**: Update system configuration
7. **Manage Appointments**: Approve/reject service requests

## 📁 Project Structure

```
bpsms/
├── admin/              # Admin panel
├── assets/            # CSS, JS, images
├── classes/           # PHP classes
├── config.php         # Configuration
├── initialize.php     # Database configuration
├── uploads/           # Uploaded files
├── vendor/            # Composer dependencies
├── index.php          # Main entry point
├── home.php           # Home page
├── products.php       # Product listing
├── cart.php           # Shopping cart
├── services.php       # Service requests
└── README.md          # This file
```

## 🔧 Configuration

### Database Configuration
Edit `initialize.php` to set database credentials

### URL Configuration
Edit `initialize.php` to set correct `base_url`

### Email Configuration
Edit `config.php` for SMTP settings

### Timezone
Currently set to `Asia/Manila` in `config.php`

## 👤 User Roles

1. **Admin**: Full system access, user management, all reports
2. **Branch Supervisor**: Branch-specific operations, limited user management
3. **Branch Staff**: Sales and service operations, limited access
4. **Customer**: Order products, book services, view invoices

## 🐛 Troubleshooting

### Database Connection Error
- Check database credentials in `initialize.php`
- Verify MySQL service is running
- Ensure database exists

### Images Not Loading
- Check file permissions on `uploads/` directory
- Verify `base_url` is correct
- Check file paths

### 404 Errors
- Ensure Apache mod_rewrite is enabled
- Verify `.htaccess` file exists
- Check file permissions

### Email Not Sending
- Verify SMTP credentials in `config.php`
- Check firewall settings
- Use Gmail App Password if using Gmail

## 📚 Additional Documentation

- `INSTALLATION_GUIDE.md` - Detailed installation steps
- `USER_DEALERSHIP_CONTROL_MODULE.md` - User management features
- `MOTORCYCLE_SPECIFICATIONS_SUMMARY.md` - Product specifications
- `ADMIN_DASHBOARD_FIXES.md` - Dashboard features
- `PROMO_CUSTOMER_FEATURES_README.md` - Promo and customer images

## 🔐 Security Notes

1. **Change Default Passwords**: Update admin credentials immediately
2. **Secure Database**: Use strong database passwords
3. **File Permissions**: Set appropriate file permissions
4. **HTTPS**: Use SSL certificate in production
5. **Regular Updates**: Keep PHP and dependencies updated
6. **Backup**: Regular database backups recommended

## 📄 License

This project is proprietary software for internal use at Star Honda Calamba.

## 👥 Credits

**System**: MotoEase  
**Developed for**: Star Honda Calamba  
**Location**: Km. 50 National Highway, Parian, Calamba, Laguna

**Status**: Production Ready ✅
