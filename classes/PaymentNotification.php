<?php
require_once dirname(__DIR__) . '/config.php';

class PaymentNotification extends DBConnection {
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
     * Check for upcoming payments and create notifications
     */
    public function checkUpcomingPayments() {
        $upcoming_days = 3; // Notify 3 days before due date
        
        // Get customers with unpaid orders
        $qry = $this->conn->query("SELECT c.id, c.firstname, c.lastname, c.email,
                                          o.id as order_id, o.ref_code, o.total_amount, o.date_created,
                                          DATEDIFF(DATE_ADD(o.date_created, INTERVAL 30 DAY), CURDATE()) as days_until_due
                                   FROM client_list c
                                   INNER JOIN order_list o ON c.id = o.client_id
                                   WHERE o.status != 4 AND o.status != 5
                                   AND DATEDIFF(DATE_ADD(o.date_created, INTERVAL 30 DAY), CURDATE()) <= {$upcoming_days}
                                   AND DATEDIFF(DATE_ADD(o.date_created, INTERVAL 30 DAY), CURDATE()) > 0");
        
        $notifications_created = 0;
        
        while($row = $qry->fetch_assoc()) {
            // Check if notification already exists
            $existing = $this->conn->query("SELECT id FROM notifications 
                                           WHERE user_id = '{$row['id']}' 
                                           AND type = 'payment_upcoming' 
                                           AND reference_id = '{$row['order_id']}'
                                           AND is_read = 0")->num_rows;
            
            if($existing == 0) {
                $message = "Payment reminder: Your order {$row['ref_code']} (₱" . number_format($row['total_amount'], 2) . ") is due in {$row['days_until_due']} day(s).";
                
                $this->conn->query("INSERT INTO notifications (user_id, type, title, message, reference_id, date_created) 
                                   VALUES ('{$row['id']}', 'payment_upcoming', 'Payment Due Soon', '{$message}', '{$row['order_id']}', NOW())");
                $notifications_created++;
            }
        }
        
        return $notifications_created;
    }
    
    /**
     * Check for missed payments and create notifications
     */
    public function checkMissedPayments() {
        // Get customers with overdue payments (past 30 days)
        $qry = $this->conn->query("SELECT c.id, c.firstname, c.lastname, c.email,
                                          o.id as order_id, o.ref_code, o.total_amount, o.date_created,
                                          DATEDIFF(CURDATE(), DATE_ADD(o.date_created, INTERVAL 30 DAY)) as days_overdue
                                   FROM client_list c
                                   INNER JOIN order_list o ON c.id = o.client_id
                                   WHERE o.status != 4 AND o.status != 5
                                   AND DATEDIFF(CURDATE(), DATE_ADD(o.date_created, INTERVAL 30 DAY)) > 0");
        
        $notifications_created = 0;
        
        while($row = $qry->fetch_assoc()) {
            // Check if notification already exists
            $existing = $this->conn->query("SELECT id FROM notifications 
                                           WHERE user_id = '{$row['id']}' 
                                           AND type = 'payment_missed' 
                                           AND reference_id = '{$row['order_id']}'
                                           AND is_read = 0")->num_rows;
            
            if($existing == 0) {
                $message = "Overdue payment: Your order {$row['ref_code']} (₱" . number_format($row['total_amount'], 2) . ") is {$row['days_overdue']} day(s) overdue.";
                
                $this->conn->query("INSERT INTO notifications (user_id, type, title, message, reference_id, date_created) 
                                   VALUES ('{$row['id']}', 'payment_missed', 'Payment Overdue', '{$message}', '{$row['order_id']}', NOW())");
                $notifications_created++;
            }
        }
        
        return $notifications_created;
    }
    
    /**
     * Get customer notifications
     */
    public function getCustomerNotifications($customer_id) {
        $qry = $this->conn->query("SELECT * FROM notifications 
                                   WHERE user_id = '{$customer_id}' 
                                   ORDER BY date_created DESC 
                                   LIMIT 10");
        
        $notifications = [];
        while($row = $qry->fetch_assoc()) {
            $notifications[] = $row;
        }
        
        return $notifications;
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notification_id, $customer_id) {
        return $this->conn->query("UPDATE notifications 
                                  SET is_read = 1, date_read = NOW() 
                                  WHERE id = '{$notification_id}' AND user_id = '{$customer_id}'");
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount($customer_id) {
        $result = $this->conn->query("SELECT COUNT(*) as count FROM notifications 
                                     WHERE user_id = '{$customer_id}' AND is_read = 0")->fetch_assoc();
        return $result['count'];
    }
    
    /**
     * Run daily payment checks (to be called by cron job)
     */
    public function runDailyChecks() {
        $upcoming = $this->checkUpcomingPayments();
        $missed = $this->checkMissedPayments();
        
        return [
            'upcoming_notifications' => $upcoming,
            'missed_notifications' => $missed
        ];
    }
}

// Handle AJAX requests
if(isset($_GET['action'])) {
    $notification = new PaymentNotification();
    
    switch($_GET['action']) {
        case 'get_notifications':
            if(isset($_GET['customer_id'])) {
                $notifications = $notification->getCustomerNotifications($_GET['customer_id']);
                echo json_encode(['status' => 'success', 'notifications' => $notifications]);
            }
            break;
            
        case 'mark_read':
            if(isset($_POST['notification_id']) && isset($_POST['customer_id'])) {
                $result = $notification->markAsRead($_POST['notification_id'], $_POST['customer_id']);
                echo json_encode(['status' => $result ? 'success' : 'error']);
            }
            break;
            
        case 'get_unread_count':
            if(isset($_GET['customer_id'])) {
                $count = $notification->getUnreadCount($_GET['customer_id']);
                echo json_encode(['status' => 'success', 'count' => $count]);
            }
            break;
            
        case 'run_daily_checks':
            $result = $notification->runDailyChecks();
            echo json_encode(['status' => 'success', 'result' => $result]);
            break;
    }
}
?>



