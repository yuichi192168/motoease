# MotoEase :A Client-Focused Web-Based System for Motorcycle Sales and Service

MotoEase is a comprehensive web-based management system designed for Star Honda Calamba, providing end-to-end solutions for motorcycle dealership operations including sales, service management, customer accounts, and administration.

## ðŸš€ Quick Start

### Prerequisites
- PHP 7.4+ or PHP 8.0+
- MySQL 5.7+ or MariaDB 10.3+
- Apache 2.4+ (or Nginx)
- Composer 2.0+

### Installation

1. **Clone or extract the project:**
   ```bash
   git clone <repository-url> motoease
   cd motoease
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Configure database:**
   - Edit `initialize.php` with your database credentials
   - Create database: `motoease_final_db`
   - Import schema: `database/motoease_db.sql`

4. **Set permissions:**
   ```bash
   chmod -R 755 uploads/
   ```

5. **Access the system:**
   - Open: `http://localhost/motoease/`
   - Default login: `admin` / `admin123`

> **âš ï¸ Important:** Change the default password immediately after first login!

For detailed installation instructions, see [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md).

## ðŸ“‹ Features

### Core Features
- **Product & Inventory Management** - Complete product catalog with stock tracking
- **ABC Inventory Classification** - Automated inventory analysis and optimization
- **Advance Orders** - Order processing with status tracking
- **Service Management** - Service request handling with mechanic assignment
- **Customer Accounts** - Account balance tracking with installment plans
- **Invoice & Receipt Generation** - Automated PDF invoice/receipt creation
- **Service History** - Complete service tracking per customer
- **Role-Based Access Control** - Granular permissions system

### User Roles
1. **System Admin** - User and customer account management
2. **Desk/Staff** - Daily operations, orders, inventory, services
3. **Data Admin** - Promotional content and image management
4. **Inventory Admin** - Specialized inventory management
5. **Service Receptionist** - Service request handling
6. **Service Admin** - Extended service management with images
7. **Customer** - Public-facing customer portal

See [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md#user-roles-and-permissions) for detailed role permissions.

## ðŸ› ï¸ Technology Stack

- **Backend:** PHP 7.4+ / PHP 8.0+
- **Database:** MySQL/MariaDB
- **Frontend:** HTML5, CSS3, JavaScript (jQuery, Bootstrap)
- **Dependencies:** PHPMailer 6.5+
- **Server:** Apache 2.4+ (mod_rewrite required)

## ðŸ“ Project Structure

```
motoease/
â”œâ”€â”€ admin/                 # Admin panel
â”‚   â”œâ”€â”€ clients/          # Customer management
â”‚   â”œâ”€â”€ inventory/        # Inventory management
â”‚   â”œâ”€â”€ orders/           # Order management
â”‚   â”œâ”€â”€ service_requests/ # Service management
â”‚   â””â”€â”€ ...
â”œâ”€â”€ assets/               # CSS, JS, images
â”œâ”€â”€ classes/              # PHP classes
â”‚   â”œâ”€â”€ DBConnection.php
â”‚   â”œâ”€â”€ Master.php
â”‚   â”œâ”€â”€ Users.php
â”‚   â””â”€â”€ ...
â”œâ”€â”€ database/             # Database files
â”‚   â””â”€â”€ motoease_db.sql     # Main database schema
â”œâ”€â”€ migrations/           # Database migrations
â”œâ”€â”€ uploads/              # User uploaded files
â”œâ”€â”€ config.php           # Main configuration
â”œâ”€â”€ initialize.php       # Database & base config
â””â”€â”€ index.php            # Entry point
```

## ðŸ”§ Configuration

### Database Configuration
Edit `initialize.php`:
```php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'motoease_final_db');
define('DB_PORT', '3306');
```

### Base URL Configuration
```php
define('base_url', 'http://localhost/motoease/');
```

### Email Configuration
Edit `config.php` for SMTP settings:
```php
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your_email@example.com');
define('SMTP_PASS', 'your_password');
```

See [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md#environment-configuration) for complete configuration details.

## ðŸ“š Documentation

- **[Installation Guide](INSTALLATION_GUIDE.md)** - Complete setup instructions
- **Database Schema** - See `database/motoease_db.sql`
- **Code Documentation** - Inline comments in PHP files

## ðŸ” Security

### Default Credentials

This is the default username and password for each role:

**System Admin**
- Username: `euniel_system_admin`
- Password: `euniel`

**Desk/Staff**
- Username: `henry_desk_staff`
- Password: `henry`

**Data Admin**
- Username: `mark_data_admin`
- Password: `mark`

**Inventory Admin**
- Username: `joshua_inventory`
- Password: `joshua`

**Service Receptionist**
- Username: `karen_service`
- Password: `karen`

**âš ï¸ CHANGE THESE IMMEDIATELY AFTER INSTALLATION!**

### Security Features
- Password hashing (MD5 - consider upgrading to bcrypt)
- Session management
- SQL injection prevention (prepared statements)
- Role-based access control
- File upload validation

### Production Checklist
- [ ] Change default admin password
- [ ] Enable HTTPS/SSL
- [ ] Set strong database passwords
- [ ] Configure firewall rules
- [ ] Enable error logging
- [ ] Disable error display
- [ ] Set up regular backups

## ðŸ§ª Testing

### Test Scenarios
1. **User Authentication**
   - Login with different roles
   - Test permission restrictions

2. **Order Processing**
   - Create advance order
   - Update order status
   - Generate invoice

3. **Service Management**
   - Create service request
   - Assign mechanic
   - Complete service

4. **Inventory Management**
   - Add stock
   - Update stock levels
   - Run ABC analysis

5. **Account Management**
   - Create customer account
   - Record payments
   - Verify balance calculations

## ðŸ› Troubleshooting

### Common Issues

**Database Connection Error:**
- Verify MySQL is running
- Check credentials in `initialize.php`
- Ensure database exists

**404 Errors:**
- Enable Apache `mod_rewrite`
- Check `.htaccess` file exists
- Verify `base_url` configuration

**Permission Errors:**
- Set `uploads/` folder permissions: `chmod -R 755 uploads/`
- Check file ownership

**Email Not Sending:**
- Verify SMTP credentials
- Check firewall allows SMTP port
- Test with Mailtrap for development

See [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md#troubleshooting) for detailed troubleshooting.

## ðŸ“Š Key System Features

### 1. Advance Orders
Customers can place advance orders for products. Orders are tracked through multiple statuses: Pending â†’ Packed â†’ For Delivery â†’ On the Way â†’ Delivered.

### 2. Invoice & Receipt Generation
Automated PDF invoice generation with order details, customer information, and payment terms. Receipts generated upon payment recording.

### 3. Service History
Complete tracking of all services performed per customer, including service type, mechanic, dates, and costs.

### 4. Account Balance Tracking
Advanced account management with installment plan support, payment tracking, and automatic balance calculations.

### 5. ABC Inventory Classification
Automated inventory analysis classifying products into A (high-value), B (medium-value), and C (low-value) categories for optimization.

## ðŸ‘¥ User Roles & Permissions

| Role | Key Permissions |
|------|----------------|
| **System Admin** | User management, customer accounts, invoices |
| **Desk/Staff** | Orders, inventory, services, reports |
| **Data Admin** | Promo images, customer images, content |
| **Inventory Admin** | Products, stock management, ABC analysis |
| **Service Receptionist** | Service requests, mechanics |
| **Service Admin** | Services, promo/customer images |
| **Customer** | Orders, services, account balance (view only) |

See [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md#user-roles-and-permissions) for complete permission matrix.

## ðŸ”„ Database Schema

### Main Tables
- `users` - System users and administrators
- `client_list` - Customer accounts
- `product_list` - Product catalog
- `order_list` - Customer orders
- `order_items` - Order line items
- `stock_list` - Inventory movements
- `service_requests` - Service requests
- `customer_account_balances` - Account balances
- `customer_transactions` - Payment records
- `invoices` - Generated invoices

See `database/motoease_db.sql` for complete schema.

## ðŸ“ License

This project is developed for academic/capstone purposes. Please refer to your institution's guidelines for usage and distribution.

## ðŸ¤ Contributing

This is a capstone project. For contributions or modifications, please follow:
1. Code style guidelines (PSR-12 recommended)
2. Database migration procedures
3. Testing requirements
4. Documentation standards

## ðŸ“ž Support

For installation and setup issues, refer to:
- [Installation Guide](INSTALLATION_GUIDE.md)
- Database schema: `database/motoease_db.sql`
- Code comments in PHP files

## ðŸ—ºï¸ Roadmap

Potential future enhancements:
- [ ] Multi-branch support expansion
- [ ] Mobile app integration
- [ ] Advanced reporting and analytics
- [ ] Payment gateway integration
- [ ] SMS notifications
- [ ] Barcode scanning
- [ ] Advanced inventory forecasting

## ðŸ“„ Version History

- **v1.0** (2025) - Initial release
  - Core features implemented
  - Role-based access control
  - ABC inventory classification
  - Account balance tracking

---

**System Name:** Star Honda Motorcycle Service Management System 
**Version:** 1.0  
**Last Updated:** 2025

For detailed installation and setup instructions, please refer to [INSTALLATION_GUIDE.md](INSTALLATION_GUIDE.md).



