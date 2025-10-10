# Product Recommendations and Availability Notifications Implementation

## Overview
This implementation adds comprehensive product recommendation and notification features to the BPSMS system, allowing customers to receive alternative product suggestions when items are out of stock and get notified when products become available again.

## Features Implemented

### 1. Product Recommendations System
- **Alternative Product Suggestions**: When a product is out of stock, customers can see alternative options
- **Multiple Recommendation Types**: 
  - Alternative (similar functionality)
  - Similar (same category/brand)
  - Upgrade (higher-end options)
  - Cross-sell (complementary products)
- **Priority-based Ordering**: Recommendations are shown in priority order
- **Stock-aware Recommendations**: Only shows available alternatives

### 2. Product Availability Notifications
- **Notification Subscription**: Customers can subscribe to be notified when out-of-stock products become available
- **Automatic Notifications**: System automatically sends notifications when stock is updated
- **User-friendly Interface**: Simple "Notify When Available" buttons for out-of-stock products
- **Login Integration**: Requires user login to set up notifications

### 3. Enhanced User Interface
- **Out-of-Stock Product Display**: 
  - "Notify When Available" button
  - "See Alternatives" button
  - Visual feedback when notification is set
- **Recommendation Modal**: Beautiful modal showing alternative products with:
  - Product images
  - Prices
  - Stock status
  - Direct links to view/add to cart

### 4. Admin Management Interface
- **Recommendation Management**: Admin can add, edit, and delete product recommendations
- **Bulk Management**: Easy interface to manage all recommendations
- **Product Selection**: Dropdown menus for easy product selection
- **Priority Control**: Set recommendation priority levels

## Database Changes

### New Tables Created:

1. **`product_recommendations`**:
   - Links products to their recommended alternatives
   - Supports different recommendation types
   - Priority-based ordering
   - Active/inactive status

2. **`product_availability_notifications`**:
   - Tracks user notification subscriptions
   - Prevents duplicate notifications
   - Tracks notification status

## Files Modified/Created

### Core System Files:
- **`classes/Master.php`**: Added recommendation and notification functions
- **`products/index.php`**: Enhanced product display with recommendation buttons
- **`products/view_product.php`**: Added notification and recommendation features

### Database Setup:
- **`create_product_recommendations.sql`**: Database schema for new tables
- **`setup_product_recommendations.php`**: Setup script with sample data

### Admin Interface:
- **`admin/product_recommendations.php`**: Admin management interface

## API Endpoints Added

### Customer-facing:
- `get_product_recommendations`: Get alternative products for a given product
- `request_product_notification`: Subscribe to availability notifications
- `cancel_product_notification`: Unsubscribe from notifications

### Admin:
- `save_product_recommendation`: Add/edit recommendations
- `get_all_recommendations`: List all recommendations
- `get_recommendation`: Get specific recommendation details
- `delete_product_recommendation`: Remove recommendations

## How It Works

### For Customers:
1. **Viewing Out-of-Stock Products**: 
   - Products show "Notify When Available" and "See Alternatives" buttons
   - Clicking "Notify When Available" subscribes them to notifications
   - Clicking "See Alternatives" shows recommended products in a modal

2. **Receiving Notifications**:
   - When stock is updated, system checks for subscribed users
   - Automatic notifications are sent via the existing notification system
   - Users receive both in-app notifications and can be configured for email

### For Admins:
1. **Managing Recommendations**:
   - Access admin interface at `/admin/product_recommendations.php`
   - Add recommendations by selecting products and setting types/priorities
   - Edit or delete existing recommendations

2. **Stock Updates**:
   - When stock is added, system automatically checks for notification subscribers
   - Notifications are sent automatically without manual intervention

## Setup Instructions

1. **Run Database Setup**:
   ```bash
   # Access setup script in browser
   http://your-domain/setup_product_recommendations.php
   ```

2. **Add Sample Recommendations** (Optional):
   - Use the admin interface to add product recommendations
   - Or the setup script will create some sample recommendations

3. **Test the Features**:
   - Create some out-of-stock products
   - Test the notification subscription
   - Test the recommendation display

## Benefits

### For Customers:
- **Better Shopping Experience**: Don't lose customers when products are out of stock
- **Alternative Options**: See similar products that are available
- **Stay Informed**: Get notified when desired products are back in stock
- **No Missed Opportunities**: Never miss out on products they want

### For Business:
- **Increased Sales**: Convert out-of-stock situations into sales opportunities
- **Customer Retention**: Keep customers engaged even when products are unavailable
- **Better Inventory Management**: Understand which products customers want
- **Cross-selling Opportunities**: Promote related products

## Technical Features

- **Responsive Design**: Works on all device sizes
- **AJAX Integration**: Smooth user experience without page reloads
- **Error Handling**: Comprehensive error handling and user feedback
- **Security**: Input validation and SQL injection prevention
- **Performance**: Optimized queries and efficient data handling

## Future Enhancements

- **Email Notifications**: Send email notifications when products become available
- **SMS Notifications**: Optional SMS notifications for urgent restocks
- **Recommendation Analytics**: Track which recommendations are most effective
- **Machine Learning**: AI-powered recommendations based on user behavior
- **Bulk Notifications**: Notify multiple users about new product arrivals

