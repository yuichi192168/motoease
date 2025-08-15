<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	
	// Basic CRUD functions
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `categories` where `category` = '{$category}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `categories` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `categories` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Category successfully saved.");
			else
				$this->settings->set_flashdata('success',"Category successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `categories` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	// Cart functions
	function save_to_cart(){
		$_POST['client_id'] = $this->settings->userdata('id');
		extract($_POST);
		
		// Validate inputs
		if(empty($client_id) || empty($product_id) || empty($quantity) || $quantity <= 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invalid input parameters.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$client_id = $this->conn->real_escape_string($client_id);
		$product_id = $this->conn->real_escape_string($product_id);
		$quantity = $this->conn->real_escape_string($quantity);
		
		// Check if product exists and is active
		$product_check = $this->conn->query("SELECT id, name, price FROM `product_list` WHERE id = '{$product_id}' AND delete_flag = 0 AND status = 1");
		if($product_check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product not found or unavailable.";
			return json_encode($resp);
		}
		
		// Check stock availability
		$stocks = $this->conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$product_id}' AND type = 1")->fetch_assoc()['total_stock'];
		$out = $this->conn->query("SELECT SUM(oi.quantity) as total_out FROM order_items oi 
								  INNER JOIN order_list ol ON oi.order_id = ol.id 
								  WHERE oi.product_id = '{$product_id}' AND ol.status != 5")->fetch_assoc()['total_out'];
		
		$stocks = $stocks > 0 ? $stocks : 0;
		$out = $out > 0 ? $out : 0;
		$available = $stocks - $out;
		
		// Check if product is already in cart
		$cart_check = $this->conn->query("SELECT id, quantity FROM `cart_list` WHERE client_id = '{$client_id}' AND product_id = '{$product_id}'");
		
		if($cart_check->num_rows > 0){
			// Product already in cart, update quantity
			$cart_item = $cart_check->fetch_assoc();
			$new_quantity = $cart_item['quantity'] + $quantity;
			
			// Check if new quantity exceeds available stock
			if($new_quantity > $available){
				$resp['status'] = 'failed';
				$resp['msg'] = "Cannot add more items. Only {$available} units available in stock.";
				return json_encode($resp);
			}
			
			$sql = "UPDATE `cart_list` SET quantity = '{$new_quantity}' WHERE id = '{$cart_item['id']}'";
		} else {
			// New product in cart
			if($quantity > $available){
				$resp['status'] = 'failed';
				$resp['msg'] = "Cannot add to cart. Only {$available} units available in stock.";
				return json_encode($resp);
			}
			
			$sql = "INSERT INTO `cart_list` (client_id, product_id, quantity) VALUES ('{$client_id}', '{$product_id}', '{$quantity}')";
		}
		
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$resp['cart_count'] = $this->conn->query("SELECT SUM(quantity) as total from cart_list where client_id = '{$client_id}'")->fetch_assoc()['total'];
			$resp['msg'] = "Product has been added to cart successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to add product to cart.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function get_cart_count(){
		$client_id = $this->settings->userdata('id');
		if(empty($client_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "User not logged in.";
			return json_encode($resp);
		}
		
		$cart_count = $this->conn->query("SELECT SUM(quantity) as total from cart_list where client_id = '{$client_id}'")->fetch_assoc()['total'];
		$cart_count = $cart_count > 0 ? $cart_count : 0;
		
		$resp['status'] = 'success';
		$resp['cart_count'] = $cart_count;
		return json_encode($resp);
	}
	
	// Wishlist functions
	function addToWishlist(){
		extract($_POST);
		
		$client_id = $this->settings->userdata('id');
		$product_id = $this->conn->real_escape_string($product_id);
		
		// Check if already in wishlist
		$check = $this->conn->query("SELECT id FROM wishlist WHERE client_id = '{$client_id}' AND product_id = '{$product_id}'");
		
		if($check->num_rows > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product is already in your wishlist.";
			return json_encode($resp);
		}
		
		// Add to wishlist
		$insert = $this->conn->query("INSERT INTO wishlist (client_id, product_id) VALUES ('{$client_id}', '{$product_id}')");
		
		if($insert){
			$resp['status'] = 'success';
			$resp['msg'] = "Product has been added to your wishlist.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to add product to wishlist.";
			$resp['error'] = $this->conn->error;
		}
		
		return json_encode($resp);
	}
	
	function removeFromWishlist(){
		extract($_POST);
		
		$client_id = $this->settings->userdata('id');
		$wishlist_id = $this->conn->real_escape_string($wishlist_id);
		
		// Verify ownership
		$check = $this->conn->query("SELECT id FROM wishlist WHERE id = '{$wishlist_id}' AND client_id = '{$client_id}'");
		
		if($check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Wishlist item not found or access denied.";
			return json_encode($resp);
		}
		
		// Remove from wishlist
		$delete = $this->conn->query("DELETE FROM wishlist WHERE id = '{$wishlist_id}'");
		
		if($delete){
			$resp['status'] = 'success';
			$resp['msg'] = "Product has been removed from your wishlist.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to remove product from wishlist.";
			$resp['error'] = $this->conn->error;
		}
		
		return json_encode($resp);
	}
	
	function get_wishlist_count(){
		$client_id = $this->settings->userdata('id');
		if(empty($client_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "User not logged in.";
			return json_encode($resp);
		}
		
		$wishlist_count = $this->conn->query("SELECT COUNT(*) as count FROM wishlist WHERE client_id = '{$client_id}'")->fetch_assoc()['count'];
		
		$resp['status'] = 'success';
		$resp['wishlist_count'] = $wishlist_count;
		return json_encode($resp);
	}
	
	// Notification functions
	function getNotifications(){
		$client_id = $this->settings->userdata('id');
		$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
		
		$notifications = $this->conn->query("SELECT * FROM notifications WHERE user_id = '{$client_id}' ORDER BY date_created DESC LIMIT {$limit}");
		
		$data = [];
		while($row = $notifications->fetch_assoc()){
			$data[] = $row;
		}
		
		$resp['status'] = 'success';
		$resp['data'] = $data;
		
		return json_encode($resp);
	}
	
	function markNotificationRead(){
		extract($_POST);
		
		$client_id = $this->settings->userdata('id');
		$notification_id = $this->conn->real_escape_string($notification_id);
		
		// Verify ownership
		$check = $this->conn->query("SELECT id FROM notifications WHERE id = '{$notification_id}' AND user_id = '{$client_id}'");
		
		if($check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Notification not found or access denied.";
			return json_encode($resp);
		}
		
		// Mark as read
		$update = $this->conn->query("UPDATE notifications SET is_read = 1 WHERE id = '{$notification_id}'");
		
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = "Notification marked as read.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to mark notification as read.";
			$resp['error'] = $this->conn->error;
		}
		
		return json_encode($resp);
	}
	
	function get_notifications_count(){
		$client_id = $this->settings->userdata('id');
		if(empty($client_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "User not logged in.";
			return json_encode($resp);
		}
		
		$count = $this->conn->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = '{$client_id}' AND is_read = 0")->fetch_assoc()['count'];
		
		$resp['status'] = 'success';
		$resp['count'] = $count;
		return json_encode($resp);
	}
	
	// Product functions
	function save_product(){
		$_POST['description'] = htmlentities($_POST['description']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `product_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `product_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `product_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			$pid = empty($id) ? $this->conn->insert_id : $id;
			$resp['id'] = $pid ;
			if(empty($id))
				$resp['msg'] = "New Product successfully saved.";
			else
				$resp['msg'] = "Product successfully updated.";
			if(!empty($_FILES['img']['tmp_name'])){
				$ext = $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
				$dir = base_app."uploads/products/";
				if(!is_dir($dir))
				mkdir($dir);
				$name = $pid.".".$ext;
				if(is_file($dir.$name))
					unlink($dir.$name);
				$move = move_uploaded_file($_FILES['img']['tmp_name'],$dir.$name);
				if($move){
					$this->conn->query("UPDATE `product_list` set image_path = CONCAT('uploads/products/$name','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$pid}'");
				}else{
					$resp['msg'] .= " But logo has failed to upload.";
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if(isset($resp['msg']) && $resp['status'] == 'success'){
			$this->settings->set_flashdata('success',$resp['msg']);
		}
		return json_encode($resp);
	}
	
	function delete_product(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `product_list` set `delete_flag` = 1  where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Product successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	// Service functions
	function save_service(){
		extract($_POST);
		$data = "";
		$_POST['description'] = addslashes(htmlentities($description));
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `service_list` where `service` = '{$service}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Service already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `service_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `service_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Service successfully saved.");
			else
				$this->settings->set_flashdata('success',"Service successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	
	function delete_service(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `service_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Service successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	// Service request functions
	function save_request(){
		if(empty($_POST['id']))
		$_POST['client_id'] = $this->settings->userdata('id');
		extract($_POST);
		$data = "";
		foreach($_POST as $k=> $v){
			if(in_array($k,array('client_id','service_type','mechanic_id','status'))){
				if(!empty($data)){ $data .= ", "; }

				$data .= " `{$k}` = '{$v}'";

			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `service_requests` set {$data} ";
		}else{
			$sql = "UPDATE `service_requests` set {$data} where id ='{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$rid = empty($id) ? $this->conn->insert_id : $id ;
			$data = "";
			foreach($_POST as $k=> $v){
				if(!in_array($k,array('id','client_id','service_type','mechanic_id','status'))){
					if(!empty($data)){ $data .= ", "; }
					if(is_array($_POST[$k]))
					$v = implode(",",$_POST[$k]);
					$v = $this->conn->real_escape_string($v);
					$data .= "('{$rid}','{$k}','{$v}')";
				}
			}
			$sql = "INSERT INTO `request_meta` (`request_id`,`meta_field`,`meta_value`) VALUES {$data} ";
			$this->conn->query("DELETE FROM `request_meta` where `request_id` = '{$rid}' ");
			$save = $this->conn->query($sql);
			if($save){
				$resp['status'] = 'success';
				$resp['id'] = $rid;
				if(empty($id))
				$resp['msg'] = " Service Request has been submitted successfully.";
				else
				$resp['msg'] = " Service Request details has been updated successfully.";
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = $this->conn->error;
				$resp['sql'] = $sql;
				if(empty($id))
				$resp['msg'] = " Service Request has failed to submit.";
				else
				$resp['msg'] = " Service Request details has failed to update.";
				$this->conn->query("DELETE FROM `service_requests` where id = '{$rid}'");
			}

		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			$resp['sql'] = $sql;
			if(empty($id))
			$resp['msg'] = " Service Request has failed to submit.";
			else
			$resp['msg'] = " Service Request details has failed to update.";
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata("success", $resp['msg']);
		return json_encode($resp);
	}
	
	function delete_request(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `service_requests` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Request successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function cancel_service(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `service_requests` set status = 4 where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = " Service Request has been cancelled.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " Service Request has failed to cancel.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['status']);
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
	switch ($action) {
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_product':
		echo $Master->save_product();
	break;
	case 'delete_product':
		echo $Master->delete_product();
	break;
	case 'save_service':
		echo $Master->save_service();
	break;
	case 'delete_service':
		echo $Master->delete_service();
	break;
	case 'save_request':
		echo $Master->save_request();
	break;
	case 'delete_request':
		echo $Master->delete_request();
	break;
	case 'cancel_service':
		echo $Master->cancel_service();
	break;
	case 'save_to_cart':
		echo $Master->save_to_cart();
	break;
	case 'get_cart_count':
		echo $Master->get_cart_count();
	break;
	case 'add_to_wishlist':
		echo $Master->addToWishlist();
	break;
	case 'remove_from_wishlist':
		echo $Master->removeFromWishlist();
	break;
	case 'get_notifications':
		echo $Master->getNotifications();
	break;
	case 'mark_notification_read':
		echo $Master->markNotificationRead();
	break;
	default:
		break;
}
