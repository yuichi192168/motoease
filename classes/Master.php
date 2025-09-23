<?php
require_once(__DIR__.'/../config.php');
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
        $color = isset($color) ? trim($color) : NULL;
        if($color !== NULL && $color === '') $color = NULL;
        $color_sql = $color !== NULL ? "'".$this->conn->real_escape_string($color)."'" : "NULL";
		
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
        $cart_check = $this->conn->query("SELECT id, quantity FROM `cart_list` WHERE client_id = '{$client_id}' AND product_id = '{$product_id}' AND ((color IS NULL AND {$color_sql} IS NULL) OR color = {$color_sql})");
		
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
			
            $sql = "INSERT INTO `cart_list` (client_id, product_id, color, quantity) VALUES ('{$client_id}', '{$product_id}', {$color_sql}, '{$quantity}')";
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
		$client_id = $this->settings->userdata('id');
		
		// Validate inputs
		if(empty($cart_id) || empty($quantity)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invalid input parameters.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$cart_id = $this->conn->real_escape_string($cart_id);
		$quantity = $this->conn->real_escape_string($quantity);
		
		// Get current cart item
        $cart_item = $this->conn->query("SELECT c.*, p.name FROM cart_list c 
										INNER JOIN product_list p ON c.product_id = p.id 
										WHERE c.id = '{$cart_id}' AND c.client_id = '{$client_id}'");
		
		if($cart_item->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Cart item not found.";
			return json_encode($resp);
		}
		
		$item = $cart_item->fetch_assoc();
		$current_qty = $item['quantity'];
		
		// Parse quantity change
		if(strpos($quantity, '+') !== false){
			$new_qty = $current_qty + (int)str_replace('+', '', $quantity);
		} elseif(strpos($quantity, '-') !== false){
			$new_qty = $current_qty - (int)str_replace('-', '', $quantity);
		} else {
			$new_qty = (int)$quantity;
		}
		
		// Validate new quantity
		if($new_qty <= 0){
			// Remove item from cart
			$sql = "DELETE FROM cart_list WHERE id = '{$cart_id}'";
		} else {
			// Check stock availability
			$stocks = $this->conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$item['product_id']}' AND type = 1")->fetch_assoc()['total_stock'];
			$out = $this->conn->query("SELECT SUM(oi.quantity) as total_out FROM order_items oi 
									  INNER JOIN order_list ol ON oi.order_id = ol.id 
									  WHERE oi.product_id = '{$item['product_id']}' AND ol.status != 5")->fetch_assoc()['total_out'];
			
			$stocks = $stocks > 0 ? $stocks : 0;
			$out = $out > 0 ? $out : 0;
			$available = $stocks - $out;
			
			if($new_qty > $available){
				$resp['status'] = 'failed';
				$resp['msg'] = "Cannot update quantity. Only {$available} units available in stock.";
				return json_encode($resp);
			}
			
			$sql = "UPDATE cart_list SET quantity = '{$new_qty}' WHERE id = '{$cart_id}'";
		}
		
		$update = $this->conn->query($sql);
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = "Cart updated successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to update cart.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function remove_from_cart(){
		extract($_POST);
		$client_id = $this->settings->userdata('id');
		
		// Validate inputs
		if(empty($cart_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invalid cart item.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$cart_id = $this->conn->real_escape_string($cart_id);
		
		// Verify ownership and delete
		$delete = $this->conn->query("DELETE FROM cart_list WHERE id = '{$cart_id}' AND client_id = '{$client_id}'");
		
		if($delete){
			$resp['status'] = 'success';
			$resp['msg'] = "Item removed from cart successfully.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to remove item from cart.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function place_order(){
		$client_id = $this->settings->userdata('id');
		
        // Delivery address UI removed; use saved address if available
        $customer = $this->conn->query("SELECT address FROM client_list WHERE id = '{$client_id}'");
        $saved_address = '';
        if($customer && $customer->num_rows > 0){
            $saved_address = $customer->fetch_assoc()['address'];
        }
		
		// Check if cart has items
		$cart_items = $this->conn->query("SELECT COUNT(*) as count FROM cart_list WHERE client_id = '{$client_id}'");
		if($cart_items->fetch_assoc()['count'] == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Your cart is empty.";
			return json_encode($resp);
		}
		
		// Start transaction
		$this->conn->begin_transaction();
		
		try {
			// Generate reference code
			$ref_code = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
			
			// Calculate total amount
			$total_query = $this->conn->query("SELECT SUM(c.quantity * p.price) as total FROM cart_list c 
											  INNER JOIN product_list p ON c.product_id = p.id 
											  WHERE c.client_id = '{$client_id}'");
			$total_amount = $total_query->fetch_assoc()['total'];
			
			// Create order - only use columns that exist in the database
            $order_data = "client_id = '{$client_id}', 
                           ref_code = '{$ref_code}', 
                           total_amount = '{$total_amount}', 
                           delivery_address = '" . $this->conn->real_escape_string($saved_address) . "', 
                           status = 0";
			
			$create_order = $this->conn->query("INSERT INTO order_list SET {$order_data}");
			
			if(!$create_order){
				throw new Exception("Failed to create order: " . $this->conn->error);
			}
			
			$order_id = $this->conn->insert_id;
			
            // Get cart items and create order items
            $cart_query = $this->conn->query("SELECT c.*, p.name, p.price FROM cart_list c 
											 INNER JOIN product_list p ON c.product_id = p.id 
											 WHERE c.client_id = '{$client_id}'");
			
			while($item = $cart_query->fetch_assoc()){
				$order_item_data = "order_id = '{$order_id}', 
								   product_id = '{$item['product_id']}', 
                                   quantity = '{$item['quantity']}'";
                if(!empty($item['color'])){
                    $order_item_data .= ", color = '".$this->conn->real_escape_string($item['color'])."'";
                }
				
				$create_item = $this->conn->query("INSERT INTO order_items SET {$order_item_data}");
				
				if(!$create_item){
					throw new Exception("Failed to create order item: " . $this->conn->error);
				}
			}
			
			// Clear cart
			$clear_cart = $this->conn->query("DELETE FROM cart_list WHERE client_id = '{$client_id}'");
			
			if(!$clear_cart){
				throw new Exception("Failed to clear cart: " . $this->conn->error);
			}
			
			// Commit transaction
			$this->conn->commit();
			
			$resp['status'] = 'success';
			$resp['msg'] = "Order placed successfully!";
			$resp['ref_code'] = $ref_code;
			$resp['order_id'] = $order_id;
			
		} catch (Exception $e) {
			// Rollback transaction
			$this->conn->rollback();
			
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to place order: " . $e->getMessage();
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
	
	// Brand functions
	function save_brand(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
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
			$bid = empty($id) ? $this->conn->insert_id : $id;
			$resp['id'] = $bid ;
			if(empty($id))
				$resp['msg'] = "New Brand successfully saved.";
			else
				$resp['msg'] = "Brand successfully updated.";
			if(!empty($_FILES['img']['tmp_name'])){
				$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
				$dir = base_app."uploads/brands/";
				if(!is_dir($dir))
				mkdir($dir);
				$name = $bid.".".$ext;
				if(is_file($dir.$name))
					unlink($dir.$name);
				$move = move_uploaded_file($_FILES['img']['tmp_name'],$dir.$name);
				if($move){
					$this->conn->query("UPDATE `brand_list` set image_path = CONCAT('uploads/brands/$name','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$bid}'");
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
		$del = $this->conn->query("UPDATE `brand_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Brand successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	// Mechanic functions
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
		$del = $this->conn->query("UPDATE `mechanics_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Mechanic successfully deleted.");
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
	
	// Order status update
	function update_order_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set status = '{$status}' where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = "Order status successfully updated.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Order status update failed.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	// Document management functions
	function update_document_status(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('document_id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$sql = "UPDATE `or_cr_documents` set {$data} where id = '{$document_id}' ";
		$update = $this->conn->query($sql);
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = "Document status successfully updated.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Document status update failed.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function delete_document(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `or_cr_documents` where id = '{$document_id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = "Document successfully deleted.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Document deletion failed.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	// Account balance functions
	function add_account_balance(){
		extract($_POST);
		$client_id = $this->settings->userdata('id');
		
		// Get current balance
		$current_balance = $this->conn->query("SELECT account_balance FROM client_list WHERE id = '{$client_id}'")->fetch_assoc()['account_balance'];
		$current_balance = $current_balance ? $current_balance : 0;
		
		// Add new amount
		$new_balance = $current_balance + $amount;
		
		// Update balance
		$update = $this->conn->query("UPDATE client_list SET account_balance = '{$new_balance}' WHERE id = '{$client_id}'");
		
		if($update){
			// Record transaction
			$transaction_data = "('{$client_id}', 'payment', '{$amount}', 'Account balance added via {$payment_method}', '{$reference_number}', NOW())";
			$this->conn->query("INSERT INTO customer_transactions (client_id, transaction_type, amount, description, reference_id, date_created) VALUES {$transaction_data}");
			
			$resp['status'] = 'success';
			$resp['msg'] = "Account balance successfully updated.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to update account balance.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function update_vehicle_info(){
		extract($_POST);
		$client_id = $this->settings->userdata('id');
		
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		
		$sql = "UPDATE `client_list` set {$data} where id = '{$client_id}' ";
		$update = $this->conn->query($sql);
		
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = "Vehicle information successfully updated.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to update vehicle information.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	function upload_orcr_document(){
		extract($_POST);
		$client_id = $this->settings->userdata('id');
		
		$data = "client_id = '{$client_id}', document_type = '{$document_type}', document_number = '{$document_number}', plate_number = '{$plate_number}', release_date = '{$release_date}', remarks = '{$remarks}', status = 'pending'";
		
		$sql = "INSERT INTO `or_cr_documents` set {$data} ";
		$save = $this->conn->query($sql);
		
		if($save){
			$doc_id = $this->conn->insert_id;
			
			// Handle file upload
			if(!empty($_FILES['document_file']['tmp_name'])){
				$ext = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
				$dir = base_app."uploads/documents/";
				if(!is_dir($dir))
				mkdir($dir);
				$name = $doc_id.".".$ext;
				if(is_file($dir.$name))
					unlink($dir.$name);
				$move = move_uploaded_file($_FILES['document_file']['tmp_name'],$dir.$name);
				if($move){
					$this->conn->query("UPDATE `or_cr_documents` set file_path = CONCAT('uploads/documents/$name','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$doc_id}'");
				}
			}
			
			$resp['status'] = 'success';
			$resp['msg'] = "Document successfully uploaded.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to upload document.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	// Customer Account Management functions
	function get_client_balance(){
		extract($_POST);
		
		// Validate inputs
		if(empty($client_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Client ID is required.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$client_id = $this->conn->real_escape_string($client_id);
		
		// Get client balance
		$client = $this->conn->query("SELECT account_balance FROM client_list WHERE id = '{$client_id}' AND delete_flag = 0");
		
		if($client->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Client not found.";
			return json_encode($resp);
		}
		
		$balance = $client->fetch_assoc()['account_balance'];
		$balance = $balance ? $balance : 0;
		
		$resp['status'] = 'success';
		$resp['balance'] = $balance;
		return json_encode($resp);
	}
	
	function get_client_transactions(){
		extract($_POST);
		
		// Validate inputs
		if(empty($client_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Client ID is required.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$client_id = $this->conn->real_escape_string($client_id);
		
		// Get client info
		$client = $this->conn->query("SELECT CONCAT(lastname, ', ', firstname, ' ', middlename) as fullname FROM client_list WHERE id = '{$client_id}'");
		if($client->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Client not found.";
			return json_encode($resp);
		}
		$client_name = $client->fetch_assoc()['fullname'];
		
		// Get transactions
		$transactions = $this->conn->query("SELECT * FROM customer_transactions WHERE client_id = '{$client_id}' ORDER BY date_created DESC LIMIT 50");
		
		$html = '<div class="table-responsive">';
		$html .= '<h6>Transaction History for: <strong>' . $client_name . '</strong></h6>';
		$html .= '<table class="table table-bordered table-striped">';
		$html .= '<thead><tr>';
		$html .= '<th>Date</th>';
		$html .= '<th>Type</th>';
		$html .= '<th>Amount</th>';
		$html .= '<th>Description</th>';
		$html .= '<th>Reference</th>';
		$html .= '</tr></thead>';
		$html .= '<tbody>';
		
		if($transactions->num_rows > 0){
			while($row = $transactions->fetch_assoc()){
				$amount_class = $row['transaction_type'] == 'payment' ? 'text-success' : 'text-danger';
				$amount_sign = $row['transaction_type'] == 'payment' ? '+' : '-';
				
				$html .= '<tr>';
				$html .= '<td>' . date("M d, Y H:i", strtotime($row['date_created'])) . '</td>';
				$html .= '<td><span class="badge badge-' . ($row['transaction_type'] == 'payment' ? 'success' : 'danger') . '">' . ucfirst($row['transaction_type']) . '</span></td>';
				$html .= '<td class="' . $amount_class . '">' . $amount_sign . '₱' . number_format($row['amount'], 2) . '</td>';
				$html .= '<td>' . htmlspecialchars($row['description']) . '</td>';
				$html .= '<td>' . htmlspecialchars($row['reference_id']) . '</td>';
				$html .= '</tr>';
			}
		} else {
			$html .= '<tr><td colspan="5" class="text-center text-muted">No transactions found.</td></tr>';
		}
		
		$html .= '</tbody></table></div>';
		
		$resp['status'] = 'success';
		$resp['html'] = $html;
		return json_encode($resp);
	}
	
	function adjust_client_balance(){
		extract($_POST);
		
		// Validate inputs
		if(empty($client_id) || empty($adjustment_type) || empty($amount) || empty($reason)){
			$resp['status'] = 'failed';
			$resp['msg'] = "All fields are required.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$client_id = $this->conn->real_escape_string($client_id);
		$adjustment_type = $this->conn->real_escape_string($adjustment_type);
		$amount = (float)$amount;
		$reason = $this->conn->real_escape_string($reason);
		
		// Validate amount
		if($amount <= 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Amount must be greater than 0.";
			return json_encode($resp);
		}
		
		// Get current balance
		$client = $this->conn->query("SELECT account_balance FROM client_list WHERE id = '{$client_id}' AND delete_flag = 0");
		if($client->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Client not found.";
			return json_encode($resp);
		}
		
		$current_balance = $client->fetch_assoc()['account_balance'];
		$current_balance = $current_balance ? $current_balance : 0;
		
		// Calculate new balance
		$new_balance = $current_balance;
		$transaction_type = 'payment';
		$transaction_amount = $amount;
		
		switch($adjustment_type){
			case 'add':
				$new_balance += $amount;
				$transaction_type = 'payment';
				$transaction_amount = $amount;
				break;
			case 'deduct':
				$new_balance -= $amount;
				$transaction_type = 'withdrawal';
				$transaction_amount = $amount;
				break;
			case 'set':
				$new_balance = $amount;
				$transaction_type = $amount > $current_balance ? 'payment' : 'withdrawal';
				$transaction_amount = abs($amount - $current_balance);
				break;
			default:
				$resp['status'] = 'failed';
				$resp['msg'] = "Invalid adjustment type.";
				return json_encode($resp);
		}
		
		// Start transaction
		$this->conn->begin_transaction();
		
		try {
			// Update client balance
			$update_balance = $this->conn->query("UPDATE client_list SET account_balance = '{$new_balance}' WHERE id = '{$client_id}'");
			if(!$update_balance){
				throw new Exception("Failed to update balance: " . $this->conn->error);
			}
			
			// Record transaction
			$reference_id = 'ADJ-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
			$description = "Balance adjustment: " . $reason;
			
			$transaction_data = "('{$client_id}', '{$transaction_type}', '{$transaction_amount}', '{$description}', '{$reference_id}', NOW())";
			$insert_transaction = $this->conn->query("INSERT INTO customer_transactions (client_id, transaction_type, amount, description, reference_id, date_created) VALUES {$transaction_data}");
			
			if(!$insert_transaction){
				throw new Exception("Failed to record transaction: " . $this->conn->error);
			}
			
			// Commit transaction
			$this->conn->commit();
			
			$resp['status'] = 'success';
			$resp['msg'] = "Account balance adjusted successfully.";
			
		} catch (Exception $e) {
			// Rollback transaction
			$this->conn->rollback();
			
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to adjust balance: " . $e->getMessage();
		}
		
		return json_encode($resp);
	}

	// ABC Inventory Management functions
	function save_stock(){
		extract($_POST);
		
		// Validate inputs
		if(empty($product_id) || empty($quantity) || $quantity <= 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product and quantity are required.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$product_id = $this->conn->real_escape_string($product_id);
		$quantity = (float)$quantity;
		$reason = isset($reason) ? $this->conn->real_escape_string($reason) : 'Stock addition';
		
		// Get current stock
		$current_stock_query = $this->conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$product_id}' AND type = 1");
		$current_stock = $current_stock_query->fetch_assoc()['total_stock'];
		$current_stock = $current_stock ? $current_stock : 0;
		
		// Calculate new stock
		$new_stock = $current_stock + $quantity;
		
		// Start transaction
		$this->conn->begin_transaction();
		
		try {
			// Add stock to stock_list
			$stock_data = "('{$product_id}', '{$quantity}', 1, NOW())";
			$insert_stock = $this->conn->query("INSERT INTO stock_list (product_id, quantity, type, date_created) VALUES {$stock_data}");
			
			if(!$insert_stock){
				throw new Exception("Failed to add stock: " . $this->conn->error);
			}
			
			// Record stock movement if table exists
			$movement_data = "('{$product_id}', 'IN', '{$quantity}', '{$current_stock}', '{$new_stock}', '{$reason}', 'STOCK_ADD', 'PURCHASE', NOW(), NULL)";
			$insert_movement = $this->conn->query("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, reference_id, reference_type, date_created, created_by) VALUES {$movement_data}");
			
			// Check for stock alerts
			$this->check_stock_alerts($product_id, $new_stock);
			
			// Commit transaction
			$this->conn->commit();
			
			$resp['status'] = 'success';
			$resp['msg'] = "Stock added successfully.";
			$resp['new_stock'] = $new_stock;
			
		} catch (Exception $e) {
			// Rollback transaction
			$this->conn->rollback();
			
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to add stock: " . $e->getMessage();
		}
		
		return json_encode($resp);
	}
	
	function update_stock(){
		extract($_POST);
		
		// Validate inputs
		if(empty($product_id) || empty($quantity) || empty($movement_type)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product, quantity, and movement type are required.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$product_id = $this->conn->real_escape_string($product_id);
		$quantity = (float)$quantity;
		$movement_type = $this->conn->real_escape_string($movement_type);
		$reason = isset($reason) ? $this->conn->real_escape_string($reason) : 'Stock adjustment';
		$reference_id = isset($reference_id) ? $this->conn->real_escape_string($reference_id) : 'STOCK_ADJ';
		$reference_type = isset($reference_type) ? $this->conn->real_escape_string($reference_type) : 'ADJUSTMENT';
		
		// Get current stock
		$current_stock_query = $this->conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$product_id}' AND type = 1");
		$current_stock = $current_stock_query->fetch_assoc()['total_stock'];
		$current_stock = $current_stock ? $current_stock : 0;
		
		// Calculate new stock based on movement type
		$stock_type = 1; // IN
		switch($movement_type){
			case 'IN':
				$new_stock = $current_stock + $quantity;
				$stock_type = 1;
				break;
			case 'OUT':
				$new_stock = $current_stock - $quantity;
				$stock_type = 2;
				if($new_stock < 0){
					$resp['status'] = 'failed';
					$resp['msg'] = "Insufficient stock for this operation.";
					return json_encode($resp);
				}
				break;
			case 'ADJUSTMENT':
				$new_stock = $quantity;
				$stock_type = 1;
				break;
			default:
				$resp['status'] = 'failed';
				$resp['msg'] = "Invalid movement type.";
				return json_encode($resp);
		}
		
		// Start transaction
		$this->conn->begin_transaction();
		
		try {
			// Add stock movement record
			$stock_data = "('{$product_id}', '{$quantity}', '{$stock_type}', NOW())";
			$insert_stock = $this->conn->query("INSERT INTO stock_list (product_id, quantity, type, date_created) VALUES {$stock_data}");
			
			if(!$insert_stock){
				throw new Exception("Failed to update stock: " . $this->conn->error);
			}
			
			// Record stock movement if table exists
			$movement_data = "('{$product_id}', '{$movement_type}', '{$quantity}', '{$current_stock}', '{$new_stock}', '{$reason}', '{$reference_id}', '{$reference_type}', NOW(), NULL)";
			$insert_movement = $this->conn->query("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, reference_id, reference_type, date_created, created_by) VALUES {$movement_data}");
			
			// Check for stock alerts
			$this->check_stock_alerts($product_id, $new_stock);
			
			// Commit transaction
			$this->conn->commit();
			
			$resp['status'] = 'success';
			$resp['msg'] = "Stock updated successfully.";
			$resp['new_stock'] = $new_stock;
			
		} catch (Exception $e) {
			// Rollback transaction
			$this->conn->rollback();
			
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to update stock: " . $e->getMessage();
		}
		
		return json_encode($resp);
	}
	
	function get_abc_analysis(){
		// Get ABC analysis data
		$abc_query = $this->conn->query("SELECT * FROM abc_analysis_view ORDER BY abc_category, price DESC");
		
		$data = [];
		$category_stats = ['A' => 0, 'B' => 0, 'C' => 0];
		$total_value = 0;
		
		while($row = $abc_query->fetch_assoc()){
			$data[] = $row;
			$category_stats[$row['abc_category']]++;
			$total_value += ($row['price'] * $row['available_stock']);
		}
		
		$resp['status'] = 'success';
		$resp['data'] = $data;
		$resp['category_stats'] = $category_stats;
		$resp['total_value'] = $total_value;
		
		return json_encode($resp);
	}
	
	function get_product_recommendations(){
		extract($_POST);
		
		// Validate inputs
		if(empty($product_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product ID is required.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$product_id = $this->conn->real_escape_string($product_id);
		
		// Get recommendations
		$recommendations_query = $this->conn->query("
			SELECT pr.*, p.name, p.price, p.image_path, p.abc_category,
				   COALESCE(s.total_stock, 0) as current_stock,
				   COALESCE(o.total_ordered, 0) as total_ordered,
				   (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) as available_stock
			FROM product_recommendations pr
			JOIN product_list p ON pr.recommended_product_id = p.id
			LEFT JOIN (
				SELECT product_id, SUM(quantity) as total_stock 
				FROM stock_list 
				WHERE type = 1 
				GROUP BY product_id
			) s ON p.id = s.product_id
			LEFT JOIN (
				SELECT oi.product_id, SUM(oi.quantity) as total_ordered
				FROM order_items oi
				JOIN order_list ol ON oi.order_id = ol.id
				WHERE ol.status != 5
				GROUP BY oi.product_id
			) o ON p.id = o.product_id
			WHERE pr.product_id = '{$product_id}' 
			AND p.delete_flag = 0 
			AND p.status = 1
			ORDER BY pr.priority ASC, pr.recommendation_type ASC
		");
		
		$recommendations = [];
		while($row = $recommendations_query->fetch_assoc()){
			$recommendations[] = $row;
		}
		
		$resp['status'] = 'success';
		$resp['recommendations'] = $recommendations;
		
		return json_encode($resp);
	}
	
	function check_stock_alerts($product_id, $current_stock){
		// Get product details
		$product_query = $this->conn->query("SELECT * FROM product_list WHERE id = '{$product_id}'");
		if($product_query->num_rows == 0) return;
		
		$product = $product_query->fetch_assoc();
		
		// Check for low stock alert
		if($current_stock <= $product['reorder_point']){
			$alert_message = "Low stock alert: {$product['name']} has {$current_stock} units remaining (Reorder point: {$product['reorder_point']})";
			$this->create_stock_alert($product_id, 'LOW_STOCK', $current_stock, $product['reorder_point'], $alert_message);
		}
		
		// Check for out of stock alert
		if($current_stock <= 0){
			$alert_message = "Out of stock: {$product['name']} is no longer available";
			$this->create_stock_alert($product_id, 'OUT_OF_STOCK', $current_stock, 0, $alert_message);
		}
		
		// Check for overstock alert
		if($current_stock >= $product['max_stock']){
			$alert_message = "Overstock alert: {$product['name']} has {$current_stock} units (Max stock: {$product['max_stock']})";
			$this->create_stock_alert($product_id, 'OVERSTOCK', $current_stock, $product['max_stock'], $alert_message);
		}
	}
	
	function create_stock_alert($product_id, $alert_type, $current_stock, $threshold_value, $message){
		// Check if alert already exists and is not resolved
		$existing_alert = $this->conn->query("SELECT id FROM inventory_alerts WHERE product_id = '{$product_id}' AND alert_type = '{$alert_type}' AND is_resolved = 0");
		
		if($existing_alert->num_rows == 0){
			// Create new alert
			$alert_data = "('{$product_id}', '{$alert_type}', '{$current_stock}', '{$threshold_value}', '{$message}', 0, NULL, NULL, NOW())";
			$this->conn->query("INSERT INTO inventory_alerts (product_id, alert_type, current_stock, threshold_value, message, is_resolved, resolved_by, resolved_date, date_created) VALUES {$alert_data}");
		}
	}
	
	function get_stock_alerts(){
		$alerts_query = $this->conn->query("
			SELECT ia.*, p.name as product_name, p.abc_category
			FROM inventory_alerts ia
			JOIN product_list p ON ia.product_id = p.id
			WHERE ia.is_resolved = 0
			ORDER BY ia.date_created DESC
		");
		
		$alerts = [];
		while($row = $alerts_query->fetch_assoc()){
			$alerts[] = $row;
		}
		
		$resp['status'] = 'success';
		$resp['alerts'] = $alerts;
		
		return json_encode($resp);
	}
	
	function resolve_stock_alert(){
		extract($_POST);
		
		// Validate inputs
		if(empty($alert_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Alert ID is required.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$alert_id = $this->conn->real_escape_string($alert_id);
		$resolved_by = isset($resolved_by) ? $this->conn->real_escape_string($resolved_by) : NULL;
		
		// Update alert
		$update_query = "UPDATE inventory_alerts SET is_resolved = 1, resolved_by = " . ($resolved_by ? "'{$resolved_by}'" : "NULL") . ", resolved_date = NOW() WHERE id = '{$alert_id}'";
		$update = $this->conn->query($update_query);
		
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = "Alert resolved successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to resolve alert.";
		}
		
		return json_encode($resp);
	}
	
	function auto_classify_abc(){
		// Get all products with their sales data
		$products_query = $this->conn->query("
			SELECT p.*, 
				   COALESCE(sales.total_sales_value, 0) as total_sales_value,
				   COALESCE(sales.total_quantity_sold, 0) as total_quantity_sold
			FROM product_list p
			LEFT JOIN (
				SELECT oi.product_id,
					   SUM(oi.quantity * p.price) as total_sales_value,
					   SUM(oi.quantity) as total_quantity_sold
				FROM order_items oi
				JOIN order_list ol ON oi.order_id = ol.id
				JOIN product_list p ON oi.product_id = p.id
				WHERE ol.status != 5
				GROUP BY oi.product_id
			) sales ON p.id = sales.product_id
			WHERE p.delete_flag = 0
			ORDER BY sales.total_sales_value DESC
		");
		
		$products = [];
		$total_value = 0;
		
		while($row = $products_query->fetch_assoc()){
			$products[] = $row;
			$total_value += $row['total_sales_value'];
		}
		
		// Calculate cumulative percentages and assign ABC categories
		$cumulative_value = 0;
		$updated_count = 0;
		
		foreach($products as $product){
			$cumulative_value += $product['total_sales_value'];
			$percentage = $total_value > 0 ? ($cumulative_value / $total_value) * 100 : 0;
			
			// Assign ABC category based on cumulative percentage
			$abc_category = 'C';
			if($percentage <= 80){
				$abc_category = 'A';
			} elseif($percentage <= 95){
				$abc_category = 'B';
			}
			
			// Update product ABC category
			$update_query = "UPDATE product_list SET abc_category = '{$abc_category}' WHERE id = '{$product['id']}'";
			if($this->conn->query($update_query)){
				$updated_count++;
			}
		}
		
		$resp['status'] = 'success';
		$resp['msg'] = "ABC classification updated for {$updated_count} products.";
		$resp['total_products'] = count($products);
		$resp['updated_count'] = $updated_count;
		
		return json_encode($resp);
	}
    
    // Customer Feedback & Engagement functions
    function save_review(){
        extract($_POST);
        $user_id = $this->settings->userdata('id');
        $login_type = $this->settings->userdata('login_type');

        if(empty($user_id) || $login_type != 2){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Only logged-in customers can submit reviews.';
            return json_encode($resp);
        }

        $allowed_types = array('product','service','dealership','order');
        if(empty($target_type) || empty($target_id) || empty($rating)){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Target, target id and rating are required.';
            return json_encode($resp);
        }
        $target_type = strtolower(trim($target_type));
        if(!in_array($target_type, $allowed_types)){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid review target type.';
            return json_encode($resp);
        }
        $target_id = (int)$target_id;
        $rating = (int)$rating;
        if($rating < 1 || $rating > 5){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Rating must be between 1 and 5.';
            return json_encode($resp);
        }
        $comment = isset($comment) ? $this->conn->real_escape_string(trim($comment)) : '';

        switch($target_type){
            case 'product':
                $exists = $this->conn->query("SELECT id FROM product_list WHERE id = '{$target_id}' AND delete_flag = 0")->num_rows > 0;
            break;
            case 'service':
                $exists = $this->conn->query("SELECT id FROM service_list WHERE id = '{$target_id}' AND delete_flag = 0")->num_rows > 0;
            break;
            case 'order':
                $exists = $this->conn->query("SELECT id FROM order_list WHERE id = '{$target_id}' AND client_id = '{$user_id}'")->num_rows > 0;
            break;
            case 'dealership':
                $exists = true;
            break;
            default:
                $exists = false;
        }
        if(!$exists){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Target to review was not found.';
            return json_encode($resp);
        }

        $check = $this->conn->query("SELECT id FROM reviews WHERE user_id = '{$user_id}' AND target_type = '{$target_type}' AND target_id = '{$target_id}'");
        if($check->num_rows > 0){
            $row = $check->fetch_assoc();
            $sql = "UPDATE reviews SET rating = '{$rating}', comment = '{$comment}', date_updated = NOW() WHERE id = '{$row['id']}'";
        } else {
            $sql = "INSERT INTO reviews (user_id, target_type, target_id, rating, comment, date_created) VALUES ('{$user_id}', '{$target_type}', '{$target_id}', '{$rating}', '{$comment}', NOW())";
        }
        $save = $this->conn->query($sql);
        if($save){
            $resp['status'] = 'success';
            $resp['msg'] = 'Thank you for your feedback!';
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to save review.';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function get_reviews(){
        extract($_POST);
        if(empty($target_type) || empty($target_id)){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Target and target id are required.';
            return json_encode($resp);
        }
        $target_type = $this->conn->real_escape_string(strtolower(trim($target_type)));
        $target_id = (int)$target_id;
        $limit = isset($limit) ? (int)$limit : 20;
        $offset = isset($offset) ? (int)$offset : 0;

        $q = $this->conn->query("SELECT r.*, CONCAT(cl.lastname, ', ', cl.firstname) as reviewer_name
            FROM reviews r
            LEFT JOIN client_list cl ON r.user_id = cl.id
            WHERE r.target_type = '{$target_type}' AND r.target_id = '{$target_id}'
            ORDER BY r.date_created DESC, r.id DESC
            LIMIT {$limit} OFFSET {$offset}");
        $reviews = array();
        while($row = $q->fetch_assoc()){
            $reviews[] = $row;
        }

        $stats_q = $this->conn->query("SELECT COUNT(*) as count, AVG(rating) as avg_rating FROM reviews WHERE target_type = '{$target_type}' AND target_id = '{$target_id}'");
        $stats = $stats_q->fetch_assoc();
        $resp['status'] = 'success';
        $resp['reviews'] = $reviews;
        $resp['count'] = (int)$stats['count'];
        $resp['avg_rating'] = $stats['avg_rating'] ? round((float)$stats['avg_rating'], 2) : 0;
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
	case 'update_cart_quantity':
		echo $Master->update_cart_quantity();
	break;
	case 'remove_from_cart':
		echo $Master->remove_from_cart();
	break;
	case 'place_order':
		echo $Master->place_order();
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
	case 'save_brand':
		echo $Master->save_brand();
	break;
	case 'delete_brand':
		echo $Master->delete_brand();
	break;
	case 'save_mechanic':
		echo $Master->save_mechanic();
	break;
	case 'delete_mechanic':
		echo $Master->delete_mechanic();
	break;
	case 'update_order_status':
		echo $Master->update_order_status();
	break;
	case 'update_document_status':
		echo $Master->update_document_status();
	break;
	case 'delete_document':
		echo $Master->delete_document();
	break;
	case 'add_account_balance':
		echo $Master->add_account_balance();
	break;
	case 'update_vehicle_info':
		echo $Master->update_vehicle_info();
	break;
	case 'upload_orcr_document':
		echo $Master->upload_orcr_document();
	break;
	case 'get_client_balance':
		echo $Master->get_client_balance();
	break;
	case 'get_client_transactions':
		echo $Master->get_client_transactions();
	break;
	case 'adjust_client_balance':
		echo $Master->adjust_client_balance();
	break;
	case 'save_stock':
		echo $Master->save_stock();
	break;
	case 'update_stock':
		echo $Master->update_stock();
	break;
	case 'get_abc_analysis':
		echo $Master->get_abc_analysis();
	break;
	case 'get_product_recommendations':
		echo $Master->get_product_recommendations();
	break;
	case 'get_stock_alerts':
		echo $Master->get_stock_alerts();
	break;
	case 'resolve_stock_alert':
		echo $Master->resolve_stock_alert();
	break;
	case 'auto_classify_abc':
		echo $Master->auto_classify_abc();
	break;
	case 'save_review':
		echo $Master->save_review();
	break;
	case 'get_reviews':
		echo $Master->get_reviews();
	break;
	default:
		break;
}
