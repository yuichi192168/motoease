<?php
/**
 * Cron job script to check for upcoming and missed payments
 * Run this daily via cron: 0 9 * * * /usr/bin/php /path/to/cron_payment_checks.php
 */

require_once 'config.php';
require_once 'classes/PaymentNotification.php';

try {
    $notification = new PaymentNotification();
    $result = $notification->runDailyChecks();
    
    // Log the results
    $log_message = date('Y-m-d H:i:s') . " - Payment checks completed. " .
                   "Upcoming: {$result['upcoming_notifications']}, " .
                   "Missed: {$result['missed_notifications']}\n";
    
    file_put_contents('logs/payment_checks.log', $log_message, FILE_APPEND | LOCK_EX);
    
    echo "Payment checks completed successfully.\n";
    echo "Upcoming notifications created: {$result['upcoming_notifications']}\n";
    echo "Missed payment notifications created: {$result['missed_notifications']}\n";
    
} catch (Exception $e) {
    $error_message = date('Y-m-d H:i:s') . " - Error in payment checks: " . $e->getMessage() . "\n";
    file_put_contents('logs/payment_checks.log', $error_message, FILE_APPEND | LOCK_EX);
    
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>




