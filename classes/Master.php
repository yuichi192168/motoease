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

	function delete_order(){
		extract($_POST);
		$resp = array();
		// Only allow admin-like roles to delete orders
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor');
		if(!in_array($role, $allowed)){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Access denied.';
			return json_encode($resp);
		}
		$id = isset($id) ? intval($id) : 0;
		if($id <= 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Invalid order id.';
			return json_encode($resp);
		}
		$check = $this->conn->query("SELECT id FROM `order_list` WHERE id = '{$id}'");
		if(!$check || $check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Order not found.';
			return json_encode($resp);
		}
		// Start transaction
		$this->conn->begin_transaction();
		try{
			// Delete order items first
			$del_items = $this->conn->query("DELETE FROM `order_items` WHERE order_id = '{$id}'");
			if($del_items === false) throw new Exception('Failed to delete order items: '.$this->conn->error);
			// Delete the order
			$del = $this->conn->query("DELETE FROM `order_list` WHERE id = '{$id}'");
			if($del === false) throw new Exception('Failed to delete order: '.$this->conn->error);
			$this->conn->commit();
			$resp['status'] = 'success';
			$resp['msg'] = 'Order successfully deleted.';
			$this->settings->set_flashdata('success',$resp['msg']);
		}catch(Exception $e){
			$this->conn->rollback();
			$resp['status'] = 'failed';
			$resp['msg'] = $e->getMessage();
		}
		return json_encode($resp);
	}
	
	function delete_category(){
		extract($_POST);
		$resp = array();
		$del = $this->conn->query("UPDATE `categories` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error;
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
		$product_check = $this->conn->query("SELECT p.id, p.name, p.price, c.category FROM `product_list` p 
											INNER JOIN categories c ON p.category_id = c.id 
											WHERE p.id = '{$product_id}' AND p.delete_flag = 0 AND p.status = 1");
		if($product_check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product not found or unavailable.";
			return json_encode($resp);
		}
		
		$product_data = $product_check->fetch_assoc();
		$is_motorcycle = (strtolower($product_data['category']) == 'motorcycles');
		
		// Check if product has available colors defined
		$product_colors_check = $this->conn->query("SELECT available_colors FROM product_list WHERE id = '{$product_id}'");
		$product_colors_data = $product_colors_check->fetch_assoc();
		$has_available_colors = !empty($product_colors_data['available_colors']) && trim($product_colors_data['available_colors']) !== '';
		
		// For products with available colors (motorcycles or any product with colors), require color selection
		if($has_available_colors && empty($color)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Please select a color before adding to cart.";
			$resp['requires_color_selection'] = true;
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
			
            $sql = "INSERT INTO `cart_list` (client_id, product_id, color, quantity, date_added) VALUES ('{$client_id}', '{$product_id}', {$color_sql}, '{$quantity}', NOW())";
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
	
	// Get product details (colors and price) for add to cart modal
	function get_product_details(){
		$product_id = isset($_POST['product_id']) ? $this->conn->real_escape_string($_POST['product_id']) : '';
		
		if(empty($product_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product ID is required.";
			return json_encode($resp);
		}
		
		// Get product details
		$product_query = $this->conn->query("SELECT available_colors, price FROM product_list WHERE id = '{$product_id}' AND delete_flag = 0 AND status = 1");
		
		if($product_query->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Product not found.";
			return json_encode($resp);
		}
		
		$product_data = $product_query->fetch_assoc();
		$colors = [];
		
		if(!empty($product_data['available_colors']) && trim($product_data['available_colors']) !== ''){
			$colors = array_map('trim', explode(',', $product_data['available_colors']));
			$colors = array_filter($colors); // Remove empty values
		}
		
		$resp['status'] = 'success';
		$resp['colors'] = $colors;
		$resp['price'] = floatval($product_data['price']);
		return json_encode($resp);
	}
	
	// Validate cart for checkout - simplified validation
	function validate_cart_checkout(){
		$client_id = $this->settings->userdata('id');
		$resp = array();
		
		// Validate client ID
		if(empty($client_id) || $client_id <= 0) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Please log in to proceed with checkout.";
			return json_encode($resp);
		}
		
		// Get cart items with product categories - filter out invalid items
		$cart_items = $this->conn->query("SELECT c.*, p.name, p.price, cat.category 
										 FROM cart_list c 
										 INNER JOIN product_list p ON c.product_id = p.id 
										 INNER JOIN categories cat ON p.category_id = cat.id 
										 WHERE c.client_id = '{$client_id}' 
										 AND c.product_id > 0 
										 AND p.id > 0 
										 AND p.delete_flag = 0 
										 AND p.status = 1");
		
		// Check for query errors
		if(!$cart_items) {
			$resp['status'] = 'failed';
			$resp['msg'] = "Database error occurred while validating cart.";
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		if($cart_items->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Your cart is empty or contains invalid items.";
			return json_encode($resp);
		}
		
		// Simple validation - just check if cart has valid items
		$resp['status'] = 'success';
		$resp['requires_credit_application'] = false;
		$resp['msg'] = "Cart validation passed. Ready for checkout.";
		
		return json_encode($resp);
	}
	
	function update_cart_quantity(){
		extract($_POST);
		$client_id = $this->settings->userdata('id');
		$resp = array();
		
		// Ensure cart_id is properly extracted
		$cart_id = isset($cart_id) ? $cart_id : '';
		
		// Debug logging
		error_log("Update cart quantity called - cart_id: " . $cart_id . ", quantity: " . $quantity . ", client_id: " . $client_id);
		
		// Validate inputs
		if(empty($cart_id) || $cart_id == 0 || $cart_id == '0'){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invalid cart item ID.";
			error_log("Invalid cart_id: " . $cart_id);
			return json_encode($resp);
		}
		
		if(empty($quantity)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Quantity parameter is required.";
			error_log("Empty quantity parameter");
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$cart_id = intval($cart_id);
		$quantity = trim($quantity);
		
		// Get current cart item
		$cart_item = $this->conn->query("SELECT c.*, p.name FROM cart_list c 
										INNER JOIN product_list p ON c.product_id = p.id 
										WHERE c.id = '{$cart_id}' AND c.client_id = '{$client_id}'");
		
		if(!$cart_item || $cart_item->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Cart item not found or already removed.";
			error_log("Cart item not found - cart_id: " . $cart_id . ", client_id: " . $client_id);
			return json_encode($resp);
		}
		
		$item = $cart_item->fetch_assoc();
		$current_qty = floatval($item['quantity']);
		
		// Parse quantity change
		$new_qty = $current_qty;
		if(strpos($quantity, '+') !== false){
			$change = intval(str_replace(['+', ' '], '', $quantity));
			$new_qty = $current_qty + $change;
		} elseif(strpos($quantity, '-') !== false){
			$change = intval(str_replace(['-', ' '], '', $quantity));
			$new_qty = $current_qty - $change;
		} else {
			$new_qty = intval($quantity);
		}
		
		// Ensure minimum quantity of 1
		$new_qty = max(1, $new_qty);
		
		// Update quantity
		$sql = "UPDATE cart_list SET quantity = '{$new_qty}' WHERE id = '{$cart_id}' AND client_id = '{$client_id}'";
		$update = $this->conn->query($sql);
		
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = "Cart updated successfully.";
			$resp['new_quantity'] = $new_qty;
			error_log("Cart updated successfully - new_qty: " . $new_qty);
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to update cart.";
			$resp['error'] = $this->conn->error;
			error_log("Failed to update cart: " . $this->conn->error);
		}
		return json_encode($resp);
	}
	
	function remove_from_cart(){
		extract($_POST);
		$client_id = $this->settings->userdata('id');
		$resp = array();
		
		// Debug logging - check if cart_id exists before using it
		$cart_id = isset($cart_id) ? $cart_id : '';
		error_log("Remove from cart called - cart_id: " . $cart_id . ", client_id: " . $client_id);
		
		// Validate inputs
		if(empty($cart_id) || $cart_id == 0 || $cart_id == '0'){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invalid cart item ID.";
			error_log("Invalid cart_id for removal: " . $cart_id);
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$cart_id = intval($cart_id);
		
		// Verify ownership first
		$exists = $this->conn->query("SELECT id FROM cart_list WHERE id = '{$cart_id}' AND client_id = '{$client_id}'");
		if(!$exists || $exists->num_rows == 0){
			// Idempotent success: item already gone or not owned by user
			$resp['status'] = 'success';
			$resp['msg'] = "Item removed from cart successfully.";
			error_log("Cart item not found for removal - cart_id: " . $cart_id . ", client_id: " . $client_id);
			return json_encode($resp);
		}
		
		// Delete item
		$delete = $this->conn->query("DELETE FROM cart_list WHERE id = '{$cart_id}' AND client_id = '{$client_id}'");
		
		if($delete){
			$resp['status'] = 'success';
			$resp['msg'] = "Item removed from cart successfully.";
			error_log("Cart item removed successfully - cart_id: " . $cart_id);
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to remove item from cart.";
			$resp['error'] = $this->conn->error;
			error_log("Failed to remove cart item: " . $this->conn->error);
		}
		return json_encode($resp);
	}
	
	function place_order(){
		$client_id = $this->settings->userdata('id');
		$resp = array();
		
		// Enforce Terms & Conditions acceptance
		$tnc_ok = isset($_POST['terms_accepted']) && in_array($_POST['terms_accepted'], ['on','1','true','yes'], true);
		if(!$tnc_ok){
			$resp['status'] = 'failed';
			$resp['msg'] = "Please accept the terms and conditions before placing an order.";
			return json_encode($resp);
		}
		
		// Check if cart has items
		$cart_items = $this->conn->query("SELECT COUNT(*) as count FROM cart_list WHERE client_id = '{$client_id}'");
		if($cart_items->fetch_assoc()['count'] == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Your cart is empty.";
			return json_encode($resp);
		}
		
		// Check if customer has completed Motorcentral Credit Application for motorcycle orders
		// Only enforce when payment method is installment; allow cash/full payments without the requirement
		$payment_method = isset($_POST['payment_method']) ? strtolower(trim($_POST['payment_method'])) : '';
		$motorcycle_cart_items = $this->conn->query("SELECT COUNT(*) as count FROM cart_list c 
											INNER JOIN product_list p ON c.product_id = p.id 
											INNER JOIN categories cat ON p.category_id = cat.id 
											WHERE c.client_id = '{$client_id}' 
											AND (cat.category LIKE '%motorcycle%' OR cat.category LIKE '%bike%' OR p.name LIKE '%motorcycle%' OR p.name LIKE '%bike%')");
		$motorcycle_count_row = $motorcycle_cart_items ? $motorcycle_cart_items->fetch_assoc() : ['count' => 0];
		$has_motorcycle = isset($motorcycle_count_row['count']) && (int)$motorcycle_count_row['count'] > 0;
		
		if($has_motorcycle && $payment_method === 'installment'){
		// Note: Credit application validation is now handled on the frontend
		// The frontend will redirect users to the credit application form if needed
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
			
			// Add add-ons total if provided
			$addons_total = isset($_POST['addons_total']) ? floatval($_POST['addons_total']) : 0;
			$total_amount += $addons_total;
			
			// Create order - only use columns required in the database
			$addons_data = isset($_POST['addons']) ? $this->conn->real_escape_string($_POST['addons']) : '';
			$agreed_to_terms = $tnc_ok ? 1 : 0;
			$requires_credit = $has_motorcycle ? 1 : 0;
			$order_data = "client_id = '{$client_id}', 
						   ref_code = '{$ref_code}', 
						   total_amount = '{$total_amount}', 
						   status = 0,
						   requires_credit = '{$requires_credit}',
						   agreed_to_terms = '{$agreed_to_terms}',
						   date_created = NOW()";
			
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
			
			// Add selected add-ons as separate order items
			if(!empty($addons_data) && $addons_total > 0){
				$addons_list = explode(',', $addons_data);
				foreach($addons_list as $addon_id){
					$addon_id = trim($addon_id);
					if(!empty($addon_id)){
						// Get addon details
						$addon_query = $this->conn->query("SELECT * FROM product_list WHERE id = '{$addon_id}'");
						if($addon_query && $addon_query->num_rows > 0){
							$addon = $addon_query->fetch_assoc();
							
							$addon_item_data = "order_id = '{$order_id}', 
											   product_id = '{$addon_id}', 
											   quantity = 1";
							
							$create_addon_item = $this->conn->query("INSERT INTO order_items SET {$addon_item_data}");
							
							if(!$create_addon_item){
								throw new Exception("Failed to create addon item: " . $this->conn->error);
							}
						}
					}
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
			
			// Create notifications for client and admins
			try {
				if(file_exists(base_app.'classes/Notification.php')){
					require_once base_app.'classes/Notification.php';
					$notif = new Notification();
					// Client notification
					$notif->createNotification($client_id, 'order', 'Order Created', "Your order {$ref_code} was created successfully.", [ 'order_id' => $order_id, 'ref_code' => $ref_code ]);
					// Admin notifications (all system users)
					$admins = $this->conn->query("SELECT id FROM users WHERE status = 1");
					if($admins){
						while($a = $admins->fetch_assoc()){
							$notif->createNotification($a['id'], 'order', 'New Order', "New order {$ref_code} placed.", [ 'order_id' => $order_id, 'ref_code' => $ref_code, 'client_id' => $client_id ]);
						}
					}
				}
			} catch (Exception $e) { /* non-fatal */ }
			
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

	// Admin notification functions
	function get_admin_notifications_count(){
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin');
		if(!in_array($role, $allowed)){
			$resp = ['status' => 'failed','msg'=>'Access denied.'];
			return json_encode($resp);
		}
		$count = $this->conn->query("SELECT COUNT(*) as cnt FROM notifications WHERE is_read = 0")->fetch_assoc()['cnt'];
		return json_encode(['status'=>'success','count'=>(int)$count]);
	}

	function get_admin_notifications(){
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin');
		if(!in_array($role, $allowed)){
			return json_encode(['status' => 'failed','msg'=>'Access denied.']);
		}
		$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 20;
		$offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
		$data = [];
		// 1) Fetch explicit notifications (global/admin notifications stored in notifications table)
		$qn = $this->conn->query("SELECT * FROM notifications ORDER BY date_created DESC LIMIT {$offset},{$limit}");
		while($row = $qn->fetch_assoc()){
			// normalize
			$data[] = [
				'id' => 'n_'.$row['id'],
				'type' => $row['type'],
				'title' => $row['title'],
				'message' => $row['message'],
				'data' => $row['data'],
				'date_created' => $row['date_created'],
				'is_read' => isset($row['is_read']) ? (int)$row['is_read'] : 0,
				'target' => null
			];
		}
		// 2) Also include recent system events (orders, service_requests, appointments)
		// we fetch recent pending items and append if not enough results
		$remaining = $limit - count($data);
		if($remaining > 0){
			// orders pending
			$qo = $this->conn->query("SELECT id, ref_code, client_id, date_created FROM order_list WHERE status = 0 ORDER BY date_created DESC LIMIT {$remaining}");
			while($r = $qo->fetch_assoc()){
				$data[] = [
					'id' => 'order_'.$r['id'],
					'type' => 'order',
					'title' => 'New Order #'.($r['ref_code']?:$r['id']),
					'message' => 'Pending order received',
					'data' => json_encode(['order_id'=>$r['id'],'client_id'=>$r['client_id']]),
					'date_created' => $r['date_created'],
					'is_read' => 0,
					'target' => './?page=orders/view_order&id='.$r['id']
				];
			}
		}
		// services
		$remaining = $limit - count($data);
		if($remaining > 0){
			$qs = $this->conn->query("SELECT id, client_id, date_created FROM service_requests WHERE status = 0 ORDER BY date_created DESC LIMIT {$remaining}");
			while($r = $qs->fetch_assoc()){
				$data[] = [
					'id' => 'service_'.$r['id'],
					'type' => 'service',
					'title' => 'New Service Request #'.$r['id'],
					'message' => 'Pending service request',
					'data' => json_encode(['request_id'=>$r['id'],'client_id'=>$r['client_id']]),
					'date_created' => $r['date_created'],
					'is_read' => 0,
					'target' => './?page=service_requests/view_request&id='.$r['id']
				];
			}
		}
		// appointments
		$remaining = $limit - count($data);
		if($remaining > 0){
			$qa = $this->conn->query("SELECT id, client_id, appointment_date as date_created FROM appointments WHERE status IN ('pending',0) ORDER BY appointment_date DESC LIMIT {$remaining}");
			while($r = $qa->fetch_assoc()){
				$data[] = [
					'id' => 'appointment_'.$r['id'],
					'type' => 'appointment',
					'title' => 'New Appointment #'.$r['id'],
					'message' => 'Pending appointment',
					'data' => json_encode(['appointment_id'=>$r['id'],'client_id'=>$r['client_id']]),
					'date_created' => $r['date_created'],
					'is_read' => 0,
					'target' => './?page=appointments&view_id='.$r['id']
				];
			}
		}
		// Sort combined by date_created desc
		usort($data, function($a,$b){
			$ta = strtotime($a['date_created']);
			$tb = strtotime($b['date_created']);
			return $tb <=> $ta;
		});
		return json_encode(['status'=>'success','data'=>$data]);
	}

	function mark_admin_notification_read(){
		extract($_POST);
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin');
		if(!in_array($role, $allowed)){
			return json_encode(['status'=>'failed','msg'=>'Access denied.']);
		}
		$id = $this->conn->real_escape_string($id);
		$upd = $this->conn->query("UPDATE notifications SET is_read = 1 WHERE id = '{$id}'");
		if($upd) return json_encode(['status'=>'success']);
		return json_encode(['status'=>'failed','msg'=>$this->conn->error]);
	}

	function mark_all_admin_notifications_read(){
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin');
		if(!in_array($role, $allowed)) return json_encode(['status'=>'failed','msg'=>'Access denied.']);
		$upd = $this->conn->query("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
		if($upd) return json_encode(['status'=>'success']);
		return json_encode(['status'=>'failed','msg'=>$this->conn->error]);
	}

	function delete_admin_notification(){
		extract($_POST);
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin');
		if(!in_array($role, $allowed)) return json_encode(['status'=>'failed','msg'=>'Access denied.']);
		$id = $this->conn->real_escape_string($id);
		$del = $this->conn->query("DELETE FROM notifications WHERE id = '{$id}'");
		if($del) return json_encode(['status'=>'success']);
		return json_encode(['status'=>'failed','msg'=>$this->conn->error]);
	}
	
	// Product functions
	function save_product(){
		$_POST['description'] = htmlentities($_POST['description']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			// Skip non-persisted or array fields
			if(in_array($k, array('id', 'compatible_models'))) continue;
			if(is_array($v)) continue;
			$v = $this->conn->real_escape_string($v);
			if(!empty($data)) $data .= ",";
			$data .= " `{$k}`='{$v}' ";
		}

		// Ensure required foreign keys and defaults for new records
		if(empty($id)){
			// Default placeholder image to satisfy NOT NULL constraint
			$hasImageUpload = !empty($_FILES['img']['tmp_name']);
			if(!$hasImageUpload && stripos($data, "`image_path`") === false){
				if(!empty($data)) $data .= ",";
				$data .= " `image_path`='dist/img/no-image-available.png' ";
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

			// Handle color images if provided
			if(isset($_POST['available_colors']) && isset($_FILES['color_image'])){
				$colors_raw = $_POST['available_colors'];
				$colors = array_filter(array_map('trim', explode(',', $colors_raw)));
				$files = $_FILES['color_image'];
				$dir = base_app."uploads/products/colors/";
				if(!is_dir($dir)) mkdir($dir, 0777, true);
				foreach($colors as $c){
					$key = strtolower(preg_replace('/[^a-z0-9]+/i','_', $c));
					if(isset($files['tmp_name'][$key]) && !empty($files['tmp_name'][$key])){
						$ext = pathinfo($files['name'][$key], PATHINFO_EXTENSION);
						$name = $pid.'_'.$key.'.'.$ext;
						if(is_file($dir.$name)) unlink($dir.$name);
						$move = move_uploaded_file($files['tmp_name'][$key], $dir.$name);
						if($move){
							$path = "uploads/products/colors/$name?v=".time();
							$this->conn->query("INSERT INTO product_color_images (product_id, color, image_path) VALUES ('{$pid}', '".$this->conn->real_escape_string($c)."', '".$this->conn->real_escape_string($path)."') ON DUPLICATE KEY UPDATE image_path = VALUES(image_path)");
						}
					}
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

	function createTestNotification(){
		$client_id = $this->settings->userdata('id');
		$resp = ['status'=>'failed'];
		if(empty($client_id)){
			$resp['msg'] = 'Not logged in.';
			return json_encode($resp);
		}
		// Ensure notifications table exists
		$this->conn->query("CREATE TABLE IF NOT EXISTS notifications (\n\t\tid INT AUTO_INCREMENT PRIMARY KEY,\n\t\tuser_id INT NOT NULL,\n\t\ttype VARCHAR(50) NOT NULL,\n\t\ttitle VARCHAR(255) NOT NULL,\n\t\tmessage TEXT NOT NULL,\n\t\tdata JSON DEFAULT NULL,\n\t\tis_read TINYINT(1) NOT NULL DEFAULT 0,\n\t\tdate_created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n\t\tKEY user_id (user_id), KEY is_read (is_read), KEY type (type)\n\t) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
		$title = 'Test Notification';
		$msg = 'This is a test notification to verify the bell dropdown.';
		$esc_title = $this->conn->real_escape_string($title);
		$esc_msg = $this->conn->real_escape_string($msg);
		$ins = $this->conn->query("INSERT INTO notifications (user_id, type, title, message, data, is_read, date_created) VALUES ('{$client_id}','test','{$esc_title}','{$esc_msg}', NULL, 0, NOW())");
		if($ins){
			$resp['status'] = 'success';
			$resp['msg'] = 'Test notification created.';
		}else{
			$resp['msg'] = 'Failed to create notification.';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	function save_product_compatibility(){
		extract($_POST);
		$resp = array('status' => 'failed');
		$product_id = isset($product_id) ? (int)$product_id : 0;
		if($product_id <= 0){
			$resp['msg'] = 'Invalid product ID.';
			return json_encode($resp);
		}
		$models = isset($models) ? (array)$models : [];
		$this->conn->begin_transaction();
		try{
			// Ensure table exists (idempotent)
			$this->conn->query("CREATE TABLE IF NOT EXISTS product_compatibility (\n				id INT AUTO_INCREMENT PRIMARY KEY,\n				product_id INT NOT NULL,\n				model_name VARCHAR(255) NOT NULL,\n				UNIQUE KEY unique_product_model (product_id, model_name),\n				KEY product_id (product_id)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
			// Clear existing
			$this->conn->query("DELETE FROM product_compatibility WHERE product_id = '{$product_id}'");
			// Insert new
			if(count($models)){
				$values = [];
				foreach($models as $m){
					$mn = $this->conn->real_escape_string($m);
					$values[] = "('{$product_id}','{$mn}')";
				}
				$this->conn->query("INSERT INTO product_compatibility (product_id, model_name) VALUES ".implode(',', $values));
			}
			$this->conn->commit();
			$resp['status'] = 'success';
			$resp['msg'] = 'Compatibility saved.';
		}catch(Exception $e){
			$this->conn->rollback();
			$resp['msg'] = 'Failed to save compatibility.';
			$resp['error'] = $e->getMessage();
		}
		return json_encode($resp);
	}
	function delete_product(){
		extract($_POST);
		$resp = array();
		$del = $this->conn->query("UPDATE `product_list` set `delete_flag` = 1  where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Product successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	
	// Service functions
	function save_service(){
		extract($_POST);
		$data = "";
		$_POST['description'] = addslashes(htmlentities($description));
		
		// Convert minutes to hours if estimated_hours is provided
		if(isset($estimated_hours) && !empty($estimated_hours)) {
			$_POST['estimated_hours'] = $estimated_hours / 60; // Convert minutes to hours
		}
		
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
		$resp = array();
		$del = $this->conn->query("UPDATE `service_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Service successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error;
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
		$resp = array();
		$del = $this->conn->query("UPDATE `brand_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Brand successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error;
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
			$mid = empty($id) ? $this->conn->insert_id : $id;
			if(!empty($_FILES['img']['tmp_name'])){
				$ext = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
				$dir = base_app."uploads/mechanics/";
				if(!is_dir($dir)) mkdir($dir, 0777, true);
				$namef = $mid.".".$ext;
				if(is_file($dir.$namef)) unlink($dir.$namef);
				$move = move_uploaded_file($_FILES['img']['tmp_name'],$dir.$namef);
				if($move){
					$this->conn->query("ALTER TABLE mechanics_list ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL");
					$this->conn->query("UPDATE `mechanics_list` set avatar = CONCAT('uploads/mechanics/$namef','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$mid}'");
				}
			}
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
		$resp = array();
		$del = $this->conn->query("UPDATE `mechanics_list` set delete_flag = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Mechanic successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	// Service request functions
	function save_request(){
		if(empty($_POST['id']))
		$_POST['client_id'] = $this->settings->userdata('id');
		
		// Enforce Terms & Conditions acceptance only for new requests (not admin updates)
		$is_update = !empty($_POST['id']);
		if(!$is_update) {
			$tnc_ok = isset($_POST['terms_accepted']) && in_array($_POST['terms_accepted'], ['on','1','true','yes'], true);
			if(!$tnc_ok){
				$resp['status'] = 'failed';
				$resp['msg'] = "Please accept the terms and conditions before submitting the service request.";
				return json_encode($resp);
			}
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k=> $v){
			if(in_array($k,array('client_id','mechanic_id','status','vehicle_type','vehicle_name','vehicle_registration_number','vehicle_model'))){
				if(!empty($data)){ $data .= ", "; }
				$v = $this->conn->real_escape_string($v);
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
				if(!in_array($k,array('id','client_id','mechanic_id','status','vehicle_type','vehicle_name','vehicle_registration_number','vehicle_model'))){
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
				// Send notifications (client + admins)
				try {
					if(file_exists(base_app.'classes/Notification.php')){
						require_once base_app.'classes/Notification.php';
						$notif = new Notification();
						$notif->createNotification($client_id, 'service', 'Service Request Submitted', 'Your service request was submitted successfully.', [ 'service_id' => $rid ]);
						$admins = $this->conn->query("SELECT id FROM users WHERE status = 1");
						if($admins){ while($a = $admins->fetch_assoc()){ $notif->createNotification($a['id'], 'service', 'New Service Request', 'A new service request has been submitted.', [ 'service_id' => $rid, 'client_id' => $client_id ]); } }
					}
				} catch (Exception $e) { /* non-fatal */ }
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
		$resp = array();
		$del = $this->conn->query("DELETE FROM `service_requests` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Request successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error;
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
	
	function delete_invoice(){
		extract($_POST);
		$resp = array();
		
		// Debug logging
		error_log("Delete invoice called with ID: " . $id);
		
		// Check if invoice exists and is not paid
		$check = $this->conn->query("SELECT id, payment_status FROM `invoices` WHERE id = '{$id}'");
		if($check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invoice not found.";
			error_log("Invoice not found: " . $id);
			return json_encode($resp);
		}
		
		$invoice = $check->fetch_assoc();
		// If invoice is marked paid, do not allow deletion unless forced by admin
		$force = isset($_POST['force']) && intval($_POST['force']) === 1;
		if(isset($invoice['payment_status']) && strtolower($invoice['payment_status']) == 'paid'){
			if(!$force || $this->settings->userdata('login_type') != 1){
				$resp['status'] = 'failed';
				$resp['msg'] = "Cannot delete invoice: invoice is already paid.";
				error_log("Attempt to delete paid invoice without force/admin: {$id}");
				return json_encode($resp);
			}
			// admin is forcing deletion, proceed but log it
			error_log("Admin force-deleting paid invoice: {$id} by user {$this->settings->userdata('id')}");
		}

		// If there are any receipts/payments recorded for this invoice, prevent deletion
		$rec_check = $this->conn->query("SELECT COUNT(*) as cnt, COALESCE(SUM(amount_paid),0) as total_paid FROM `receipts` WHERE invoice_id = '{$id}'");
		if($rec_check){
			$rec_data = $rec_check->fetch_assoc();
			if($rec_data['cnt'] > 0){
				// If not forced by admin, prevent deletion
				if(!$force || $this->settings->userdata('login_type') != 1){
					$resp['status'] = 'failed';
					$resp['msg'] = 'Cannot delete invoice: payments/receipts have been recorded ('.$rec_data['cnt'].' receipt(s)). Please reverse the payments first.';
					error_log("Attempt to delete invoice with receipts: {$id}, receipts_count: {$rec_data['cnt']}, total_paid: {$rec_data['total_paid']}");
					return json_encode($resp);
				}
				// admin is forcing deletion, log it
				error_log("Admin force-deleting invoice with receipts: {$id}, receipts_count: {$rec_data['cnt']}, total_paid: {$rec_data['total_paid']} by user {$this->settings->userdata('id')}");
			}
		}
		
		// Start transaction
		$this->conn->query("START TRANSACTION");
		
		try {
			// Delete invoice items first
			$del_items = $this->conn->query("DELETE FROM `invoice_items` WHERE invoice_id = '{$id}'");
			if(!$del_items){
				throw new Exception("Failed to delete invoice items: " . $this->conn->error);
			}
			
			// Delete any receipts for this invoice
			$del_receipts = $this->conn->query("DELETE FROM `receipts` WHERE invoice_id = '{$id}'");
			if(!$del_receipts){
				throw new Exception("Failed to delete receipts: " . $this->conn->error);
			}
			
			// Delete the invoice
			$del_invoice = $this->conn->query("DELETE FROM `invoices` WHERE id = '{$id}'");
			if(!$del_invoice){
				throw new Exception("Failed to delete invoice: " . $this->conn->error);
			}
			
			// Commit transaction
			$this->conn->query("COMMIT");
			
			$resp['status'] = 'success';
			$resp['msg'] = "Invoice successfully deleted.";
			$this->settings->set_flashdata('success', $resp['msg']);
			
		} catch (Exception $e) {
			// Rollback transaction
			$this->conn->query("ROLLBACK");
			$resp['status'] = 'failed';
			$resp['msg'] = $e->getMessage();
			error_log("Delete invoice error: " . $e->getMessage());
		}
		
		error_log("Delete invoice response: " . json_encode($resp));
		return json_encode($resp);
	}
	
	function cancel_order(){
		extract($_POST);
		$resp = array();
		$id = isset($id) ? intval($id) : 0;
		if($id <= 0){
			return json_encode(['status'=>'failed','msg'=>'Invalid order id.']);
		}
		
		$check = $this->conn->query("SELECT id, status, client_id FROM `order_list` WHERE id = '{$id}'");
		if(!$check || $check->num_rows == 0){
			return json_encode(['status'=>'failed','msg'=>'Order not found.']);
		}
		$order = $check->fetch_assoc();
		
		$role_type = $this->settings->userdata('role_type');
		$is_admin_like = in_array($role_type, ['admin','branch_supervisor','service_admin']);
		$user_id = $this->settings->userdata('id');
		
		// Clients can only cancel their own orders; Admins can cancel broader set
		if(!$is_admin_like && $order['client_id'] != $user_id){
			return json_encode(['status'=>'failed','msg'=>'You can only cancel your own orders.']);
		}
		
		// Block final states; allow others. Clients only: allow Pending(0), Processing(2), Ready(3)
		$final_statuses = [4,5,6]; // Completed, Cancelled, Claimed
		if(in_array((int)$order['status'], $final_statuses)){
			return json_encode(['status'=>'failed','msg'=>'This order can no longer be cancelled.']);
		}
		if(!$is_admin_like){
			$client_cancellable = [0,2,3];
			if(!in_array((int)$order['status'], $client_cancellable)){
				return json_encode(['status'=>'failed','msg'=>'Only pending or in-progress orders can be cancelled.']);
			}
		}
		
		$update = $this->conn->query("UPDATE `order_list` SET status = 5 WHERE id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = 'Order cancelled successfully.';
			try {
				if(file_exists(base_app.'classes/Notification.php')){
					require_once base_app.'classes/Notification.php';
					$notif = new Notification();
					$notif->createNotification($order['client_id'], 'order', 'Order Cancelled', 'Your order has been cancelled successfully.', [ 'order_id' => $id ]);
				}
			} catch (Exception $e) { /* non-fatal */ }
		}else{
			$resp = ['status'=>'failed','msg'=>'Failed to cancel order.','error'=>$this->conn->error];
		}
		return json_encode($resp);
	}

	/**
	 * Return counts for pending items shown on admin sidebar badges
	 * - orders: order_list with status = 0 (pending)
	 * - services: service_requests with status = 0 (pending)
	 * - appointments: appointments with status = 'pending'
	 */
	function get_admin_sidebar_counts(){
		$resp = ['status'=>'failed'];
		// Allow admin-like roles (role_type values)
		$role_type = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin');
		if(!in_array($role_type, $allowed)){
			$resp['msg'] = 'Access denied.';
			return json_encode($resp);
		}
		// Ensure admin_section_views table exists
		$this->conn->query("CREATE TABLE IF NOT EXISTS admin_section_views (
			id INT AUTO_INCREMENT PRIMARY KEY,
			admin_id INT NOT NULL,
			section VARCHAR(50) NOT NULL,
			last_viewed DATETIME NOT NULL,
			UNIQUE KEY admin_section (admin_id, section)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
		$admin_id = $this->settings->userdata('id');
		// Fetch last viewed timestamps
		$views = [];
		$vr = $this->conn->query("SELECT section, last_viewed FROM admin_section_views WHERE admin_id = '{$admin_id}'");
		while($row = $vr->fetch_assoc()) $views[$row['section']] = $row['last_viewed'];
		$counts = ['orders'=>0,'services'=>0,'appointments'=>0,'clients'=>0];
		// Orders pending created after last_viewed (or all if no last_viewed)
		$where_orders = isset($views['orders']) ? "AND date_created > '{$views['orders']}'" : '';
		$orders_q = $this->conn->query("SELECT COUNT(*) as cnt FROM order_list WHERE status = 0 {$where_orders}");
		if($orders_q) $counts['orders'] = (int)$orders_q->fetch_assoc()['cnt'];
		// Services
		$where_services = isset($views['services']) ? "AND date_created > '{$views['services']}'" : '';
		$svc_q = $this->conn->query("SELECT COUNT(*) as cnt FROM service_requests WHERE status = 0 {$where_services}");
		if($svc_q) $counts['services'] = (int)$svc_q->fetch_assoc()['cnt'];
		// Appointments
		$where_appt = isset($views['appointments']) ? "AND appointment_date > '{$views['appointments']}'" : '';
		$appt_q = $this->conn->query("SELECT COUNT(*) as cnt FROM appointments WHERE status IN ('pending',0) {$where_appt}");
		if($appt_q) $counts['appointments'] = (int)$appt_q->fetch_assoc()['cnt'];
		// New clients / users (optional)
		$where_clients = isset($views['clients']) ? "AND date_created > '{$views['clients']}'" : '';
		$cli_q = $this->conn->query("SELECT COUNT(*) as cnt FROM client_list WHERE delete_flag = 0 {$where_clients}");
		if($cli_q) $counts['clients'] = (int)$cli_q->fetch_assoc()['cnt'];
		$resp['status'] = 'success';
		$resp['counts'] = $counts;
		return json_encode($resp);
	}

	function clear_admin_section_view(){
		extract($_POST);
		$section = $this->conn->real_escape_string($section);
		$admin_id = $this->settings->userdata('id');
		if(empty($admin_id)) return json_encode(['status'=>'failed','msg'=>'Not logged in']);
		// ensure table
		$this->conn->query("CREATE TABLE IF NOT EXISTS admin_section_views (
			id INT AUTO_INCREMENT PRIMARY KEY,
			admin_id INT NOT NULL,
			section VARCHAR(50) NOT NULL,
			last_viewed DATETIME NOT NULL,
			UNIQUE KEY admin_section (admin_id, section)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
		$now = date('Y-m-d H:i:s');
		// upsert
		$exists = $this->conn->query("SELECT id FROM admin_section_views WHERE admin_id = '{$admin_id}' AND section = '{$section}'")->num_rows;
		if($exists){
			$upd = $this->conn->query("UPDATE admin_section_views SET last_viewed = '{$now}' WHERE admin_id = '{$admin_id}' AND section = '{$section}'");
		}else{
			$ins = $this->conn->query("INSERT INTO admin_section_views (admin_id, section, last_viewed) VALUES ('{$admin_id}','{$section}','{$now}')");
		}
		return json_encode(['status'=>'success']);
	}
	
	// Order status update
	function update_order_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `order_list` set status = '{$status}' where id = '{$id}'");
		if($update){
			// Auto-generate invoice when order is marked as "Claimed" (status 6)
			if($status == 6) {
				require_once 'Invoice.php';
				$invoice = new Invoice();
				$invoice_result = $invoice->createInvoiceFromOrder($id, $this->settings->userdata('id'));
				
				if($invoice_result['status'] == 'success') {
					$resp['status'] = 'success';
					$resp['msg'] = "Order status updated and invoice generated successfully. Invoice: " . $invoice_result['invoice_number'];
					$resp['invoice_number'] = $invoice_result['invoice_number'];
				} else {
					$resp['status'] = 'success';
					$resp['msg'] = "Order status updated successfully, but invoice generation failed: " . $invoice_result['msg'];
				}
			} else {
				$resp['status'] = 'success';
				$resp['msg'] = "Order status successfully updated.";
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Order status update failed.";
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	
	// Confirm order receipt
	function confirm_receipt(){
		extract($_POST);
		$resp = array();
		$id = isset($id) ? intval($id) : 0;
		if($id <= 0){
			return json_encode(['status'=>'failed','msg'=>'Invalid order id.']);
		}
		
		$check = $this->conn->query("SELECT id, status, client_id FROM `order_list` WHERE id = '{$id}'");
		if(!$check || $check->num_rows == 0){
			return json_encode(['status'=>'failed','msg'=>'Order not found.']);
		}
		$order = $check->fetch_assoc();
		
		$user_id = $this->settings->userdata('id');
		
		// Only allow the customer who placed the order to confirm receipt
		if($order['client_id'] != $user_id){
			return json_encode(['status'=>'failed','msg'=>'You can only confirm receipt of your own orders.']);
		}
		
		// Only allow confirmation if order is delivered (status 4)
		if($order['status'] != 4){
			return json_encode(['status'=>'failed','msg'=>'You can only confirm receipt of delivered orders.']);
		}
		
		// Update order status to claimed (status 6)
		$update = $this->conn->query("UPDATE `order_list` SET status = 6 WHERE id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = 'Order receipt confirmed successfully. Thank you for your purchase!';
			
			// Auto-generate invoice when order is confirmed as received
			try {
				require_once 'Invoice.php';
				$invoice = new Invoice();
				$invoice_result = $invoice->createInvoiceFromOrder($id, $this->settings->userdata('id'));
				
				if($invoice_result['status'] == 'success') {
					$resp['msg'] .= " Invoice generated: " . $invoice_result['invoice_number'];
					$resp['invoice_number'] = $invoice_result['invoice_number'];
				}
			} catch (Exception $e) { 
				// Non-fatal error - invoice generation failed but order is still confirmed
			}
			
			// Create notification
			try {
				if(file_exists(base_app.'classes/Notification.php')){
					require_once base_app.'classes/Notification.php';
					$notif = new Notification();
					$notif->createNotification($order['client_id'], 'order', 'Order Confirmed', 'You have confirmed receipt of your order. Thank you for your purchase!', [ 'order_id' => $id ]);
				}
			} catch (Exception $e) { /* non-fatal */ }
		}else{
			$resp = ['status'=>'failed','msg'=>'Failed to confirm order receipt.','error'=>$this->conn->error];
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
    // Fetch file path to delete from storage
    $fp = $this->conn->query("SELECT file_path FROM `or_cr_documents` WHERE id = '{$document_id}'");
    $filePath = '';
    if($fp && $fp->num_rows > 0){
        $row = $fp->fetch_assoc();
        $filePath = $row['file_path'];
    }
    $del = $this->conn->query("DELETE FROM `or_cr_documents` where id = '{$document_id}'");
		if($del){
        // Attempt to delete the physical file if present
        if(!empty($filePath)){
            $parsed = parse_url($filePath);
            $pathOnly = isset($parsed['path']) ? $parsed['path'] : $filePath;
            $absPath = base_app . ltrim($pathOnly, '/');
            if(is_file($absPath)) @unlink($absPath);
        }
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
		$resp = array();
		
		// Basic validation
		if(empty($client_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Not authenticated.';
			return json_encode($resp);
		}
		if(empty($document_type) || empty($document_number)){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Document type and number are required.';
			return json_encode($resp);
		}
		if(!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Please select a valid document file to upload.';
			return json_encode($resp);
		}
		
		// Ensure upload directory
		$dir = base_app."uploads/documents/";
		if(!is_dir($dir)){
			mkdir($dir, 0755, true);
		}
		
		// Validate file type
		$allowed_types = ['pdf','jpg','jpeg','png'];
		$extension = strtolower(pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION));
		if(!in_array($extension, $allowed_types)){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Invalid file type. Allowed: PDF, JPG, JPEG, PNG.';
			return json_encode($resp);
		}
		
		// Insert minimal, safe columns plus optional plate and release date
		$doc_type = $this->conn->real_escape_string($document_type);
		$doc_number = $this->conn->real_escape_string($document_number);
		$plate_val = isset($plate_number) ? $this->conn->real_escape_string($plate_number) : '';
		$release_val = isset($release_date) && !empty($release_date) ? $this->conn->real_escape_string($release_date) : null;
		$remarks_val = isset($remarks) ? $this->conn->real_escape_string($remarks) : '';
		$data = "client_id = '{$client_id}', document_type = '{$doc_type}', document_number = '{$doc_number}', status = 'pending', remarks = '{$remarks_val}', plate_number = '{$plate_val}'";
		if(!is_null($release_val)){
			$data .= ", release_date = '{$release_val}'";
		}
		$sql = "INSERT INTO `or_cr_documents` SET {$data}";
		$save = $this->conn->query($sql);
		if(!$save){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Failed to create document record.';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		$doc_id = $this->conn->insert_id;
		$name = $doc_id.'.'.$extension;
		if(is_file($dir.$name)) unlink($dir.$name);
		$move = move_uploaded_file($_FILES['document_file']['tmp_name'], $dir.$name);
		if(!$move){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Failed to move uploaded file.';
			return json_encode($resp);
		}
		$this->conn->query("UPDATE `or_cr_documents` SET file_path = CONCAT('uploads/documents/{$name}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) WHERE id = '{$doc_id}'");
		
		$resp['status'] = 'success';
		$resp['msg'] = 'Document successfully uploaded.';
		return json_encode($resp);
	}
	
	function add_document(){
		extract($_POST);
		$resp = array();
		
		// Debug logging
		error_log("Add document function called with data: " . print_r($_POST, true));
		error_log("Files: " . print_r($_FILES, true));
		
		// Validate required inputs
		if(empty($client_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Client is required.';
			return json_encode($resp);
		}
		if(empty($document_type)){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Document type is required.';
			return json_encode($resp);
		}
		// File must be provided
		if(!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Please select a valid document file to upload.';
			return json_encode($resp);
		}
		
		// Sanitize and normalize
		$client_id = $this->conn->real_escape_string($client_id);
		$doc_type = in_array(strtolower($document_type), ['or','cr']) ? strtolower($document_type) : 'or';
		$doc_number = isset($document_number) ? $this->conn->real_escape_string($document_number) : '';
		$plate = isset($plate_number) ? $this->conn->real_escape_string($plate_number) : '';
		$remarks_val = isset($remarks) ? $this->conn->real_escape_string($remarks) : '';
		$status_val = isset($status) && in_array($status, ['pending','released','expired']) ? $status : 'pending';
		
		// Ensure upload directory
		$dir = base_app."uploads/documents/";
		if(!is_dir($dir)){
			mkdir($dir, 0755, true);
		}
		
		// Validate file type
		$allowed_types = ['pdf','jpg','jpeg','png'];
		$extension = strtolower(pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION));
		if(!in_array($extension, $allowed_types)){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Invalid file type. Allowed: PDF, JPG, JPEG, PNG.';
			return json_encode($resp);
		}
		
		// Insert only safe/common columns
		$data = "client_id = '{$client_id}', document_type = '{$doc_type}', document_number = '{$doc_number}', plate_number = '{$plate}', status = '{$status_val}', remarks = '{$remarks_val}'";
		$sql = "INSERT INTO `or_cr_documents` SET {$data}";
		$save = $this->conn->query($sql);
		if(!$save){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Failed to add document.';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
		}
		
		$doc_id = $this->conn->insert_id;
		$name = $doc_id.'.'.$extension;
		if(is_file($dir.$name)) unlink($dir.$name);
		$move = move_uploaded_file($_FILES['document_file']['tmp_name'], $dir.$name);
		if(!$move){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Failed to move uploaded file.';
			return json_encode($resp);
		}
		$this->conn->query("UPDATE `or_cr_documents` SET file_path = CONCAT('uploads/documents/{$name}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) WHERE id = '{$doc_id}'");
		
		$resp['status'] = 'success';
		$resp['msg'] = 'Document successfully added.';
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

	// Customer dashboard lightweight data for auto-refresh
	function get_customer_dashboard_data(){
		$resp = array();
		$client_id = $this->settings->userdata('id');
		if(empty($client_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Not authenticated.';
			return json_encode($resp);
		}
		$client_id = $this->conn->real_escape_string($client_id);
		$client = $this->conn->query("SELECT account_balance FROM client_list WHERE id = '{$client_id}' AND delete_flag = 0");
		if(!$client || $client->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Client not found.';
			return json_encode($resp);
		}
		$balance = $client->fetch_assoc()['account_balance'];
		$balance = $balance ? $balance : 0;
		$resp['status'] = 'success';
		$resp['data'] = [ 'balance' => (float)$balance ];
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
		
		// Validate inputs (make reason optional to avoid false "All fields are required" errors)
		if(empty($client_id) || empty($adjustment_type) || empty($amount)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Client, adjustment type, and amount are required.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$client_id = $this->conn->real_escape_string($client_id);
		$adjustment_type = $this->conn->real_escape_string($adjustment_type);
		$amount = (float)$amount;
		$reason = isset($reason) ? $this->conn->real_escape_string(trim($reason)) : '';
		
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
        if(empty($product_id) || $product_id <= 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Please select a valid product.";
			return json_encode($resp);
		}
		
		if(!isset($quantity) || $quantity <= 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Please enter a valid quantity greater than 0.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$product_id = $this->conn->real_escape_string($product_id);
		$quantity = (float)$quantity;
        $reason = isset($reason) ? $this->conn->real_escape_string($reason) : (empty($id) ? 'Stock addition' : 'Stock edit');
        $stock_id = isset($id) && !empty($id) ? (int)$id : 0;
		
        // Get current total stock
		$current_stock_query = $this->conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$product_id}' AND type = 1");
		$current_stock = $current_stock_query->fetch_assoc()['total_stock'];
		$current_stock = $current_stock ? $current_stock : 0;
		
		// Start transaction
		$this->conn->begin_transaction();
		
		try {
            if($stock_id > 0){
                // Editing an existing stock entry
                $existing = $this->conn->query("SELECT * FROM stock_list WHERE id = '{$stock_id}' AND product_id = '{$product_id}'");
                if($existing->num_rows == 0){
                    throw new Exception('Stock entry not found.');
                }
                $row = $existing->fetch_assoc();
                $old_qty = (float)$row['quantity'];
                $delta = $quantity - $old_qty;
                
                // Update the stock_list row
                $update = $this->conn->query("UPDATE stock_list SET quantity = '{$quantity}' WHERE id = '{$stock_id}'");
                if(!$update){
                    throw new Exception('Failed to update stock row: ' . $this->conn->error);
                }
                
                // Compute new total stock after edit
                $total_after = $current_stock + $delta;
                
                // Record adjustment movement
                $movement_data = "('{$product_id}', 'ADJUSTMENT', '".($delta)."', '{$current_stock}', '{$total_after}', '{$reason}', 'STOCK_EDIT', 'ADJUSTMENT', NOW(), NULL)";
                $this->conn->query("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, reference_id, reference_type, date_created, created_by) VALUES {$movement_data}");
                
                // Alerts
                $this->check_stock_alerts($product_id, $total_after);
                
                $this->conn->commit();
                $resp['status'] = 'success';
                $resp['msg'] = 'Stock updated successfully.';
                $resp['new_stock'] = $total_after;
            } else {
                // Adding a new stock entry (IN)
                if($quantity <= 0){
                    throw new Exception('Quantity must be greater than zero for new stock.');
                }
                $new_stock = $current_stock + $quantity;
                
			$stock_data = "('{$product_id}', '{$quantity}', 1, NOW())";
			$insert_stock = $this->conn->query("INSERT INTO stock_list (product_id, quantity, type, date_created) VALUES {$stock_data}");
			if(!$insert_stock){
				throw new Exception("Failed to add stock: " . $this->conn->error);
			}
			
			$movement_data = "('{$product_id}', 'IN', '{$quantity}', '{$current_stock}', '{$new_stock}', '{$reason}', 'STOCK_ADD', 'PURCHASE', NOW(), NULL)";
                $this->conn->query("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, reference_id, reference_type, date_created, created_by) VALUES {$movement_data}");
			
				$this->check_stock_alerts($product_id, $new_stock);

				// If previously out of stock and now available, trigger back-in-stock notifications
				try {
					$was_zero = ($current_stock <= 0);
					$now_positive = ($new_stock > 0);
					if($was_zero && $now_positive){
						// Fire product availability notifications via Notification class if present
						if(file_exists(base_app.'classes/Notification.php')){
							require_once(base_app.'classes/Notification.php');
							$notif = new Notification();
							if(method_exists($notif, 'sendProductAvailabilityNotification')){
								$notif->sendProductAvailabilityNotification($product_id);
							}
						}
					}
				} catch (Exception $e) { /* non-fatal */ }
			
			$this->conn->commit();
			$resp['status'] = 'success';
			$resp['msg'] = "Stock added successfully.";
			$resp['new_stock'] = $new_stock;
            }
		} catch (Exception $e) {
			$this->conn->rollback();
			$resp['status'] = 'failed';
            $resp['msg'] = "Failed to save stock: " . $e->getMessage();
		}
		
		return json_encode($resp);
	}
	
	function delete_stock(){
		extract($_POST);
		$resp = array();
		
		// Validate input
		if(empty($id) || $id <= 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invalid stock entry ID.";
			return json_encode($resp);
		}
		
		$stock_id = (int)$id;
		
		// Check if stock entry exists
		$check = $this->conn->query("SELECT * FROM stock_list WHERE id = '{$stock_id}'");
		if($check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Stock entry not found.";
			return json_encode($resp);
		}
		
		$stock_data = $check->fetch_assoc();
		$product_id = $stock_data['product_id'];
		$quantity = $stock_data['quantity'];
		
		// Start transaction
		$this->conn->begin_transaction();
		
		try {
			// Get current total stock
			$current_stock_query = $this->conn->query("SELECT SUM(quantity) as total_stock FROM stock_list WHERE product_id = '{$product_id}' AND type = 1");
			$current_stock = $current_stock_query->fetch_assoc()['total_stock'];
			$current_stock = $current_stock ? $current_stock : 0;
			
			// Calculate new stock after deletion
			$new_stock = $current_stock - $quantity;
			
			// Delete the stock entry
			$delete = $this->conn->query("DELETE FROM stock_list WHERE id = '{$stock_id}'");
			if(!$delete){
				throw new Exception("Failed to delete stock entry: " . $this->conn->error);
			}
			
			// Record stock movement
			$movement_data = "('{$product_id}', 'OUT', '{$quantity}', '{$current_stock}', '{$new_stock}', 'Stock entry deleted', 'STOCK_DELETE', 'DELETION', NOW(), NULL)";
			$this->conn->query("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, reference_id, reference_type, date_created, created_by) VALUES {$movement_data}");
			
			// Check for stock alerts
			$this->check_stock_alerts($product_id, $new_stock);
			
			$this->conn->commit();
			$resp['status'] = 'success';
			$resp['msg'] = "Stock entry deleted successfully.";
			$resp['new_stock'] = $new_stock;
			
		} catch (Exception $e) {
			$this->conn->rollback();
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to delete stock entry: " . $e->getMessage();
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
		// Get ABC analysis data directly from product_list with stock information
		$abc_query = $this->conn->query("
			SELECT p.*,
				   COALESCE(s.total_stock, 0) as current_stock,
				   COALESCE(o.total_ordered, 0) as total_ordered,
				   (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) as available_stock,
				   CASE 
					   WHEN (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) <= 0 THEN 'OUT_OF_STOCK'
					   WHEN (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) <= p.reorder_point THEN 'LOW_STOCK'
					   WHEN (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) >= p.max_stock THEN 'OVERSTOCK'
					   ELSE 'NORMAL'
				   END as stock_status
			FROM product_list p
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
			WHERE p.delete_flag = 0
			ORDER BY COALESCE(p.abc_category, 'C'), p.price DESC
		");
		
		$data = [];
		$category_stats = ['A' => 0, 'B' => 0, 'C' => 0];
		$total_value = 0;
		
		while($row = $abc_query->fetch_assoc()){
			// Ensure abc_category is set, default to 'C' if not
			if(empty($row['abc_category'])) {
				$row['abc_category'] = 'C';
			}
			
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
		$resp = array();
		
		// Check if inventory_alerts table exists
		$table_check = $this->conn->query("SHOW TABLES LIKE 'inventory_alerts'");
		if($table_check->num_rows == 0){
			// Table doesn't exist, create it
			$create_table = $this->conn->query("
				CREATE TABLE IF NOT EXISTS `inventory_alerts` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`product_id` int(11) NOT NULL,
					`alert_type` varchar(50) NOT NULL,
					`current_stock` decimal(10,2) NOT NULL,
					`threshold_value` decimal(10,2) NOT NULL,
					`message` text NOT NULL,
					`is_resolved` tinyint(1) NOT NULL DEFAULT 0,
					`resolved_by` varchar(100) DEFAULT NULL,
					`resolved_date` datetime DEFAULT NULL,
					`date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (`id`),
					KEY `idx_product_id` (`product_id`),
					KEY `idx_alert_type` (`alert_type`),
					KEY `idx_is_resolved` (`is_resolved`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			");
			
			if(!$create_table){
				$resp['status'] = 'failed';
				$resp['msg'] = "Failed to create inventory_alerts table: " . $this->conn->error;
				return json_encode($resp);
			}
		}
		
		$alerts_query = $this->conn->query("
			SELECT ia.*, p.name as product_name, p.abc_category
			FROM inventory_alerts ia
			JOIN product_list p ON ia.product_id = p.id
			WHERE ia.is_resolved = 0
			ORDER BY ia.date_created DESC
		");
		
		if(!$alerts_query){
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to query inventory alerts: " . $this->conn->error;
			return json_encode($resp);
		}
		
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
		$resp = array();
		
		// Validate inputs
		if(empty($alert_id)){
			$resp['status'] = 'failed';
			$resp['msg'] = "Alert ID is required.";
			return json_encode($resp);
		}
		
		// Sanitize inputs
		$alert_id = $this->conn->real_escape_string($alert_id);
		$resolved_by = isset($resolved_by) ? $this->conn->real_escape_string($resolved_by) : NULL;
		
		// Check if alert exists
		$check_alert = $this->conn->query("SELECT id FROM inventory_alerts WHERE id = '{$alert_id}'");
		if($check_alert->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Alert not found.";
			return json_encode($resp);
		}
		
		// Update alert
		$update_query = "UPDATE inventory_alerts SET is_resolved = 1, resolved_by = " . ($resolved_by ? "'{$resolved_by}'" : "NULL") . ", resolved_date = NOW() WHERE id = '{$alert_id}'";
		$update = $this->conn->query($update_query);
		
		if($update){
			$resp['status'] = 'success';
			$resp['msg'] = "Alert resolved successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to resolve alert: " . $this->conn->error;
		}
		
		return json_encode($resp);
	}
	
	function create_test_alerts(){
		$resp = array();
		
		// Check if inventory_alerts table exists
		$table_check = $this->conn->query("SHOW TABLES LIKE 'inventory_alerts'");
		if($table_check->num_rows == 0){
			// Table doesn't exist, create it
			$create_table = $this->conn->query("
				CREATE TABLE IF NOT EXISTS `inventory_alerts` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`product_id` int(11) NOT NULL,
					`alert_type` varchar(50) NOT NULL,
					`current_stock` decimal(10,2) NOT NULL,
					`threshold_value` decimal(10,2) NOT NULL,
					`message` text NOT NULL,
					`is_resolved` tinyint(1) NOT NULL DEFAULT 0,
					`resolved_by` varchar(100) DEFAULT NULL,
					`resolved_date` datetime DEFAULT NULL,
					`date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
					PRIMARY KEY (`id`),
					KEY `idx_product_id` (`product_id`),
					KEY `idx_alert_type` (`alert_type`),
					KEY `idx_is_resolved` (`is_resolved`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
			");
		}
		
		// Get some products to create test alerts
		$products = $this->conn->query("SELECT id, name FROM product_list WHERE delete_flag = 0 LIMIT 3");
		
		if($products->num_rows > 0){
			$alert_count = 0;
			while($product = $products->fetch_assoc()){
				// Create a low stock alert
				$alert_data = "('{$product['id']}', 'LOW_STOCK', '5', '10', 'Low stock alert: {$product['name']} has 5 units remaining (Reorder point: 10)', 0, NULL, NULL, NOW())";
				$insert = $this->conn->query("INSERT INTO inventory_alerts (product_id, alert_type, current_stock, threshold_value, message, is_resolved, resolved_by, resolved_date, date_created) VALUES {$alert_data}");
				if($insert) $alert_count++;
			}
			
			$resp['status'] = 'success';
			$resp['msg'] = "Created {$alert_count} test alerts.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "No products found to create test alerts.";
		}
		
		return json_encode($resp);
	}
	
	function clear_all_alerts(){
		$resp = array();
		
		// Clear all alerts
		$clear = $this->conn->query("DELETE FROM inventory_alerts");
		
		if($clear){
			$resp['status'] = 'success';
			$resp['msg'] = "All alerts cleared successfully.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to clear alerts: " . $this->conn->error;
		}
		
		return json_encode($resp);
	}
	
	function get_alternative_products(){
		$resp = array();
		
		// Set content type to JSON
		header('Content-Type: application/json');
		
		try {
			extract($_POST);
			
			// Validate inputs
			if(empty($product_id)){
				$resp['status'] = 'failed';
				$resp['msg'] = "Product ID is required.";
				return json_encode($resp);
			}
		
			// Sanitize inputs
			$product_id = $this->conn->real_escape_string($product_id);
			$category = isset($category) ? $this->conn->real_escape_string($category) : '';
			
			// Get alternative products from the same category
			$alternatives_query = $this->conn->query("
				SELECT p.*, b.name as brand, c.category,
					   COALESCE(s.total_stock, 0) as current_stock,
					   COALESCE(o.total_ordered, 0) as total_ordered,
					   (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) as available_stock
				FROM product_list p
				INNER JOIN brand_list b ON p.brand_id = b.id
				INNER JOIN categories c ON p.category_id = c.id
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
				WHERE p.delete_flag = 0 
				AND p.status = 1 
				AND p.id != '{$product_id}'
				AND (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) > 0
				" . (!empty($category) ? "AND c.category = '{$category}'" : "") . "
				ORDER BY p.price ASC
				LIMIT 4
			");
			
			$alternatives = [];
			if($alternatives_query){
				while($row = $alternatives_query->fetch_assoc()){
					$alternatives[] = $row;
				}
			}
			
			$resp['status'] = 'success';
			$resp['alternatives'] = $alternatives;
			
		} catch (Exception $e) {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred: " . $e->getMessage();
			$resp['alternatives'] = [];
		}
		
		return json_encode($resp);
	}
	
	function set_product_notification(){
		$resp = array();
		
		// Set content type to JSON
		header('Content-Type: application/json');
		
		try {
			extract($_POST);
			
			// Debug logging
			error_log("Product notification request - Product ID: " . (isset($product_id) ? $product_id : 'not set'));
			error_log("POST data: " . print_r($_POST, true));
			
			// Validate inputs
			if(empty($product_id)){
				$resp['status'] = 'failed';
				$resp['msg'] = "Product ID is required.";
				return json_encode($resp);
			}
			
			// Check if user is logged in
			$user_id = $this->settings->userdata('id');
			$login_type = $this->settings->userdata('login_type');
			
			if(empty($user_id) || $login_type != 2){
				$resp['status'] = 'failed';
				$resp['msg'] = "Please login to set product notifications.";
				return json_encode($resp);
			}
			
			// Sanitize inputs
			$product_id = $this->conn->real_escape_string($product_id);
			$user_id = $this->conn->real_escape_string($user_id);
			
			// Check if product_notifications table exists, create if not
			$table_check = $this->conn->query("SHOW TABLES LIKE 'product_notifications'");
			if($table_check->num_rows == 0){
				$create_table = $this->conn->query("
					CREATE TABLE `product_notifications` (
						`id` int(11) NOT NULL AUTO_INCREMENT,
						`product_id` int(11) NOT NULL,
						`user_id` int(11) NOT NULL,
						`is_active` tinyint(1) DEFAULT 1,
						`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
						`notified_at` datetime DEFAULT NULL,
						PRIMARY KEY (`id`),
						KEY `product_id` (`product_id`),
						KEY `user_id` (`user_id`),
						KEY `is_active` (`is_active`),
						UNIQUE KEY `unique_notification` (`product_id`, `user_id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
				");
				if(!$create_table){
					$resp['status'] = 'failed';
					$resp['msg'] = "Failed to create notifications table: " . $this->conn->error;
					return json_encode($resp);
				}
			}
			
			// Check if product exists
			$product_check = $this->conn->query("SELECT id FROM product_list WHERE id = '{$product_id}' AND delete_flag = 0");
			if($product_check->num_rows == 0){
				$resp['status'] = 'failed';
				$resp['msg'] = "Product not found.";
				return json_encode($resp);
			}
			
			// Check if notification already exists
			$existing = $this->conn->query("SELECT id FROM product_notifications WHERE product_id = '{$product_id}' AND user_id = '{$user_id}' AND is_active = 1");
			
			if($existing->num_rows > 0){
				$resp['status'] = 'failed';
				$resp['msg'] = "You are already subscribed to notifications for this product.";
				return json_encode($resp);
			}
			
			// Create notification
			$insert_query = "INSERT INTO product_notifications (product_id, user_id, is_active, created_at) VALUES ('{$product_id}', '{$user_id}', 1, NOW())";
			$insert = $this->conn->query($insert_query);
			
			if($insert){
				$resp['status'] = 'success';
				$resp['msg'] = "Notification set successfully. You will be notified when this product becomes available.";
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = "Failed to set notification: " . $this->conn->error;
			}
			
		} catch (Exception $e) {
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred: " . $e->getMessage();
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
    
    // Mark credit application as completed
    function mark_credit_application_completed(){
        extract($_POST);
        $update = $this->conn->query("UPDATE `client_list` SET credit_application_completed = 1 WHERE id = '{$customer_id}'");
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = "Credit application marked as completed successfully.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to update application status.";
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    // Promo and Customer Image Management Functions
    function toggle_promo_status(){
        extract($_POST);
        $update = $this->conn->query("UPDATE `promo_images` SET is_active = '{$status}' WHERE id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = "Promo status updated successfully.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to update promo status.";
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function delete_promo(){
        extract($_POST);
        $qry = $this->conn->query("SELECT image_path FROM `promo_images` WHERE id = '{$id}'");
        if($qry->num_rows > 0){
            $row = $qry->fetch_assoc();
            if(is_file('../'.$row['image_path'])) unlink('../'.$row['image_path']);
        }
        $delete = $this->conn->query("DELETE FROM `promo_images` WHERE id = '{$id}'");
        if($delete){
            $resp['status'] = 'success';
            $resp['msg'] = "Promo image deleted successfully.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to delete promo image.";
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function toggle_customer_status(){
        extract($_POST);
        $update = $this->conn->query("UPDATE `customer_purchase_images` SET is_active = '{$status}' WHERE id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = "Customer image status updated successfully.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to update customer image status.";
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function delete_customer(){
        extract($_POST);
        $qry = $this->conn->query("SELECT image_path FROM `customer_purchase_images` WHERE id = '{$id}'");
        if($qry->num_rows > 0){
            $row = $qry->fetch_assoc();
            if(is_file('../'.$row['image_path'])) unlink('../'.$row['image_path']);
        }
        $delete = $this->conn->query("DELETE FROM `customer_purchase_images` WHERE id = '{$id}'");
        if($delete){
            $resp['status'] = 'success';
            $resp['msg'] = "Customer image deleted successfully.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to delete customer image.";
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function update_promo(){
        extract($_POST);
        $title = $this->conn->real_escape_string($title);
        $description = $this->conn->real_escape_string($description);
        $update = $this->conn->query("UPDATE `promo_images` SET title = '{$title}', description = '{$description}' WHERE id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = "Promo image updated successfully.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to update promo image.";
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function update_customer(){
        extract($_POST);
        $customer_name = $this->conn->real_escape_string($customer_name);
        $motorcycle_model = $this->conn->real_escape_string($motorcycle_model);
        $testimonial = $this->conn->real_escape_string($testimonial);
        $purchase_date = !empty($purchase_date) ? $purchase_date : null;
        $update = $this->conn->query("UPDATE `customer_purchase_images` SET customer_name = '{$customer_name}', motorcycle_model = '{$motorcycle_model}', testimonial = '{$testimonial}', purchase_date = " . ($purchase_date ? "'{$purchase_date}'" : "NULL") . " WHERE id = '{$id}'");
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = "Customer image updated successfully.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to update customer image.";
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    // Appointment functions
    function book_appointment(){
        extract($_POST);
        
        // Validate required fields
        if(empty($client_id) || empty($service_type) || empty($appointment_date) || empty($appointment_time)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Please fill in all required fields.";
            return json_encode($resp);
        }
        
        // Sanitize inputs
        $client_id = $this->conn->real_escape_string($client_id);
        $service_type = $this->conn->real_escape_string($service_type);
        $mechanic_id = isset($mechanic_id) && !empty($mechanic_id) ? $this->conn->real_escape_string($mechanic_id) : 'NULL';
        $appointment_date = $this->conn->real_escape_string($appointment_date);
        $appointment_time = $this->conn->real_escape_string($appointment_time);
        $vehicle_info = isset($vehicle_info) ? $this->conn->real_escape_string($vehicle_info) : '';
        $notes = isset($notes) ? $this->conn->real_escape_string($notes) : '';
        
        // Check if appointment slot is available
        $availability_check = $this->conn->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date = '{$appointment_date}' AND appointment_time = '{$appointment_time}' AND status != 'cancelled'");
        if($availability_check->fetch_assoc()['count'] > 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "This time slot is already booked. Please choose another time.";
            return json_encode($resp);
        }
        
        // Check if service exists
        $service_check = $this->conn->query("SELECT id FROM service_list WHERE id = '{$service_type}' AND status = 1 AND delete_flag = 0");
        if($service_check->num_rows == 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Selected service is not available.";
            return json_encode($resp);
        }
        
        // Check if mechanic exists (if specified)
        if($mechanic_id != 'NULL'){
            $mechanic_check = $this->conn->query("SELECT id FROM mechanics_list WHERE id = '{$mechanic_id}' AND status = 1");
            if($mechanic_check->num_rows == 0){
                $resp['status'] = 'failed';
                $resp['msg'] = "Selected mechanic is not available.";
                return json_encode($resp);
            }
        }
        
        // Insert appointment
        $sql = "INSERT INTO appointments (client_id, service_type, mechanic_id, appointment_date, appointment_time, vehicle_info, notes, status) VALUES ('{$client_id}', '{$service_type}', {$mechanic_id}, '{$appointment_date}', '{$appointment_time}', '{$vehicle_info}', '{$notes}', 'pending')";
        
        $save = $this->conn->query($sql);
        if($save){
            $resp['status'] = 'success';
            $resp['msg'] = "Appointment booked successfully!";
            $resp['appointment_id'] = $this->conn->insert_id;
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to book appointment.";
            $resp['error'] = $this->conn->error;
        }
        
        return json_encode($resp);
    }
    
    function save_appointment(){
        extract($_POST);
        $resp = [];
        // Required fields
        if(empty($client_id) || empty($service_type) || empty($appointment_date) || empty($appointment_time)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Please fill in all required fields.";
            return json_encode($resp);
        }

        $client_id = $this->conn->real_escape_string($client_id);
        $service_type = $this->conn->real_escape_string($service_type);
        $mechanic_id = isset($mechanic_id) && $mechanic_id !== '' ? $this->conn->real_escape_string($mechanic_id) : 'NULL';
        $appointment_date = $this->conn->real_escape_string($appointment_date);
        $appointment_time = $this->conn->real_escape_string($appointment_time);
        $vehicle_info = isset($vehicle_info) ? $this->conn->real_escape_string($vehicle_info) : '';
        $notes = isset($notes) ? $this->conn->real_escape_string($notes) : '';
        $status = isset($status) && in_array($status, ['pending','confirmed','cancelled','completed']) ? $this->conn->real_escape_string($status) : 'pending';

        if(empty($id)){
            // ensure slot availability on create
            $availability_check = $this->conn->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date = '{$appointment_date}' AND appointment_time = '{$appointment_time}' AND status != 'cancelled'");
            if($availability_check->fetch_assoc()['count'] > 0){
                $resp['status'] = 'failed';
                $resp['msg'] = "This time slot is already booked. Please choose another time.";
                return json_encode($resp);
            }
            $sql = "INSERT INTO appointments (client_id, service_type, mechanic_id, appointment_date, appointment_time, vehicle_info, notes, status) VALUES ('{$client_id}', '{$service_type}', {$mechanic_id}, '{$appointment_date}', '{$appointment_time}', '{$vehicle_info}', '{$notes}', '{$status}')";
        } else {
            $id = $this->conn->real_escape_string($id);
            $sql = "UPDATE appointments SET client_id='{$client_id}', service_type='{$service_type}', mechanic_id={$mechanic_id}, appointment_date='{$appointment_date}', appointment_time='{$appointment_time}', vehicle_info='{$vehicle_info}', notes='{$notes}', status='{$status}' WHERE id='{$id}'";
        }

        $save = $this->conn->query($sql);
        if($save){
            $resp['status'] = 'success';
            $resp['msg'] = empty($id) ? 'Appointment saved successfully.' : 'Appointment updated successfully.';
            if(empty($id)) $resp['id'] = $this->conn->insert_id; else $resp['id'] = $id;
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to save appointment.';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }

    function delete_appointment(){
        extract($_POST);
        $resp = [];
        if(empty($id)){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Missing appointment id.';
            return json_encode($resp);
        }
        $id = $this->conn->real_escape_string($id);
        $del = $this->conn->query("DELETE FROM appointments WHERE id='{$id}'");
        if($del){
            $resp['status'] = 'success';
            $resp['msg'] = 'Appointment deleted successfully.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to delete appointment.';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    
    function check_appointment_availability(){
        extract($_POST);
        
        // Validate inputs
        if(empty($appointment_date) || empty($appointment_time)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Date and time are required.";
            return json_encode($resp);
        }
        
        // Sanitize inputs
        $appointment_date = $this->conn->real_escape_string($appointment_date);
        $appointment_time = $this->conn->real_escape_string($appointment_time);
        
        // Check availability
        $check = $this->conn->query("SELECT COUNT(*) as count FROM appointments WHERE appointment_date = '{$appointment_date}' AND appointment_time = '{$appointment_time}' AND status != 'cancelled'");
        $count = $check->fetch_assoc()['count'];
        
        $resp['status'] = 'success';
        $resp['available'] = $count == 0;
        $resp['msg'] = $count == 0 ? "Time slot is available" : "Time slot is not available";
        
        return json_encode($resp);
    }
    
    function get_related_motorcycles(){
        extract($_POST);
        
        // Validate inputs
        if(empty($product_id)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Product ID is required.";
            return json_encode($resp);
        }
        
        // Sanitize inputs
        $product_id = $this->conn->real_escape_string($product_id);
        $category_id = !empty($category_id) ? $this->conn->real_escape_string($category_id) : null;
        $brand_id = !empty($brand_id) ? $this->conn->real_escape_string($brand_id) : null;
        
        // Build the query to get related motorcycles
        $where_conditions = ["p.delete_flag = 0", "p.status = 1", "p.id != '{$product_id}'"];
        
        // Add category filter if provided
        if($category_id) {
            $where_conditions[] = "p.category_id = '{$category_id}'";
        }
        
        // Add brand filter if provided
        if($brand_id) {
            $where_conditions[] = "p.brand_id = '{$brand_id}'";
        }
        
        $where_clause = implode(" AND ", $where_conditions);
        
        // Get related motorcycles with stock information
        $related_query = $this->conn->query("
            SELECT p.*, b.name as brand, c.category,
                   COALESCE(s.total_stock, 0) as current_stock,
                   COALESCE(o.total_ordered, 0) as total_ordered,
                   (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) as available_stock
            FROM product_list p
            LEFT JOIN brand_list b ON p.brand_id = b.id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN (
                SELECT product_id, SUM(quantity) as total_stock 
                FROM stock_list 
                WHERE type = 1 
                GROUP BY product_id
            ) s ON p.id = s.product_id
            LEFT JOIN (
                SELECT oi.product_id, SUM(oi.quantity) as total_ordered
                FROM order_items oi
                INNER JOIN order_list ol ON oi.order_id = ol.id
                WHERE ol.status != 5
                GROUP BY oi.product_id
            ) o ON p.id = o.product_id
            WHERE {$where_clause}
            ORDER BY 
                CASE WHEN p.brand_id = '{$brand_id}' THEN 1 ELSE 2 END,
                CASE WHEN p.category_id = '{$category_id}' THEN 1 ELSE 2 END,
                p.name ASC
            LIMIT 6
        ");
        
        if($this->capture_err())
            return $this->capture_err();
        
        $related_motorcycles = [];
        while($row = $related_query->fetch_assoc()){
            $related_motorcycles[] = $row;
        }
        
        $resp['status'] = 'success';
        $resp['related_motorcycles'] = $related_motorcycles;
        
        return json_encode($resp);
    }

    function get_related_products(){
        extract($_POST);
        
        // Validate inputs
        if(empty($product_id)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Product ID is required.";
            return json_encode($resp);
        }
        
        // Sanitize inputs
        $product_id = $this->conn->real_escape_string($product_id);
        $category_id = !empty($category_id) ? $this->conn->real_escape_string($category_id) : null;
        $brand_id = !empty($brand_id) ? $this->conn->real_escape_string($brand_id) : null;
        $category_filter = !empty($category_filter) ? $this->conn->real_escape_string($category_filter) : 'all';
        
        // Build the query to get related products based on category
        $where_conditions = ["p.delete_flag = 0", "p.status = 1", "p.id != '{$product_id}'"];
        
        // Add category-specific filtering
        switch($category_filter) {
            case 'motorcycles':
                // Show motorcycles when viewing motorcycle parts or oils
                $where_conditions[] = "p.category_id = 10"; // Motorcycles category
                break;
            case 'motorcycle_parts':
                // Show motorcycle parts when viewing motorcycles or oils
                $where_conditions[] = "p.category_id = 13"; // Motorcycle Parts category
                break;
            case 'oils':
                // Show oils when viewing motorcycles or motorcycle parts
                $where_conditions[] = "p.category_id = 15"; // Oils category
                break;
            default:
                // Show same category products
                if($category_id) {
                    $where_conditions[] = "p.category_id = '{$category_id}'";
                }
                break;
        }
        
        // Add brand filter if provided (for better relevance)
        if($brand_id) {
            $where_conditions[] = "p.brand_id = '{$brand_id}'";
        }
        
        $where_clause = implode(" AND ", $where_conditions);
        
        // Get related products with stock information
        $related_query = $this->conn->query("
            SELECT p.*, b.name as brand, c.category,
                   COALESCE(s.total_stock, 0) as current_stock,
                   COALESCE(o.total_ordered, 0) as total_ordered,
                   (COALESCE(s.total_stock, 0) - COALESCE(o.total_ordered, 0)) as available_stock
            FROM product_list p
            LEFT JOIN brand_list b ON p.brand_id = b.id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN (
                SELECT product_id, SUM(quantity) as total_stock 
                FROM stock_list 
                WHERE type = 1 
                GROUP BY product_id
            ) s ON p.id = s.product_id
            LEFT JOIN (
                SELECT oi.product_id, SUM(oi.quantity) as total_ordered
                FROM order_items oi
                INNER JOIN order_list ol ON oi.order_id = ol.id
                WHERE ol.status != 5
                GROUP BY oi.product_id
            ) o ON p.id = o.product_id
            WHERE {$where_clause}
            ORDER BY 
                CASE WHEN p.brand_id = '{$brand_id}' THEN 1 ELSE 2 END,
                CASE WHEN p.category_id = '{$category_id}' THEN 1 ELSE 2 END,
                p.name ASC
            LIMIT 6
        ");
        
        if($this->capture_err())
            return $this->capture_err();
        
        $related_products = [];
        while($row = $related_query->fetch_assoc()){
            $related_products[] = $row;
        }
        
        $resp['status'] = 'success';
        $resp['related_products'] = $related_products;
        
        return json_encode($resp);
    }
    
    function update_cart_color(){
        extract($_POST);
        $resp = array();
        
        if(empty($cart_id)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Cart ID is required.";
            return json_encode($resp);
        }
        
        $cart_id = $this->conn->real_escape_string($cart_id);
        $color = !empty($color) ? "'" . $this->conn->real_escape_string($color) . "'" : "NULL";
        
        $sql = "UPDATE `cart_list` SET color = {$color} WHERE id = '{$cart_id}'";
        $save = $this->conn->query($sql);
        
        if($save){
            $resp['status'] = 'success';
            $resp['msg'] = "Color updated successfully.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to update color.";
            $resp['error'] = $this->conn->error;
        }
        
        return json_encode($resp);
    }
    
    function remove_multiple_from_cart(){
        extract($_POST);
        $resp = array();
        
        if(empty($cart_ids)){
            $resp['status'] = 'failed';
            $resp['msg'] = "No items selected.";
            return json_encode($resp);
        }
        
        $cart_ids = $this->conn->real_escape_string($cart_ids);
        $sql = "DELETE FROM `cart_list` WHERE id IN ({$cart_ids})";
        $delete = $this->conn->query($sql);
        
        if($delete){
            $resp['status'] = 'success';
            $resp['msg'] = "Selected items removed from cart.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to remove items from cart.";
            $resp['error'] = $this->conn->error;
        }
        
        return json_encode($resp);
    }

    function cleanup_invalid_cart_items(){
        $resp = array();
        
        try {
            // Start transaction
            $this->conn->query("START TRANSACTION");
            
            // 1. Remove cart items with ID 0 or invalid product_id
            $this->conn->query("DELETE FROM cart_list WHERE id = 0 OR product_id = 0 OR product_id IS NULL");
            
            // 2. Remove cart items that reference non-existent products
            $this->conn->query("DELETE c FROM cart_list c 
                              LEFT JOIN product_list p ON c.product_id = p.id 
                              WHERE p.id IS NULL");
            
            // 3. Remove cart items with invalid client_id
            $this->conn->query("DELETE c FROM cart_list c 
                              LEFT JOIN client_list cl ON c.client_id = cl.id 
                              WHERE cl.id IS NULL");
            
            // 4. Update any cart items with quantity 0 or negative
            $this->conn->query("UPDATE cart_list SET quantity = 1 WHERE quantity <= 0");
            
            // 5. Set proper default values for any NULL quantities
            $this->conn->query("UPDATE cart_list SET quantity = 1 WHERE quantity IS NULL");
            
            // 6. Ensure date_added is set for any items without it
            $this->conn->query("UPDATE cart_list SET date_added = NOW() WHERE date_added IS NULL");
            
            // 7. Remove duplicate cart items (keep the most recent one)
            $this->conn->query("DELETE c1 FROM cart_list c1
                              INNER JOIN cart_list c2 
                              WHERE c1.id < c2.id 
                              AND c1.client_id = c2.client_id 
                              AND c1.product_id = c2.product_id 
                              AND (c1.color = c2.color OR (c1.color IS NULL AND c2.color IS NULL))");
            
            // Commit transaction
            $this->conn->query("COMMIT");
            
            $resp['status'] = 'success';
            $resp['msg'] = "Cart cleanup completed successfully.";
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->conn->query("ROLLBACK");
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to cleanup cart items: " . $e->getMessage();
        }
        
        return json_encode($resp);
    }

    function manual_cart_cleanup(){
        $resp = array();
        
        try {
            // Start transaction
            $this->conn->query("START TRANSACTION");
            
            // Get count of items to be cleaned
            $count_query = $this->conn->query("SELECT COUNT(*) as count FROM cart_list WHERE id = 0 OR product_id = 0 OR product_id IS NULL");
            $count = $count_query->fetch_assoc()['count'];
            
            // Perform cleanup
            $this->conn->query("DELETE FROM cart_list WHERE id = 0 OR product_id = 0 OR product_id IS NULL");
            $this->conn->query("DELETE c FROM cart_list c LEFT JOIN product_list p ON c.product_id = p.id WHERE p.id IS NULL");
            $this->conn->query("DELETE c FROM cart_list c LEFT JOIN client_list cl ON c.client_id = cl.id WHERE cl.id IS NULL");
            $this->conn->query("UPDATE cart_list SET quantity = 1 WHERE quantity <= 0 OR quantity IS NULL");
            $this->conn->query("UPDATE cart_list SET date_added = NOW() WHERE date_added IS NULL");
            
            // Commit transaction
            $this->conn->query("COMMIT");
            
            $resp['status'] = 'success';
            $resp['msg'] = "Cart cleanup completed successfully. Removed {$count} invalid items.";
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->conn->query("ROLLBACK");
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to cleanup cart items: " . $e->getMessage();
        }
        
        return json_encode($resp);
    }
    
    // Upload OR/CR documents for client
    function upload_client_orcr(){
        extract($_POST);
        $resp = array();
        
        // Validate client exists
        $client_check = $this->conn->query("SELECT id, CONCAT(lastname, ', ', firstname) as name FROM client_list WHERE id = '{$client_id}' AND delete_flag = 0");
        if($client_check->num_rows == 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Client not found.';
            return json_encode($resp);
        }
        $client_data = $client_check->fetch_assoc();
        
        // Ensure upload directory
        $dir = base_app."uploads/documents/";
        if(!is_dir($dir)){
            mkdir($dir, 0755, true);
        }
        
        $allowed_types = ['pdf','jpg','jpeg','png'];
        $uploaded_any = false;
        $errors = [];
        
        // Helper to insert a document row and move file
        $process_upload = function($file, $docType) use ($client_id, $dir, $allowed_types){
            if(!isset($_FILES[$file]) || $_FILES[$file]['error'] !== 0){
                return [false, ''];
            }
            $extension = strtolower(pathinfo($_FILES[$file]['name'], PATHINFO_EXTENSION));
            if(!in_array($extension, $allowed_types)){
                return [false, ''];
            }
            // Collect metadata if provided; otherwise fallback to placeholder
            $document_number = isset($_POST['document_number']) && $_POST['document_number'] !== ''
                ? $this->conn->real_escape_string($_POST['document_number'])
                : (strtoupper($docType).' upload '.date('Ymd-His'));
            $plate_number = isset($_POST['plate_number']) ? $this->conn->real_escape_string($_POST['plate_number']) : '';
            $release_date = (isset($_POST['release_date']) && $_POST['release_date'] !== '') ? $this->conn->real_escape_string($_POST['release_date']) : null;
            $status = 'pending';
            $remarks = isset($_POST['remarks']) ? $this->conn->real_escape_string($_POST['remarks']) : '';
            
            // Insert only common columns to avoid SQL errors on missing columns
            $data = "client_id = '{$client_id}', document_type = '{$docType}', document_number = '{$document_number}', plate_number = '{$plate_number}', status = '{$status}', remarks = '{$remarks}'";
            if(!is_null($release_date)){
                $data .= ", release_date = '{$release_date}'";
            }
            $sql = "INSERT INTO `or_cr_documents` set {$data} ";
            $save = $this->conn->query($sql);
            if(!$save){
                return [false, ''];
            }
            $doc_id = $this->conn->insert_id;
            $name = $doc_id.'.'.$extension;
            if(is_file($dir.$name)) unlink($dir.$name);
            $moved = move_uploaded_file($_FILES[$file]['tmp_name'], $dir.$name);
            if($moved){
                $this->conn->query("UPDATE `or_cr_documents` set file_path = CONCAT('uploads/documents/{$name}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$doc_id}'");
                return [true, $doc_id];
            }
            return [false, ''];
        };
        
        list($okOr,) = $process_upload('or_document','or');
        $uploaded_any = $uploaded_any || $okOr;
        list($okCr,) = $process_upload('cr_document','cr');
        $uploaded_any = $uploaded_any || $okCr;
        
        if(!$uploaded_any){
            $resp['status'] = 'failed';
            $resp['msg'] = 'No valid documents uploaded.';
            return json_encode($resp);
        }
        
        $resp['status'] = 'success';
        $resp['msg'] = 'Documents uploaded successfully for ' . $client_data['name'];
        return json_encode($resp);
    }
    
    // Get client OR/CR documents
    function get_client_orcr(){
        extract($_POST);
        $resp = array();
        
        $client_check = $this->conn->query("SELECT id, CONCAT(lastname, ', ', firstname) as name FROM client_list WHERE id = '{$client_id}' AND delete_flag = 0");
        if($client_check->num_rows == 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Client not found.';
            return json_encode($resp);
        }
        $client_data = $client_check->fetch_assoc();
        
		$docs = $this->conn->query("SELECT * FROM or_cr_documents WHERE client_id = '{$client_id}' ORDER BY date_created DESC");
		$html = '<div class="container-fluid">';
		$html .= '<h6 class="mb-3">Documents for: <strong>'.htmlspecialchars($client_data['name']).'</strong></h6>';
		
		if($docs && $docs->num_rows > 0){
			$html .= '<div class="table-responsive">';
			$html .= '<table class="table table-bordered table-striped">';
			$html .= '<thead><tr>'
				. '<th>Document Type</th>'
				. '<th>Document Number</th>'
				. '<th>Plate Number</th>'
				. '<th>Release Date</th>'
				. '<th>Status</th>'
				// . '<th>Action</th>'
				. '</tr></thead>';
			$html .= '<tbody>';
			while($doc = $docs->fetch_assoc()){
				$filePath = $doc['file_path'];
				$displayMissing = false;
				if(!empty($filePath)){
					$parsed = parse_url($filePath);
					$pathOnly = isset($parsed['path']) ? $parsed['path'] : $filePath;
					$absPath = base_app . ltrim($pathOnly, '/');
					if(!is_file($absPath)){
						$displayMissing = true;
					}
				} else {
					$displayMissing = true;
				}
				$status_badge = '<span class="badge badge-'.($doc['status']=='released'?'success':($doc['status']=='expired'?'danger':'warning')).'">'.ucfirst($doc['status']).'</span>';
				$viewBtn = !$displayMissing 
					? '<button type="button" class="btn btn-sm btn-info btn-view-orcr" data-file="'.htmlspecialchars($filePath).'" data-ext="'.htmlspecialchars(strtolower(pathinfo(isset($parsed['path'])?$parsed['path']:$filePath, PATHINFO_EXTENSION))).'">View</button>' 
					: '<span class="text-muted">No File</span>';
				$deleteBtn = '<button type="button" class="btn btn-sm btn-danger btn-delete-orcr" data-id="'.(int)$doc['id'].'">Delete</button>';
				$html .= '<tr>'
					. '<td>'.strtoupper($doc['document_type']).'</td>'
					. '<td>'.htmlspecialchars($doc['document_number']).'</td>'
					. '<td>'.(!empty($doc['plate_number']) ? htmlspecialchars($doc['plate_number']) : 'N/A').'</td>'
					. '<td>'.(!empty($doc['release_date']) ? date('M d, Y', strtotime($doc['release_date'])) : 'N/A').'</td>'
					. '<td>'.$status_badge.'</td>'
					// . '<td class="text-nowrap">'.$viewBtn.' '.$deleteBtn.'</td>'
					. '</tr>';
			}
			$html .= '</tbody></table></div>';
		} else {
			$html .= '<p class="text-muted">No OR/CR documents uploaded yet.</p>';
		}
		
		$html .= '</div>';
        $resp['status'] = 'success';
        $resp['html'] = $html;
        return json_encode($resp);
    }
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
// If an AJAX action is requested, ensure responses are sent as JSON where possible
if(isset($_GET['f'])){
	header('Content-Type: application/json; charset=utf-8');
}
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
	case 'save_appointment':
		echo $Master->save_appointment();
	break;
	case 'delete_appointment':
		echo $Master->delete_appointment();
	break;
	case 'cancel_service':
		echo $Master->cancel_service();
	break;
	case 'save_to_cart':
		echo $Master->save_to_cart();
	break;
	case 'get_product_details':
		echo $Master->get_product_details();
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
	case 'create_test_notification':
		echo $Master->createTestNotification();
	break;
	case 'save_product_compatibility':
		echo $Master->save_product_compatibility();
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
	case 'upload_client_orcr':
		echo $Master->upload_client_orcr();
	break;
	case 'get_client_orcr':
		echo $Master->get_client_orcr();
	break;
	case 'get_client_balance':
		echo $Master->get_client_balance();
	break;
	case 'get_client_transactions':
		echo $Master->get_client_transactions();
	break;
		case 'get_customer_dashboard_data':
			echo $Master->get_customer_dashboard_data();
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
	case 'delete_stock':
		echo $Master->delete_stock();
	break;
	case 'get_abc_analysis':
		echo $Master->get_abc_analysis();
	break;
	case 'get_product_recommendations':
		echo $Master->get_product_recommendations();
	break;
	case 'get_related_motorcycles':
		echo $Master->get_related_motorcycles();
	break;
	case 'get_related_products':
		echo $Master->get_related_products();
	break;
	case 'update_cart_color':
		echo $Master->update_cart_color();
	break;
	case 'remove_multiple_from_cart':
		echo $Master->remove_multiple_from_cart();
	break;
	case 'get_stock_alerts':
		echo $Master->get_stock_alerts();
	break;
	case 'resolve_stock_alert':
		echo $Master->resolve_stock_alert();
	break;
	case 'create_test_alerts':
		echo $Master->create_test_alerts();
	break;
	case 'clear_all_alerts':
		echo $Master->clear_all_alerts();
	break;
	case 'auto_classify_abc':
		echo $Master->auto_classify_abc();
	break;
	case 'get_alternative_products':
		echo $Master->get_alternative_products();
	break;
	case 'set_product_notification':
		echo $Master->set_product_notification();
	break;
	case 'save_review':
		echo $Master->save_review();
	break;
	case 'get_reviews':
		echo $Master->get_reviews();
	break;
	case 'mark_credit_application_completed':
		echo $Master->mark_credit_application_completed();
	break;
	case 'toggle_promo_status':
		echo $Master->toggle_promo_status();
	break;
	case 'delete_promo':
		echo $Master->delete_promo();
	break;
	case 'toggle_customer_status':
		echo $Master->toggle_customer_status();
	break;
	case 'delete_customer':
		echo $Master->delete_customer();
	break;
	case 'delete_invoice':
		echo $Master->delete_invoice();
	break;
	case 'delete_order':
		echo $Master->delete_order();
	break;
	case 'cancel_order':
		echo $Master->cancel_order();
	break;
	case 'confirm_receipt':
		echo $Master->confirm_receipt();
	break;
	case 'update_promo':
		echo $Master->update_promo();
	break;
	case 'update_customer':
		echo $Master->update_customer();
	break;
	case 'book_appointment':
		echo $Master->book_appointment();
	break;
	case 'check_appointment_availability':
		echo $Master->check_appointment_availability();
	break;
	case 'add_document':
		echo $Master->add_document();
	break;
	case 'cleanup_invalid_cart_items':
		echo $Master->cleanup_invalid_cart_items();
	break;
	case 'manual_cart_cleanup':
		echo $Master->manual_cart_cleanup();
	break;
	default:
		break;
}
