<?php
// Ensure config and DB connection are loaded when needed
// Use absolute path based on this file's directory to avoid include_path issues
if (!class_exists('DBConnection')) {
    $config_path = __DIR__ . '/../config.php';
    if (file_exists($config_path)) {
        require_once $config_path;
    } else {
        // Fail gracefully if config is missing when Notification is included indirectly
        // (e.g., from AJAX handlers that already loaded config)
        // In that case, we expect DBConnection to already exist.
        // If it doesn't, we avoid a fatal error and simply skip notification features.
        return;
    }
}

class Notification extends DBConnection {
    private $settings;
    private $tableEnsured = false;
    
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }
    
    public function __destruct(){
        parent::__destruct();
    }
    
    /**
     * Ensure notifications table exists (idempotent)
     */
    private function ensureNotificationTable(){
        if($this->tableEnsured){
            return;
        }
        $this->conn->query("CREATE TABLE IF NOT EXISTS notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            data JSON DEFAULT NULL,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            date_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY user_id (user_id), KEY is_read (is_read), KEY type (type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $this->tableEnsured = true;
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
        $this->ensureNotificationTable();
        $uid = (int)$user_id;
        $type = trim($type);
        $title = trim($title);
        $message = trim($message);
        $dataJson = null;
        if(is_array($data) || is_object($data)){
            $dataJson = json_encode($data);
        }elseif(is_string($data) && strlen(trim($data))){
            $dataJson = trim($data);
        }
        if($dataJson === null){
            $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, type, title, message, data, is_read, date_created) VALUES (?, ?, ?, ?, NULL, 0, NOW())");
            $stmt->bind_param("isss", $uid, $type, $title, $message);
        }else{
            $stmt = $this->conn->prepare("INSERT INTO notifications (user_id, type, title, message, data, is_read, date_created) VALUES (?, ?, ?, ?, ?, 0, NOW())");
            $stmt->bind_param("issss", $uid, $type, $title, $message, $dataJson);
        }
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /**
     * Create notification for multiple users at once
     */
    public function createNotificationsForUsers(array $user_ids, $type, $title, $message, $data = null){
        foreach($user_ids as $uid){
            $this->createNotification($uid, $type, $title, $message, $data);
        }
    }
    
    /**
     * Helper to notify admin/staff roles
     */
    public function notifyAdmins($type, $title, $message, $data = null, $roles = ['admin','branch_supervisor','service_admin','accounting']){
        if(empty($roles)){
            $roles = ['admin'];
        }
        $role_list = array_map(function($role){
            return $this->conn->real_escape_string($role);
        }, $roles);
        $role_sql = "'" . implode("','", $role_list) . "'";
        $admins = $this->conn->query("SELECT id FROM users WHERE status = 1 AND role_type IN ({$role_sql})");
        if($admins){
            while($row = $admins->fetch_assoc()){
                $this->createNotification($row['id'], $type, $title, $message, $data);
            }
        }
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
        $uid = (int)$user_id;
        $result = $this->conn->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = '{$uid}' AND is_read = 0");
        $row = $result ? $result->fetch_assoc() : ['count' => 0];
        return isset($row['count']) ? (int)$row['count'] : 0;
    }
    
    /**
     * Get user notifications
     */
    public function getUserNotifications($user_id, $limit = 10, $offset = 0) {
        $uid = (int)$user_id;
        $limit = max(1, (int)$limit);
        $offset = max(0, (int)$offset);
        $sql = "SELECT * FROM notifications WHERE user_id = '{$uid}' ORDER BY date_created DESC LIMIT {$offset},{$limit}";
        $rows = [];
        if($result = $this->conn->query($sql)){
            while($row = $result->fetch_assoc()){
                $rows[] = $this->formatNotificationRow($row);
            }
        }
        return $rows;
    }
    
    /**
     * Format row (decode JSON, add helpers)
     */
    private function formatNotificationRow($row){
        if(isset($row['data']) && !is_null($row['data'])){
            $decoded = json_decode($row['data'], true);
            if(json_last_error() === JSON_ERROR_NONE){
                $row['data'] = $decoded;
            }
        }
        $row['is_read'] = isset($row['is_read']) ? (int)$row['is_read'] : 0;
        $row['date_created'] = isset($row['date_created']) ? $row['date_created'] : date('Y-m-d H:i:s');
        return $row;
    }
    
    /**
     * Fetch history with pagination metadata
     */
    public function getNotificationHistory($user_id, $limit = 20, $offset = 0){
        $rows = $this->getUserNotifications($user_id, $limit, $offset);
        $uid = (int)$user_id;
        $total = $this->conn->query("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = '{$uid}'");
        $count = $total ? (int)$total->fetch_assoc()['cnt'] : 0;
        return [
            'items' => $rows,
            'total' => $count,
            'has_more' => ($offset + $limit) < $count
        ];
    }
    
    /**
     * Mark all notifications read for user
     */
    public function markAllRead($user_id){
        $uid = (int)$user_id;
        return $this->conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = '{$uid}' AND is_read = 0");
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

if(php_sapi_name() !== 'cli' && basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)){
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
            $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
            $offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
            $data = $notification->getUserNotifications($_POST['user_id'], $limit, $offset);
            echo json_encode($data);
            break;
        default:
            echo "Access Denied";
            break;
    }
}
?>
