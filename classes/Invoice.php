<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/inc/transaction_type_helper.php';
if(file_exists(base_app.'classes/CustomerAccountBalance.php')){
    require_once base_app.'classes/CustomerAccountBalance.php';
}

class Invoice extends DBConnection {
    private $settings;
    private $accountBalanceManager;
    
    public function __construct(){
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
        if(class_exists('CustomerAccountBalance')){
            $this->accountBalanceManager = new CustomerAccountBalance($this->conn);
        }
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
     * Determine transaction type based on order contents
     */
    private function determineTransactionTypeFromOrder($order_id){
        $query = $this->conn->query("SELECT p.name as product_name, cat.category 
                                     FROM order_items oi 
                                     INNER JOIN product_list p ON oi.product_id = p.id 
                                     LEFT JOIN categories cat ON p.category_id = cat.id 
                                     WHERE oi.order_id = '{$order_id}'");
        if(!$query || $query->num_rows === 0){
            return 'motorcycle_purchase';
        }
        $flags = [
            'has_motorcycle' => false,
            'has_parts' => false,
            'has_genuine_oil' => false,
        ];
        while($row = $query->fetch_assoc()){
            $classification = classify_transaction_item($row['category'] ?? '', $row['product_name'] ?? '');
            if($classification['is_motorcycle']){
                $flags['has_motorcycle'] = true;
            } elseif($classification['is_parts']){
                $flags['has_parts'] = true;
            } elseif($classification['is_genuine_oil']){
                $flags['has_genuine_oil'] = true;
            }
        }
        return resolve_transaction_type($flags['has_motorcycle'], $flags['has_parts'], $flags['has_genuine_oil']);
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
     * Sync invoice receipt to customer account balance module (if available)
     */
    private function syncReceiptToAccountBalance($invoice_id, $amount, $receipt_number, $payment_method, $staff_id){
        if(!$this->accountBalanceManager){
            return false;
        }
        $account = $this->conn->query("SELECT id FROM customer_account_balances WHERE invoice_id = '{$invoice_id}'")->fetch_assoc();
        if(!$account || !$account['id']){
            return false;
        }
        try{
            return $this->accountBalanceManager->recordTransaction(
                $account['id'],
                null,
                'invoice_payment',
                $amount,
                $payment_method,
                $receipt_number,
                'Synced from invoice receipt',
                $staff_id
            );
        } catch(\Throwable $e){
            error_log("Failed to sync receipt to account balance: ".$e->getMessage());
            return false;
        }
    }
    
    /**
     * Create invoice from order
     */
    public function createInvoiceFromOrder($order_id, $staff_id, $transaction_type = null) {
        $order = $this->conn->query("SELECT o.*, c.firstname, c.lastname, c.middlename, c.email, c.contact 
                                    FROM order_list o 
                                    INNER JOIN client_list c ON o.client_id = c.id 
                                    WHERE o.id = '{$order_id}'")->fetch_assoc();
        if(!$order) {
            return ['status' => 'error', 'msg' => 'Order not found'];
        }
        $existing = $this->conn->query("SELECT id FROM invoices WHERE order_id = '{$order_id}'")->num_rows;
        if($existing > 0) {
            return ['status' => 'error', 'msg' => 'Invoice already exists for this order'];
        }
        $items = $this->conn->query("SELECT oi.*, p.name as product_name, p.description as product_description, p.price 
                                    FROM order_items oi 
                                    INNER JOIN product_list p ON oi.product_id = p.id 
                                    WHERE oi.order_id = '{$order_id}'");
        $invoice_number = $this->generateInvoiceNumber();
        if(empty($transaction_type)){
            $transaction_type = $this->determineTransactionTypeFromOrder($order_id);
        }
        // VAT removed - total_amount equals subtotal
        $invoice_data = [
            'order_id' => $order_id,
            'invoice_number' => $invoice_number,
            'customer_id' => $order['client_id'],
            'transaction_type' => $transaction_type,
            'payment_type' => 'cash',
            'subtotal' => $order['total_amount'],
            'vat_amount' => 0,
            'total_amount' => $order['total_amount'],
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
            $this->recalculateClientBalance($invoice_data['customer_id']);
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
        $invoice = $this->conn->query("SELECT * FROM invoices WHERE id = '{$invoice_id}'")->fetch_assoc();
        if(!$invoice) {
            return ['status' => 'error', 'msg' => 'Invoice not found'];
        }
        if($invoice['payment_status'] == 'paid') {
            return ['status' => 'error', 'msg' => 'Invoice already paid'];
        }
        $receipt_number = $this->generateReceiptNumber();
        $acknowledgment_note = $this->getSetting('acknowledgment_note');
        if(empty($acknowledgment_note)){
            $acknowledgment_note = 'Thank you for your purchase at Star Honda Calamba!';
        }
        
        // Escape values to prevent SQL injection
        $invoice_id_escaped = $this->conn->real_escape_string($invoice_id);
        $receipt_number_escaped = $this->conn->real_escape_string($receipt_number);
        $customer_id_escaped = $this->conn->real_escape_string($invoice['customer_id']);
        $amount_paid_escaped = $this->conn->real_escape_string($payment_data['amount_paid']);
        $payment_method_escaped = $this->conn->real_escape_string($payment_data['payment_method']);
        $payment_reference_escaped = $this->conn->real_escape_string($payment_data['payment_reference'] ?? '');
        $staff_id_escaped = $this->conn->real_escape_string($staff_id);
        $acknowledgment_note_escaped = $this->conn->real_escape_string($acknowledgment_note);
        
        // Insert receipt with all required fields including archive_flag
        $insert_sql = "INSERT INTO receipts (
            invoice_id, 
            receipt_number, 
            customer_id, 
            amount_paid, 
            payment_method, 
            payment_reference, 
            received_by, 
            acknowledgment_note,
            archive_flag
        ) VALUES (
            '{$invoice_id_escaped}',
            '{$receipt_number_escaped}',
            '{$customer_id_escaped}',
            '{$amount_paid_escaped}',
            '{$payment_method_escaped}',
            '{$payment_reference_escaped}',
            '{$staff_id_escaped}',
            '{$acknowledgment_note_escaped}',
            0
        )";
        
        if($this->conn->query($insert_sql)) {
            $receipt_id = $this->conn->insert_id;
            
            // Get financial data from view (invoice_financials is a VIEW, automatically calculated from receipts)
            // The view includes the newly inserted receipt in its calculations
            $fin = $this->conn->query("SELECT total_paid, balance_remaining, computed_status FROM invoice_financials WHERE id = '{$invoice_id}'")->fetch_assoc();
            
            // Use computed_status from view - it's already calculated correctly based on all receipts including the new one
            $new_status = 'partial';
            if($fin && isset($fin['computed_status'])){
                $new_status = $fin['computed_status'];
            } else {
                // Fallback: calculate manually if view doesn't have computed_status
                $total_amount = isset($invoice['total_amount']) ? floatval($invoice['total_amount']) : 0;
                $total_paid = $fin && isset($fin['total_paid']) ? floatval($fin['total_paid']) : 0;
                $balance = $fin && isset($fin['balance_remaining']) ? floatval($fin['balance_remaining']) : $total_amount;
                
                if($total_paid >= $total_amount){
                    $new_status = 'paid';
                } elseif($invoice['due_date'] && strtotime($invoice['due_date']) < time() && $balance > 0){
                    $new_status = 'late';
                } elseif($balance > 0){
                    $new_status = 'partial';
                } else {
                    $new_status = 'paid';
                }
            }
            
            // Update invoice status (invoice_financials is a VIEW, so we only update the underlying invoices table)
            // The view will automatically recalculate based on receipts
            $this->conn->query("UPDATE invoices SET payment_status = '{$new_status}', updated_at = NOW() WHERE id = '{$invoice_id}'");
            if($new_status === 'paid'){
                $this->conn->query("UPDATE order_list SET status = 6 WHERE id = '{$invoice['order_id']}'");
            }
            $this->conn->query("UPDATE invoices SET updated_at = NOW() WHERE id = '{$invoice_id}'");
            $this->recalculateClientBalance($invoice['customer_id']);
            $this->syncReceiptToAccountBalance($invoice_id, $payment_data['amount_paid'], $receipt_number, $payment_data['payment_method'], $staff_id);
            return [
                'status' => 'success', 
                'msg' => 'Receipt created successfully',
                'receipt_id' => $receipt_id,
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
        $invoice_id = $this->conn->real_escape_string($invoice_id);
        
        // Check which columns exist in invoice_financials
        $columns_check = $this->conn->query("SHOW COLUMNS FROM invoice_financials");
        $available_columns = [];
        if($columns_check){
            while($col = $columns_check->fetch_assoc()){
                $available_columns[] = $col['Field'];
            }
        }
        
        // Build field list based on available columns
        $fields = ['fin.total_paid', 'fin.payment_date', 'fin.balance_remaining', 'fin.computed_status', 'fin.late_fee_amount'];
        if(in_array('interest_amount', $available_columns)){
            $fields[] = 'fin.interest_amount';
        }
        if(in_array('arrears_amount', $available_columns)){
            $fields[] = 'fin.arrears_amount';
        }
        if(in_array('total_balance_due', $available_columns)){
            $fields[] = 'fin.total_balance_due';
        }
        
        $fields_str = implode(', ', $fields);
        
        $invoice = $this->conn->query("SELECT i.*, c.firstname, c.lastname, c.middlename, c.email, c.contact,
                                              u.firstname as staff_firstname, u.lastname as staff_lastname,
                                              {$fields_str}
                                      FROM invoices i
                                      INNER JOIN client_list c ON i.customer_id = c.id
                                      LEFT JOIN users u ON i.generated_by = u.id
                                      LEFT JOIN invoice_financials fin ON fin.id = i.id
                                      WHERE i.id = '{$invoice_id}'");
        
        if(!$invoice) {
            error_log("Invoice query error: " . $this->conn->error);
            return null;
        }
        
        $invoice = $invoice->fetch_assoc();
        if($invoice) {
            // Set default values for columns that might not exist
            if(!isset($invoice['interest_amount'])){
                $invoice['interest_amount'] = 0;
            }
            if(!isset($invoice['arrears_amount'])){
                $invoice['arrears_amount'] = 0;
            }
            if(!isset($invoice['total_balance_due'])){
                // Calculate total_balance_due if not in view
                $balance = floatval($invoice['balance_remaining'] ?? 0);
                $interest = floatval($invoice['interest_amount'] ?? 0);
                $late_fee = floatval($invoice['late_fee_amount'] ?? 0);
                $arrears = floatval($invoice['arrears_amount'] ?? 0);
                $invoice['total_balance_due'] = $balance + $interest + $late_fee + $arrears;
            }
            $items = $this->conn->query("SELECT * FROM invoice_items WHERE invoice_id = '{$invoice_id}'");
            $invoice['items'] = [];
            while($item = $items->fetch_assoc()) {
                $invoice['items'][] = $item;
            }
            $invoice['service_details'] = [];
            $invoice['service_schedule'] = [];
            if(!empty($invoice['service_request_id'])){
                $service_request_id = $this->conn->real_escape_string($invoice['service_request_id']);
                $meta = $this->conn->query("SELECT meta_field, meta_value FROM request_meta WHERE request_id = '{$service_request_id}' AND meta_field IN ('service_id','preferred_date','preferred_time')");
                $service_ids = [];
                if($meta){
                    while($row = $meta->fetch_assoc()){
                        if($row['meta_field'] === 'service_id'){
                            $service_ids = array_filter(array_map('trim', explode(',', (string)$row['meta_value'])));
                        }elseif($row['meta_field'] === 'preferred_date' || $row['meta_field'] === 'preferred_time'){
                            $invoice['service_schedule'][$row['meta_field']] = $row['meta_value'];
                        }
                    }
                }
                if(!empty($service_ids)){
                    $ids = array_values(array_filter(array_map('intval', $service_ids)));
                    if(!empty($ids)){
                        $id_list = implode(',', $ids);
                        $services_q = $this->conn->query("SELECT id, service, service_amount FROM service_list WHERE id IN ({$id_list})");
                        $services_map = [];
                        if($services_q){
                            while($svc = $services_q->fetch_assoc()){
                                $services_map[$svc['id']] = [
                                    'id' => (int)$svc['id'],
                                    'name' => $svc['service'],
                                    'amount' => isset($svc['service_amount']) ? (float)$svc['service_amount'] : 0
                                ];
                            }
                        }
                        foreach($ids as $sid){
                            if(isset($services_map[$sid])){
                                $invoice['service_details'][] = $services_map[$sid];
                            }
                        }
                    }
                }
            }
            
            // Get installment schedule if payment type is installment
            $invoice['installment_schedule'] = [];
            if(!empty($invoice['payment_type']) && strtolower($invoice['payment_type']) == 'installment' && !empty($invoice['order_id'])){
                try {
                    $order_id = $this->conn->real_escape_string($invoice['order_id']);
                    // Try to get installment contract from order_id
                    $contract_query = $this->conn->query("SELECT ic.* FROM installment_contracts ic 
                                                         WHERE ic.order_id = '{$order_id}' 
                                                         LIMIT 1");
                    if($contract_query && $contract_query->num_rows > 0){
                        $installment_contract = $contract_query->fetch_assoc();
                        if($installment_contract && !empty($installment_contract['id'])){
                            $contract_id = $this->conn->real_escape_string($installment_contract['id']);
                            // Get installment schedule from installment_schedule table with calculated amounts
                            $schedule = $this->conn->query("SELECT 
                                                               isch.*,
                                                               (isch.amount_due - COALESCE(isch.paid_amount, 0)) as remaining_due,
                                                               (isch.amount_due + COALESCE(isch.penalty_amount, 0) + COALESCE(isch.late_fee, 0) - COALESCE(isch.paid_amount, 0)) as total_due_with_penalties
                                                           FROM installment_schedule isch
                                                           WHERE isch.contract_id = '{$contract_id}' 
                                                           ORDER BY isch.due_date ASC");
                            if($schedule && $schedule->num_rows > 0){
                                while($payment = $schedule->fetch_assoc()){
                                    $amount_due = floatval($payment['amount_due'] ?? 0);
                                    $paid_amount = floatval($payment['paid_amount'] ?? 0);
                                    $penalty_amount = floatval($payment['penalty_amount'] ?? 0);
                                    $late_fee = floatval($payment['late_fee'] ?? 0);
                                    $remaining_due = floatval($payment['remaining_due'] ?? ($amount_due - $paid_amount));
                                    $total_due = $amount_due + $penalty_amount + $late_fee;
                                    
                                    $invoice['installment_schedule'][] = [
                                        'due_date' => $payment['due_date'] ?? '',
                                        'amount' => $amount_due,
                                        'amount_due' => $amount_due,
                                        'status' => $payment['status'] ?? 'pending',
                                        'paid_amount' => $paid_amount,
                                        'remaining_due' => $remaining_due,
                                        'penalty_amount' => $penalty_amount,
                                        'late_fee' => $late_fee,
                                        'total_due' => $total_due,
                                        'total_due_with_penalties' => floatval($payment['total_due_with_penalties'] ?? $total_due),
                                        'principal_amount' => floatval($payment['principal_amount'] ?? 0),
                                        'interest_amount' => floatval($payment['interest_amount'] ?? 0)
                                    ];
                                }
                            }
                        }
                    }
                } catch(Exception $e) {
                    // If tables don't exist or query fails, just skip installment schedule
                    error_log("Error loading installment schedule: " . $e->getMessage());
                    $invoice['installment_schedule'] = [];
                } catch(Error $e) {
                    // Catch fatal errors too
                    error_log("Fatal error loading installment schedule: " . $e->getMessage());
                    $invoice['installment_schedule'] = [];
                }
            }
        }
        return $invoice;
    }
    
    /**
     * Get receipt details for a given invoice
     */
    public function getReceipt($invoice_id) {
        $receipt = $this->conn->query("SELECT r.*, u.firstname as staff_firstname, u.lastname as staff_lastname FROM receipts r LEFT JOIN users u ON r.received_by = u.id WHERE r.invoice_id = '{$invoice_id}' ORDER BY r.id DESC LIMIT 1")->fetch_assoc();
        return $receipt ?: null;
    }
    
    /**
     * Get customer invoices
     */
    public function getCustomerInvoices($customer_id, $limit = 10) {
        // Check which columns exist in invoice_financials
        $columns_check = $this->conn->query("SHOW COLUMNS FROM invoice_financials");
        $available_columns = [];
        if($columns_check){
            while($col = $columns_check->fetch_assoc()){
                $available_columns[] = $col['Field'];
            }
        }
        
        $fields = ['fin.total_paid', 'fin.balance_remaining', 'fin.computed_status', 'fin.late_fee_amount'];
        if(in_array('interest_amount', $available_columns)){
            $fields[] = 'fin.interest_amount';
        }
        if(in_array('arrears_amount', $available_columns)){
            $fields[] = 'fin.arrears_amount';
        }
        if(in_array('total_balance_due', $available_columns)){
            $fields[] = 'fin.total_balance_due';
        }
        
        $fields_str = implode(', ', $fields);
        
        $invoices = $this->conn->query("SELECT i.*, {$fields_str},
                                               COALESCE(rc.latest_receipt_date, fin.payment_date) as receipt_date,
                                               COALESCE(rc.receipt_count, 0) as receipt_count
                                       FROM invoices i
                                       LEFT JOIN invoice_financials fin ON fin.id = i.id
                                       LEFT JOIN (
                                            SELECT invoice_id,
                                                   COUNT(*) as receipt_count,
                                                   MAX(issued_at) as latest_receipt_date
                                            FROM receipts
                                            GROUP BY invoice_id
                                       ) rc ON rc.invoice_id = i.id
                                       WHERE i.customer_id = '{$customer_id}'
                                       ORDER BY i.generated_at DESC
                                       LIMIT {$limit}");
        $result = [];
        while($invoice = $invoices->fetch_assoc()) {
            // Set defaults for missing columns
            if(!isset($invoice['interest_amount'])) $invoice['interest_amount'] = 0;
            if(!isset($invoice['arrears_amount'])) $invoice['arrears_amount'] = 0;
            if(!isset($invoice['total_balance_due'])){
                $balance = floatval($invoice['balance_remaining'] ?? 0);
                $interest = floatval($invoice['interest_amount'] ?? 0);
                $late_fee = floatval($invoice['late_fee_amount'] ?? 0);
                $arrears = floatval($invoice['arrears_amount'] ?? 0);
                $invoice['total_balance_due'] = $balance + $interest + $late_fee + $arrears;
            }
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
        // Check which columns exist in invoice_financials
        $columns_check = $this->conn->query("SHOW COLUMNS FROM invoice_financials");
        $available_columns = [];
        if($columns_check){
            while($col = $columns_check->fetch_assoc()){
                $available_columns[] = $col['Field'];
            }
        }
        
        $fields = ['fin.total_paid', 'fin.balance_remaining', 'fin.computed_status', 'fin.late_fee_amount'];
        if(in_array('interest_amount', $available_columns)){
            $fields[] = 'fin.interest_amount';
        }
        if(in_array('arrears_amount', $available_columns)){
            $fields[] = 'fin.arrears_amount';
        }
        if(in_array('total_balance_due', $available_columns)){
            $fields[] = 'fin.total_balance_due';
        }
        
        $fields_str = implode(', ', $fields);
        
        $invoices = $this->conn->query("SELECT i.*, c.firstname, c.lastname, c.middlename, c.email,
                                               {$fields_str},
                                               COALESCE(rc.latest_receipt_date, fin.payment_date) as receipt_date,
                                               COALESCE(rc.receipt_count, 0) as receipt_count
                                       FROM invoices i
                                       INNER JOIN client_list c ON i.customer_id = c.id
                                       LEFT JOIN invoice_financials fin ON fin.id = i.id
                                       LEFT JOIN (
                                            SELECT invoice_id,
                                                   COUNT(*) as receipt_count,
                                                   MAX(issued_at) as latest_receipt_date
                                            FROM receipts
                                            GROUP BY invoice_id
                                       ) rc ON rc.invoice_id = i.id
                                       WHERE {$where}
                                       ORDER BY i.generated_at DESC");
        $result = [];
        while($invoice = $invoices->fetch_assoc()) {
            // Set defaults for missing columns
            if(!isset($invoice['interest_amount'])) $invoice['interest_amount'] = 0;
            if(!isset($invoice['arrears_amount'])) $invoice['arrears_amount'] = 0;
            if(!isset($invoice['total_balance_due'])){
                $balance = floatval($invoice['balance_remaining'] ?? 0);
                $interest = floatval($invoice['interest_amount'] ?? 0);
                $late_fee = floatval($invoice['late_fee_amount'] ?? 0);
                $arrears = floatval($invoice['arrears_amount'] ?? 0);
                $invoice['total_balance_due'] = $balance + $interest + $late_fee + $arrears;
            }
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
                $transaction_type = $_POST['transaction_type'] ?? null;
                $result = $invoice->createInvoiceFromOrder($_POST['order_id'], $_POST['staff_id'], $transaction_type);
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
                try {
                    $result = $invoice->getInvoice($_GET['invoice_id']);
                    if($result) {
                        echo json_encode(['status' => 'success', 'data' => $result]);
                    } else {
                        echo json_encode(['status' => 'error', 'msg' => 'Invoice not found']);
                    }
                } catch(Exception $e) {
                    error_log("Error getting invoice: " . $e->getMessage());
                    echo json_encode(['status' => 'error', 'msg' => 'Error loading invoice: ' . $e->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Invoice ID is required']);
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
