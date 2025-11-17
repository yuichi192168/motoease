<?php
require_once dirname(__DIR__) . '/config.php';

/**
 * ActivityLogger Class
 * Handles automatic logging of admin actions for audit tracking
 */
class ActivityLogger {
    private $conn;
    private $db; // Keep reference to DBConnection to prevent destruction
    private $settings;
    
    public function __construct() {
        global $_settings;
        $this->settings = $_settings;
        // Keep reference to DBConnection object to prevent it from being destroyed
        $this->db = new DBConnection();
        $this->conn = $this->db->conn;
    }
    
    /**
     * Ensure database connection is valid
     * Reconnects if connection is closed
     */
    private function ensureConnection() {
        // Check if connection is closed or invalid
        $needs_reconnect = false;
        
        if (!($this->conn instanceof mysqli)) {
            $needs_reconnect = true;
        } else {
            // Try to ping the connection, catch any errors
            try {
                if (!@$this->conn->ping()) {
                    $needs_reconnect = true;
                }
            } catch (Exception $e) {
                $needs_reconnect = true;
            }
        }
        
        if ($needs_reconnect) {
            // Recreate connection
            $this->db = new DBConnection();
            $this->conn = $this->db->conn;
        }
    }
    
    /**
     * Log an admin action
     * 
     * @param string $action The action description (e.g., "approved Order #2045")
     * @param string $module The module name (e.g., "Orders", "Invoices", "Customer Accounts")
     * @param int|null $reference_id Optional reference ID (order_id, invoice_id, customer_id, etc.)
     * @return bool Success status
     */
    public function log($action, $module = null, $reference_id = null) {
        // Only log if user is logged in and is an admin
        if (!$this->settings->userdata('id') || $this->settings->userdata('login_type') != 1) {
            return false;
        }
        
        // Ensure connection is valid before proceeding
        $this->ensureConnection();
        
        $user_id = $this->settings->userdata('id');
        $role = 'admin'; // Always admin for admin dashboard actions
        
        // Get admin name for the action message
        $admin_name = $this->getAdminName($user_id);
        
        // Format the action message with admin name
        $formatted_action = "Admin {$admin_name} {$action}";
        
        // Escape values
        $formatted_action = $this->conn->real_escape_string($formatted_action);
        $module = $module ? $this->conn->real_escape_string($module) : 'NULL';
        $reference_id = $reference_id ? intval($reference_id) : 'NULL';
        
        // Insert log entry
        $sql = "INSERT INTO `admin_activity_log` (`user_id`, `role`, `action`, `module`, `reference_id`, `timestamp`) 
                VALUES ('{$user_id}', '{$role}', '{$formatted_action}', " . ($module !== 'NULL' ? "'{$module}'" : 'NULL') . ", " . ($reference_id !== 'NULL' ? "'{$reference_id}'" : 'NULL') . ", NOW())";
        
        $result = $this->conn->query($sql);
        
        if (!$result) {
            error_log("ActivityLogger Error: " . $this->conn->error);
            return false;
        }
        
        return true;
    }
    
    /**
     * Get admin full name
     * 
     * @param int $user_id
     * @return string
     */
    private function getAdminName($user_id) {
        // Ensure connection is valid before querying
        $this->ensureConnection();
        
        $result = $this->conn->query("SELECT CONCAT(firstname, ' ', lastname) as fullname FROM users WHERE id = '{$user_id}'");
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['fullname'];
        }
        return 'Unknown';
    }
    
    /**
     * Log login action
     */
    public function logLogin() {
        return $this->log("logged in.", "Authentication", null);
    }
    
    /**
     * Log logout action
     */
    public function logLogout() {
        return $this->log("logged out.", "Authentication", null);
    }
    
    /**
     * Log order approval
     */
    public function logOrderApproval($order_id) {
        return $this->log("approved Order #{$order_id}.", "Orders", $order_id);
    }
    
    /**
     * Log order rejection
     */
    public function logOrderRejection($order_id, $reason = null) {
        $action = "rejected Order #{$order_id}.";
        if ($reason) {
            $action .= " Reason: {$reason}";
        }
        return $this->log($action, "Orders", $order_id);
    }
    
    /**
     * Log account balance update
     */
    public function logAccountBalanceUpdate($customer_id, $new_balance) {
        $formatted_balance = "₱" . number_format($new_balance, 2);
        return $this->log("updated account balance for Customer #{$customer_id}. New Balance: {$formatted_balance}.", "Customer Accounts", $customer_id);
    }
    
    /**
     * Log onsite payment
     */
    public function logOnsitePayment($customer_id, $amount) {
        $formatted_amount = "₱" . number_format($amount, 2);
        return $this->log("recorded an onsite payment of {$formatted_amount} for Customer #{$customer_id}.", "Customer Accounts", $customer_id);
    }
    
    /**
     * Log payment status change
     */
    public function logPaymentStatusChange($invoice_id, $status) {
        return $this->log("changed payment status of Invoice #{$invoice_id} to {$status}.", "Invoices", $invoice_id);
    }
    
    /**
     * Log archive action
     */
    public function logArchive($record_type, $record_id) {
        return $this->log("archived {$record_type} #{$record_id}.", "Archives", $record_id);
    }
    
    /**
     * Log OR/CR document upload
     */
    public function logORCRUpload($customer_id, $document_type) {
        return $this->log("uploaded {$document_type} for Customer #{$customer_id}.", "OR/CR Documents", $customer_id);
    }
    
    /**
     * Log invoice creation
     */
    public function logInvoiceCreation($invoice_id, $customer_id) {
        return $this->log("generated Invoice #{$invoice_id} for Customer #{$customer_id}.", "Invoices", $invoice_id);
    }
    
    /**
     * Log receipt creation
     */
    public function logReceiptCreation($receipt_id, $customer_id) {
        return $this->log("created Receipt #{$receipt_id} for Customer #{$customer_id}.", "Receipts", $receipt_id);
    }
    
    /**
     * Log inventory/stock update
     */
    public function logStockUpdate($product_id, $details = null) {
        $action = "updated stock for Product #{$product_id}.";
        if ($details) {
            $action .= " {$details}";
        }
        return $this->log($action, "Inventory", $product_id);
    }
    
    /**
     * Log system settings update
     */
    public function logSystemSettingsUpdate($setting_key = null) {
        $action = "modified system settings.";
        if ($setting_key) {
            $action = "modified system setting: {$setting_key}.";
        }
        return $this->log($action, "System Settings", null);
    }
}

