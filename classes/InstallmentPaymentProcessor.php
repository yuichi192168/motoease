<?php
require_once dirname(__DIR__) . '/config.php';

class InstallmentPaymentProcessor extends DBConnection {
    private $settings;
    
    public function __construct() {
        global $_settings;
        $this->settings = $_settings;
        parent::__construct();
    }
    
    public function __destruct() {
        parent::__destruct();
    }
    
    /**
     * Process installment payment
     */
    public function processPayment($schedule_id, $amount_paid, $payment_method, $notes = '', $created_by = null) {
        try {
            $this->conn->begin_transaction();
            
            // Get schedule details
            $schedule = $this->getScheduleDetails($schedule_id);
            if (!$schedule) {
                throw new Exception("Installment schedule not found");
            }
            
            if ($schedule['status'] == 'paid') {
                throw new Exception("This installment is already fully paid");
            }
            
            // Generate payment reference and receipt number
            $year = date('Y');
            $timestamp = date('YmdHis');
            
            $last_payment = $this->conn->query(
                "SELECT payment_reference FROM installment_payments 
                WHERE payment_reference LIKE 'PAY-{$year}-%' 
                ORDER BY id DESC LIMIT 1"
            )->fetch_assoc();
            
            $payment_reference = "PAY-{$year}-" . str_pad(
                $last_payment ? (intval(substr($last_payment['payment_reference'], -5)) + 1) : 1, 
                5, 
                '0', 
                STR_PAD_LEFT
            );
            
            $last_receipt = $this->conn->query(
                "SELECT receipt_number FROM installment_payments 
                WHERE receipt_number LIKE 'INST-RCPT-{$year}-%' 
                ORDER BY id DESC LIMIT 1"
            )->fetch_assoc();
            
            $receipt_number = "INST-RCPT-{$year}-" . str_pad(
                $last_receipt ? (intval(substr($last_receipt['receipt_number'], -5)) + 1) : 1, 
                5, 
                '0', 
                STR_PAD_LEFT
            );
            
            // Record payment
            $payment_sql = "INSERT INTO installment_payments 
                           (payment_reference, schedule_id, contract_id, amount_paid, 
                            payment_date, payment_method, receipt_number, notes, created_by) 
                           VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($payment_sql);
            $stmt->bind_param(
                "siidsssi", 
                $payment_reference, 
                $schedule_id, 
                $schedule['contract_id'], 
                $amount_paid, 
                $payment_method, 
                $receipt_number, 
                $notes, 
                $created_by
            );
            $stmt->execute();
            $payment_id = $stmt->insert_id;
            
            // Update schedule status
            $this->updateScheduleStatus($schedule_id, $amount_paid, $schedule['amount_due']);
            
            // Update contract balance
            $this->updateContractBalance($schedule['contract_id'], $amount_paid);
            
            $this->conn->commit();
            
            return [
                'status' => 'success',
                'msg' => 'Payment processed successfully',
                'payment_id' => $payment_id,
                'receipt_number' => $receipt_number,
                'payment_reference' => $payment_reference
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }
    
    /**
     * Get schedule details
     */
    private function getScheduleDetails($schedule_id) {
        $result = $this->conn->query(
            "SELECT * FROM installment_schedule WHERE id = '{$schedule_id}'"
        );
        return $result->fetch_assoc();
    }
    
    /**
     * Update schedule status based on payment
     */
    private function updateScheduleStatus($schedule_id, $amount_paid, $amount_due) {
        $status = 'partial';
        $paid_amount = $amount_paid;
        
        if ($amount_paid >= $amount_due) {
            $status = 'paid';
            $paid_amount = $amount_due;
        }
        
        $sql = "UPDATE installment_schedule 
                SET status = ?, paid_amount = ?, paid_date = NOW() 
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdi", $status, $paid_amount, $schedule_id);
        $stmt->execute();
    }
    
    /**
     * Update contract balance
     */
    private function updateContractBalance($contract_id, $amount_paid) {
        $sql = "UPDATE installment_contracts 
                SET remaining_balance = remaining_balance - ? 
                WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("di", $amount_paid, $contract_id);
        $stmt->execute();
        
        // Check if contract is completed
        $this->checkContractCompletion($contract_id);
    }
    
    /**
     * Check if contract is fully paid
     */
    private function checkContractCompletion($contract_id) {
        $result = $this->conn->query(
            "SELECT remaining_balance, status 
            FROM installment_contracts 
            WHERE id = '{$contract_id}'"
        )->fetch_assoc();
        
        if ($result && $result['remaining_balance'] <= 0 && $result['status'] == 'active') {
            $this->conn->query(
                "UPDATE installment_contracts 
                SET status = 'completed' 
                WHERE id = '{$contract_id}'"
            );
            
            // Update related invoice status to paid
            $contract = $this->conn->query(
                "SELECT invoice_id FROM installment_contracts WHERE id = '{$contract_id}'"
            )->fetch_assoc();
            
            if ($contract) {
                $this->conn->query(
                    "UPDATE invoices 
                    SET payment_status = 'paid' 
                    WHERE id = '{$contract['invoice_id']}'"
                );
            }
        }
    }
    
    /**
     * Get payment history for contract
     */
    public function getContractPayments($contract_id) {
        $result = $this->conn->query(
            "SELECT ip.*, isch.installment_number, isch.due_date,
                    u.firstname as staff_firstname, u.lastname as staff_lastname
             FROM installment_payments ip
             JOIN installment_schedule isch ON ip.schedule_id = isch.id
             LEFT JOIN users u ON ip.created_by = u.id
             WHERE ip.contract_id = '{$contract_id}'
             ORDER BY ip.payment_date DESC"
        );
        
        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[] = $row;
        }
        return $payments;
    }
    
    /**
     * Get overdue installments
     */
    public function getOverdueInstallments($contract_id = null) {
        $where = "isch.due_date < CURDATE() AND isch.status IN ('pending', 'partial')";
        if ($contract_id) {
            $where .= " AND isch.contract_id = '{$contract_id}'";
        }
        
        $result = $this->conn->query(
            "SELECT isch.*, ic.contract_number, c.firstname, c.lastname, c.email
             FROM installment_schedule isch
             JOIN installment_contracts ic ON isch.contract_id = ic.id
             JOIN client_list c ON ic.customer_id = c.id
             WHERE {$where}
             ORDER BY isch.due_date ASC"
        );
        
        $overdue = [];
        while ($row = $result->fetch_assoc()) {
            $overdue[] = $row;
        }
        return $overdue;
    }
    
    /**
     * Apply late fees
     */
    public function applyLateFees() {
        $today = date('Y-m-d');
        $result = $this->conn->query(
            "SELECT id, amount_due, due_date 
            FROM installment_schedule 
            WHERE due_date < '{$today}' 
            AND status IN ('pending', 'partial', 'overdue')"
        );
        
        $updated = 0;
        while ($row = $result->fetch_assoc()) {
            $days_overdue = (strtotime($today) - strtotime($row['due_date'])) / 86400;
            $late_fee = $row['amount_due'] * 0.05 * $days_overdue; // 5% per day
            
            $this->conn->query(
                "UPDATE installment_schedule 
                SET late_fee = '{$late_fee}', status = 'overdue' 
                WHERE id = '{$row['id']}'"
            );
            $updated++;
        }
        
        return $updated;
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $processor = new InstallmentPaymentProcessor();
    
    switch ($_GET['action']) {
        case 'process_payment':
            if (isset($_POST['schedule_id']) && isset($_POST['amount_paid'])) {
                $result = $processor->processPayment(
                    $_POST['schedule_id'],
                    $_POST['amount_paid'],
                    $_POST['payment_method'] ?? 'cash',
                    $_POST['notes'] ?? '',
                    $_POST['created_by'] ?? null
                );
                echo json_encode($result);
            }
            break;
            
        case 'get_payments':
            if (isset($_GET['contract_id'])) {
                $result = $processor->getContractPayments($_GET['contract_id']);
                echo json_encode(['status' => 'success', 'data' => $result]);
            }
            break;
            
        case 'get_overdue':
            $result = $processor->getOverdueInstallments($_GET['contract_id'] ?? null);
            echo json_encode(['status' => 'success', 'data' => $result]);
            break;
    }
}

