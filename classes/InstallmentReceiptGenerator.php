<?php
require_once dirname(__DIR__) . '/config.php';

class InstallmentReceiptGenerator extends DBConnection {
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
     * Generate installment payment receipt HTML
     */
    public function generatePaymentReceipt($payment_id) {
        $sql = "SELECT 
                ip.*,
                ic.contract_number,
                ic.total_amount,
                ic.down_payment_amount,
                ic.remaining_balance,
                isch.installment_number,
                isch.due_date,
                isch.amount_due,
                isch.late_fee,
                c.firstname, c.lastname, c.email, c.contact,
                s.business_name, s.address, s.contact_info, s.email as business_email
                FROM installment_payments ip
                JOIN installment_schedule isch ON ip.schedule_id = isch.id
                JOIN installment_contracts ic ON ip.contract_id = ic.id
                JOIN client_list c ON ic.customer_id = c.id
                JOIN system_info s ON 1=1
                WHERE ip.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment_data = $result->fetch_assoc();
        
        if (!$payment_data) {
            return null;
        }
        
        return $this->formatReceipt($payment_data);
    }
    
    /**
     * Format receipt HTML
     */
    private function formatReceipt($data) {
        $receipt = '<div class="receipt-container" style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; border: 2px solid #333;">';
        
        // Header
        $receipt .= '<div class="receipt-header" style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px;">';
        $receipt .= '<h1 style="color: #e74c3c; margin: 0;">' . $data['business_name'] . '</h1>';
        $receipt .= '<p style="margin: 5px 0;">' . $data['address'] . '</p>';
        $receipt .= '<p style="margin: 5px 0;">Contact: ' . $data['contact_info'] . ' | Email: ' . $data['business_email'] . '</p>';
        $receipt .= '</div>';
        
        // Title
        $receipt .= '<div class="receipt-title" style="text-align: center; margin-bottom: 30px;">';
        $receipt .= '<h2 style="color: #2c3e50; margin: 0;">INSTALLMENT PAYMENT RECEIPT</h2>';
        $receipt .= '<p style="margin: 5px 0;">Date: ' . date('F d, Y', strtotime($data['payment_date'])) . '</p>';
        $receipt .= '</div>';
        
        // Receipt Details
        $receipt .= '<div class="receipt-details" style="margin-bottom: 30px;">';
        $receipt .= '<table style="width: 100%; border-collapse: collapse;">';
        $receipt .= '<tr>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd; width: 30%;"><strong>Receipt Number:</strong></td>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['receipt_number']) . '</td>';
        $receipt .= '</tr>';
        $receipt .= '<tr>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Contract Number:</strong></td>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['contract_number']) . '</td>';
        $receipt .= '</tr>';
        $receipt .= '<tr>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Customer Name:</strong></td>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['firstname'] . ' ' . $data['lastname']) . '</td>';
        $receipt .= '</tr>';
        $receipt .= '</table>';
        $receipt .= '</div>';
        
        // Payment Details
        $receipt .= '<div class="payment-details" style="margin-bottom: 30px;">';
        $receipt .= '<h3 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Payment Details</h3>';
        $receipt .= '<table style="width: 100%; border-collapse: collapse;">';
        $receipt .= '<tr>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd; width: 30%;"><strong>Installment Number:</strong></td>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['installment_number']) . '</td>';
        $receipt .= '</tr>';
        $receipt .= '<tr>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Due Date:</strong></td>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;">' . date('F d, Y', strtotime($data['due_date'])) . '</td>';
        $receipt .= '</tr>';
        $receipt .= '<tr>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Amount Due:</strong></td>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;">₱ ' . number_format($data['amount_due'], 2) . '</td>';
        $receipt .= '</tr>';
        
        if ($data['late_fee'] > 0) {
            $receipt .= '<tr>';
            $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Late Fee:</strong></td>';
            $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;">₱ ' . number_format($data['late_fee'], 2) . '</td>';
            $receipt .= '</tr>';
        }
        
        $receipt .= '<tr>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Amount Paid:</strong></td>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd; font-weight: bold; color: #27ae60;">₱ ' . number_format($data['amount_paid'], 2) . '</td>';
        $receipt .= '</tr>';
        $receipt .= '<tr>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Payment Method:</strong></td>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;">' . ucfirst(str_replace('_', ' ', $data['payment_method'])) . '</td>';
        $receipt .= '</tr>';
        $receipt .= '<tr>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Remaining Balance:</strong></td>';
        $receipt .= '<td style="padding: 8px; border: 1px solid #ddd;">₱ ' . number_format($data['remaining_balance'], 2) . '</td>';
        $receipt .= '</tr>';
        $receipt .= '</table>';
        $receipt .= '</div>';
        
        // Footer
        $receipt .= '<div class="receipt-footer" style="text-align: center; border-top: 2px solid #333; padding-top: 20px; margin-top: 30px;">';
        $receipt .= '<p style="margin: 5px 0;"><strong>Thank you for your payment!</strong></p>';
        $receipt .= '<p style="margin: 5px 0; font-size: 12px; color: #7f8c8d;">';
        $receipt .= 'This is a computer-generated receipt. No signature required.';
        $receipt .= '</p>';
        $receipt .= '</div>';
        
        $receipt .= '</div>';
        
        return $receipt;
    }
    
    /**
     * Generate installment schedule for customer
     */
    public function generateInstallmentSchedule($contract_id) {
        $sql = "SELECT 
                ic.contract_number,
                ic.total_amount,
                ic.down_payment_amount,
                ic.remaining_balance,
                ic.start_date,
                ic.end_date,
                ic.status,
                ip.plan_name,
                ip.number_of_installments,
                ip.interest_rate,
                c.firstname, c.lastname, c.email, c.contact,
                s.business_name, s.address, s.contact_info
                FROM installment_contracts ic
                JOIN installment_plans ip ON ic.installment_plan_id = ip.id
                JOIN client_list c ON ic.customer_id = c.id
                JOIN system_info s ON 1=1
                WHERE ic.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $contract_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $contract = $result->fetch_assoc();
        
        if (!$contract) {
            return null;
        }
        
        // Get installment schedule
        $schedule_sql = "SELECT * FROM installment_schedule WHERE contract_id = ? ORDER BY installment_number";
        $schedule_stmt = $this->conn->prepare($schedule_sql);
        $schedule_stmt->bind_param("i", $contract_id);
        $schedule_stmt->execute();
        $schedule_result = $schedule_stmt->get_result();
        $schedule = [];
        
        while ($row = $schedule_result->fetch_assoc()) {
            $schedule[] = $row;
        }
        
        return $this->formatSchedule($contract, $schedule);
    }
    
    /**
     * Format schedule HTML
     */
    private function formatSchedule($contract, $schedule) {
        $html = '<div class="schedule-container" style="font-family: Arial, sans-serif; max-width: 900px; margin: 0 auto; padding: 20px; border: 2px solid #333;">';
        
        // Header
        $html .= '<div class="schedule-header" style="text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px;">';
        $html .= '<h1 style="color: #e74c3c; margin: 0;">' . htmlspecialchars($contract['business_name']) . '</h1>';
        $html .= '<p style="margin: 5px 0;">' . htmlspecialchars($contract['address']) . '</p>';
        $html .= '<h2 style="color: #2c3e50; margin: 20px 0 0 0;">INSTALLMENT PAYMENT SCHEDULE</h2>';
        $html .= '</div>';
        
        // Contract Details
        $html .= '<div class="contract-details" style="margin-bottom: 30px;">';
        $html .= '<table style="width: 100%; border-collapse: collapse;">';
        $html .= '<tr>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd; width: 30%;"><strong>Contract Number:</strong></td>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($contract['contract_number']) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Customer:</strong></td>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($contract['firstname'] . ' ' . $contract['lastname']) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Plan:</strong></td>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($contract['plan_name']) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Total Amount:</strong></td>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;">₱ ' . number_format($contract['total_amount'], 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Down Payment:</strong></td>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;">₱ ' . number_format($contract['down_payment_amount'], 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Remaining Balance:</strong></td>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;">₱ ' . number_format($contract['remaining_balance'], 2) . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;"><strong>Status:</strong></td>';
        $html .= '<td style="padding: 8px; border: 1px solid #ddd;">' . ucfirst($contract['status']) . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '</div>';
        
        // Schedule Table
        $html .= '<div class="schedule-table">';
        $html .= '<h3 style="color: #2c3e50; border-bottom: 1px solid #ddd; padding-bottom: 10px;">Payment Schedule</h3>';
        $html .= '<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f8f9fa;">';
        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: center;">#</th>';
        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Due Date</th>';
        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Amount Due</th>';
        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Principal</th>';
        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Interest</th>';
        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: right;">Late Fee</th>';
        $html .= '<th style="padding: 10px; border: 1px solid #ddd; text-align: center;">Status</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        
        foreach ($schedule as $item) {
            $status_class = '';
            $status_text = '';
            switch($item['status']) {
                case 'paid':
                    $status_class = 'badge badge-success';
                    $status_text = 'Paid';
                    break;
                case 'overdue':
                    $status_class = 'badge badge-danger';
                    $status_text = 'Overdue';
                    break;
                case 'partial':
                    $status_class = 'badge badge-info';
                    $status_text = 'Partial';
                    break;
                default:
                    $status_class = 'badge badge-warning';
                    $status_text = 'Pending';
                    break;
            }
            
            $html .= '<tr>';
            $html .= '<td style="padding: 10px; border: 1px solid #ddd; text-align: center;">' . htmlspecialchars($item['installment_number']) . '</td>';
            $html .= '<td style="padding: 10px; border: 1px solid #ddd; text-align: center;">' . date('M d, Y', strtotime($item['due_date'])) . '</td>';
            $html .= '<td style="padding: 10px; border: 1px solid #ddd; text-align: right;">₱ ' . number_format($item['amount_due'], 2) . '</td>';
            $html .= '<td style="padding: 10px; border: 1px solid #ddd; text-align: right;">₱ ' . number_format($item['principal_amount'], 2) . '</td>';
            $html .= '<td style="padding: 10px; border: 1px solid #ddd; text-align: right;">₱ ' . number_format($item['interest_amount'], 2) . '</td>';
            $html .= '<td style="padding: 10px; border: 1px solid #ddd; text-align: right;">₱ ' . number_format($item['late_fee'], 2) . '</td>';
            $html .= '<td style="padding: 10px; border: 1px solid #ddd; text-align: center;"><span class="' . $status_class . '">' . $status_text . '</span></td>';
            $html .= '</tr>';
        }
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';
        
        // Footer
        $html .= '<div class="schedule-footer" style="text-align: center; border-top: 2px solid #333; padding-top: 20px; margin-top: 30px;">';
        $html .= '<p style="margin: 5px 0; font-size: 12px; color: #7f8c8d;">';
        $html .= 'Generated on ' . date('F d, Y') . '. Please keep this document for your records.';
        $html .= '</p>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    $generator = new InstallmentReceiptGenerator();
    
    switch ($_GET['action']) {
        case 'generate_receipt':
            if (isset($_GET['payment_id'])) {
                $result = $generator->generatePaymentReceipt($_GET['payment_id']);
                if ($result) {
                    echo json_encode(['status' => 'success', 'html' => $result]);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Payment not found']);
                }
            }
            break;
            
        case 'generate_schedule':
            if (isset($_GET['contract_id'])) {
                $result = $generator->generateInstallmentSchedule($_GET['contract_id']);
                if ($result) {
                    echo json_encode(['status' => 'success', 'html' => $result]);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Contract not found']);
                }
            }
            break;
    }
}

