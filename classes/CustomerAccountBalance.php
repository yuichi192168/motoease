<?php
/**
 * Customer Account Balance Management Class
 * Handles account creation, payment tracking, late fee calculation
 */
class CustomerAccountBalance {
    private $conn;
    private $notifier = null;
    
    public function __construct($connection) {
        if(!$connection){
            // If no connection provided, try to get from global
            global $conn;
            $this->conn = $conn;
        } else {
            $this->conn = $connection;
        }
    }
    
    /**
     * Create customer account record when order is placed
     * @param int $client_id
     * @param int $order_id
     * @param int $invoice_id (optional)
     * @param int $contract_id (optional)
     * @param string $item_purchased
     * @param float $total_price (without VAT)
     * @param float $downpayment_amount
     * @param int $installment_plan_months
     * @param float $monthly_payment_amount
     * @return int|false Account ID or false on failure
     */
    public function createAccount($client_id, $order_id, $item_purchased, $total_price, $downpayment_amount, $installment_plan_months = null, $monthly_payment_amount = null, $invoice_id = null, $contract_id = null) {
        /**
         * ACCOUNT BALANCE FORMULA:
         * ========================
         * total_cost = downpayment_amount + (monthly_payment_amount × installment_plan_months)
         * paid_amount = SUM of recorded transactions (downpayment + monthly payments)
         * remaining_balance = total_cost - paid_amount
         * 
         * Initial state (at account creation):
         * - paid_amount = 0.00 (no payments recorded yet)
         * - remaining_balance = total_cost (full amount outstanding until payments are recorded)
         * - downpayment_amount = stored as preference (not as paid until admin records it)
         */
        
        // Calculate total financed amount (applies to both installment and non-installment)
        $total_financed = $downpayment_amount + ($monthly_payment_amount && $installment_plan_months ? ($monthly_payment_amount * $installment_plan_months) : 0);
        
        // If no installment plan or monthly payment, use the original total_price
        if (!$installment_plan_months || !$monthly_payment_amount || $monthly_payment_amount <= 0) {
            $total_financed = $total_price;
        }
        
        // Store the financed total as the account total_price
        $account_total_price = $total_financed;
        
        // Initial state: no payments recorded yet
        $initial_paid = 0.00;
        // remaining_balance starts equal to total_price until payments are recorded
        $db_remaining_balance = $account_total_price;
        
        // If installment plan but no monthly payment provided, calculate it
        if ($installment_plan_months && !$monthly_payment_amount && $account_total_price > 0) {
            // For non-predefined amortization, calculate monthly from total
            // But we need downpayment separately, so monthly applies to (total - downpayment)
            $monthly_to_finance = $account_total_price - $downpayment_amount;
            if ($installment_plan_months > 0 && $monthly_to_finance > 0) {
                $monthly_payment_amount = $monthly_to_finance / $installment_plan_months;
            }
        }
        
        // Insert account record
        $stmt = $this->conn->prepare("
            INSERT INTO customer_account_balances 
            (client_id, order_id, invoice_id, contract_id, item_purchased, total_price, 
             downpayment_amount, paid_amount, remaining_balance, installment_plan_months, 
             monthly_payment_amount, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ");
        
        $stmt->bind_param("iiiisddddid", 
            $client_id, 
            $order_id, 
            $invoice_id, 
            $contract_id, 
            $item_purchased, 
            $account_total_price, 
            $downpayment_amount, 
            $initial_paid, 
            $db_remaining_balance, 
            $installment_plan_months, 
            $monthly_payment_amount
        );
        
        if (!$stmt->execute()) {
            error_log("Error creating account: " . $stmt->error);
            return false;
        }
        
        $account_id = $stmt->insert_id;
        $stmt->close();
        
        // Create monthly payment schedule if installment plan exists
        // The schedule should cover monthly payments only (downpayment is separate)
        if ($installment_plan_months && $monthly_payment_amount && $monthly_payment_amount > 0) {
            $schedule_total_balance = $monthly_payment_amount * $installment_plan_months;
            if ($schedule_total_balance > 0) {
                $this->createPaymentSchedule($account_id, $installment_plan_months, $monthly_payment_amount, $schedule_total_balance);
            }
        }
        
        return $account_id;
    }
    
    /**
     * Create monthly payment schedule
     * @param int $account_id
     * @param int $months
     * @param float $monthly_amount
     * @param float $total_balance
     * @return bool
     */
    private function createPaymentSchedule($account_id, $months, $monthly_amount, $total_balance) {
        $start_date = date('Y-m-d');
        $schedule_inserted = 0;
        
        // Calculate total expected from all monthly payments
        $total_monthly_payments = $monthly_amount * $months;
        
        // Calculate rounding difference (if any)
        $rounding_diff = $total_balance - $total_monthly_payments;
        
        for ($i = 1; $i <= $months; $i++) {
            // Calculate due date (first payment due 1 month after order)
            $due_date = date('Y-m-d', strtotime("+$i month", strtotime($start_date)));
            
            // Amount due for this installment
            $amount_due = $monthly_amount;
            
            // For the last payment, adjust if there's a rounding difference
            // This ensures the sum of all payments equals the total_balance
            if ($i == $months && abs($rounding_diff) > 0.01) {
                $amount_due = $monthly_amount + $rounding_diff;
            }
            
            // Initially, remaining_balance equals amount_due (nothing paid yet)
            // This represents how much is still owed for this specific installment
            $remaining_balance = $amount_due;
            
            $stmt = $this->conn->prepare("
                INSERT INTO customer_account_schedule 
                (account_id, installment_number, due_date, amount_due, remaining_balance, payment_status) 
                VALUES (?, ?, ?, ?, ?, 'Unpaid')
            ");
            
            $stmt->bind_param("iisdd", $account_id, $i, $due_date, $amount_due, $remaining_balance);
            
            if ($stmt->execute()) {
                $schedule_inserted++;
            }
            $stmt->close();
        }
        
        return $schedule_inserted > 0;
    }
    
    /**
     * Record a payment transaction
     * @param int $account_id
     * @param int|null $schedule_id (if paying specific month)
     * @param string $transaction_type
     * @param float $amount
     * @param string $payment_method
     * @param string|null $receipt_number
     * @param string|null $notes
     * @param int|null $processed_by (admin/staff ID)
     * @return int|false Transaction ID or false
     */
    public function recordTransaction($account_id, $schedule_id, $transaction_type, $amount, $payment_method = 'cash', $receipt_number = null, $notes = null, $processed_by = null, $transaction_date = null) {
        // Validate inputs
        if ($account_id <= 0) {
            error_log("Invalid account_id: {$account_id}");
            return false;
        }
        
        if ($amount <= 0) {
            error_log("Invalid amount: {$amount}");
            return false;
        }
        
        // Check if account exists
        $account = $this->getAccountInfo($account_id);
        if (!$account) {
            error_log("Account not found: {$account_id}");
            return false;
        }
        
        // allow explicit transaction_date (actual payment date) to be recorded
        $stmt = $this->conn->prepare("
            INSERT INTO customer_account_transactions 
            (account_id, schedule_id, transaction_type, amount, payment_method, receipt_number, notes, processed_by, transaction_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        if (!$stmt) {
            error_log("Error preparing transaction statement: " . $this->conn->error);
            return false;
        }
        
        // If transaction_date not provided, use current timestamp
        $transaction_date = $transaction_date ? $transaction_date : date('Y-m-d H:i:s');

        $stmt->bind_param("iisdsssis", $account_id, $schedule_id, $transaction_type, $amount, $payment_method, $receipt_number, $notes, $processed_by, $transaction_date);
        
        if (!$stmt->execute()) {
            error_log("Error recording transaction: " . $stmt->error . " | Account ID: {$account_id} | Amount: {$amount}");
            $stmt->close();
            return false;
        }
        
        $transaction_id = $stmt->insert_id;
        $stmt->close();
        
        // Update account balance
        if (!$this->updateAccountBalance($account_id, $amount)) {
            error_log("Failed to update account balance for account: {$account_id}");
            // Transaction was recorded but balance update failed - this is a critical error
            return false;
        }
        
        // If schedule_id provided, evaluate lateness based on provided transaction_date
        if ($schedule_id) {
            $schedule = $this->getScheduleInfo($schedule_id);
            if ($schedule) {
                // If a payment date was supplied and it's after due_date, mark as Late and apply fee
                if ($transaction_date) {
                    $due_ts = strtotime($schedule['due_date']);
                    $pay_ts = strtotime($transaction_date);
                    $days_diff = floor(($pay_ts - $due_ts) / (60 * 60 * 24));
                    // 7 or more days late -> apply late fee rule
                    if ($days_diff >= 7 && $schedule['payment_status'] != 'Paid') {
                        $account_info = $this->getAccountInfo($account_id);
                        $monthly_amount = isset($account_info['monthly_payment_amount']) ? floatval($account_info['monthly_payment_amount']) : floatval($schedule['amount_due']);
                        $late_fee_rate = 0.03;
                        $late_fee_amount = $monthly_amount * $late_fee_rate;

                        // Update schedule to include late fee
                        $update_stmt = $this->conn->prepare("
                            UPDATE customer_account_schedule 
                            SET late_fee = IFNULL(late_fee,0) + ?, 
                                remaining_balance = remaining_balance + ?, 
                                amount_due = amount_due + ?, 
                                payment_status = 'Late', 
                                updated_at = NOW() 
                            WHERE id = ? 
                        ");
                        $update_stmt->bind_param("dddi", $late_fee_amount, $late_fee_amount, $late_fee_amount, $schedule_id);
                        $update_stmt->execute();
                        $update_stmt->close();

                        // Increase account remaining balance to reflect new charge
                        $acc_stmt = $this->conn->prepare("
                            UPDATE customer_account_balances 
                            SET remaining_balance = remaining_balance + ?, 
                                updated_at = NOW() 
                            WHERE id = ? 
                        ");
                        $acc_stmt->bind_param("di", $late_fee_amount, $account_id);
                        $acc_stmt->execute();
                        $acc_stmt->close();

                        // Notify customer/admin about late fee
                        $this->createNotification(
                            $account_id, 
                            $schedule_id, 
                            $account_info['client_id'], 
                            'late_payment', 
                            'Late Payment Fee Applied', 
                            "A late fee of ₱" . number_format($late_fee_amount, 2) . " has been applied to your payment due on " . date('M d, Y', $due_ts) . "."
                        );
                    }
                }
            }

            // Update schedule payment using the transaction_date
            if (!$this->updateSchedulePayment($schedule_id, $amount, $transaction_date)) {
                error_log("Failed to update schedule payment for schedule: {$schedule_id}");
                // Non-critical
            }
        }
        
        // Create notification for payment received
        $account_info = $this->getAccountInfo($account_id);
        if ($account_info) {
            $this->createNotification(
                $account_id, 
                $schedule_id, 
                $account_info['client_id'], 
                'payment_received', 
                'Payment Received', 
                "Payment of ₱" . number_format($amount, 2) . " has been recorded for your account."
            );
            
            if($processed_by){
                $customer_name = trim(($account_info['firstname'] ?? '').' '.($account_info['lastname'] ?? '')) ?: 'Customer #'.$account_info['client_id'];
                $payload = [
                    'account_id' => $account_id,
                    'schedule_id' => $schedule_id,
                    'amount' => $amount,
                    'processed_by' => $processed_by
                ];
                $admin_message = "{$customer_name} payment of ₱" . number_format($amount, 2) . " recorded by staff.";
                $this->notifyAdminUsers('payment_update', 'Payment Recorded', $admin_message, $payload);
            }
        }
        
        return $transaction_id;
    }
    
    /**
     * Add payment to account (manual by admin)
     * @param int $account_id
     * @param int|null $schedule_id (specific month, or null for general payment)
     * @param float $amount
     * @param string $payment_method
     * @param string|null $receipt_number
     * @param string|null $notes
     * @param int|null $processed_by
     * @return bool
     */
    public function addPayment($account_id, $schedule_id, $amount, $payment_method = 'cash', $receipt_number = null, $notes = null, $processed_by = null, $transaction_date = null, $skip_late_fee_check = false) {
        // Check and apply late fees before processing payment (unless already checked)
        if(!$skip_late_fee_check) {
            $this->checkAndApplyLateFees($account_id);
        }

        $transaction_type = $schedule_id ? 'monthly_payment' : 'monthly_payment';

        return $this->recordTransaction($account_id, $schedule_id, $transaction_type, $amount, $payment_method, $receipt_number, $notes, $processed_by, $transaction_date) !== false;
    }
    
    /**
     * Update account balance after payment
     * @param int $account_id
     * @param float $amount
     * @return bool
     */
    private function updateAccountBalance($account_id, $amount) {
        $stmt = $this->conn->prepare("
            UPDATE customer_account_balances 
            SET paid_amount = paid_amount + ?, 
                remaining_balance = GREATEST(0, remaining_balance - ?),
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->bind_param("ddi", $amount, $amount, $account_id);
        $result = $stmt->execute();
        $stmt->close();
        
        // Check if account is fully paid
        $account = $this->getAccountInfo($account_id);
        if ($account && $account['remaining_balance'] <= 0) {
            $this->updateAccountStatus($account_id, 'paid');
        }
        
        return $result;
    }
    
    /**
     * Update schedule payment status
     * @param int $schedule_id
     * @param float $amount
     * @return bool
     */
    private function updateSchedulePayment($schedule_id, $amount, $payment_date = null) {
        $schedule = $this->getScheduleInfo($schedule_id);
        if (!$schedule) return false;
        
        $new_paid = $schedule['paid_amount'] + $amount;
        $new_remaining = $schedule['remaining_balance'] - $amount;
        $status = 'Partial';
        
        if ($new_remaining <= 0) {
            $status = 'Paid';
            $new_remaining = 0;
        } elseif ($schedule['payment_status'] == 'Late' && $new_remaining > 0) {
            $status = 'Late';
        }
        
        // Set paid_date to provided payment_date if given, otherwise leave as-is
        $stmt = $this->conn->prepare("
            UPDATE customer_account_schedule 
            SET paid_amount = ?, 
                remaining_balance = ?,
                payment_status = ?,
                paid_date = IFNULL(?, paid_date),
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->bind_param("ddssi", $new_paid, $new_remaining, $status, $payment_date, $schedule_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Check and apply late fees (3% if payment is >7 days late)
     * RULE: If payment is 7+ days late, apply 3% fee based on MONTHLY AMORTIZATION amount
     * @param int $account_id
     * @return bool
     */
    public function checkAndApplyLateFees($account_id) {
        $today = date('Y-m-d');
        $late_fee_rate = 0.03; // 3%
        
        // Get account info to retrieve monthly payment amount
        $account_info = $this->getAccountInfo($account_id);
        if (!$account_info || !$account_info['monthly_payment_amount']) {
            return false;
        }
        
        $monthly_amount = floatval($account_info['monthly_payment_amount']);
        
        // Get all unpaid or partial schedules
        $stmt = $this->conn->prepare("
            SELECT id, due_date, amount_due, remaining_balance, late_fee, payment_status
            FROM customer_account_schedule 
            WHERE account_id = ? 
            AND payment_status IN ('Unpaid', 'Partial', 'Late')
            AND remaining_balance > 0
        ");
        
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $updated = false;
        
        while ($schedule = $result->fetch_assoc()) {
            $due_date = strtotime($schedule['due_date']);
            $current_date = strtotime($today);
            $days_overdue = floor(($current_date - $due_date) / (60 * 60 * 24));
            
            // If 7 or more days late and no late fee applied yet
            if ($days_overdue >= 7 && $schedule['late_fee'] == 0 && $schedule['payment_status'] != 'Paid') {
                // CORRECTED: Late fee = 3% of MONTHLY AMORTIZATION (not remaining_balance)
                $late_fee_amount = $monthly_amount * $late_fee_rate;
                
                // Update schedule with late fee
                $update_stmt = $this->conn->prepare("
                    UPDATE customer_account_schedule 
                    SET late_fee = ?,
                        remaining_balance = remaining_balance + ?,
                        amount_due = amount_due + ?,
                        payment_status = 'Late',
                        updated_at = NOW()
                    WHERE id = ?
                ");
                
                $update_stmt->bind_param("dddi", $late_fee_amount, $late_fee_amount, $late_fee_amount, $schedule['id']);
                
                if ($update_stmt->execute()) {
                    // Update account balance to include late fee
                    $acc_stmt = $this->conn->prepare("
                        UPDATE customer_account_balances 
                        SET remaining_balance = remaining_balance + ?,
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    
                    $acc_stmt->bind_param("di", $late_fee_amount, $account_id);
                    $acc_stmt->execute();
                    $acc_stmt->close();
                    
                    // Record late fee transaction
                    $this->recordTransaction(
                        $account_id, 
                        $schedule['id'], 
                        'late_fee', 
                        $late_fee_amount, 
                        'cash', 
                        null, 
                        "Late fee (3%) applied for payment overdue by $days_overdue days"
                    );
                    
                    // Create notification for late payment
                    $account_info = $this->getAccountInfo($account_id);
                    if ($account_info) {
                        $this->createNotification(
                            $account_id, 
                            $schedule['id'], 
                            $account_info['client_id'], 
                            'late_payment', 
                            'Late Payment Fee Applied', 
                            "A late fee of ₱" . number_format($late_fee_amount, 2) . " has been applied to your payment due on " . date('M d, Y', $due_date) . ". Please settle your account to avoid additional charges."
                        );
                        
                        $payload = [
                            'account_id' => $account_id,
                            'schedule_id' => $schedule['id'],
                            'late_fee' => $late_fee_amount
                        ];
                        $admin_message = "Late fee of ₱" . number_format($late_fee_amount, 2) . " applied to account #{$account_id}.";
                        $this->notifyAdminUsers('late_payment', 'Late Payment Fee Applied', $admin_message, $payload);
                    }
                    
                    $updated = true;
                }
                $update_stmt->close();
            } elseif ($days_overdue >= 7 && $schedule['payment_status'] == 'Unpaid') {
                // Update status to Late even if fee already applied
                $status_stmt = $this->conn->prepare("
                    UPDATE customer_account_schedule 
                    SET payment_status = 'Late',
                        updated_at = NOW()
                    WHERE id = ? AND payment_status = 'Unpaid'
                ");
                
                $status_stmt->bind_param("i", $schedule['id']);
                $status_stmt->execute();
                $status_stmt->close();
            }
        }
        
        $stmt->close();
        return $updated;
    }
    
    /**
     * Get account information
     * @param int $account_id
     * @return array|null
     */
    public function getAccountInfo($account_id) {
        $stmt = $this->conn->prepare("
            SELECT cab.*, cl.firstname, cl.lastname, cl.email 
            FROM customer_account_balances cab
            LEFT JOIN client_list cl ON cab.client_id = cl.id
            WHERE cab.id = ?
        ");
        
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $account = $result->fetch_assoc();
        $stmt->close();
        
        return $account;
    }
    
    /**
     * Get schedule information
     * @param int $schedule_id
     * @return array|null
     */
    public function getScheduleInfo($schedule_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM customer_account_schedule WHERE id = ?
        ");
        
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result->fetch_assoc();
        $stmt->close();
        
        return $schedule;
    }
    
    /**
     * Get all accounts for a client
     * @param int $client_id
     * @return array
     */
    public function getCustomerAccounts($client_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM customer_account_balances 
            WHERE client_id = ? 
            ORDER BY created_at DESC
        ");
        
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $accounts = [];
        
        while ($row = $result->fetch_assoc()) {
            // Check and apply late fees before returning
            $this->checkAndApplyLateFees($row['id']);
            $accounts[] = $row;
        }
        
        $stmt->close();
        return $accounts;
    }
    
    /**
     * Get payment schedule for an account
     * @param int $account_id
     * @return array
     */
    public function getPaymentSchedule($account_id) {
        // Check and apply late fees first
        $this->checkAndApplyLateFees($account_id);
        
        $stmt = $this->conn->prepare("
            SELECT * FROM customer_account_schedule 
            WHERE account_id = ? 
            ORDER BY installment_number ASC
        ");
        
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = [];
        
        while ($row = $result->fetch_assoc()) {
            $schedule[] = $row;
        }
        
        $stmt->close();
        return $schedule;
    }
    
    /**
     * Get transaction history for an account
     * @param int $account_id
     * @return array
     */
    public function getTransactionHistory($account_id) {
        $stmt = $this->conn->prepare("
                SELECT cat.*, u.firstname as processor_firstname, u.lastname as processor_lastname, cas.due_date AS schedule_due_date, cas.paid_date AS schedule_paid_date
            FROM customer_account_transactions cat
            LEFT JOIN users u ON cat.processed_by = u.id
            LEFT JOIN customer_account_schedule cas ON cat.schedule_id = cas.id
            WHERE cat.account_id = ? 
            ORDER BY cat.transaction_date DESC
        ");
        
        $stmt->bind_param("i", $account_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $transactions = [];
        
        while ($row = $result->fetch_assoc()) {
            $transactions[] = $row;
        }
        
        $stmt->close();
        return $transactions;
    }
    
    /**
     * Create notification
     * @param int $account_id
     * @param int|null $schedule_id
     * @param int $client_id
     * @param string $type
     * @param string $title
     * @param string $message
     * @return bool
     */
    public function createNotification($account_id, $schedule_id, $client_id, $type, $title, $message) {
        $stmt = $this->conn->prepare("
            INSERT INTO customer_account_notifications 
            (account_id, schedule_id, client_id, notification_type, title, message) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("iiisss", $account_id, $schedule_id, $client_id, $type, $title, $message);
        $result = $stmt->execute();
        $stmt->close();
        
        if($result){
            $payload = [
                'account_id' => $account_id,
                'schedule_id' => $schedule_id,
                'notification_type' => $type
            ];
            $this->sendGlobalNotification($client_id, $type, $title, $message, $payload);
        }
        
        return $result;
    }
    
    /**
     * Update account status
     * @param int $account_id
     * @param string $status
     * @return bool
     */
    private function updateAccountStatus($account_id, $status) {
        $stmt = $this->conn->prepare("
            UPDATE customer_account_balances 
            SET status = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        
        $stmt->bind_param("si", $status, $account_id);
        $result = $stmt->execute();
        $stmt->close();
        
        if($result){
            $account = $this->getAccountInfo($account_id);
            if($account){
                $status_label = ucfirst($status);
                $payload = [
                    'account_id' => $account_id,
                    'status' => $status_label,
                    'order_id' => $account['order_id'],
                    'contract_id' => $account['contract_id']
                ];
                $customer_message = "Your installment account for {$account['item_purchased']} is now {$status_label}.";
                $this->sendGlobalNotification($account['client_id'], 'account_status', 'Account Status Updated', $customer_message, $payload);
                $admin_message = "Account #{$account_id} ({$account['item_purchased']}) is now {$status_label}.";
                $this->notifyAdminUsers('account_status', 'Customer Account Status Updated', $admin_message, $payload);
            }
        }
        
        return $result;
    }
    
    /**
     * Get client notifications
     * @param int $client_id
     * @param bool $unread_only
     * @return array
     */
    public function getCustomerNotifications($client_id, $unread_only = false) {
        $sql = "
            SELECT * FROM customer_account_notifications 
            WHERE client_id = ?
        ";
        
        if ($unread_only) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT 50";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = [];
        
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        
        $stmt->close();
        return $notifications;
    }
    
    /**
     * Mark notification as read
     * @param int $notification_id
     * @param int $client_id
     * @return bool
     */
    public function markNotificationRead($notification_id, $client_id) {
        $stmt = $this->conn->prepare("
            UPDATE customer_account_notifications 
            SET is_read = 1 
            WHERE id = ? AND client_id = ?
        ");
        
        $stmt->bind_param("ii", $notification_id, $client_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Helpers for pushing events into the global notifications table
     */
    private function getNotifier(){
        if($this->notifier !== null){
            return $this->notifier;
        }
        $path = defined('base_app') ? base_app.'classes/Notification.php' : __DIR__.'/Notification.php';
        if(file_exists($path)){
            require_once $path;
            $this->notifier = new Notification();
            return $this->notifier;
        }
        return null;
    }
    
    private function sendGlobalNotification($user_id, $type, $title, $message, $data = []){
        $notifier = $this->getNotifier();
        if($notifier){
            try{
                $notifier->createNotification($user_id, $type, $title, $message, $data);
            }catch(Exception $e){
                // non-fatal; logging handled by Notification class
            }
        }
    }
    
    private function notifyAdminUsers($type, $title, $message, $data = []){
        $notifier = $this->getNotifier();
        if($notifier && method_exists($notifier, 'notifyAdmins')){
            try{
                $notifier->notifyAdmins($type, $title, $message, $data);
            }catch(Exception $e){
                // silent fallback
            }
        }
    }
}

