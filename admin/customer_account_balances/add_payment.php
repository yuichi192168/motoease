<?php
// Completely suppress all output and errors for clean JSON response
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Clean ALL existing output buffers first
while (ob_get_level() > 0) {
	ob_end_clean();
}

// Start a fresh output buffer
ob_start();

// Prevent config.php from flushing output
define('AJAX_REQUEST', true);

require_once('../../config.php');
require_once('../../classes/CustomerAccountBalance.php');

// Clean any output that config.php might have generated
ob_clean();

if(!defined('base_app')){
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	header('Content-Type: application/json');
	http_response_code(200);
	echo json_encode(['status' => 'failed', 'msg' => 'File not found']);
	exit(0);
}

if($_settings->userdata('id') < 1){
	while (ob_get_level() > 0) {
		ob_end_clean();
	}
	header('Content-Type: application/json');
	http_response_code(200);
	echo json_encode(['status' => 'failed', 'msg' => 'Unauthorized Access']);
	exit(0);
}

$response = ['status' => 'failed', 'msg' => 'An error occurred'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$account_id = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
	$schedule_id = isset($_POST['schedule_id']) && !empty($_POST['schedule_id']) ? intval($_POST['schedule_id']) : null;
	$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
	$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cash';
	$receipt_number = isset($_POST['receipt_number']) ? trim($_POST['receipt_number']) : null;
	$notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;
	// Accept optional payment_date (actual payment datetime) from admin UI
	$payment_date = isset($_POST['payment_date']) && !empty($_POST['payment_date']) ? trim($_POST['payment_date']) : null;
	$processed_by = $_settings->userdata('id');
	
	if($account_id <= 0){
		$response['msg'] = 'Invalid account ID';
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		header('Content-Type: application/json');
		http_response_code(200);
		echo json_encode($response);
		exit(0);
	}
	
	if($amount <= 0){
		$response['msg'] = 'Invalid payment amount';
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		header('Content-Type: application/json');
		http_response_code(200);
		echo json_encode($response);
		exit(0);
	}
	
	try {
		$accountBalance = new CustomerAccountBalance($conn);
		
		// Check if account exists
		$account = $accountBalance->getAccountInfo($account_id);
		if(!$account){
			$response['msg'] = 'Account not found';
			while (ob_get_level() > 0) {
				ob_end_clean();
			}
			header('Content-Type: application/json');
			http_response_code(200);
			echo json_encode($response);
			exit(0);
		}
		
		// Check and apply late fees BEFORE balance validation
		// This ensures we're checking against the correct balance including late fees
		$accountBalance->checkAndApplyLateFees($account_id);
		
		// Refresh account info after late fees are applied
		$account = $accountBalance->getAccountInfo($account_id);
		
		// Check if amount exceeds remaining balance (after late fees)
		if($amount > $account['remaining_balance']){
			$response['msg'] = 'Payment amount (₱' . number_format($amount, 2) . ') exceeds remaining balance (₱' . number_format($account['remaining_balance'], 2) . ')';
			while (ob_get_level() > 0) {
				ob_end_clean();
			}
			header('Content-Type: application/json');
			http_response_code(200);
			echo json_encode($response);
			exit(0);
		}
		
		// Add payment (skip late fee check since we already did it)
		$result = $accountBalance->addPayment(
			$account_id,
			$schedule_id,
			$amount,
			$payment_method,
			$receipt_number,
			$notes,
			$processed_by,
			$payment_date,
			true // Skip late fee check - already done above
		);
		
		// Check if result is truthy (transaction ID or true)
		if($result !== false && $result !== null && $result !== 0){
			// Log admin action (optional - don't fail if logging fails)
			try {
				if(file_exists('../../classes/ActivityLogger.php')){
					require_once('../../classes/ActivityLogger.php');
					$logger = new ActivityLogger();
					$logger->logOnsitePayment($account['client_id'], $amount);
				}
			} catch (Exception $logError) {
				// Non-fatal: log error but don't fail payment
				error_log("ActivityLogger error: " . $logError->getMessage());
			}
			
			$response['status'] = 'success';
			$response['msg'] = 'Payment recorded successfully';
			$response['transaction_id'] = is_numeric($result) ? intval($result) : null;
		} else {
			// Get last error from database connection for more details
			$db_error = $conn->error ?? 'Unknown database error';
			error_log("Payment failed - Account ID: {$account_id}, Amount: {$amount}, DB Error: {$db_error}");
			$response['status'] = 'failed';
			$response['msg'] = 'Failed to record payment. Please check the error logs or contact support.';
		}
		
	} catch (Exception $e) {
		error_log("Payment submission error: " . $e->getMessage());
		$response['msg'] = 'Error: ' . $e->getMessage();
	}
}

// Clean ALL output buffers completely
while (ob_get_level() > 0) {
	ob_end_clean();
}

// Ensure response is properly formatted
if(!isset($response['status'])){
	$response['status'] = 'failed';
}
if(!isset($response['msg'])){
	$response['msg'] = 'An error occurred';
}

// Set headers - MUST be before any output
if(!headers_sent()){
	header('Content-Type: application/json; charset=utf-8');
	header('Cache-Control: no-cache, must-revalidate');
	header('X-Content-Type-Options: nosniff');
	http_response_code(200); // Explicitly set 200 status
}

// Encode JSON
$json_output = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Validate JSON encoding
if($json_output === false){
	$json_output = json_encode([
		'status' => 'failed',
		'msg' => 'Error encoding response: ' . json_last_error_msg()
	], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

// Output ONLY the JSON - no whitespace, no other output
echo $json_output;

// Exit immediately - no flush needed since we cleaned all buffers
exit(0);

