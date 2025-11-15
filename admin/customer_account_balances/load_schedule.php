<?php
require_once('../../config.php');
require_once('../../classes/CustomerAccountBalance.php');

if(!defined('base_app'))
	die('File not found');

$_settings->userdata('id') < 1 ? die('Unauthorized Access') : '';

$response = ['status' => 'failed', 'schedule' => []];

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$account_id = isset($_POST['account_id']) ? intval($_POST['account_id']) : 0;
	
	if($account_id <= 0){
		echo json_encode($response);
		exit;
	}
	
	try {
		$accountBalance = new CustomerAccountBalance($conn);
		$schedule = $accountBalance->getPaymentSchedule($account_id);
		
		// Filter to show only unpaid/partial/late schedules
		$filtered_schedule = array_filter($schedule, function($item){
			return in_array($item['payment_status'], ['Unpaid', 'Partial', 'Late']);
		});
		
		$response['status'] = 'success';
		$response['schedule'] = array_values($filtered_schedule);
		
	} catch (Exception $e) {
		$response['msg'] = 'Error: ' . $e->getMessage();
	}
}

echo json_encode($response);


