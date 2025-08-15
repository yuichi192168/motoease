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
	function save_brand(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `brand_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Brand already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `brand_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `brand_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			$id = empty($id) ? $this->conn->insert_id : $id;
			if(empty($id))
				$resp['msg'] = "New Brand successfully saved.";
			else
				$resp['msg'] = "Brand successfully updated.";
			if(!empty($_FILES['img']['tmp_name'])){
				$ext = $ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
				$dir = base_app."uploads/brands/";
				if(!is_dir($dir))
				mkdir($dir);
				$name = $id.".".$ext;
				if(is_file($dir.$name))
					unlink($dir.$name);
				$move = move_uploaded_file($_FILES['img']['tmp_name'],$dir.$name);
				if($move){
					$this->conn->query("UPDATE `brand_list` set image_path = CONCAT('uploads/brands/$name','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$id}'");
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
	function delete_brand(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `brand_list` set `delete_flag` = 1  where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Brand successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
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
	function save_stock(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `stock_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `stock_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Stock successfully saved.");
			else
				$this->settings->set_flashdata('success',"Stock successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_stock(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `stock_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Stock successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_mechanic(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `mechanics_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Mechanic already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `mechanics_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `mechanics_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Mechanic successfully saved.");
			else
				$this->settings->set_flashdata('success',"Mechanic successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_mechanic(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `mechanics_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Mechanic successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
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
	function update_cart_quantity(){
		extract($_POST);
		
		// Validate inputs
		if(empty($cart_id) || empty($quantity)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invalid input parameters.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$cart_id = $this->conn->real_escape_string($cart_id);
		$quantity = $this->conn->real_escape_string($quantity);
		
		// Get cart item details
		$cart_item = $this->conn->query("SELECT c.*, p.name FROM `cart_list` c 
										INNER JOIN product_list p ON c.product_id = p.id 
										WHERE c.id = '{$cart_id}'");
		
		if($cart_item->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Cart item not found.";
			return json_encode($resp);
		}
		
		$cart_data = $cart_item->fetch_assoc();
		$pid = $cart_data['product_id'];
		$current_quantity = $cart_data['quantity'];
		
		// Calculate new quantity based on operation
		$new_quantity = 0;
		if(strpos($quantity, '+') !== false){
			$new_quantity = $current_quantity + (int)str_replace('+', '', $quantity);
		} elseif(strpos($quantity, '-') !== false){
			$new_quantity = $current_quantity - (int)str_replace('-', '', $quantity);
		} else {
			$new_quantity = (int)$quantity;
		}
		
		// Check stock availability
		$stocks = $this->conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$pid}' AND type = 1")->fetch_assoc()['total_stock'];
		$out = $this->conn->query("SELECT SUM(oi.quantity) as total_out FROM order_items oi 
								  INNER JOIN order_list ol ON oi.order_id = ol.id 
								  WHERE oi.product_id = '{$pid}' AND ol.status != 5")->fetch_assoc()['total_out'];
		
		$stocks = $stocks > 0 ? $stocks : 0;
		$out = $out > 0 ? $out : 0;
		$available = $stocks - $out;
		
		// Validate quantity constraints
		if($new_quantity < 1){
			$resp['status'] = 'failed';
			$resp['msg'] = "Quantity cannot be less than 1.";
			$save = $this->conn->query("UPDATE cart_list SET quantity = '1' WHERE id = '{$cart_id}'");
		} elseif($new_quantity > $available){
			$resp['status'] = 'failed';
			$resp['msg'] = "Only {$available} units available in stock.";
			$save = $this->conn->query("UPDATE cart_list SET quantity = '{$available}' WHERE id = '{$cart_id}'");
		} elseif($available < 1){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product is out of stock.";
			$save = $this->conn->query("UPDATE cart_list SET quantity = '0' WHERE id = '{$cart_id}'");
		} else {
			$resp['status'] = 'success';
			$save = $this->conn->query("UPDATE cart_list SET quantity = '{$new_quantity}' WHERE id = '{$cart_id}'");
		}
		
		if(!$save){
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to update cart quantity.";
			$resp['error'] = $this->conn->error;
		}
		
		return json_encode($resp);
	}
	function remove_from_cart(){
		extract($_POST);
		
		// Validate inputs
		if(empty($cart_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invalid cart item ID.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$cart_id = $this->conn->real_escape_string($cart_id);
		
		// Verify cart item belongs to current user
		$client_id = $this->settings->userdata('id');
		$cart_check = $this->conn->query("SELECT id FROM `cart_list` WHERE id = '{$cart_id}' AND client_id = '{$client_id}'");
		
		if($cart_check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Cart item not found or access denied.";
			return json_encode($resp);
		}
		
		$del = $this->conn->query("DELETE FROM `cart_list` WHERE id = '{$cart_id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = "Product has been removed from cart successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to remove product from cart.";
			$resp['error'] = $this->conn->error;
		}
		
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function place_order(){
		$_POST['client_id'] = $this->settings->userdata('id');
		extract($_POST);
		
		// Validate inputs
		if(empty($client_id) || empty($delivery_address)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Please provide delivery address.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$client_id = $this->conn->real_escape_string($client_id);
		$delivery_address = $this->conn->real_escape_string($delivery_address);
		
		// Check if cart has items
		$cart_check = $this->conn->query("SELECT COUNT(*) as cart_count FROM cart_list WHERE client_id = '{$client_id}'");
		$cart_count = $cart_check->fetch_assoc()['cart_count'];
		
		if($cart_count == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Your cart is empty.";
			return json_encode($resp);
		}
		
		// Start transaction
		$this->conn->begin_transaction();
		
		try {
			// Generate unique reference code
			$pref = date("Ym-");
			$code = sprintf("%'.05d",1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `order_list` WHERE ref_code = '{$pref}{$code}'")->num_rows;
				if($check > 0){
					$code = sprintf("%'.05d",ceil($code) + 1);
				}else{
					break;
				}
			} 
			$ref_code = $pref.$code;
			
			// Create order
			$sql1 = "INSERT INTO `order_list` (`ref_code`,`client_id`,`delivery_address`) VALUES ('{$ref_code}','{$client_id}','{$delivery_address}')";
			$save = $this->conn->query($sql1);
			
			if(!$save){
				throw new Exception("Failed to create order: " . $this->conn->error);
			}
			
			$oid = $this->conn->insert_id;
			$data = "";
			$total_amount = 0;
			
			// Get cart items with product details
			$cart = $this->conn->query("SELECT c.*, p.price, p.name FROM cart_list c 
									   INNER JOIN product_list p ON c.product_id = p.id 
									   WHERE c.client_id = '{$client_id}'");
			
			while($row = $cart->fetch_assoc()){
				// Check stock availability for each item
				$stocks = $this->conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$row['product_id']}' AND type = 1")->fetch_assoc()['total_stock'];
				$out = $this->conn->query("SELECT SUM(oi.quantity) as total_out FROM order_items oi 
										  INNER JOIN order_list ol ON oi.order_id = ol.id 
										  WHERE oi.product_id = '{$row['product_id']}' AND ol.status != 5")->fetch_assoc()['total_out'];
				
				$stocks = $stocks > 0 ? $stocks : 0;
				$out = $out > 0 ? $out : 0;
				$available = $stocks - $out;
				
				if($row['quantity'] > $available){
					throw new Exception("Product '{$row['name']}' has insufficient stock. Available: {$available}, Requested: {$row['quantity']}");
				}
				
				if(!empty($data)) $data .= ", ";
				$data .= "('{$oid}','{$row['product_id']}','{$row['quantity']}')";
				$total_amount += ($row['price'] * $row['quantity']);
			}
			
			if(!empty($data)){
				$sql2 = "INSERT INTO `order_items` (`order_id`,`product_id`,`quantity`) VALUES {$data}";
				$save2 = $this->conn->query($sql2);
				
				if(!$save2){
					throw new Exception("Failed to add order items: " . $this->conn->error);
				}
				
				// Update order total
				$update_total = $this->conn->query("UPDATE `order_list` SET total_amount = '{$total_amount}' WHERE id = '{$oid}'");
				if(!$update_total){
					throw new Exception("Failed to update order total: " . $this->conn->error);
				}
				
				// Clear cart
				$clear_cart = $this->conn->query("DELETE FROM `cart_list` WHERE client_id = '{$client_id}'");
				if(!$clear_cart){
					throw new Exception("Failed to clear cart: " . $this->conn->error);
				}
				
				// Commit transaction
				$this->conn->commit();
				
				$resp['status'] = 'success';
				$resp['msg'] = "Order has been placed successfully. Reference Code: {$ref_code}";
				$resp['order_id'] = $oid;
				$resp['ref_code'] = $ref_code;
				
			} else {
				throw new Exception("No items found in cart.");
			}
			
		} catch (Exception $e) {
			// Rollback transaction on error
			$this->conn->rollback();
			$resp['status'] = 'failed';
			$resp['msg'] = $e->getMessage();
		}
		
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function cancel_order(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set status = 5 where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = " Order has been cancelled.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " Order has failed to cancel.";
			$resp['error'] = $this->conn->error;
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['status']);
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
	function update_order_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set `status` = '{$status}' where id = '{$id}'");
		if($update){
			$resp['status'] ='success';
			$resp['msg'] = " Order's status has been updated successfully.";
		}else{
			$resp['error'] = $this->conn->error;
			$resp['status'] ='failed';
			$resp['msg'] = " Order's status has failed to update.";
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	function delete_order(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `order_list` where id = '{$id}'");
		if($delete){
			$resp['status'] ='success';
			$resp['msg'] = " Order's status has been deleted successfully.";
		}else{
			$resp['error'] = $this->conn->error;
			$resp['status'] ='failed';
			$resp['msg'] = " Order's status has failed to delete.";
		}
		if($resp['status'] == 'success')
		$this->settings->set_flashdata('success',$resp['msg']);
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
	case 'save_brand':
		echo $Master->save_brand();
	break;
	case 'delete_brand':
		echo $Master->delete_brand();
	break;
	case 'save_service':
		echo $Master->save_service();
	break;
	case 'delete_service':
		echo $Master->delete_service();
	break;
	case 'save_product':
		echo $Master->save_product();
	break;
	case 'delete_product':
		echo $Master->delete_product();
	break;
	case 'save_stock':
		echo $Master->save_stock();
	break;
	case 'delete_stock':
		echo $Master->delete_stock();
	break;
	case 'save_mechanic':
		echo $Master->save_mechanic();
	break;
	case 'delete_mechanic':
		echo $Master->delete_mechanic();
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
	case 'update_cart_quantity':
		echo $Master->update_cart_quantity();
	break;
	case 'remove_from_cart':
		echo $Master->remove_from_cart();
	break;
	case 'get_cart_count':
		echo $Master->get_cart_count();
	break;
	case 'place_order':
		echo $Master->place_order();
	break;
	case 'cancel_order':
		echo $Master->cancel_order();
	break;
	case 'update_order_status':
		echo $Master->update_order_status();
	break;
	case 'delete_order':
		echo $Master->delete_order();
	break;
	default:
		// echo $sysset->index();
		break;
}