<?php
/**
 * Cron Job: Customer Account Notifications
 * 
 * This script should be run daily via cron to:
 * 1. Check for upcoming due dates (3 days before)
 * 2. Check for late payments (>7 days)
 * 3. Create notifications for customers
 * 
 * Recommended cron: 0 9 * * * php /path/to/cron_account_notifications.php
 */

require_once('config.php');
require_once('classes/CustomerAccountBalance.php');

// Only allow execution via CLI or direct access with security token
$is_cli = php_sapi_name() === 'cli';
$has_token = isset($_GET['token']) && $_GET['token'] === 'your_secure_token_here'; // Change this token

if(!$is_cli && !$has_token){
    die('Unauthorized access');
}

$accountBalance = new CustomerAccountBalance($conn);
$today = date('Y-m-d');
$notifications_created = 0;

// 1. Check for upcoming due dates (3 days before due date)
$upcoming_query = $conn->query("
    SELECT cas.*, cab.client_id, cab.item_purchased
    FROM customer_account_schedule cas
    INNER JOIN customer_account_balances cab ON cas.account_id = cab.id
    WHERE cas.payment_status IN ('Unpaid', 'Partial')
    AND cas.due_date = DATE_ADD('{$today}', INTERVAL 3 DAY)
    AND cas.remaining_balance > 0
");

while($schedule = $upcoming_query->fetch_assoc()){
    $due_date = date('M d, Y', strtotime($schedule['due_date']));
    $accountBalance->createNotification(
        $schedule['account_id'],
        $schedule['id'],
        $schedule['client_id'],
        'upcoming_due_date',
        'Upcoming Payment Due',
        "Your payment of ₱" . number_format($schedule['remaining_balance'], 2) . " for {$schedule['item_purchased']} is due on {$due_date}. Please make payment to avoid late fees."
    );
    $notifications_created++;
}

// 2. Check for overdue payments (>7 days late) - only send once per schedule
$overdue_query = $conn->query("
    SELECT cas.*, cab.client_id, cab.item_purchased
    FROM customer_account_schedule cas
    INNER JOIN customer_account_balances cab ON cas.account_id = cab.id
    WHERE cas.payment_status IN ('Unpaid', 'Partial', 'Late')
    AND cas.due_date < DATE_SUB('{$today}', INTERVAL 7 DAY)
    AND cas.remaining_balance > 0
    AND NOT EXISTS (
        SELECT 1 FROM customer_account_notifications can
        WHERE can.schedule_id = cas.id
        AND can.notification_type = 'late_payment'
        AND DATE(can.created_at) = '{$today}'
    )
");

while($schedule = $overdue_query->fetch_assoc()){
    $days_overdue = floor((strtotime($today) - strtotime($schedule['due_date'])) / (60*60*24));
    $due_date = date('M d, Y', strtotime($schedule['due_date']));
    
    $accountBalance->createNotification(
        $schedule['account_id'],
        $schedule['id'],
        $schedule['client_id'],
        'late_payment',
        'Overdue Payment',
        "Your payment of ₱" . number_format($schedule['remaining_balance'], 2) . " for {$schedule['item_purchased']} was due on {$due_date} and is now {$days_overdue} days overdue. A late fee may apply. Please settle your payment immediately."
    );
    $notifications_created++;
}

// 3. Apply late fees automatically (this also creates notifications)
$accounts_query = $conn->query("
    SELECT DISTINCT account_id 
    FROM customer_account_schedule 
    WHERE payment_status IN ('Unpaid', 'Partial', 'Late')
    AND remaining_balance > 0
    AND due_date < DATE_SUB('{$today}', INTERVAL 7 DAY)
");

while($account = $accounts_query->fetch_assoc()){
    $accountBalance->checkAndApplyLateFees($account['account_id']);
}

// Log execution
if($is_cli){
    echo "Notifications cron executed. Created {$notifications_created} notifications.\n";
} else {
    echo json_encode([
        'status' => 'success',
        'notifications_created' => $notifications_created,
        'execution_time' => date('Y-m-d H:i:s')
    ]);
}

