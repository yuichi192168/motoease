<?php
require_once('../../config.php');
require_once('../../classes/CustomerAccountBalance.php');

if(!defined('base_app'))
	die('File not found');

$_settings->userdata('id') < 1 ? die('Unauthorized Access') : '';

$response = ['status' => 'failed', 'msg' => 'An error occurred'];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$account_id = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
	$schedule_id = isset($_POST['schedule_id']) && !empty($_POST['schedule_id']) ? intval($_POST['schedule_id']) : null;
	$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
	$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cash';
	$receipt_number = isset($_POST['receipt_number']) ? trim($_POST['receipt_number']) : null;
	$notes = isset($_POST['notes']) ? trim($_POST['notes']) : null;
	$processed_by = $_settings->userdata('id');
	
	if($account_id <= 0){
		$response['msg'] = 'Invalid account ID';
		echo json_encode($response);
		exit;
	}
	
	if($amount <= 0){
		$response['msg'] = 'Invalid payment amount';
		echo json_encode($response);
		exit;
	}
	
	try {
		$accountBalance = new CustomerAccountBalance($conn);
		
		// Check if account exists
		$account = $accountBalance->getAccountInfo($account_id);
		if(!$account){
			$response['msg'] = 'Account not found';
			echo json_encode($response);
			exit;
		}
		
		// Check if amount exceeds remaining balance
		if($amount > $account['remaining_balance']){
			$response['msg'] = 'Payment amount exceeds remaining balance';
			echo json_encode($response);
			exit;
		}
		
		// Check and apply late fees before processing payment
		$accountBalance->checkAndApplyLateFees($account_id);
		
		// Add payment
		$result = $accountBalance->addPayment(
			$account_id,
			$schedule_id,
			$amount,
			$payment_method,
			$receipt_number,
			$notes,
			$processed_by
		);
		
		if($result){
			// Log admin action
			require_once('../../classes/ActivityLogger.php');
			$logger = new ActivityLogger();
			$logger->logOnsitePayment($account['client_id'], $amount);
			
			$response['status'] = 'success';
			$response['msg'] = 'Payment recorded successfully';
		} else {
			$response['msg'] = 'Failed to record payment';
		}
		
	} catch (Exception $e) {
		$response['msg'] = 'Error: ' . $e->getMessage();
	}
}

echo json_encode($response);

