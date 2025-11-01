<?php
require_once dirname(__DIR__) . '/config.php';

class InstallmentManager extends DBConnection {
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
     * Get installment plan details
     */
    public function getInstallmentPlan($plan_id) {
        $result = $this->conn->query("SELECT * FROM installment_plans WHERE id = '{$plan_id}' AND status = 'active'");
        return $result->fetch_assoc();
    }
    
    /**
     * Get all active installment plans
     */
    public function getAllActivePlans() {
        $result = $this->conn->query("SELECT * FROM installment_plans WHERE status = 'active' ORDER BY number_of_installments");
        $plans = [];
        while ($row = $result->fetch_assoc()) {
            $plans[] = $row;
        }
        return $plans;
    }
    
    /**
     * Create new installment contract from invoice
     */
    public function createInstallmentContract($invoice_id, $plan_id) {
        try {
            // Get invoice details
            $invoice = $this->conn->query("SELECT * FROM invoices WHERE id = '{$invoice_id}'")->fetch_assoc();
            if (!$invoice) {
                throw new Exception("Invoice not found");
            }
            
            // Check if invoice already has a contract
            $existing = $this->conn->query("SELECT id FROM installment_contracts WHERE invoice_id = '{$invoice_id}'")->num_rows;
            if ($existing > 0) {
                throw new Exception("Installment contract already exists for this invoice");
            }
            
            // Get installment plan details
            $plan = $this->getInstallmentPlan($plan_id);
            if (!$plan) {
                throw new Exception("Installment plan not found or inactive");
            }
            
            // Calculate amounts
            $total_amount = $invoice['total_amount'];
            $down_payment = ($total_amount * $plan['down_payment_percentage']) / 100;
            $remaining_balance = $total_amount - $down_payment;
            
            // Calculate installment amount with interest
            $installment_amount = $this->calculateInstallmentAmount(
                $remaining_balance, 
                $plan['interest_rate'], 
                $plan['number_of_installments']
            );
            
            // Generate contract number
            $year = date('Y');
            $last_contract = $this->conn->query(
                "SELECT contract_number FROM installment_contracts 
                WHERE contract_number LIKE 'CONTRACT-{$year}-%' 
                ORDER BY id DESC LIMIT 1"
            )->fetch_assoc();
            
            $contract_number = "CONTRACT-{$year}-" . str_pad(
                $last_contract ? (intval(substr($last_contract['contract_number'], -4)) + 1) : 1, 
                4, 
                '0', 
                STR_PAD_LEFT
            );
            
            // Start transaction
            $this->conn->begin_transaction();
            
            // Create contract
            $contract_sql = "INSERT INTO installment_contracts 
                            (contract_number, invoice_id, customer_id, installment_plan_id, total_amount, 
                             down_payment_amount, remaining_balance, start_date, end_date) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL ? MONTH))";
            
            $stmt = $this->conn->prepare($contract_sql);
            $stmt->bind_param(
                "siiidddi", 
                $contract_number, 
                $invoice_id, 
                $invoice['customer_id'], 
                $plan_id, 
                $total_amount, 
                $down_payment, 
                $remaining_balance, 
                $plan['number_of_installments']
            );
            $stmt->execute();
            $contract_id = $stmt->insert_id;
            
            // Create installment schedule
            $this->createInstallmentSchedule($contract_id, $installment_amount, $plan['number_of_installments'], $remaining_balance, $plan['interest_rate']);
            
            // Update invoice payment type to installment
            $this->conn->query("UPDATE invoices SET payment_type = 'installment' WHERE id = '{$invoice_id}'");
            
            $this->conn->commit();
            return [
                'status' => 'success',
                'msg' => 'Installment contract created successfully',
                'contract_id' => $contract_id,
                'contract_number' => $contract_number
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            return ['status' => 'error', 'msg' => $e->getMessage()];
        }
    }
    
    /**
     * Create installment schedule
     */
    private function createInstallmentSchedule($contract_id, $installment_amount, $num_installments, $total_principal, $interest_rate) {
        $principal_per_installment = $total_principal / $num_installments;
        $interest_per_installment = ($interest_rate > 0) ? ($total_principal * $interest_rate) / 100 / $num_installments : 0;
        
        // If interest is included in installment amount, recalculate
        if ($interest_rate > 0) {
            $monthly_rate = $interest_rate / 100 / 12;
            $monthly_payment = $total_principal * ($monthly_rate * pow(1 + $monthly_rate, $num_installments)) / (pow(1 + $monthly_rate, $num_installments) - 1);
            $installment_amount = round($monthly_payment, 2);
            $total_interest = ($installment_amount * $num_installments) - $total_principal;
            $interest_per_installment = $total_interest / $num_installments;
        }
        
        for ($i = 1; $i <= $num_installments; $i++) {
            $due_date = date('Y-m-d', strtotime("+{$i} months"));
            
            $sql = "INSERT INTO installment_schedule 
                    (contract_id, installment_number, due_date, amount_due, principal_amount, interest_amount) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "iisddd", 
                $contract_id, 
                $i, 
                $due_date, 
                $installment_amount, 
                $principal_per_installment, 
                $interest_per_installment
            );
            $stmt->execute();
        }
    }
    
    /**
     * Calculate installment amount with interest
     */
    private function calculateInstallmentAmount($principal, $interest_rate, $num_installments) {
        if ($interest_rate == 0) {
            return $principal / $num_installments;
        }
        
        $monthly_rate = $interest_rate / 100 / 12;
        $installment = $principal * $monthly_rate * pow(1 + $monthly_rate, $num_installments) 
                      / (pow(1 + $monthly_rate, $num_installments) - 1);
        
        return round($installment, 2);
    }
    
    /**
     * Get contract details
     */
    public function getContract($contract_id) {
        $result = $this->conn->query(
            "SELECT ic.*, ip.plan_name, ip.number_of_installments,
                    c.firstname, c.lastname, c.email, c.contact,
                    i.invoice_number, i.transaction_type
             FROM installment_contracts ic
             JOIN installment_plans ip ON ic.installment_plan_id = ip.id
             JOIN invoices i ON ic.invoice_id = i.id
             JOIN client_list c ON ic.customer_id = c.id
             WHERE ic.id = '{$contract_id}'"
        );
        return $result->fetch_assoc();
    }
    
    /**
     * Get contract schedule
     */
    public function getContractSchedule($contract_id) {
        $result = $this->conn->query(
            "SELECT * FROM installment_schedule 
            WHERE contract_id = '{$contract_id}' 
            ORDER BY installment_number"
        );
        $schedule = [];
        while ($row = $result->fetch_assoc()) {
            $schedule[] = $row;
        }
        return $schedule;
    }
    
    /**
     * Get customer contracts
     */
    public function getCustomerContracts($customer_id) {
        $result = $this->conn->query(
            "SELECT ic.*, ip.plan_name, ip.number_of_installments,
                    i.invoice_number, i.transaction_type
             FROM installment_contracts ic
             JOIN installment_plans ip ON ic.installment_plan_id = ip.id
             JOIN invoices i ON ic.invoice_id = i.id
             WHERE ic.customer_id = '{$customer_id}'
             ORDER BY ic.created_at DESC"
        );
        $contracts = [];
        while ($row = $result->fetch_assoc()) {
            $contracts[] = $row;
        }
        return $contracts;
    }
    
    /**
     * Check and update overdue installments
     */
    public function updateOverdueInstallments() {
        $today = date('Y-m-d');
        $this->conn->query(
            "UPDATE installment_schedule 
            SET status = 'overdue' 
            WHERE due_date < '{$today}' AND status = 'pending'"
        );
    }
    
    /**
     * Update contract status
     */
    private function checkContractCompletion($contract_id) {
        $result = $this->conn->query(
            "SELECT remaining_balance, status 
            FROM installment_contracts 
            WHERE id = '{$contract_id}'"
        )->fetch_assoc();
        
        if ($result['remaining_balance'] <= 0 && $result['status'] == 'active') {
            $this->conn->query(
                "UPDATE installment_contracts 
                SET status = 'completed' 
                WHERE id = '{$contract_id}'"
            );
        }
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $manager = new InstallmentManager();
    
    switch ($_GET['action']) {
        case 'get_plans':
            $result = $manager->getAllActivePlans();
            echo json_encode(['status' => 'success', 'data' => $result]);
            break;
            
        case 'get_contract':
            if (isset($_GET['contract_id'])) {
                $result = $manager->getContract($_GET['contract_id']);
                if ($result) {
                    $result['schedule'] = $manager->getContractSchedule($_GET['contract_id']);
                    echo json_encode(['status' => 'success', 'data' => $result]);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Contract not found']);
                }
            }
            break;
            
        case 'get_customer_contracts':
            if (isset($_GET['customer_id'])) {
                $result = $manager->getCustomerContracts($_GET['customer_id']);
                echo json_encode(['status' => 'success', 'data' => $result]);
            }
            break;
            
        case 'create_contract':
            if (isset($_POST['invoice_id']) && isset($_POST['plan_id'])) {
                $result = $manager->createInstallmentContract($_POST['invoice_id'], $_POST['plan_id']);
                echo json_encode($result);
            }
            break;
    }
}

