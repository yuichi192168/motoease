# System Installation and Setup Guide

## Motorcycle Parts & Services Shop Management System (MPSSMS)

**Version:** 1.0  
**Last Updated:** 2025  
**Documentation Type:** Installation and Setup Guide

---

## Table of Contents

1. [System Overview](#system-overview)
2. [System Requirements](#system-requirements)
3. [Local Installation](#local-installation)
4. [Server Installation](#server-installation)
5. [Database Setup](#database-setup)
6. [Environment Configuration](#environment-configuration)
7. [User Roles and Permissions](#user-roles-and-permissions)
8. [Key System Features](#key-system-features)
9. [Troubleshooting](#troubleshooting)
10. [Post-Installation Checklist](#post-installation-checklist)

---

## System Overview

The Motorcycle Parts & Services Shop Management System (MPSSMS) is a comprehensive web-based management solution designed for motorcycle parts and service shops. The system facilitates:

- **Product and Inventory Management** with ABC classification
- **Order Processing** including advance orders and installment plans
- **Service Request Management** with mechanic assignment
- **Customer Account Management** with balance tracking
- **Invoice and Receipt Generation**
- **Service History Tracking**
- **Role-Based Access Control** for multiple user types

**Technology Stack:**
- **Backend:** PHP 7.4+ / PHP 8.0+
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (jQuery, Bootstrap)
- **Dependencies:** PHPMailer 6.5+

---

## System Requirements

### Minimum Requirements

#### For Local Development (XAMPP/WAMP/MAMP)
- **Operating System:** Windows 7+, macOS 10.12+, or Linux
- **Web Server:** Apache 2.4+ (included in XAMPP/WAMP/MAMP)
- **PHP Version:** PHP 7.4 or PHP 8.0+
- **Database:** MySQL 5.7+ or MariaDB 10.3+
- **PHP Extensions Required:**
  - `mysqli` (MySQL Improved Extension)
  - `mbstring` (Multibyte String Support)
  - `gd` (Image Processing)
  - `openssl` (SSL Support for email)
- **Memory:** Minimum 512MB RAM (1GB recommended)
- **Disk Space:** Minimum 100MB free space

#### For Production Server
- **Web Server:** Apache 2.4+ or Nginx 1.18+
- **PHP Version:** PHP 7.4 or PHP 8.0+
- **Database:** MySQL 5.7+ or MariaDB 10.3+
- **PHP Extensions:** Same as local requirements
- **SSL Certificate:** Recommended for production
- **Email Server:** SMTP server for email notifications (optional)

### Recommended Software Versions

| Software | Minimum Version | Recommended Version |
|----------|----------------|---------------------|
| PHP | 7.4 | 8.0+ |
| MySQL/MariaDB | 5.7 / 10.3 | 8.0 / 10.6+ |
| Apache | 2.4 | 2.4.50+ |
| Composer | 2.0 | Latest |

---

## Local Installation

### Step 1: Install XAMPP/WAMP/MAMP

#### For Windows (XAMPP):
1. Download XAMPP from https://www.apachefriends.org/
2. Run the installer and select:
   - Apache
   - MySQL
   - PHP
   - phpMyAdmin
3. Install to `C:\xampp\` (default location)
4. Start Apache and MySQL from the XAMPP Control Panel

#### For macOS (MAMP):
1. Download MAMP from https://www.mamp.info/
2. Install and start Apache and MySQL servers
3. Default web root: `/Applications/MAMP/htdocs/`

#### For Linux:
```bash
sudo apt-get update
sudo apt-get install apache2 mysql-server php php-mysqli php-mbstring php-gd
```

### Step 2: Clone or Extract Project Files

1. **If using Git:**
   ```bash
   git clone <repository-url> motoease
   cd motoease
   ```

2. **If using ZIP file:**
   - Extract the ZIP file to your web server root:
     - **XAMPP:** `C:\xampp\htdocs\motoease\`
     - **WAMP:** `C:\wamp64\www\motoease\`
     - **MAMP:** `/Applications/MAMP/htdocs/motoease/`
     - **Linux:** `/var/www/html/motoease/`

### Step 3: Install PHP Dependencies

1. Open terminal/command prompt in the project directory
2. Install Composer dependencies:
   ```bash
   composer install
   ```
   
   If Composer is not installed, download it from https://getcomposer.org/

### Step 4: Configure Database Connection

1. Open `initialize.php` in a text editor
2. Update the database configuration:
   ```php
   if(!defined('DB_SERVER')) define('DB_SERVER',"localhost");
   if(!defined('DB_USERNAME')) define('DB_USERNAME',"root");
   if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"");
   if(!defined('DB_NAME')) define('DB_NAME',"motoease_final_db");
   if(!defined('DB_PORT')) define('DB_PORT',"3306"); // or 3307 for XAMPP
   ```

3. Update the base URL:
   ```php
   if(!defined('base_url')) define('base_url','http://localhost/motoease/');
   ```

### Step 5: Create Database

See [Database Setup](#database-setup) section below.

### Step 6: Set File Permissions

#### For Windows:
- Ensure the `uploads/` folder exists and is writable
- Right-click `uploads/` folder → Properties → Security → Allow "Modify" for your user

#### For Linux/macOS:
```bash
chmod -R 755 uploads/
chmod -R 755 admin/uploads/
```

### Step 7: Access the Application

1. Open your web browser
2. Navigate to: `http://localhost/motoease/`
3. You should see the login page

**Default Admin Credentials:**
- **Username:** `admin`
- **Password:** `admin123`

> **⚠️ Security Note:** Change the default admin password immediately after first login!

---

## Server Installation

### Step 1: Prepare Server Environment

1. **SSH into your server:**
   ```bash
   ssh user@your-server-ip
   ```

2. **Install required software:**
   ```bash
   # For Ubuntu/Debian
   sudo apt-get update
   sudo apt-get install apache2 mysql-server php php-mysqli php-mbstring php-gd php-xml php-curl
   
   # For CentOS/RHEL
   sudo yum install httpd mysql-server php php-mysqli php-mbstring php-gd
   ```

### Step 2: Upload Project Files

**Option A: Using Git (Recommended)**
```bash
cd /var/www/html
git clone <repository-url> motoease
cd motoease
composer install
```

**Option B: Using FTP/SFTP**
1. Upload all project files to `/var/www/html/motoease/` or your web root
2. Ensure file ownership is correct:
   ```bash
   sudo chown -R www-data:www-data /var/www/html/motoease
   ```

### Step 3: Configure Apache Virtual Host

1. Create virtual host configuration:
   ```bash
   sudo nano /etc/apache2/sites-available/motoease.conf
   ```

2. Add the following configuration:
   ```apache
   <VirtualHost *:80>
       ServerName yourdomain.com
       ServerAlias www.yourdomain.com
       DocumentRoot /var/www/html/motoease
       
       <Directory /var/www/html/motoease>
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/motoease_error.log
       CustomLog ${APACHE_LOG_DIR}/motoease_access.log combined
   </VirtualHost>
   ```

3. Enable the site and restart Apache:
   ```bash
   sudo a2ensite motoease.conf
   sudo a2enmod rewrite
   sudo systemctl restart apache2
   ```

### Step 4: Configure Database

1. Create MySQL database and user:
   ```sql
   CREATE DATABASE motoease_final_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   CREATE USER 'motoease_user'@'localhost' IDENTIFIED BY 'strong_password_here';
   GRANT ALL PRIVILEGES ON motoease_final_db.* TO 'motoease_user'@'localhost';
   FLUSH PRIVILEGES;
   ```

2. Update `initialize.php` with production database credentials

3. Import database schema (see Database Setup section)

### Step 5: Configure SSL (Recommended)

1. Install Certbot:
   ```bash
   sudo apt-get install certbot python3-certbot-apache
   ```

2. Obtain SSL certificate:
   ```bash
   sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
   ```

### Step 6: Set Production Environment Variables

Update `initialize.php` with production settings:
```php
if(!defined('base_url')) define('base_url','https://yourdomain.com/');
```

---

## Database Setup

### Step 1: Create Database

1. **Using phpMyAdmin:**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Click "New" to create a database
   - Name: `motoease_final_db`
   - Collation: `utf8mb4_general_ci`
   - Click "Create"

2. **Using MySQL Command Line:**
   ```sql
   CREATE DATABASE motoease_final_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
   ```

### Step 2: Import Database Schema

1. **Using phpMyAdmin:**
   - Select the `motoease_final_db` database
   - Click "Import" tab
   - Choose file: `database/motoease_db.sql`
   - Click "Go"

2. **Using MySQL Command Line:**
   ```bash
   mysql -u root -p motoease_final_db < database/motoease_db.sql
   ```

### Step 3: Run Additional Migrations (If Any)

If there are migration files in the `migrations/` folder:

```bash
# For each migration file
mysql -u root -p motoease_final_db < migrations/20251128_add_payment_date_to_transactions.sql
```

### Step 4: Verify Database Tables

The following tables should be created:

- `users` - System users and administrators
- `client_list` - Customer accounts
- `product_list` - Product catalog
- `categories` - Product categories
- `brand_list` - Product brands
- `order_list` - Customer orders
- `order_items` - Order line items
- `stock_list` - Inventory stock movements
- `service_list` - Available services
- `service_requests` - Service requests from customers
- `mechanics_list` - Mechanic information
- `customer_account_balances` - Customer account balances
- `customer_transactions` - Payment transactions
- `invoices` - Generated invoices
- `system_info` - System configuration
- `or_cr_documents` - OR/CR document management
- `branches` - Branch locations

### Step 5: Seed Sample Data (Optional)

Sample data is included in the SQL dump. To add additional test data:

1. Create test users, products, and orders through the admin interface
2. Or manually insert data using SQL scripts

---

## Environment Configuration

### Configuration File: `initialize.php`

The main configuration file is located at the root: `initialize.php`

#### Database Configuration

```php
if(!defined('DB_SERVER')) define('DB_SERVER',"localhost");
if(!defined('DB_USERNAME')) define('DB_USERNAME',"root");
if(!defined('DB_PASSWORD')) define('DB_PASSWORD',"");
if(!defined('DB_NAME')) define('DB_NAME',"motoease_final_db");
if(!defined('DB_PORT')) define('DB_PORT',"3306");
```

**Configuration Parameters:**
- `DB_SERVER`: Database host (usually `localhost` or IP address)
- `DB_USERNAME`: MySQL username
- `DB_PASSWORD`: MySQL password
- `DB_NAME`: Database name
- `DB_PORT`: MySQL port (default: 3306, XAMPP: 3307)

#### Base URL Configuration

```php
if(!defined('base_url')) define('base_url','http://localhost/motoease/');
```

**For Production:**
```php
if(!defined('base_url')) define('base_url','https://yourdomain.com/');
```

#### Email Configuration (SMTP)

Email settings are configured in `config.php`:

```php
// SMTP Configuration
define('SMTP_HOST', 'smtp.mailtrap.io');      // SMTP server
define('SMTP_PORT', 587);                      // SMTP port (587 for TLS, 465 for SSL)
define('SMTP_USER', 'your_smtp_username');     // SMTP username
define('SMTP_PASS', 'your_smtp_password');     // SMTP password
define('SMTP_SECURE', 'tls');                  // 'tls' or 'ssl'
define('MAIL_FROM', 'no-reply@yourdomain.com'); // From email address
define('MAIL_FROM_NAME', 'MotoEase System');    // From name
```

**Popular SMTP Providers:**
- **Gmail:** `smtp.gmail.com`, Port: 587, TLS
- **SendGrid:** `smtp.sendgrid.net`, Port: 587, TLS
- **Mailtrap:** `smtp.mailtrap.io`, Port: 587, TLS (for testing)

#### Timezone Configuration

```php
ini_set('date.timezone','Asia/Manila');
date_default_timezone_set('Asia/Manila');
```

Change to your local timezone as needed.

### Environment Variables (Alternative Method)

You can also use environment variables by modifying `config.php`:

```php
if(!defined('DB_SERVER')) define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
if(!defined('DB_USERNAME')) define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
if(!defined('DB_PASSWORD')) define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
if(!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'motoease_final_db');
```

Then set environment variables in your server configuration.

---

## User Roles and Permissions

The system implements a comprehensive Role-Based Access Control (RBAC) system with the following user roles:

### 1. System Admin (`system_admin`)

**Purpose:** Manages system users, customer accounts, and administrative functions.

**Access Permissions:**
- ✅ **User Management:** Create, edit, delete system users
- ✅ **Customer Management:** View and manage customer accounts
- ✅ **Customer Account Balances:** View and manage customer account balances
- ✅ **Invoice Management:** View and generate invoices
- ✅ **OR/CR Documents:** Manage OR/CR document tracking
- ❌ **Inventory Management:** No access
- ❌ **Service Management:** No access
- ❌ **Order Management:** No access
- ❌ **Reports:** No access

**Use Cases:**
- Managing staff accounts
- Handling customer account issues
- Processing invoices and payments
- Document management

**Default Permissions:**
- `user_management`
- `customer_management`
- `customer_accounts`
- `invoice_management`
- `orcr_documents`

---

### 2. Desk/Staff (`desk_staff`)

**Purpose:** Handles daily operations including orders, inventory, services, and reports.

**Access Permissions:**
- ✅ **Product Management:** View and manage products
- ✅ **Inventory Management:** Stock management and ABC analysis
- ✅ **Service Requests:** Manage service requests and mechanics
- ✅ **Order Management:** Process and manage customer orders
- ✅ **Reports:** Access to order, service, and invoice reports
- ✅ **System Settings:** Basic system configuration
- ✅ **Categories & Services:** Manage product categories and services
- ✅ **OR/CR Documents:** Manage OR/CR documents
- ❌ **User Management:** No access
- ❌ **Customer Management:** Limited access (view only)

**Use Cases:**
- Processing daily orders
- Managing inventory and stock levels
- Handling service requests
- Generating reports
- System maintenance

**Default Permissions:**
- `product_management`
- `inventory_management`
- `stock_management`
- `service_requests`
- `mechanics`
- `order_management`
- `reports`
- `system_settings`
- `orcr_documents`

---

### 3. Data Admin (`data_admin`)

**Purpose:** Manages promotional content, customer images, and marketing materials.

**Access Permissions:**
- ✅ **Promo Images:** Upload and manage promotional images
- ✅ **Customer Images:** Manage customer gallery images
- ✅ **Reviews Management:** Manage customer reviews (if enabled)
- ✅ **Announcements:** Manage system announcements (if enabled)
- ✅ **Content Management:** Manage website content (if enabled)
- ❌ **All Other Modules:** No access

**Use Cases:**
- Managing promotional campaigns
- Uploading customer showcase images
- Content marketing

**Default Permissions:**
- `promo_images`
- `customer_images`
- `reviews_management` (if enabled)
- `announcements_management` (if enabled)
- `content_management` (if enabled)

---

### 4. Inventory Admin (`inventory`)

**Purpose:** Specialized role for inventory and product management.

**Access Permissions:**
- ✅ **Product Management:** Full product catalog management
- ✅ **Stock Management:** Add, edit, and track stock movements
- ✅ **ABC Analysis:** Access ABC inventory classification
- ❌ **All Other Modules:** No access

**Use Cases:**
- Product catalog maintenance
- Stock level monitoring
- ABC inventory analysis
- Inventory optimization

**Default Permissions:**
- `product_management`
- `stock_management`
- `inventory_management`

---

### 5. Service Receptionist (`service_receptionist`)

**Purpose:** Handles service requests and customer service interactions.

**Access Permissions:**
- ✅ **Service Requests:** Create, view, and update service requests
- ✅ **Mechanics:** View mechanic list (read-only)
- ❌ **Image Management:** No access (hard-blocked)
- ❌ **All Other Modules:** No access

**Use Cases:**
- Receiving service requests from customers
- Assigning mechanics to service requests
- Updating service request status
- Customer service interactions

**Default Permissions:**
- `service_requests`
- `mechanics` (read-only)

**Important:** Service Receptionist role is **hard-blocked** from accessing promo images and customer images, even if permissions are misconfigured.

---

### 6. Service Admin (`service_admin`)

**Purpose:** Extended service management with image management capabilities.

**Access Permissions:**
- ✅ **Service Requests:** Full service request management
- ✅ **Promo Images:** Manage promotional images
- ✅ **Customer Images:** Manage customer gallery images
- ✅ **Mechanics:** Manage mechanic information
- ❌ **All Other Modules:** No access

**Use Cases:**
- Complete service management
- Marketing through images
- Service-related content management

**Default Permissions:**
- `service_requests`
- `promo_images`
- `customer_images`
- `mechanics`

---

### 7. Customer (`customer` / `client_list`)

**Purpose:** End-user role for customers accessing the public-facing website.

**Access Permissions:**
- ✅ **View Products:** Browse product catalog
- ✅ **Shopping Cart:** Add products to cart
- ✅ **Place Orders:** Create advance orders
- ✅ **View Orders:** View order history and status
- ✅ **Service Requests:** Request services
- ✅ **View Services:** View service history
- ✅ **Account Balance:** View account balance and payment history
- ✅ **Invoices:** View and download invoices
- ✅ **Profile Management:** Update personal information
- ❌ **Admin Panel:** No access

**Use Cases:**
- Shopping for motorcycle parts
- Requesting services
- Tracking orders and payments
- Managing account information

---

### 8. Super Admin (`admin`)

**Purpose:** Full system access with all permissions.

**Access Permissions:**
- ✅ **All Modules:** Complete access to all system features
- ✅ **System Settings:** Full system configuration
- ✅ **User Management:** Create and manage all user roles
- ✅ **Permission Management:** Assign custom permissions

**Note:** The `admin` role bypasses all permission checks and has access to everything.

---

### Permission System

The system uses a granular permission-based access control system. Each role can be assigned specific permissions:

**Available Permission Keys:**
- `user_management` - Manage system users
- `customer_management` - Manage customers
- `product_management` - Manage products
- `inventory_management` - Full inventory access
- `stock_management` - Stock operations
- `service_requests` - Service request management
- `mechanics` - Mechanic management
- `order_management` - Order processing
- `invoice_management` - Invoice generation
- `customer_accounts` - Account balance management
- `reports` - Report access
- `system_settings` - System configuration
- `promo_images` - Promotional image management
- `customer_images` - Customer image management
- `orcr_documents` - OR/CR document management

**Assigning Permissions:**
1. Navigate to: **Admin Panel → User Management → System Users**
2. Edit a user
3. Select permissions from the permission list
4. Save changes

---

## Key System Features

### 1. Advance Orders

**Description:** Allows customers to place orders for products that may not be immediately available or to secure items for future delivery.

**Features:**
- Customers can place advance orders directly from the cart
- No credit application required for parts, accessories, or oils
- Order status tracking (Pending, Packed, For Delivery, On the Way, Delivered, Cancelled)
- Automatic stock reservation when order is placed
- Email notifications for order status changes

**How It Works:**
1. Customer adds products to cart
2. Proceeds to checkout
3. Selects "Advance Order" option
4. System creates order with status "Pending"
5. Admin processes order and updates status
6. Customer receives notifications at each stage

**Access:**
- **Customers:** Can place advance orders from cart
- **Desk/Staff:** Can process and manage advance orders
- **System Admin:** Can view orders (limited)

**Related Tables:**
- `order_list` - Main order records
- `order_items` - Order line items
- `stock_list` - Stock reservations

---

### 2. Invoice and Receipt Generation

**Description:** Automated invoice and receipt generation for orders and services.

**Features:**
- Automatic invoice number generation
- PDF invoice/receipt download
- Print-friendly format
- Invoice includes:
  - Order details and items
  - Customer information
  - Payment terms
  - Tax calculations (if applicable)
  - Payment history
- Receipt generation upon payment
- Email delivery of invoices

**Invoice Types:**
1. **Order Invoices:** Generated when order is placed
2. **Service Invoices:** Generated for service requests
3. **Payment Receipts:** Generated when payments are recorded

**How It Works:**
1. Order/Service is created
2. System automatically generates invoice
3. Invoice is stored in database
4. Customer can view/download from "My Invoices"
5. Admin can generate receipts for payments

**Access:**
- **Customers:** View and download their invoices
- **Desk/Staff:** Generate and manage all invoices
- **System Admin:** Full invoice management

**Related Tables:**
- `invoices` - Invoice records
- `order_list` - Linked orders
- `service_requests` - Linked services
- `customer_transactions` - Payment records

---

### 3. Service History

**Description:** Comprehensive tracking of all services performed for each customer.

**Features:**
- Complete service request history per customer
- Service details including:
  - Service type and description
  - Mechanic assigned
  - Service date and completion date
  - Service status
  - Vehicle information
  - Service cost
- Filterable and searchable history
- Export capabilities
- Linked to customer account

**How It Works:**
1. Customer requests a service
2. Service request is created in system
3. Mechanic is assigned
4. Service is completed
5. History is automatically recorded
6. Customer can view history in "My Services"

**Access:**
- **Customers:** View their own service history
- **Desk/Staff:** View all service histories
- **Service Receptionist:** View and manage service requests

**Related Tables:**
- `service_requests` - Service request records
- `request_meta` - Additional service metadata
- `mechanics_list` - Assigned mechanics
- `service_list` - Service types

---

### 4. Account Balance Tracking

**Description:** Advanced customer account balance management with installment plan support.

**Features:**
- Account creation for orders with installment plans
- Real-time balance calculation
- Payment tracking and history
- Late fee calculation (if configured)
- Payment reminders
- Account status monitoring (Active, Paid, Overdue, Closed)
- Multiple payment methods support

**Account Balance Formula:**
```
Total Cost = Downpayment + (Monthly Payment × Installment Months)
Paid Amount = Sum of all recorded transactions
Remaining Balance = Total Cost - Paid Amount
```

**How It Works:**
1. Customer places order with installment plan
2. System creates account record
3. Initial balance = total cost
4. Payments are recorded as transactions
5. Balance is automatically updated
6. System tracks payment schedule
7. Alerts for overdue accounts

**Payment Types:**
- `payment` - Regular payment
- `refund` - Refund transaction
- `adjustment` - Manual balance adjustment
- `order_payment` - Payment linked to specific order

**Access:**
- **Customers:** View their account balance and payment history
- **System Admin:** Full account management
- **Desk/Staff:** View accounts and record payments

**Related Tables:**
- `customer_account_balances` - Account records
- `customer_transactions` - Payment transactions
- `order_list` - Linked orders
- `client_list` - Customer information

---

### 5. Inventory Management with ABC Classification

**Description:** Advanced inventory management system with ABC analysis for inventory optimization.

**Features:**
- Real-time stock level tracking
- Stock in/out movements
- Reserved stock for pending orders
- Available stock calculation
- Low stock alerts
- ABC inventory classification:
  - **Category A:** High-value, low-quantity items (80% of value, 20% of items)
  - **Category B:** Medium-value items (15% of value, 30% of items)
  - **Category C:** Low-value, high-quantity items (5% of value, 50% of items)
- Automatic ABC classification based on:
  - Sales volume
  - Inventory value
  - Movement frequency

**ABC Analysis Benefits:**
- Optimize inventory investment
- Focus on high-value items
- Reduce carrying costs
- Improve cash flow

**How It Works:**
1. Products are added to inventory
2. Stock movements are recorded (in/out)
3. System calculates current stock levels
4. ABC analysis is performed periodically
5. Products are classified into A, B, or C categories
6. Reports generated for inventory optimization

**Stock Calculation:**
```
Current Stock = Total Stock In - Total Stock Out
Reserved Stock = Sum of quantities in pending orders
Available Stock = Current Stock - Reserved Stock
```

**Access:**
- **Inventory Admin:** Full inventory management
- **Desk/Staff:** Inventory management and ABC analysis
- **Customers:** View product availability (public)

**Related Tables:**
- `product_list` - Product catalog
- `stock_list` - Stock movement records
- `order_items` - Reserved stock calculation
- `stock_movements` - Detailed movement history (if enabled)

---

## Troubleshooting

### Common Installation Issues

#### 1. Database Connection Error

**Error Message:**
```
Warning: mysqli_connect(): (HY000/2002): No connection could be made
```

**Solutions:**
- Verify MySQL service is running (XAMPP Control Panel)
- Check database credentials in `initialize.php`
- Ensure database exists: `motoease_final_db`
- Check MySQL port (3306 or 3307 for XAMPP)
- Verify MySQL user has proper permissions

#### 2. Page Not Found (404 Error)

**Error Message:**
```
404 - Page Not Found
```

**Solutions:**
- Verify `.htaccess` file exists in root directory
- Enable Apache `mod_rewrite`:
  ```bash
  sudo a2enmod rewrite
  sudo systemctl restart apache2
  ```
- Check `base_url` in `initialize.php` matches your installation path
- Verify file permissions

#### 3. Permission Denied Errors

**Error Message:**
```
Permission denied: /var/www/html/motoease/uploads/
```

**Solutions:**
- Set proper file permissions:
  ```bash
  chmod -R 755 uploads/
  chmod -R 755 admin/uploads/
  ```
- For Linux, change ownership:
  ```bash
  sudo chown -R www-data:www-data uploads/
  ```

#### 4. PHP Extension Missing

**Error Message:**
```
Call to undefined function mysqli_connect()
```

**Solutions:**
- Enable PHP extensions in `php.ini`:
  ```ini
  extension=mysqli
  extension=mbstring
  extension=gd
  ```
- Restart Apache after changes
- Verify extensions are loaded:
  ```bash
  php -m | grep mysqli
  ```

#### 5. Composer Dependency Issues

**Error Message:**
```
Composer dependencies not found
```

**Solutions:**
- Install Composer: https://getcomposer.org/
- Run: `composer install`
- If vendor folder missing, create it:
  ```bash
  mkdir vendor
  composer install
  ```

#### 6. Session Errors

**Error Message:**
```
Warning: session_start(): Failed to initialize storage module
```

**Solutions:**
- Check PHP session directory is writable
- Set session save path in `php.ini`:
  ```ini
  session.save_path = "/tmp"
  ```
- Verify session directory permissions

#### 7. Image Upload Issues

**Error Message:**
```
Failed to upload image
```

**Solutions:**
- Check `uploads/` folder exists and is writable
- Verify PHP upload settings in `php.ini`:
  ```ini
  upload_max_filesize = 10M
  post_max_size = 10M
  ```
- Check file permissions on uploads directory

#### 8. Email Not Sending

**Error Message:**
```
SMTP Error: Could not authenticate
```

**Solutions:**
- Verify SMTP credentials in `config.php`
- Check SMTP server allows connections
- Test with a tool like Mailtrap for development
- Verify firewall allows SMTP port (587/465)

### Performance Issues

#### Slow Page Loads

**Solutions:**
- Enable PHP OPcache
- Optimize database queries
- Use caching mechanisms
- Optimize images
- Enable GZIP compression

#### Database Performance

**Solutions:**
- Add indexes to frequently queried columns
- Optimize slow queries
- Regular database maintenance
- Consider database connection pooling

### Security Issues

#### SQL Injection Prevention

**Already Implemented:**
- Prepared statements in critical functions
- Input sanitization
- Parameter binding

**Additional Recommendations:**
- Regular security audits
- Keep PHP and MySQL updated
- Use strong passwords
- Enable HTTPS in production

---

## Post-Installation Checklist

### Immediate Tasks

- [ ] Change default admin password
- [ ] Verify database connection
- [ ] Test file uploads (images)
- [ ] Configure SMTP email settings
- [ ] Set correct timezone
- [ ] Verify all user roles can log in
- [ ] Test order placement
- [ ] Test service request creation
- [ ] Verify invoice generation
- [ ] Check account balance calculations

### Configuration Tasks

- [ ] Update system name and logo (System Settings)
- [ ] Configure payment methods
- [ ] Set up product categories
- [ ] Add initial product catalog
- [ ] Configure service types
- [ ] Set up mechanics list
- [ ] Configure branch locations (if multi-branch)
- [ ] Set inventory alert thresholds
- [ ] Configure ABC analysis parameters

### User Management

- [ ] Create user accounts for all staff
- [ ] Assign appropriate roles
- [ ] Set user permissions
- [ ] Test role-based access control
- [ ] Document user credentials securely

### Testing

- [ ] Test customer registration
- [ ] Test order placement (advance order)
- [ ] Test service request workflow
- [ ] Test invoice generation
- [ ] Test payment recording
- [ ] Test account balance updates
- [ ] Test inventory stock movements
- [ ] Test ABC analysis generation
- [ ] Test report generation
- [ ] Test email notifications

### Documentation

- [ ] Document custom configurations
- [ ] Create user training materials
- [ ] Document business processes
- [ ] Create backup procedures
- [ ] Document support contacts

### Security Hardening

- [ ] Enable HTTPS (production)
- [ ] Set strong database passwords
- [ ] Restrict file permissions
- [ ] Configure firewall rules
- [ ] Set up regular backups
- [ ] Enable error logging
- [ ] Disable error display in production

### Backup Setup

- [ ] Configure automated database backups
- [ ] Set up file backup procedures
- [ ] Test backup restoration
- [ ] Document backup schedule
- [ ] Set backup retention policy

---

## Additional Resources

### Support and Documentation

- **System Documentation:** See README.md for additional information
- **Database Schema:** Refer to `database/motoease_db.sql` for table structures
- **Code Documentation:** Check inline comments in PHP files

### Useful Commands

**Database Backup:**
```bash
mysqldump -u root -p motoease_final_db > backup_$(date +%Y%m%d).sql
```

**Database Restore:**
```bash
mysql -u root -p motoease_final_db < backup_20250101.sql
```

**Check PHP Version:**
```bash
php -v
```

**Check MySQL Version:**
```bash
mysql --version
```

**Test Database Connection:**
```php
<?php
$conn = new mysqli('localhost', 'root', '', 'motoease_final_db', 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>
```

---

## Conclusion

This installation guide provides comprehensive instructions for setting up the Motorcycle Parts & Services Shop Management System. For additional support or questions, please refer to the system documentation or contact your system administrator.

**System Version:** 1.0  
**Last Updated:** 2025  
**Documentation Status:** Complete

---

*End of Installation Guide*

