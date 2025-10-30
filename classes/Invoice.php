<?php
require_once dirname(__DIR__) . '/config.php';

class Invoice extends DBConnection {
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
     * Generate invoice number
     */
    private function generateInvoiceNumber() {
        $prefix = $this->getSetting('invoice_prefix', 'INV');
        $year = date('Y');
        
        // Get the last invoice number for this year
        $last_invoice = $this->conn->query("SELECT invoice_number FROM invoices 
                                           WHERE invoice_number LIKE '{$prefix}-{$year}-%' 
                                           ORDER BY id DESC LIMIT 1")->fetch_assoc();
        
        if($last_invoice) {
            $last_number = intval(substr($last_invoice['invoice_number'], -4));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        return $prefix . '-' . $year . '-' . str_pad($new_number, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Generate receipt number
     */
    private function generateReceiptNumber() {
        $prefix = $this->getSetting('receipt_prefix', 'RCPT');
        $year = date('Y');
        
        // Get the last receipt number for this year
        $last_receipt = $this->conn->query("SELECT receipt_number FROM receipts 
                                           WHERE receipt_number LIKE '{$prefix}-{$year}-%' 
                                           ORDER BY id DESC LIMIT 1")->fetch_assoc();
        
        if($last_receipt) {
            $last_number = intval(substr($last_receipt['receipt_number'], -4));
            $new_number = $last_number + 1;
        } else {
            $new_number = 1;
        }
        
        return $prefix . '-' . $year . '-' . str_pad($new_number, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get setting value
     */
    private function getSetting($key, $default = '') {
        $result = $this->conn->query("SELECT setting_value FROM invoice_settings WHERE setting_key = '{$key}'")->fetch_assoc();
        return $result ? $result['setting_value'] : $default;
    }
    
    /**
     * Recalculate client account balance based on invoice_financials
     */
    private function recalculateClientBalance($clientId) {
        $sum = $this->conn->query(
            "SELECT COALESCE(SUM(balance_remaining),0) as balance FROM invoice_financials WHERE customer_id = '{$clientId}'"
        )->fetch_assoc()['balance'] ?? 0;
        $this->conn->query(
            "UPDATE client_list SET account_balance = '{$sum}' WHERE id = '{$clientId}'"
        );
    }
    
    /**
     * Create invoice from order
     */
    public function createInvoiceFromOrder($order_id, $staff_id) {
        // Get order details
        $order = $this->conn->query("SELECT o.*, c.firstname, c.lastname, c.middlename, c.email, c.contact 
                                    FROM order_list o 
                                    INNER JOIN client_list c ON o.client_id = c.id 
                                    WHERE o.id = '{$order_id}'")->fetch_assoc();
        
        if(!$order) {
            return ['status' => 'error', 'msg' => 'Order not found'];
        }
        
        // Check if invoice already exists
        $existing = $this->conn->query("SELECT id FROM invoices WHERE order_id = '{$order_id}'")->num_rows;
        if($existing > 0) {
            return ['status' => 'error', 'msg' => 'Invoice already exists for this order'];
        }
        
        // Get order items
        $items = $this->conn->query("SELECT oi.*, p.name as product_name, p.description as product_description 
                                    FROM order_items oi 
                                    INNER JOIN product_list p ON oi.product_id = p.id 
                                    WHERE oi.order_id = '{$order_id}'");
        
        $subtotal = 0;
        $invoice_number = $this->generateInvoiceNumber();
        $vat_rate = floatval($this->getSetting('vat_rate', '12'));
        
        // Create invoice
        $invoice_data = [
            'order_id' => $order_id,
            'invoice_number' => $invoice_number,
            'customer_id' => $order['client_id'],
            'transaction_type' => 'motorcycle_purchase',
            'payment_type' => 'cash', // Default, can be updated
            'subtotal' => $order['total_amount'],
            'vat_amount' => ($order['total_amount'] * $vat_rate) / 100,
            'total_amount' => $order['total_amount'] + (($order['total_amount'] * $vat_rate) / 100),
            'payment_status' => 'pending',
            'pickup_location' => $this->getSetting('pickup_location'),
            'payment_instructions' => $this->getSetting('payment_instructions'),
            'generated_by' => $staff_id,
            'due_date' => date('Y-m-d', strtotime('+7 days'))
        ];
        
        $fields = implode(',', array_keys($invoice_data));
        $values = "'" . implode("','", array_values($invoice_data)) . "'";
        
        $invoice_sql = "INSERT INTO invoices ({$fields}) VALUES ({$values})";
        
        if($this->conn->query($invoice_sql)) {
            $invoice_id = $this->conn->insert_id;
            
            // Add invoice items
            while($item = $items->fetch_assoc()) {
                $item_data = [
                    'invoice_id' => $invoice_id,
                    'item_type' => 'motorcycle',
                    'item_id' => $item['product_id'],
                    'item_name' => $item['product_name'],
                    'item_description' => $item['product_description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['quantity'] * $item['price']
                ];
                
                $item_fields = implode(',', array_keys($item_data));
                $item_values = "'" . implode("','", array_values($item_data)) . "'";
                
                $this->conn->query("INSERT INTO invoice_items ({$item_fields}) VALUES ({$item_values})");
            }
            
            // TRIGGER: Recalculate client balance
            $customer_id = $invoice_data['customer_id'];
            $this->recalculateClientBalance($customer_id);

            return [
                'status' => 'success', 
                'msg' => 'Invoice created successfully',
                'invoice_id' => $invoice_id,
                'invoice_number' => $invoice_number
            ];
        } else {
            return ['status' => 'error', 'msg' => 'Failed to create invoice: ' . $this->conn->error];
        }
    }
    
    /**
     * Create receipt from invoice
     */
    public function createReceipt($invoice_id, $payment_data, $staff_id) {
        // Get invoice details
        $invoice = $this->conn->query("SELECT * FROM invoices WHERE id = '{$invoice_id}'")->fetch_assoc();
        
        if(!$invoice) {
            return ['status' => 'error', 'msg' => 'Invoice not found'];
        }
        
        if($invoice['payment_status'] == 'paid') {
            return ['status' => 'error', 'msg' => 'Invoice already paid'];
        }
        
        $receipt_number = $this->generateReceiptNumber();
        
        $receipt_data = [
            'invoice_id' => $invoice_id,
            'receipt_number' => $receipt_number,
            'customer_id' => $invoice['customer_id'],
            'amount_paid' => $payment_data['amount_paid'],
            'payment_method' => $payment_data['payment_method'],
            'payment_reference' => $payment_data['payment_reference'] ?? '',
            'received_by' => $staff_id,
            'acknowledgment_note' => $this->getSetting('acknowledgment_note')
        ];
        
        $fields = implode(',', array_keys($receipt_data));
        $values = "'" . implode("','", array_values($receipt_data)) . "'";
        
        if($this->conn->query("INSERT INTO receipts ({$fields}) VALUES ({$values})")) {
            // Recompute invoice status based on financials view
            $fin = $this->conn->query("SELECT computed_status FROM invoice_financials WHERE id = '{$invoice_id}'")->fetch_assoc();
            if($fin && isset($fin['computed_status'])){
                $new_status = $this->conn->real_escape_string($fin['computed_status']);
                $this->conn->query("UPDATE invoices SET payment_status = '{$new_status}' WHERE id = '{$invoice_id}'");
            }
            
            // Update order status to claimed if fully paid
            if(isset($fin['computed_status']) && $fin['computed_status'] === 'paid'){
                $this->conn->query("UPDATE order_list SET status = 6 WHERE id = '{$invoice['order_id']}'");
            }
            
            // TRIGGER: Update invoice and recalculate client balance
            $this->conn->query("UPDATE invoices SET updated_at = NOW() WHERE id = '{$invoice_id}'");
            $this->recalculateClientBalance($invoice['customer_id']);
            
            return [
                'status' => 'success', 
                'msg' => 'Receipt created successfully',
                'receipt_id' => $this->conn->insert_id,
                'receipt_number' => $receipt_number
            ];
        } else {
            return ['status' => 'error', 'msg' => 'Failed to create receipt: ' . $this->conn->error];
        }
    }
    
    /**
     * Get invoice details
     */
    public function getInvoice($invoice_id) {
        $invoice = $this->conn->query("SELECT i.*, c.firstname, c.lastname, c.middlename, c.email, c.contact,
                                              u.firstname as staff_firstname, u.lastname as staff_lastname,
                                              fin.total_paid, fin.payment_date, fin.balance_remaining, fin.computed_status, fin.late_fee_amount
                                      FROM invoices i
                                      INNER JOIN client_list c ON i.customer_id = c.id
                                      LEFT JOIN users u ON i.generated_by = u.id
                                      LEFT JOIN invoice_financials fin ON fin.id = i.id
                                      WHERE i.id = '{$invoice_id}'")->fetch_assoc();
        if($invoice) {
            // Get invoice items
            $items = $this->conn->query("SELECT * FROM invoice_items WHERE invoice_id = '{$invoice_id}'");
            $invoice['items'] = [];
            while($item = $items->fetch_assoc()) {
                $invoice['items'][] = $item;
            }
            // Removed: Do not fetch or attach receipt.
        }
        return $invoice;
    }
    
    /**
     * Get receipt details for a given invoice
     */
    public function getReceipt($invoice_id) {
        // Fetch the most recent receipt for the invoice
        $receipt = $this->conn->query("SELECT r.*, u.firstname as staff_firstname, u.lastname as staff_lastname FROM receipts r LEFT JOIN users u ON r.received_by = u.id WHERE r.invoice_id = '{$invoice_id}' ORDER BY r.id DESC LIMIT 1")->fetch_assoc();
        return $receipt ?: null;
    }
    
    /**
     * Get customer invoices
     */
    public function getCustomerInvoices($customer_id, $limit = 10) {
        $invoices = $this->conn->query("SELECT i.*, fin.total_paid, fin.payment_date as receipt_date, fin.balance_remaining, fin.computed_status, fin.late_fee_amount
                                       FROM invoices i
                                       LEFT JOIN invoice_financials fin ON fin.id = i.id
                                       WHERE i.customer_id = '{$customer_id}'
                                       ORDER BY i.generated_at DESC
                                       LIMIT {$limit}");
        
        $result = [];
        while($invoice = $invoices->fetch_assoc()) {
            $result[] = $invoice;
        }
        
        return $result;
    }
    
    /**
     * Get all invoices with filters
     */
    public function getAllInvoices($filters = []) {
        $where = "1=1";
        
        if(isset($filters['date_start']) && !empty($filters['date_start'])) {
            $where .= " AND DATE(i.generated_at) >= '{$filters['date_start']}'";
        }
        
        if(isset($filters['date_end']) && !empty($filters['date_end'])) {
            $where .= " AND DATE(i.generated_at) <= '{$filters['date_end']}'";
        }
        
        if(isset($filters['payment_status']) && !empty($filters['payment_status'])) {
            $where .= " AND i.payment_status = '{$filters['payment_status']}'";
        }
        
        if(isset($filters['customer_id']) && !empty($filters['customer_id'])) {
            $where .= " AND i.customer_id = '{$filters['customer_id']}'";
        }
        
        $invoices = $this->conn->query("SELECT i.*, c.firstname, c.lastname, c.middlename, c.email,
                                               fin.total_paid, fin.payment_date as receipt_date, fin.balance_remaining, fin.computed_status, fin.late_fee_amount
                                       FROM invoices i
                                       INNER JOIN client_list c ON i.customer_id = c.id
                                       LEFT JOIN invoice_financials fin ON fin.id = i.id
                                       WHERE {$where}
                                       ORDER BY i.generated_at DESC");
        
        $result = [];
        while($invoice = $invoices->fetch_assoc()) {
            // If there was a receipt assigned here, REMOVE IT so only invoice info is used.
            $result[] = $invoice;
        }
        
        return $result;
    }
    
    /**
     * Update invoice settings
     */
    public function updateSetting($key, $value) {
        return $this->conn->query("INSERT INTO invoice_settings (setting_key, setting_value) 
                                  VALUES ('{$key}', '{$value}') 
                                  ON DUPLICATE KEY UPDATE setting_value = '{$value}'");
    }

    /**
     * Update invoice payment status manually (admin action)
     */
    public function updateInvoiceStatus($invoice_id, $status) {
        $allowed = ['pending','paid','partial','late'];
        $status = strtolower(trim($status));
        if(!in_array($status, $allowed)){
            return ['status' => 'error', 'msg' => 'Invalid status'];
        }
        $invoice = $this->conn->query("SELECT id, order_id, customer_id FROM invoices WHERE id = '{$invoice_id}'")->fetch_assoc();
        if(!$invoice){
            return ['status' => 'error', 'msg' => 'Invoice not found'];
        }
        $ok = $this->conn->query("UPDATE invoices SET payment_status = '{$status}', updated_at = NOW() WHERE id = '{$invoice_id}'");
        if(!$ok){
            return ['status' => 'error', 'msg' => 'Failed to update status: '.$this->conn->error];
        }
        if($status === 'paid'){
            $this->conn->query("UPDATE order_list SET status = 6 WHERE id = '{$invoice['order_id']}'");
        }
        return ['status' => 'success'];
    }
    
    /**
     * Get invoice statistics
     */
    public function getInvoiceStats($date_start = null, $date_end = null) {
        $where = "1=1";
        
        if($date_start) {
            $where .= " AND DATE(generated_at) >= '{$date_start}'";
        }
        
        if($date_end) {
            $where .= " AND DATE(generated_at) <= '{$date_end}'";
        }
        
        $stats = $this->conn->query("SELECT 
                                        COUNT(*) as total_invoices,
                                        SUM(CASE WHEN i.payment_status = 'paid' THEN 1 ELSE 0 END) as paid_invoices,
                                        SUM(CASE WHEN i.payment_status IN ('pending','late','partial') THEN 1 ELSE 0 END) as unpaid_invoices,
                                        SUM(CASE WHEN i.payment_status = 'paid' THEN i.total_amount ELSE 0 END) as total_paid,
                                        SUM(CASE WHEN i.payment_status IN ('pending','late','partial') THEN i.total_amount ELSE 0 END) as total_unpaid,
                                        SUM(i.total_amount) as total_amount
                                    FROM invoices i
                                    WHERE {$where}")->fetch_assoc();
        
        return $stats;
    }
}

// Handle AJAX requests
if(isset($_GET['action'])) {
    $invoice = new Invoice();
    
    switch($_GET['action']) {
        case 'create_from_order':
            if(isset($_POST['order_id']) && isset($_POST['staff_id'])) {
                $result = $invoice->createInvoiceFromOrder($_POST['order_id'], $_POST['staff_id']);
                echo json_encode($result);
            }
            break;
            
        case 'create_receipt':
            if(isset($_POST['invoice_id']) && isset($_POST['payment_data']) && isset($_POST['staff_id'])) {
                $result = $invoice->createReceipt($_POST['invoice_id'], $_POST['payment_data'], $_POST['staff_id']);
                echo json_encode($result);
            }
            break;
            
        case 'get_invoice':
            if(isset($_GET['invoice_id'])) {
                $result = $invoice->getInvoice($_GET['invoice_id']);
                echo json_encode(['status' => 'success', 'data' => $result]);
            }
            break;
            
        case 'get_receipt':
            if(isset($_GET['invoice_id'])) {
                $result = $invoice->getReceipt($_GET['invoice_id']);
                if($result) {
                    echo json_encode(['status' => 'success', 'data' => $result]);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Receipt not found']);
                }
            }
            break;
            
        case 'get_customer_invoices':
            if(isset($_GET['customer_id'])) {
                $result = $invoice->getCustomerInvoices($_GET['customer_id']);
                echo json_encode(['status' => 'success', 'data' => $result]);
            }
            break;
            
        case 'get_all_invoices':
            $filters = $_GET;
            unset($filters['action']);
            $result = $invoice->getAllInvoices($filters);
            echo json_encode(['status' => 'success', 'data' => $result]);
            break;
            
        case 'get_stats':
            $result = $invoice->getInvoiceStats($_GET['date_start'] ?? null, $_GET['date_end'] ?? null);
            echo json_encode(['status' => 'success', 'data' => $result]);
            break;
        case 'update_status':
            if(isset($_POST['invoice_id']) && isset($_POST['status'])){
                $result = $invoice->updateInvoiceStatus($_POST['invoice_id'], $_POST['status']);
                echo json_encode($result);
            }
            break;
    }
}
?>




