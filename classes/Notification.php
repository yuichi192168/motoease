<?php
require_once('../config.php');

class Notification extends DBConnection {
    private $settings;
    
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }
    
    public function __destruct(){
        parent::__destruct();
    }
    
    /**
     * Send email notification
     */
    public function sendEmail($to, $subject, $message, $from = null) {
        if(!$from) {
            $from = $_settings->info('email') ?: 'noreply@' . $_SERVER['HTTP_HOST'];
        }
        
        $headers = "From: " . $from . "\r\n";
        $headers .= "Reply-To: " . $from . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        return mail($to, $subject, $message, $headers);
    }
    
    /**
     * Send SMS notification (placeholder for SMS gateway integration)
     */
    public function sendSMS($phone, $message) {
        // This is a placeholder for SMS gateway integration
        // You can integrate with services like Twilio, Nexmo, etc.
        
        // For now, we'll just log the SMS
        $this->logNotification('sms', $phone, $message);
        return true;
    }
    
    /**
     * Create notification record in database
     */
    public function createNotification($user_id, $type, $title, $message, $data = null) {
        $user_id = $this->conn->real_escape_string($user_id);
        $type = $this->conn->real_escape_string($type);
        $title = $this->conn->real_escape_string($title);
        $message = $this->conn->real_escape_string($message);
        $data = $data ? $this->conn->real_escape_string(json_encode($data)) : null;
        
        $sql = "INSERT INTO notifications (user_id, type, title, message, data, is_read, date_created) 
                VALUES ('{$user_id}', '{$type}', '{$title}', '{$message}', '{$data}', 0, NOW())";
        
        return $this->conn->query($sql);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notification_id) {
        $notification_id = $this->conn->real_escape_string($notification_id);
        $sql = "UPDATE notifications SET is_read = 1 WHERE id = '{$notification_id}'";
        return $this->conn->query($sql);
    }
    
    /**
     * Get unread notifications count
     */
    public function getUnreadCount($user_id) {
        $user_id = $this->conn->real_escape_string($user_id);
        $result = $this->conn->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = '{$user_id}' AND is_read = 0");
        return $result->fetch_assoc()['count'];
    }
    
    /**
     * Get user notifications
     */
    public function getUserNotifications($user_id, $limit = 10) {
        $user_id = $this->conn->real_escape_string($user_id);
        $limit = (int)$limit;
        
        $sql = "SELECT * FROM notifications WHERE user_id = '{$user_id}' ORDER BY date_created DESC LIMIT {$limit}";
        return $this->conn->query($sql);
    }
    
    /**
     * Send order status notification
     */
    public function sendOrderStatusNotification($order_id) {
        $order = $this->conn->query("SELECT o.*, c.email, c.firstname, c.lastname 
                                    FROM order_list o 
                                    INNER JOIN client_list c ON o.client_id = c.id 
                                    WHERE o.id = '{$order_id}'")->fetch_assoc();
        
        if(!$order) return false;
        
        $status_text = $this->getOrderStatusText($order['status']);
        $title = "Order Status Update";
        $message = "Your order #{$order['ref_code']} has been updated to: {$status_text}";
        
        // Create notification
        $this->createNotification($order['client_id'], 'order_status', $title, $message, [
            'order_id' => $order_id,
            'ref_code' => $order['ref_code'],
            'status' => $order['status']
        ]);
        
        // Send email
        $email_message = $this->getOrderStatusEmailTemplate($order, $status_text);
        $this->sendEmail($order['email'], $title, $email_message);
        
        return true;
    }
    
    /**
     * Send service status notification
     */
    public function sendServiceStatusNotification($service_id) {
        $service = $this->conn->query("SELECT s.*, c.email, c.firstname, c.lastname 
                                      FROM service_requests s 
                                      INNER JOIN client_list c ON s.client_id = c.id 
                                      WHERE s.id = '{$service_id}'")->fetch_assoc();
        
        if(!$service) return false;
        
        $status_text = $this->getServiceStatusText($service['status']);
        $title = "Service Request Update";
        $message = "Your service request has been updated to: {$status_text}";
        
        // Create notification
        $this->createNotification($service['client_id'], 'service_status', $title, $message, [
            'service_id' => $service_id,
            'status' => $service['status']
        ]);
        
        // Send email
        $email_message = $this->getServiceStatusEmailTemplate($service, $status_text);
        $this->sendEmail($service['email'], $title, $email_message);
        
        return true;
    }
    
    /**
     * Send product availability notification
     */
    public function sendProductAvailabilityNotification($product_id) {
        // Get users who have this product in their wishlist or have shown interest
        $users = $this->conn->query("SELECT DISTINCT c.id, c.email, c.firstname, c.lastname 
                                    FROM client_list c 
                                    INNER JOIN wishlist w ON c.id = w.client_id 
                                    WHERE w.product_id = '{$product_id}' AND c.status = 1");
        
        $product = $this->conn->query("SELECT name FROM product_list WHERE id = '{$product_id}'")->fetch_assoc();
        
        while($user = $users->fetch_assoc()) {
            $title = "Product Available";
            $message = "The product '{$product['name']}' is now back in stock!";
            
            // Create notification
            $this->createNotification($user['id'], 'product_availability', $title, $message, [
                'product_id' => $product_id,
                'product_name' => $product['name']
            ]);
            
            // Send email
            $email_message = $this->getProductAvailabilityEmailTemplate($user, $product);
            $this->sendEmail($user['email'], $title, $email_message);
        }
        
        return true;
    }
    
    /**
     * Send appointment reminder
     */
    public function sendAppointmentReminder($appointment_id) {
        $appointment = $this->conn->query("SELECT a.*, c.email, c.firstname, c.lastname 
                                         FROM appointments a 
                                         INNER JOIN client_list c ON a.client_id = c.id 
                                         WHERE a.id = '{$appointment_id}'")->fetch_assoc();
        
        if(!$appointment) return false;
        
        $title = "Appointment Reminder";
        $message = "Reminder: You have an appointment scheduled for " . date('M d, Y H:i', strtotime($appointment['appointment_date']));
        
        // Create notification
        $this->createNotification($appointment['client_id'], 'appointment_reminder', $title, $message, [
            'appointment_id' => $appointment_id,
            'appointment_date' => $appointment['appointment_date']
        ]);
        
        // Send email
        $email_message = $this->getAppointmentReminderEmailTemplate($appointment);
        $this->sendEmail($appointment['email'], $title, $email_message);
        
        return true;
    }
    
    /**
     * Get order status text
     */
    private function getOrderStatusText($status) {
        $statuses = [
            0 => 'Pending',
            1 => 'Approved Order',
            2 => 'For Delivery',
            3 => 'On the Way',
            4 => 'Delivered',
            5 => 'Cancelled'
        ];
        
        return isset($statuses[$status]) ? $statuses[$status] : 'Unknown';
    }
    
    /**
     * Get service status text
     */
    private function getServiceStatusText($status) {
        $statuses = [
            0 => 'Pending',
            1 => 'Confirmed',
            2 => 'On Progress',
            3 => 'Done',
            4 => 'Cancelled'
        ];
        
        return isset($statuses[$status]) ? $statuses[$status] : 'Unknown';
    }
    
    /**
     * Email templates
     */
    private function getOrderStatusEmailTemplate($order, $status_text) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Order Status Update</h2>
            <p>Dear {$order['firstname']} {$order['lastname']},</p>
            <p>Your order has been updated:</p>
            <div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <p><strong>Order Reference:</strong> {$order['ref_code']}</p>
                <p><strong>New Status:</strong> {$status_text}</p>
                <p><strong>Total Amount:</strong> ₱" . number_format($order['total_amount'], 2) . "</p>
            </div>
            <p>Thank you for choosing our services!</p>
            <p>Best regards,<br>" . $_settings->info('name') . "</p>
        </div>";
    }
    
    private function getServiceStatusEmailTemplate($service, $status_text) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Service Request Update</h2>
            <p>Dear {$service['firstname']} {$service['lastname']},</p>
            <p>Your service request has been updated:</p>
            <div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <p><strong>Service ID:</strong> #{$service['id']}</p>
                <p><strong>New Status:</strong> {$status_text}</p>
                <p><strong>Date Created:</strong> " . date('M d, Y H:i', strtotime($service['date_created'])) . "</p>
            </div>
            <p>We'll keep you updated on the progress of your service request.</p>
            <p>Best regards,<br>" . $_settings->info('name') . "</p>
        </div>";
    }
    
    private function getProductAvailabilityEmailTemplate($user, $product) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Product Available</h2>
            <p>Dear {$user['firstname']} {$user['lastname']},</p>
            <p>Great news! The product you're interested in is now back in stock:</p>
            <div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <p><strong>Product:</strong> {$product['name']}</p>
            </div>
            <p><a href='" . base_url . "?p=products/view_product&id={$product['id']}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Product</a></p>
            <p>Hurry up and place your order before it runs out again!</p>
            <p>Best regards,<br>" . $_settings->info('name') . "</p>
        </div>";
    }
    
    private function getAppointmentReminderEmailTemplate($appointment) {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #333;'>Appointment Reminder</h2>
            <p>Dear {$appointment['firstname']} {$appointment['lastname']},</p>
            <p>This is a friendly reminder about your upcoming appointment:</p>
            <div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                <p><strong>Date & Time:</strong> " . date('M d, Y H:i', strtotime($appointment['appointment_date'])) . "</p>
                <p><strong>Service:</strong> {$appointment['service_type']}</p>
                <p><strong>Notes:</strong> {$appointment['notes']}</p>
            </div>
            <p>Please arrive 10 minutes before your scheduled time.</p>
            <p>Best regards,<br>" . $_settings->info('name') . "</p>
        </div>";
    }
    
    /**
     * Log notification for debugging
     */
    private function logNotification($type, $recipient, $message) {
        $log_file = base_app . 'logs/notifications.log';
        $log_dir = dirname($log_file);
        
        if(!is_dir($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        
        $log_entry = date('Y-m-d H:i:s') . " | {$type} | {$recipient} | {$message}\n";
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
}

$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$notification = new Notification();

switch ($action) {
    case 'mark_read':
        echo $notification->markAsRead($_POST['notification_id']);
        break;
    case 'get_unread_count':
        echo json_encode(['count' => $notification->getUnreadCount($_POST['user_id'])]);
        break;
    case 'get_notifications':
        $notifications = $notification->getUserNotifications($_POST['user_id'], $_POST['limit'] ?? 10);
        $data = [];
        while($row = $notifications->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        break;
    default:
        echo "Access Denied";
        break;
}
?>
