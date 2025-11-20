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
		$check = $this->conn->query("SELECT id FROM `order_list` WHERE id = '{$id}' AND delete_flag = 0");
		if(!$check || $check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Order not found.';
			return json_encode($resp);
		}
		// Archive the order instead of deleting
		$del = $this->conn->query("UPDATE `order_list` SET delete_flag = 1 WHERE id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = 'Order successfully archived.';
			$this->settings->set_flashdata('success',$resp['msg']);
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = $this->conn->error;
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
		// Enforce quantity = 1 for motorcycles regardless of input
		if($is_motorcycle){
			$quantity = 1;
		}
		
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
		
		// Check stock availability using unified helper
		$stock_levels = get_product_stock_levels($this->conn, $product_id);
		$available = isset($stock_levels['available_stock']) ? (float)$stock_levels['available_stock'] : 0;
		
		// Check if product is already in cart
        $cart_check = $this->conn->query("SELECT id, quantity FROM `cart_list` WHERE client_id = '{$client_id}' AND product_id = '{$product_id}' AND ((color IS NULL AND {$color_sql} IS NULL) OR color = {$color_sql})");
		
		if($cart_check->num_rows > 0){
			// Product already in cart, update quantity
			$cart_item = $cart_check->fetch_assoc();
			if($is_motorcycle){
				// Always keep motorcycles at quantity 1
				$new_quantity = 1;
			}else{
				$new_quantity = $cart_item['quantity'] + $quantity;
			}
			
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
		$cart_item = $this->conn->query("SELECT c.*, p.name, cat.category FROM cart_list c 
									INNER JOIN product_list p ON c.product_id = p.id 
									INNER JOIN categories cat ON p.category_id = cat.id 
									WHERE c.id = '{$cart_id}' AND c.client_id = '{$client_id}'");
		
		if(!$cart_item || $cart_item->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Cart item not found or already removed.";
			error_log("Cart item not found - cart_id: " . $cart_id . ", client_id: " . $client_id);
			return json_encode($resp);
		}
		
		$item = $cart_item->fetch_assoc();
		$current_qty = floatval($item['quantity']);
		$is_motorcycle = isset($item['category']) && strtolower($item['category']) === 'motorcycles';
		
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
		
		// Enforce limits
		if($is_motorcycle){
			$new_qty = 1; // Motorcycles fixed to 1
		}else{
			$new_qty = max(1, $new_qty);
		}
		
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
		
		// Get selected cart items from form data
		$selected_items = isset($_POST['selected_items']) ? $_POST['selected_items'] : '';
		$selected_items_str = '';
		
		// Check if cart has items (either selected items or all items if none selected)
		if(!empty($selected_items)) {
			$selected_items = explode(',', $selected_items);
			$selected_items = array_map('intval', $selected_items); // Convert to integers for security
			$selected_items = array_filter($selected_items);
			$selected_items_str = implode(',', $selected_items);
			$cart_items = $this->conn->query("SELECT COUNT(*) as count FROM cart_list WHERE client_id = '{$client_id}' AND id IN ({$selected_items_str})");
		} else {
			$cart_items = $this->conn->query("SELECT COUNT(*) as count FROM cart_list WHERE client_id = '{$client_id}'");
		}
		
		if($cart_items->fetch_assoc()['count'] == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Your cart is empty.";
			return json_encode($resp);
		}
		
		// Check if customer has completed Motorcentral Credit Application for motorcycle orders
		// Only enforce when payment method is installment; allow cash/full payments without the requirement
		$payment_method = isset($_POST['payment_method']) ? strtolower(trim($_POST['payment_method'])) : '';
		$cart_filter = '';
		if(!empty($selected_items_str)){
			$cart_filter = " AND c.id IN ({$selected_items_str})";
		}
        $item_type_query = $this->conn->query("SELECT p.name, p.price, c.quantity, cat.category 
											  FROM cart_list c 
												INNER JOIN product_list p ON c.product_id = p.id 
											  LEFT JOIN categories cat ON p.category_id = cat.id 
											  WHERE c.client_id = '{$client_id}' {$cart_filter}");
		$motorcycle_count = 0;
		$parts_count = 0;
		$oils_count = 0;
        $motorcycle_subtotal_amount = 0;
        $non_motorcycle_subtotal_amount = 0;
        if($item_type_query){
			while($row = $item_type_query->fetch_assoc()){
				$category = strtolower($row['category'] ?? '');
				$product_name = strtolower($row['name'] ?? '');
                $line_total = (isset($row['price']) ? floatval($row['price']) : 0) * (isset($row['quantity']) ? floatval($row['quantity']) : 0);
				$is_oil = (strpos($category, 'oil') !== false) || (strpos($category, 'lubricant') !== false) || (strpos($product_name, 'oil') !== false);
				$is_part = (strpos($category, 'part') !== false) || (strpos($category, 'accessor') !== false) || (strpos($category, 'gear') !== false) || (strpos($product_name, 'part') !== false);
				$is_motorcycle = (
					(strpos($category, 'motorcycle') !== false || strpos($category, 'bike') !== false || strpos($product_name, 'motorcycle') !== false || strpos($product_name, 'bike') !== false)
					&& !$is_part && !$is_oil
				);
				
                if($is_motorcycle){
					$motorcycle_count++;
                    $motorcycle_subtotal_amount += $line_total;
				}elseif($is_oil){
					$oils_count++;
                    $non_motorcycle_subtotal_amount += $line_total;
				}else{
					$parts_count++;
                    $non_motorcycle_subtotal_amount += $line_total;
				}
			}
		}
		$has_motorcycle = $motorcycle_count > 0;
		$has_parts = $parts_count > 0;
		$has_oils = $oils_count > 0;
		
		if($has_motorcycle && $payment_method === 'installment'){
		// Note: Credit application validation is now handled on the frontend
		// The frontend will redirect users to the credit application form if needed
		}
		
		$transaction_type = 'motorcycle_purchase';
		if($has_motorcycle){
			$transaction_type = 'motorcycle_purchase';
		}elseif($has_oils && !$has_parts){
			$transaction_type = 'oils_purchase';
		}elseif($has_parts){
			$transaction_type = 'motorcycle_parts_purchase';
		}
		
		// Start transaction
		$this->conn->begin_transaction();
		
		try {
			// Generate reference code
			$ref_code = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
			
			// Calculate total amount for selected items only
			if(!empty($selected_items)) {
				$total_query = $this->conn->query("SELECT SUM(c.quantity * p.price) as total FROM cart_list c 
												  INNER JOIN product_list p ON c.product_id = p.id 
												  WHERE c.client_id = '{$client_id}' AND c.id IN ({$selected_items_str})");
			} else {
				$total_query = $this->conn->query("SELECT SUM(c.quantity * p.price) as total FROM cart_list c 
												  INNER JOIN product_list p ON c.product_id = p.id 
												  WHERE c.client_id = '{$client_id}'");
			}
			$total_amount = $total_query->fetch_assoc()['total'];
			
			// Add add-ons total if provided
			$addons_total = isset($_POST['addons_total']) ? floatval($_POST['addons_total']) : 0;
			$total_amount += $addons_total;
            if($has_motorcycle){
                $non_motorcycle_subtotal_amount += $addons_total;
            }
			
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
			
			// Get selected cart items and create order items
			if(!empty($selected_items)) {
				$cart_query = $this->conn->query("SELECT c.*, p.name, p.price FROM cart_list c 
												 INNER JOIN product_list p ON c.product_id = p.id 
												 WHERE c.client_id = '{$client_id}' AND c.id IN ({$selected_items_str})");
			} else {
				$cart_query = $this->conn->query("SELECT c.*, p.name, p.price FROM cart_list c 
												 INNER JOIN product_list p ON c.product_id = p.id 
												 WHERE c.client_id = '{$client_id}'");
			}
			
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
			
			// Clear only selected cart items
			if(!empty($selected_items)) {
				$clear_cart = $this->conn->query("DELETE FROM cart_list WHERE client_id = '{$client_id}' AND id IN ({$selected_items_str})");
			} else {
				$clear_cart = $this->conn->query("DELETE FROM cart_list WHERE client_id = '{$client_id}'");
			}
			
			if(!$clear_cart){
				throw new Exception("Failed to clear cart: " . $this->conn->error);
			}
			
            // Commit transaction
            $this->conn->commit();
            
            // Auto-generate invoice for the new order
            $invoice_id = null;
            try{
                if(file_exists(base_app.'classes/Invoice.php')){
                    require_once base_app.'classes/Invoice.php';
                    $invoiceSvc = new Invoice();
                    // Use staff_id = 0 to denote system-generated when placed by client
                    $invRes = $invoiceSvc->createInvoiceFromOrder($order_id, 0, $transaction_type);
                    if(is_array($invRes) && isset($invRes['status']) && $invRes['status'] !== 'success'){
                        // Non-fatal: log error
                        error_log('Auto-invoice generation failed for order ' . $order_id . ': ' . ($invRes['msg'] ?? 'unknown error'));
                    } else if(is_array($invRes)){
                        $resp['invoice_id'] = $invRes['invoice_id'] ?? null;
                        $resp['invoice_number'] = $invRes['invoice_number'] ?? null;
                        $invoice_id = $invRes['invoice_id'] ?? null;
                    }
                }
            } catch (Exception $e){ /* non-fatal */ }
            
            // Auto-create customer account balance record for motorcycle orders
            try{
                if($has_motorcycle && file_exists(base_app.'classes/CustomerAccountBalance.php')){
                    require_once base_app.'classes/CustomerAccountBalance.php';
                    $accountBalance = new CustomerAccountBalance($this->conn);
                    $financed_amount = $motorcycle_subtotal_amount > 0 ? $motorcycle_subtotal_amount : $total_amount;
                    $upfront_accessory_total = $non_motorcycle_subtotal_amount;
                    
                    // Get motorcycle product name(s)
                    $motorcycle_query = $this->conn->query("SELECT p.name, p.price, oi.quantity 
                                                           FROM order_items oi 
                                                           INNER JOIN product_list p ON oi.product_id = p.id 
                                                           INNER JOIN categories cat ON p.category_id = cat.id 
                                                           WHERE oi.order_id = '{$order_id}' 
                                                           AND (cat.category LIKE '%motorcycle%' OR cat.category LIKE '%bike%' OR p.name LIKE '%motorcycle%' OR p.name LIKE '%bike%')
                                                           LIMIT 1");
                    
                    if($motorcycle_query && $motorcycle_row = $motorcycle_query->fetch_assoc()){
                        $item_purchased = $motorcycle_row['name'];
                        $total_price = $financed_amount; // Only finance motorcycle portion
                        
                        // Get payment method and installment details from form
                        $payment_method = isset($_POST['payment_method']) ? strtolower(trim($_POST['payment_method'])) : 'cash';
                        $installment_months = isset($_POST['installment_months']) ? intval($_POST['installment_months']) : null;
                        $downpayment_amount = isset($_POST['down_payment']) ? floatval($_POST['down_payment']) : 0;
                        $monthly_payment = null;
                        $contract_id = null;
                        
                        // If installment payment, get details from form
                        if($payment_method === 'installment'){
                            // Get installment months from form
                            if($installment_months && $installment_months > 0){
                                // Get monthly payment from form (remove currency symbol if present)
                                $monthly_payment_str = isset($_POST['monthly_payment']) ? trim($_POST['monthly_payment']) : '';
                                if(!empty($monthly_payment_str)){
                                    // Remove currency symbols and convert to float
                                    $monthly_payment = floatval(str_replace(['₱', ',', ' '], '', $monthly_payment_str));
                                }
                                
                                // If monthly payment not provided or invalid, calculate it
                                if(empty($monthly_payment) || $monthly_payment <= 0){
                                    $remaining = $total_price - $downpayment_amount;
                                    if($remaining > 0 && $installment_months > 0){
                                        $monthly_payment = $remaining / $installment_months;
                                    }
                                }
                                
                                // Ensure downpayment is at least 20% if not provided
                                $minimum_down = $total_price * 0.2;
                                if($downpayment_amount < $minimum_down){
                                    $downpayment_amount = $minimum_down;
                                }
                            }
                        }
                        
                        // Create customer account record
                        $account_id = $accountBalance->createAccount(
                            $client_id,
                            $order_id,
                            $item_purchased,
                            $total_price,
                            $downpayment_amount,
                            $installment_months,
                            $monthly_payment,
                            $invoice_id,
                            $contract_id
                        );
                        
                        if($account_id){
                            $resp['account_id'] = $account_id;
                            error_log("Customer account created for order {$order_id}, account ID: {$account_id}");
                        }
                    }
                }
            } catch (Exception $e){ 
                // Non-fatal: log error but don't fail order
                error_log('Customer account creation failed for order ' . $order_id . ': ' . $e->getMessage());
            }
            
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
		
		// Ensure we return clean JSON (no extra output)
		header('Content-Type: application/json; charset=utf-8');
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
		$user_id = $this->settings->userdata('id');
		if(empty($user_id)){
			return json_encode(['status'=>'failed','msg'=>'User not logged in.']);
		}
		$limit = isset($_POST['limit']) ? max(1, (int)$_POST['limit']) : 10;
		$offset = isset($_POST['offset']) ? max(0, (int)$_POST['offset']) : 0;
		$data = [];
		try{
			if(file_exists(base_app.'classes/Notification.php')){
				require_once base_app.'classes/Notification.php';
				$notif = new Notification();
				$data = $notif->getUserNotifications($user_id, $limit, $offset);
			}else{
				$query = $this->conn->query("SELECT id, type, title, message, data, is_read, date_created FROM notifications WHERE user_id = '{$user_id}' ORDER BY date_created DESC LIMIT {$offset},{$limit}");
				if($query){
					while($row = $query->fetch_assoc()){
						if(isset($row['data']) && !is_null($row['data'])){
							$decoded = json_decode($row['data'], true);
							if(json_last_error() === JSON_ERROR_NONE){
								$row['data'] = $decoded;
							}
						}
						$row['is_read'] = isset($row['is_read']) ? (int)$row['is_read'] : 0;
						$data[] = $row;
					}
				}
			}
			return json_encode(['status'=>'success','data'=>$data]);
		}catch(Exception $e){
			return json_encode(['status'=>'failed','msg'=>'Failed to load notifications.','error'=>$e->getMessage()]);
		} 
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
		$user_id = $this->settings->userdata('id');
		if(empty($user_id)){
			return json_encode(['status'=>'success','count'=>0]);
		}
		$query = $this->conn->query("SELECT COUNT(*) as count FROM notifications WHERE user_id = '{$user_id}' AND is_read = 0");
		$count = $query ? (int)$query->fetch_assoc()['count'] : 0;
		return json_encode(['status'=>'success','count'=>$count]);
	}
	
	function get_notification_history(){
		$user_id = $this->settings->userdata('id');
		if(empty($user_id)){
			return json_encode(['status'=>'failed','msg'=>'User not logged in.']);
		}
		$limit = isset($_POST['limit']) ? max(1,(int)$_POST['limit']) : 20;
		$offset = isset($_POST['offset']) ? max(0,(int)$_POST['offset']) : 0;
		$result = ['items'=>[], 'total'=>0, 'has_more'=>false];
		try{
			if(file_exists(base_app.'classes/Notification.php')){
				require_once base_app.'classes/Notification.php';
				$notif = new Notification();
				$result = $notif->getNotificationHistory($user_id, $limit, $offset);
			}else{
				$query = $this->conn->query("SELECT id, type, title, message, data, is_read, date_created FROM notifications WHERE user_id = '{$user_id}' ORDER BY date_created DESC LIMIT {$offset},{$limit}");
				$items = [];
				if($query){
					while($row = $query->fetch_assoc()){
						if(isset($row['data']) && !is_null($row['data'])){
							$decoded = json_decode($row['data'], true);
							if(json_last_error() === JSON_ERROR_NONE){
								$row['data'] = $decoded;
							}
						}
						$row['is_read'] = isset($row['is_read']) ? (int)$row['is_read'] : 0;
						$items[] = $row;
					}
				}
				$total = $this->conn->query("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = '{$user_id}'");
				$total_count = $total ? (int)$total->fetch_assoc()['cnt'] : 0;
				$result = [
					'items' => $items,
					'total' => $total_count,
					'has_more' => ($offset + $limit) < $total_count
				];
			}
			return json_encode(['status'=>'success'] + $result);
		}catch(Exception $e){
			return json_encode(['status'=>'failed','msg'=>'Failed to load notification history.','error'=>$e->getMessage()]);
		}
	}
	
	function mark_all_notifications_read(){
		$user_id = $this->settings->userdata('id');
		if(empty($user_id)){
			return json_encode(['status'=>'failed','msg'=>'User not logged in.']);
		}
		try{
			if(file_exists(base_app.'classes/Notification.php')){
				require_once base_app.'classes/Notification.php';
				$notif = new Notification();
				$notif->markAllRead($user_id);
			}else{
				$this->conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = '{$user_id}' AND is_read = 0");
			}
			return json_encode(['status'=>'success']);
		}catch(Exception $e){
			return json_encode(['status'=>'failed','msg'=>'Failed to update notifications.','error'=>$e->getMessage()]);
		}
	}

	// Admin notification functions
	function get_admin_notifications_count(){
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin','accounting');
		if(!in_array($role, $allowed)){
			return json_encode(['status' => 'failed','msg'=>'Access denied.']);
		}
		$admin_id = $this->settings->userdata('id');
		$query = $this->conn->query("SELECT COUNT(*) as cnt FROM notifications WHERE user_id = '{$admin_id}' AND is_read = 0");
		$count = $query ? (int)$query->fetch_assoc()['cnt'] : 0;
		return json_encode(['status'=>'success','count'=>$count]);
	}

	function get_admin_notifications(){
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin');
		if(!in_array($role, $allowed)){
			return json_encode(['status' => 'failed','msg'=>'Access denied.']);
		}
		$limit = isset($_POST['limit']) ? max(1,(int)$_POST['limit']) : 20;
		$offset = isset($_POST['offset']) ? max(0,(int)$_POST['offset']) : 0;
		$data = [];
		try{
			if(file_exists(base_app.'classes/Notification.php')){
				require_once base_app.'classes/Notification.php';
				$notif = new Notification();
				$data = $notif->getUserNotifications($this->settings->userdata('id'), $limit, $offset);
			}else{
				$query = $this->conn->query("SELECT id, type, title, message, data, is_read, date_created FROM notifications WHERE user_id = '{$this->settings->userdata('id')}' ORDER BY date_created DESC LIMIT {$offset},{$limit}");
				if($query){
					while($row = $query->fetch_assoc()){
						if(isset($row['data']) && !is_null($row['data'])){
							$decoded = json_decode($row['data'], true);
							if(json_last_error() === JSON_ERROR_NONE){
								$row['data'] = $decoded;
							}
						}
						$row['is_read'] = isset($row['is_read']) ? (int)$row['is_read'] : 0;
						$data[] = $row;
					}
				}
			}
			return json_encode(['status'=>'success','data'=>$data]);
		}catch(Exception $e){
			return json_encode(['status'=>'failed','msg'=>'Failed to load notifications.','error'=>$e->getMessage()]);
		}
	}

	function mark_admin_notification_read(){
		extract($_POST);
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin');
		if(!in_array($role, $allowed)){
			return json_encode(['status'=>'failed','msg'=>'Access denied.']);
		}
		$id = $this->conn->real_escape_string($id);
		$admin_id = $this->settings->userdata('id');
		$upd = $this->conn->query("UPDATE notifications SET is_read = 1 WHERE id = '{$id}' AND user_id = '{$admin_id}'");
		if($upd) return json_encode(['status'=>'success']);
		return json_encode(['status'=>'failed','msg'=>$this->conn->error]);
	}

	function mark_all_admin_notifications_read(){
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin','accounting');
		if(!in_array($role, $allowed)) return json_encode(['status'=>'failed','msg'=>'Access denied.']);
		$admin_id = $this->settings->userdata('id');
		$upd = $this->conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = '{$admin_id}' AND is_read = 0");
		if($upd) return json_encode(['status'=>'success']);
		return json_encode(['status'=>'failed','msg'=>$this->conn->error]);
	}

	function delete_admin_notification(){
		extract($_POST);
		$role = $this->settings->userdata('role_type');
		$allowed = array('admin','branch_supervisor','service_admin','accounting');
		if(!in_array($role, $allowed)) return json_encode(['status'=>'failed','msg'=>'Access denied.']);
		$id = $this->conn->real_escape_string($id);
		$admin_id = $this->settings->userdata('id');
		$del = $this->conn->query("DELETE FROM notifications WHERE id = '{$id}' AND user_id = '{$admin_id}'");
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
		header('Content-Type: application/json');
		$resp = array('status' => 'failed', 'msg' => 'An error occurred');
		$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
		
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
		$preferred_date_val = isset($_POST['preferred_date']) ? trim($_POST['preferred_date']) : '';
		$preferred_time_val = isset($_POST['preferred_time']) ? trim($_POST['preferred_time']) : '';
		if(!empty($preferred_date_val) && !empty($preferred_time_val)){
			$exclude_id = !empty($id) ? $id : null;
			if(!$this->isServiceSlotAvailable($preferred_date_val, $preferred_time_val, $exclude_id)){
				$resp['status'] = 'failed';
				$resp['msg'] = "This time slot is not available. Please choose another time.";
				return json_encode($resp);
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
			$meta_values = [];
			foreach($_POST as $k=> $v){
				if(!in_array($k,array('id','client_id','mechanic_id','status','vehicle_type','vehicle_name','vehicle_registration_number','vehicle_model'))){
					if(is_array($_POST[$k]))
						$v = implode(",",$_POST[$k]);
					$v = $this->conn->real_escape_string($v);
					if(!empty($v)){ // Only add non-empty values
						$meta_values[] = "('{$rid}','{$k}','{$v}')";
					}
				}
			}
			
			// Delete existing meta first
			$this->conn->query("DELETE FROM `request_meta` where `request_id` = '{$rid}' ");
			
			// Insert meta only if there are values
			$meta_save = true; // Default to success if no meta to insert
			if(!empty($meta_values)){
				$data = implode(", ", $meta_values);
				$sql = "INSERT INTO `request_meta` (`request_id`,`meta_field`,`meta_value`) VALUES {$data}";
				$meta_save = $this->conn->query($sql);
			}
			
			if($meta_save){
				$resp['status'] = 'success';
				$resp['id'] = $rid;
				if(empty($id))
					$resp['msg'] = "Service Request has been submitted successfully.";
				else
					$resp['msg'] = "Service Request details has been updated successfully.";
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
				$resp['sql'] = isset($sql) ? $sql : 'N/A';
				if(empty($id))
					$resp['msg'] = "Service Request has failed to submit.";
				else
					$resp['msg'] = "Service Request details has failed to update.";
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
		if($resp['status'] == 'success' && !$is_ajax)
			$this->settings->set_flashdata("success", $resp['msg']);
		
		return json_encode($resp);
	}

	private function isServiceSlotAvailable($preferred_date, $preferred_time, $exclude_id = null){
		if(empty($preferred_date) || empty($preferred_time)){
			return true;
		}
		$date = $this->conn->real_escape_string($preferred_date);
		$time = $this->conn->real_escape_string($preferred_time);
		$exclude = '';
		if(!empty($exclude_id)){
			$exclude_id = $this->conn->real_escape_string($exclude_id);
			$exclude = " AND sr.id != '{$exclude_id}'";
		}
		$sql = "SELECT COUNT(*) as count
				FROM service_requests sr
				INNER JOIN request_meta md_date ON sr.id = md_date.request_id AND md_date.meta_field = 'preferred_date'
				INNER JOIN request_meta md_time ON sr.id = md_time.request_id AND md_time.meta_field = 'preferred_time'
				WHERE md_date.meta_value = '{$date}'
				  AND md_time.meta_value = '{$time}'
				  AND sr.status != 4
				  {$exclude}";
		$count = 0;
		if($qry = $this->conn->query($sql)){
			$row = $qry->fetch_assoc();
			$count = isset($row['count']) ? (int)$row['count'] : 0;
		}
		return $count === 0;
	}
	
	function delete_request(){
		extract($_POST);
		$resp = array();
		$del = $this->conn->query("UPDATE `service_requests` SET delete_flag = 1 WHERE id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Service request successfully archived.");
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
		
		// Check if invoice exists and is not already archived
		$check = $this->conn->query("SELECT id, payment_status FROM `invoices` WHERE id = '{$id}' AND delete_flag = 0");
		if($check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Invoice not found or already archived.";
			error_log("Invoice not found or archived: " . $id);
			return json_encode($resp);
		}
		
		$invoice = $check->fetch_assoc();
		// Archive the invoice instead of deleting (preserves related records)
		$del_invoice = $this->conn->query("UPDATE `invoices` SET delete_flag = 1 WHERE id = '{$id}'");
		if($del_invoice){
			$resp['status'] = 'success';
			$resp['msg'] = "Invoice successfully archived.";
			$this->settings->set_flashdata('success', $resp['msg']);
			error_log("Invoice archived: {$id} by user {$this->settings->userdata('id')}");
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to archive invoice: " . $this->conn->error;
			error_log("Archive invoice error: " . $this->conn->error);
		}
		
		error_log("Archive invoice response: " . json_encode($resp));
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
			// TRIGGER: update invoices.updated_at and recalculate client balance
			$this->conn->query("UPDATE invoices SET updated_at = NOW() WHERE order_id = '{$id}'");
			$this->recalculateClientBalance($order['client_id']);
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
			// Log admin action
			require_once 'ActivityLogger.php';
			$logger = new ActivityLogger();
			
			// Determine action type based on status
			if($status == 1) {
				// Approved
				$logger->logOrderApproval($id);
			} elseif($status == 5) {
				// Rejected/Cancelled
				$reason = isset($reason) ? $reason : null;
				$logger->logOrderRejection($id, $reason);
			}
			
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
			
			// Notify customer and admins about status change
			try{
				$notif_path = base_app.'classes/Notification.php';
				if(file_exists($notif_path)){
					require_once $notif_path;
					$notif = new Notification();
					if(method_exists($notif, 'sendOrderStatusNotification')){
						$notif->sendOrderStatusNotification($id);
					}else{
						$order = $this->conn->query("SELECT client_id, ref_code FROM order_list WHERE id = '{$id}'")->fetch_assoc();
						if($order){
							$status_text = isset($invoice_result) && isset($invoice_result['invoice_number']) ? 'Claimed' : $status;
							$notif->createNotification($order['client_id'], 'order_status', 'Order Status Updated', "Your order {$order['ref_code']} status is now {$status_text}.", ['order_id'=>$id,'status'=>$status]);
						}
					}
					if(in_array($status, [1,5])){
						$title = $status == 1 ? 'Order Approved' : 'Order Rejected';
						$msg = $status == 1 ? "Order #{$id} has been approved." : "Order #{$id} has been rejected.";
						if(isset($reason) && $status == 5 && !empty($reason)){
							$msg .= " Reason: {$reason}.";
						}
						if(method_exists($notif, 'notifyAdmins')){
							$notif->notifyAdmins('order_status', $title, $msg, ['order_id'=>$id,'status'=>$status]);
						}
					}
				}
			}catch(Exception $e){ /* non-fatal */ }
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
		$del = $this->conn->query("UPDATE `or_cr_documents` SET delete_flag = 1 WHERE id = '{$document_id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = "Document successfully archived.";
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Document archiving failed.";
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
		
		// Check if client exists
		$client = $this->conn->query("SELECT id FROM client_list WHERE id = '{$client_id}' AND delete_flag = 0");
		
		if($client->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Client not found.";
			return json_encode($resp);
		}
		
		// Calculate balance from installment contracts (aligned with invoices/receipts)
		$balance_q = $this->conn->query("SELECT 
			COALESCE(SUM(ic.remaining_balance), 0) as total_remaining_balance,
			COALESCE(SUM(ic.down_payment_amount + IFNULL(ip.paid_amount, 0)), 0) as total_paid_amount,
			COALESCE(SUM(ic.total_amount), 0) as total_contract_amount
			FROM installment_contracts ic
			LEFT JOIN (SELECT contract_id, SUM(amount_paid) as paid_amount FROM installment_payments GROUP BY contract_id) ip ON ic.id = ip.contract_id
			WHERE ic.customer_id = '{$client_id}' AND ic.status = 'active'");
		
		if($balance_q && $balance_q->num_rows > 0) {
			$balance_data = $balance_q->fetch_assoc();
			$remaining_balance = (float)$balance_data['total_remaining_balance'];
		} else {
			// Fallback to account_balance if no installment contracts
			$client_bal = $this->conn->query("SELECT account_balance FROM client_list WHERE id = '{$client_id}'");
			if($client_bal && $client_bal->num_rows > 0) {
				$remaining_balance = (float)($client_bal->fetch_assoc()['account_balance'] ?: 0);
			} else {
				$remaining_balance = 0;
			}
		}
		
		$resp['status'] = 'success';
		$resp['balance'] = $remaining_balance;
		$resp['current_balance'] = $remaining_balance;
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
		
		// Check if client exists
		$client = $this->conn->query("SELECT id FROM client_list WHERE id = '{$client_id}' AND delete_flag = 0");
		if(!$client || $client->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = 'Client not found.';
			return json_encode($resp);
		}
		
		// Calculate balance from installment contracts
		$balance_q = $this->conn->query("SELECT 
			COALESCE(SUM(ic.remaining_balance), 0) as total_remaining_balance
			FROM installment_contracts ic
			WHERE ic.customer_id = '{$client_id}' AND ic.status = 'active'");
		
		if($balance_q && $balance_q->num_rows > 0) {
			$balance_data = $balance_q->fetch_assoc();
			$balance = (float)$balance_data['total_remaining_balance'];
		} else {
			// Fallback to account_balance
			$client_bal = $this->conn->query("SELECT account_balance FROM client_list WHERE id = '{$client_id}'");
			$balance = $client_bal && $client_bal->num_rows > 0 ? (float)($client_bal->fetch_assoc()['account_balance'] ?: 0) : 0;
		}
		
		$resp['status'] = 'success';
		$resp['data'] = [ 'balance' => $balance ];
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
		
		// Get installment contracts summary
		$contracts_q = $this->conn->query("SELECT 
			ic.*,
			i.invoice_number,
			ip.plan_name,
			ic.down_payment_amount + COALESCE(SUM(ipay.amount_paid), 0) as paid_amount,
			DATE_FORMAT(ic.created_at, '%Y-%m-%d') as created_at
			FROM installment_contracts ic
			LEFT JOIN invoices i ON ic.invoice_id = i.id
			LEFT JOIN installment_plans ip ON ic.installment_plan_id = ip.id
			LEFT JOIN installment_payments ipay ON ic.id = ipay.contract_id
			WHERE ic.customer_id = '{$client_id}'
			GROUP BY ic.id
			ORDER BY ic.created_at DESC");
		
		$contracts = array();
		if($contracts_q && $contracts_q->num_rows > 0){
			while($contract = $contracts_q->fetch_assoc()){
				$contracts[] = array(
					'contract_number' => $contract['contract_number'],
					'invoice_number' => $contract['invoice_number'],
					'plan_name' => $contract['plan_name'],
					'total_amount' => (float)$contract['total_amount'],
					'paid_amount' => (float)($contract['paid_amount'] ?: 0),
					'remaining_balance' => (float)$contract['remaining_balance'],
					'status' => $contract['status'],
					'created_at' => $contract['created_at']
				);
			}
		}
		
		// Get installment payments
		$payments_q = $this->conn->query("SELECT 
			ip.*,
			ic.contract_number,
			isch.installment_number,
			u.firstname as staff_firstname,
			u.lastname as staff_lastname,
			DATE_FORMAT(ip.payment_date, '%Y-%m-%d %H:%i:%s') as payment_date
			FROM installment_payments ip
			LEFT JOIN installment_contracts ic ON ip.contract_id = ic.id
			LEFT JOIN installment_schedule isch ON ip.schedule_id = isch.id
			LEFT JOIN users u ON ip.created_by = u.id
			WHERE ic.customer_id = '{$client_id}'
			ORDER BY ip.payment_date DESC LIMIT 50");
		
		$payments = array();
		if($payments_q && $payments_q->num_rows > 0){
			while($payment = $payments_q->fetch_assoc()){
				$payments[] = array(
					'payment_date' => $payment['payment_date'],
					'contract_number' => $payment['contract_number'],
					'installment_number' => $payment['installment_number'],
					'amount_paid' => (float)$payment['amount_paid'],
					'payment_method' => $payment['payment_method'],
					'receipt_number' => $payment['receipt_number'],
					'staff_firstname' => $payment['staff_firstname'],
					'staff_lastname' => $payment['staff_lastname']
				);
			}
		}
		
		// Get installment schedule
		$schedule_q = $this->conn->query("SELECT 
			isch.*,
			ic.contract_number,
			DATE_FORMAT(isch.due_date, '%Y-%m-%d') as due_date
			FROM installment_schedule isch
			LEFT JOIN installment_contracts ic ON isch.contract_id = ic.id
			WHERE ic.customer_id = '{$client_id}'
			ORDER BY isch.due_date ASC");
		
		$schedule = array();
		if($schedule_q && $schedule_q->num_rows > 0){
			while($sch = $schedule_q->fetch_assoc()){
				$schedule[] = array(
					'contract_number' => $sch['contract_number'],
					'installment_number' => $sch['installment_number'],
					'due_date' => $sch['due_date'],
					'amount_due' => (float)$sch['amount_due'],
					'paid_amount' => (float)($sch['paid_amount'] ?: 0),
					'status' => $sch['status']
				);
			}
		}
		
		$resp['status'] = 'success';
		$resp['contracts'] = $contracts;
		$resp['payments'] = $payments;
		$resp['schedule'] = $schedule;
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
			
			// Log admin action
			require_once 'ActivityLogger.php';
			$logger = new ActivityLogger();
			$logger->logAccountBalanceUpdate($client_id, $new_balance);
			
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
			return json_encode(['status'=>'failed','msg'=>"Please select a valid product."]);
		}
		
		if(!isset($quantity) || $quantity <= 0){
			return json_encode(['status'=>'failed','msg'=>"Please enter a valid quantity greater than 0."]);
		}
		
		// Sanitize inputs
		$product_id = $this->conn->real_escape_string($product_id);
		$quantity = (float)$quantity;
        $reason = isset($reason) ? $this->conn->real_escape_string($reason) : (empty($id) ? 'Stock addition' : 'Stock edit');
        $stock_id = isset($id) && !empty($id) ? (int)$id : 0;

        $stock_levels = get_product_stock_levels($this->conn, $product_id);
		$current_stock = (float)$stock_levels['current_stock'];
		
		// Start transaction
		$this->conn->begin_transaction();
		
		try {
            if($stock_id > 0){
                // Editing an existing stock entry
                $existing = $this->conn->query("SELECT * FROM stock_list WHERE id = '{$stock_id}' AND product_id = '{$product_id}' AND COALESCE(delete_flag,0) = 0");
                if(!$existing || $existing->num_rows == 0){
                    throw new Exception('Stock entry not found or already archived.');
                }
                $row = $existing->fetch_assoc();
                $old_qty = (float)$row['quantity'];
                $delta = $quantity - $old_qty;
                $delta_effect = $row['type'] == 2 ? -$delta : $delta;
                $new_stock_value = $current_stock + $delta_effect;
                if($new_stock_value < 0){
                    throw new Exception('Adjustment would result in negative stock.');
                }
                
                // Update the stock_list row
                $update = $this->conn->query("UPDATE stock_list SET quantity = '{$quantity}' WHERE id = '{$stock_id}'");
                if(!$update){
                    throw new Exception('Failed to update stock row: ' . $this->conn->error);
                }
                
                // Record adjustment movement
                $movement_data = "('{$product_id}', 'ADJUSTMENT', '{$delta_effect}', '{$current_stock}', '{$new_stock_value}', '{$reason}', 'STOCK_EDIT', 'ADJUSTMENT', NOW(), NULL)";
                $this->conn->query("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, reference_id, reference_type, date_created, created_by) VALUES {$movement_data}");
				$log_message = "Updated entry #{$stock_id} from {$old_qty} to {$quantity}. New total: {$new_stock_value}";
                $success_msg = 'Stock updated successfully.';
            } else {
                // Adding a new stock entry (IN)
                if($quantity <= 0){
                    throw new Exception('Quantity must be greater than zero for new stock.');
                }
                $new_stock_value = $current_stock + $quantity;
                
				$stock_data = "('{$product_id}', '{$quantity}', 1, NOW())";
				$insert_stock = $this->conn->query("INSERT INTO stock_list (product_id, quantity, type, date_created) VALUES {$stock_data}");
				if(!$insert_stock){
					throw new Exception("Failed to add stock: " . $this->conn->error);
				}
				
				$movement_data = "('{$product_id}', 'IN', '{$quantity}', '{$current_stock}', '{$new_stock_value}', '{$reason}', 'STOCK_ADD', 'PURCHASE', NOW(), NULL)";
                $this->conn->query("INSERT INTO stock_movements (product_id, movement_type, quantity, previous_stock, new_stock, reason, reference_id, reference_type, date_created, created_by) VALUES {$movement_data}");

				$log_message = "Added {$quantity} units. New total: {$new_stock_value}";
                $success_msg = 'Stock added successfully.';

				// If previously out of stock and now available, trigger back-in-stock notifications
				try {
					$was_zero = ($current_stock <= 0);
					$now_positive = ($new_stock_value > 0);
					if($was_zero && $now_positive && file_exists(base_app.'classes/Notification.php')){
						require_once(base_app.'classes/Notification.php');
						$notif = new Notification();
						if(method_exists($notif, 'sendProductAvailabilityNotification')){
							$notif->sendProductAvailabilityNotification($product_id);
						}
					}
				} catch (Exception $e) { /* non-fatal */ }
            }

			$this->conn->commit();

			require_once 'ActivityLogger.php';
			$logger = new ActivityLogger();
			$logger->logStockUpdate($product_id, $log_message);

			$updated_levels = get_product_stock_levels($this->conn, $product_id);
			$this->check_stock_alerts($product_id, $updated_levels['current_stock']);

			$resp = [
				'status' => 'success',
				'msg' => $success_msg,
				'new_stock' => $updated_levels['current_stock'],
				'available_stock' => $updated_levels['available_stock']
			];
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
		
		// Check if stock entry exists and is not archived
		$check = $this->conn->query("SELECT * FROM stock_list WHERE id = '{$stock_id}' AND delete_flag = 0");
		if($check->num_rows == 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Stock entry not found or already archived.";
			return json_encode($resp);
		}
		
		// Archive the stock entry instead of deleting
		$delete = $this->conn->query("UPDATE stock_list SET delete_flag = 1 WHERE id = '{$stock_id}'");
		if($delete){
			$resp['status'] = 'success';
			$resp['msg'] = "Stock entry successfully archived.";
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to archive stock entry: " . $this->conn->error;
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
		
		// Get current stock snapshot
		$stock_levels = get_product_stock_levels($this->conn, $product_id);
		$current_stock = (float)$stock_levels['current_stock'];
		
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
			$updated_levels = get_product_stock_levels($this->conn, $product_id);
			$this->check_stock_alerts($product_id, $updated_levels['current_stock']);
			
			// Commit transaction
			$this->conn->commit();
			
			// Log admin action
			require_once 'ActivityLogger.php';
			$logger = new ActivityLogger();
			$logger->logStockUpdate($product_id, "{$movement_type} {$quantity} units. New total: {$new_stock}");
			
			$resp['status'] = 'success';
			$resp['msg'] = "Stock updated successfully.";
			$resp['new_stock'] = $updated_levels['current_stock'];
			$resp['available_stock'] = $updated_levels['available_stock'];
			
		} catch (Exception $e) {
			// Rollback transaction
			$this->conn->rollback();
			
			$resp['status'] = 'failed';
			$resp['msg'] = "Failed to update stock: " . $e->getMessage();
		}
		
		return json_encode($resp);
	}
	
	function get_abc_analysis(){
		// Get ABC analysis data directly from product_list with REAL-TIME stock information
		// No caching - always queries fresh data from stock_list and order_items tables
		// Calculate stock the same way as stock management: total IN entries - total OUT entries - ordered stock
		$abc_query = $this->conn->query("
			SELECT p.*,
				   COALESCE(stock_in.total_stock_in, 0) as stock_in,
				   COALESCE(stock_out.total_stock_out, 0) as stock_out,
				   (COALESCE(stock_in.total_stock_in, 0) - COALESCE(stock_out.total_stock_out, 0)) as current_stock,
				   COALESCE(o.total_ordered, 0) as total_ordered,
				   (COALESCE(stock_in.total_stock_in, 0) - COALESCE(stock_out.total_stock_out, 0) - COALESCE(o.total_ordered, 0)) as available_stock,
				   ((COALESCE(stock_in.total_stock_in, 0) - COALESCE(stock_out.total_stock_out, 0) - COALESCE(o.total_ordered, 0)) * p.price) as inventory_value,
				   CASE 
					   WHEN (COALESCE(stock_in.total_stock_in, 0) - COALESCE(stock_out.total_stock_out, 0) - COALESCE(o.total_ordered, 0)) <= 0 THEN 'OUT_OF_STOCK'
					   WHEN (COALESCE(stock_in.total_stock_in, 0) - COALESCE(stock_out.total_stock_out, 0) - COALESCE(o.total_ordered, 0)) <= COALESCE(p.reorder_point, 0) THEN 'LOW_STOCK'
					   WHEN (COALESCE(stock_in.total_stock_in, 0) - COALESCE(stock_out.total_stock_out, 0) - COALESCE(o.total_ordered, 0)) >= COALESCE(p.max_stock, 0) AND COALESCE(p.max_stock, 0) > 0 THEN 'OVERSTOCK'
					   ELSE 'NORMAL'
				   END as stock_status
			FROM product_list p
			LEFT JOIN (
				SELECT product_id, SUM(quantity) as total_stock_in 
				FROM stock_list 
				WHERE type = 1 AND COALESCE(delete_flag,0) = 0
				GROUP BY product_id
			) stock_in ON p.id = stock_in.product_id
			LEFT JOIN (
				SELECT product_id, SUM(quantity) as total_stock_out 
				FROM stock_list 
				WHERE type = 2 AND COALESCE(delete_flag,0) = 0
				GROUP BY product_id
			) stock_out ON p.id = stock_out.product_id
			LEFT JOIN (
				SELECT oi.product_id, SUM(oi.quantity) as total_ordered
				FROM order_items oi
				JOIN order_list ol ON oi.order_id = ol.id
				WHERE ol.status != 5 AND COALESCE(ol.delete_flag,0) = 0
				GROUP BY oi.product_id
			) o ON p.id = o.product_id
			WHERE p.delete_flag = 0
			ORDER BY COALESCE(p.abc_category, 'C'), inventory_value DESC, p.price DESC
		");
		
		$data = [];
		$category_stats = ['A' => 0, 'B' => 0, 'C' => 0];
		$total_value = 0;
		
		while($row = $abc_query->fetch_assoc()){
			// Ensure abc_category is set, default to 'C' if not
			if(empty($row['abc_category'])) {
				$row['abc_category'] = 'C';
			}
			
			// Ensure numeric values are properly set and calculated correctly
			$stock_in_val = (float)($row['stock_in'] ?? 0);
			$stock_out_val = (float)($row['stock_out'] ?? 0);
			$ordered_val = (float)($row['total_ordered'] ?? 0);
			
			// Recalculate to ensure accuracy (in case of NULL values)
			$row['current_stock'] = max(0, $stock_in_val - $stock_out_val);
			$row['available_stock'] = max(0, $row['current_stock'] - $ordered_val);
			$row['total_ordered'] = $ordered_val;
			$row['max_stock'] = (float)($row['max_stock'] ?? 0);
			
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
				SELECT product_id, 
					   SUM(CASE WHEN type = 1 THEN quantity ELSE 0 END) - 
					   SUM(CASE WHEN type = 2 THEN quantity ELSE 0 END) as total_stock 
				FROM stock_list 
				WHERE COALESCE(delete_flag,0) = 0
				GROUP BY product_id
			) s ON p.id = s.product_id
			LEFT JOIN (
				SELECT oi.product_id, SUM(oi.quantity) as total_ordered
				FROM order_items oi
				JOIN order_list ol ON oi.order_id = ol.id
				WHERE ol.status != 5 AND COALESCE(ol.delete_flag,0) = 0
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
	
    function check_stock_alerts($product_id, $current_stock = null){
        $product_id = (int)$product_id;
        if($product_id <= 0) return;

        $product_query = $this->conn->query("SELECT * FROM product_list WHERE id = '{$product_id}'");
        if($product_query->num_rows == 0) return;
        $product = $product_query->fetch_assoc();

        $levels = get_product_stock_levels($this->conn, $product_id);
        $current = isset($levels['current_stock']) ? (float)$levels['current_stock'] : max(0.0, (float)$current_stock);
        $available = isset($levels['available_stock']) ? (float)$levels['available_stock'] : $current;

        $reorder_point = isset($product['reorder_point']) ? (float)$product['reorder_point'] : 0.0;
        $max_stock = isset($product['max_stock']) ? (float)$product['max_stock'] : 0.0;

        // Resolve existing alerts helper
        $pid = $product_id;
        $resolve_if_exists = function($type) use ($pid) {
            $this->conn->query("UPDATE inventory_alerts SET is_resolved = 1, resolved_date = NOW() WHERE product_id = '{$pid}' AND alert_type = '{$type}' AND is_resolved = 0");
        };

        // Create or update alert helper
        $create_or_update = function($type, $cur, $threshold, $message) use ($pid){
            $exists = $this->conn->query("SELECT id FROM inventory_alerts WHERE product_id = '{$pid}' AND alert_type = '{$type}' AND is_resolved = 0");
            if($exists && $exists->num_rows > 0){
                $row = $exists->fetch_assoc();
                $this->conn->query("UPDATE inventory_alerts SET current_stock = '{$cur}', threshold_value = '{$threshold}', message = '{$this->conn->real_escape_string($message)}' WHERE id = '{$row['id']}'");
            } else {
                $this->create_stock_alert($pid, $type, $cur, $threshold, $message);
            }
        };

        // Determine and manage alerts based on AVAILABLE stock
        $name = $product['name'];
        $triggered = false;

        if($available <= 0){
            $msg = "Out of stock: {$name} is no longer available";
            $create_or_update('OUT_OF_STOCK', $available, 0, $msg);
            $triggered = true;
        } else {
            $resolve_if_exists('OUT_OF_STOCK');
        }

        if($available <= $reorder_point){
            $msg = "Low stock alert: {$name} has {$available} units remaining (Reorder point: {$reorder_point})";
            $create_or_update('LOW_STOCK', $available, $reorder_point, $msg);
            $triggered = true;
        } else {
            $resolve_if_exists('LOW_STOCK');
        }

        if($max_stock > 0 && $available >= $max_stock){
            $msg = "Overstock alert: {$name} has {$available} units (Max stock: {$max_stock})";
            $create_or_update('OVERSTOCK', $available, $max_stock, $msg);
            $triggered = true;
        } else {
            $resolve_if_exists('OVERSTOCK');
        }

        // If none triggered, nothing to do (alerts already resolved above)
        return;
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
					SELECT product_id, 
						   SUM(CASE WHEN type = 1 THEN quantity ELSE 0 END) - 
						   SUM(CASE WHEN type = 2 THEN quantity ELSE 0 END) as total_stock 
					FROM stock_list 
					WHERE COALESCE(delete_flag,0) = 0
					GROUP BY product_id
				) s ON p.id = s.product_id
				LEFT JOIN (
					SELECT oi.product_id, SUM(oi.quantity) as total_ordered
					FROM order_items oi
					JOIN order_list ol ON oi.order_id = ol.id
					WHERE ol.status != 5 AND COALESCE(ol.delete_flag,0) = 0
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
		// Get all products with their sales data AND current inventory value (real-time stock)
		$products_query = $this->conn->query("
			SELECT p.*, 
				   COALESCE(sales.total_sales_value, 0) as total_sales_value,
				   COALESCE(sales.total_quantity_sold, 0) as total_quantity_sold,
				   COALESCE(stock_in.total_stock_in, 0) - COALESCE(stock_out.total_stock_out, 0) as current_stock,
				   (COALESCE(stock_in.total_stock_in, 0) - COALESCE(stock_out.total_stock_out, 0)) * p.price as inventory_value
			FROM product_list p
			LEFT JOIN (
				SELECT oi.product_id,
					   SUM(oi.quantity * p.price) as total_sales_value,
					   SUM(oi.quantity) as total_quantity_sold
				FROM order_items oi
				JOIN order_list ol ON oi.order_id = ol.id
				JOIN product_list p ON oi.product_id = p.id
				WHERE ol.status != 5 AND COALESCE(ol.delete_flag,0) = 0
				GROUP BY oi.product_id
			) sales ON p.id = sales.product_id
			LEFT JOIN (
				SELECT product_id, SUM(quantity) as total_stock_in 
				FROM stock_list 
				WHERE type = 1 AND COALESCE(delete_flag,0) = 0
				GROUP BY product_id
			) stock_in ON p.id = stock_in.product_id
			LEFT JOIN (
				SELECT product_id, SUM(quantity) as total_stock_out 
				FROM stock_list 
				WHERE type = 2 AND COALESCE(delete_flag,0) = 0
				GROUP BY product_id
			) stock_out ON p.id = stock_out.product_id
			WHERE p.delete_flag = 0
			ORDER BY sales.total_sales_value DESC, inventory_value DESC
		");
		
		$products = [];
		$total_value = 0;
		
		while($row = $products_query->fetch_assoc()){
			// Use sales value, but if no sales, use inventory value (current stock * price)
			$product_value = $row['total_sales_value'] > 0 ? $row['total_sales_value'] : $row['inventory_value'];
			$row['classification_value'] = $product_value;
			$products[] = $row;
			$total_value += $product_value;
		}
		
		// Calculate cumulative percentages and assign ABC categories
		$cumulative_value = 0;
		$updated_count = 0;
		
		foreach($products as $product){
			$cumulative_value += $product['classification_value'];
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
		$resp['msg'] = "ABC classification updated for {$updated_count} products based on sales and current inventory value.";
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
			try{
				$notif_path = base_app.'classes/Notification.php';
				if(file_exists($notif_path)){
					require_once $notif_path;
					$notif = new Notification();
					if(method_exists($notif, 'notifyAdmins')){
						$notif->notifyAdmins('credit_application', 'Credit Application Submitted', "Customer #{$customer_id} submitted their credit application.", ['client_id'=>$customer_id]);
					}
				}
			}catch(Exception $e){ /* non-fatal */ }
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
        $delete = $this->conn->query("UPDATE `promo_images` SET delete_flag = 1 WHERE id = '{$id}'");
        if($delete){
            $resp['status'] = 'success';
            $resp['msg'] = "Promo image successfully archived.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to archive promo image.";
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
        $delete = $this->conn->query("UPDATE `customer_purchase_images` SET delete_flag = 1 WHERE id = '{$id}'");
        if($delete){
            $resp['status'] = 'success';
            $resp['msg'] = "Customer image successfully archived.";
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = "Failed to archive customer image.";
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
        $service_check = $this->conn->query("SELECT id, service FROM service_list WHERE id = '{$service_type}' AND status = 1 AND delete_flag = 0");
        if($service_check->num_rows == 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Selected service is not available.";
            return json_encode($resp);
        }
        $service_row = $service_check->fetch_assoc();
        $service_name = isset($service_row['service']) ? $service_row['service'] : 'Service';
        
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
            $appointment_id = $this->conn->insert_id;
            $resp['appointment_id'] = $appointment_id;
            
            $eventData = [
                'appointment_id' => $appointment_id,
                'service_type' => $service_type,
                'service_name' => $service_name,
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time
            ];
            try{
                $notif_path = base_app.'classes/Notification.php';
                if(file_exists($notif_path)){
                    require_once $notif_path;
                    $notif = new Notification();
                    $formatted_date = date('M d, Y', strtotime($appointment_date));
                    $customer_msg = "Your {$service_name} appointment request is set for {$formatted_date} at {$appointment_time}.";
                    $notif->createNotification($client_id, 'appointment', 'Appointment Booked', $customer_msg, $eventData);
                    if(method_exists($notif, 'notifyAdmins')){
                        $admin_msg = "New appointment #{$appointment_id} requested by customer #{$client_id} for {$service_name}.";
                        $notif->notifyAdmins('service_booking', 'New Service Appointment', $admin_msg, $eventData);
                    }
                }
            }catch(Exception $e){ /* non-fatal */ }
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
        $service_info = $this->conn->query("SELECT service FROM service_list WHERE id = '{$service_type}'");
        $service_name = $service_info && $service_info->num_rows ? $service_info->fetch_assoc()['service'] : 'Service';
        
        $existing = null;
        if(!empty($id)){
            $safe_id = $this->conn->real_escape_string($id);
            $existing_q = $this->conn->query("SELECT * FROM appointments WHERE id = '{$safe_id}'");
            if($existing_q && $existing_q->num_rows){
                $existing = $existing_q->fetch_assoc();
            }
        }

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
            $appointment_id = empty($id) ? $this->conn->insert_id : $id;
            $resp['id'] = $appointment_id;
            
            $eventData = [
                'appointment_id' => $appointment_id,
                'service_type' => $service_type,
                'service_name' => $service_name,
                'appointment_date' => $appointment_date,
                'appointment_time' => $appointment_time,
                'status' => $status
            ];
            try{
                $notif_path = base_app.'classes/Notification.php';
                if(file_exists($notif_path)){
                    require_once $notif_path;
                    $notif = new Notification();
                    $formatted_date = date('M d, Y', strtotime($appointment_date));
                    if(empty($existing)){
                        $customer_msg = "Your {$service_name} appointment has been scheduled for {$formatted_date} at {$appointment_time}.";
                        $notif->createNotification($client_id, 'appointment', 'Appointment Scheduled', $customer_msg, $eventData);
                        if(method_exists($notif, 'notifyAdmins')){
                            $admin_msg = "Appointment #{$appointment_id} created for customer #{$client_id}.";
                            $notif->notifyAdmins('service_booking', 'New Service Appointment', $admin_msg, $eventData);
                        }
                    }else{
                        if($existing['status'] !== $status){
                            $status_label = ucfirst($status);
                            $customer_msg = "Your appointment #{$appointment_id} is now {$status_label}.";
                            $notif->createNotification($client_id, 'appointment_status', 'Appointment Status Updated', $customer_msg, $eventData);
                        }
                        if($existing['appointment_date'] !== $appointment_date || $existing['appointment_time'] !== $appointment_time){
                            $customer_msg = "Your appointment #{$appointment_id} has been moved to {$formatted_date} at {$appointment_time}.";
                            $notif->createNotification($client_id, 'appointment', 'Appointment Rescheduled', $customer_msg, $eventData);
                        }
                    }
                }
            }catch(Exception $e){ /* non-fatal */ }
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
        // Validate ID: ensure it exists and is a positive integer
        if(!isset($id) || $id === '' || !ctype_digit((string)$id) || (int)$id <= 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid appointment id.';
            return json_encode($resp);
        }
        $id = $this->conn->real_escape_string($id);
        $del = $this->conn->query("UPDATE appointments SET delete_flag = 1 WHERE id='{$id}'");
        if($del){
            $resp['status'] = 'success';
            $resp['msg'] = 'Appointment successfully archived.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to archive appointment.';
            $resp['error'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    
    // Restore functions for archived records
    function restore_order(){
        extract($_POST);
        $resp = array();
        $id = isset($id) ? intval($id) : 0;
        if($id <= 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid order id.';
            return json_encode($resp);
        }
        $restore = $this->conn->query("UPDATE `order_list` SET delete_flag = 0 WHERE id = '{$id}'");
        if($restore){
            $resp['status'] = 'success';
            $resp['msg'] = 'Order successfully restored.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    
    function restore_invoice(){
        extract($_POST);
        $resp = array();
        $id = isset($id) ? intval($id) : 0;
        if($id <= 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid invoice id.';
            return json_encode($resp);
        }
        $restore = $this->conn->query("UPDATE `invoices` SET delete_flag = 0 WHERE id = '{$id}'");
        if($restore){
            $resp['status'] = 'success';
            $resp['msg'] = 'Invoice successfully restored.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    
    function restore_request(){
        extract($_POST);
        $resp = array();
        $id = isset($id) ? intval($id) : 0;
        if($id <= 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid request id.';
            return json_encode($resp);
        }
        $restore = $this->conn->query("UPDATE `service_requests` SET delete_flag = 0 WHERE id = '{$id}'");
        if($restore){
            $resp['status'] = 'success';
            $resp['msg'] = 'Service request successfully restored.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    
    function restore_appointment(){
        extract($_POST);
        $resp = array();
        $id = isset($id) ? intval($id) : 0;
        if($id <= 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid appointment id.';
            return json_encode($resp);
        }
        $restore = $this->conn->query("UPDATE `appointments` SET delete_flag = 0 WHERE id = '{$id}'");
        if($restore){
            $resp['status'] = 'success';
            $resp['msg'] = 'Appointment successfully restored.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    
    function restore_document(){
        extract($_POST);
        $resp = array();
        $id = isset($document_id) ? intval($document_id) : (isset($id) ? intval($id) : 0);
        if($id <= 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid document id.';
            return json_encode($resp);
        }
        $restore = $this->conn->query("UPDATE `or_cr_documents` SET delete_flag = 0 WHERE id = '{$id}'");
        if($restore){
            $resp['status'] = 'success';
            $resp['msg'] = 'Document successfully restored.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    
    function restore_stock(){
        extract($_POST);
        $resp = array();
        $id = isset($id) ? intval($id) : 0;
        if($id <= 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid stock entry id.';
            return json_encode($resp);
        }
        $restore = $this->conn->query("UPDATE `stock_list` SET delete_flag = 0 WHERE id = '{$id}'");
        if($restore){
            $resp['status'] = 'success';
            $resp['msg'] = 'Stock entry successfully restored.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    
    function restore_promo(){
        extract($_POST);
        $resp = array();
        $id = isset($id) ? intval($id) : 0;
        if($id <= 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid promo id.';
            return json_encode($resp);
        }
        $restore = $this->conn->query("UPDATE `promo_images` SET delete_flag = 0 WHERE id = '{$id}'");
        if($restore){
            $resp['status'] = 'success';
            $resp['msg'] = 'Promo image successfully restored.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = $this->conn->error;
        }
        return json_encode($resp);
    }
    
    function restore_customer(){
        extract($_POST);
        $resp = array();
        $id = isset($id) ? intval($id) : 0;
        if($id <= 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Invalid customer image id.';
            return json_encode($resp);
        }
        $restore = $this->conn->query("UPDATE `customer_purchase_images` SET delete_flag = 0 WHERE id = '{$id}'");
        if($restore){
            $resp['status'] = 'success';
            $resp['msg'] = 'Customer image successfully restored.';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = $this->conn->error;
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

    function check_service_slot_availability(){
        $preferred_date = isset($_POST['preferred_date']) ? trim($_POST['preferred_date']) : '';
        $preferred_time = isset($_POST['preferred_time']) ? trim($_POST['preferred_time']) : '';
        $exclude_id = isset($_POST['exclude_id']) ? trim($_POST['exclude_id']) : '';
        $resp = ['status' => 'failed', 'available' => false];
        if(empty($preferred_date) || empty($preferred_time)){
            $resp['msg'] = "Date and time are required.";
            return json_encode($resp);
        }
        $available = $this->isServiceSlotAvailable($preferred_date, $preferred_time, $exclude_id);
        $resp['status'] = 'success';
        $resp['available'] = $available;
        $resp['msg'] = $available ? "Time slot is available." : "This time slot is not available. Please choose another time.";
        return json_encode($resp);
    }
    
    function cancel_appointment(){
        extract($_POST);
        
        if(empty($id)){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Missing appointment id.';
            return json_encode($resp);
        }
        
        $id = $this->conn->real_escape_string($id);
        
        // Check if appointment exists and belongs to the current user
        $currentClientId = $this->settings ? $this->settings->userdata('id') : null;
        $check = $this->conn->query("SELECT * FROM appointments WHERE id = '{$id}' AND client_id = '{$currentClientId}'");
        if($check->num_rows == 0){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Appointment not found or you do not have permission to cancel this appointment.';
            return json_encode($resp);
        }
        
        $appointment = $check->fetch_assoc();
        
        // Check if appointment can be cancelled (only pending appointments can be cancelled)
        if($appointment['status'] != 'pending'){
            $resp['status'] = 'failed';
            $resp['msg'] = 'Only pending appointments can be cancelled.';
            return json_encode($resp);
        }
        
        // Update appointment status to cancelled
        $update = $this->conn->query("UPDATE appointments SET status = 'cancelled', date_updated = NOW() WHERE id = '{$id}'");
        
        if($update){
            $resp['status'] = 'success';
            $resp['msg'] = 'Appointment cancelled successfully.';
            try{
                $notif_path = base_app.'classes/Notification.php';
                if(file_exists($notif_path)){
                    require_once $notif_path;
                    $notif = new Notification();
                    $eventData = [
                        'appointment_id' => $id,
                        'service_type' => $appointment['service_type'],
                        'appointment_date' => $appointment['appointment_date'],
                        'appointment_time' => $appointment['appointment_time']
                    ];
                    if(method_exists($notif, 'notifyAdmins')){
                        $admin_msg = "Customer #{$currentClientId} cancelled appointment #{$id}.";
                        $notif->notifyAdmins('service_booking', 'Appointment Cancelled', $admin_msg, $eventData);
                    }
                }
            }catch(Exception $e){ /* non-fatal */ }
        } else {
            $resp['status'] = 'failed';
            $resp['msg'] = 'Failed to cancel appointment.';
        }
        
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
                SELECT product_id, 
                       SUM(CASE WHEN type = 1 THEN quantity ELSE 0 END) - 
                       SUM(CASE WHEN type = 2 THEN quantity ELSE 0 END) as total_stock 
                FROM stock_list 
                WHERE COALESCE(delete_flag,0) = 0
                GROUP BY product_id
            ) s ON p.id = s.product_id
            LEFT JOIN (
                SELECT oi.product_id, SUM(oi.quantity) as total_ordered
                FROM order_items oi
                INNER JOIN order_list ol ON oi.order_id = ol.id
                WHERE ol.status != 5 AND COALESCE(ol.delete_flag,0) = 0
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
                SELECT product_id, 
                       SUM(CASE WHEN type = 1 THEN quantity ELSE 0 END) - 
                       SUM(CASE WHEN type = 2 THEN quantity ELSE 0 END) as total_stock 
                FROM stock_list 
                WHERE COALESCE(delete_flag,0) = 0
                GROUP BY product_id
            ) s ON p.id = s.product_id
            LEFT JOIN (
                SELECT oi.product_id, SUM(oi.quantity) as total_ordered
                FROM order_items oi
                INNER JOIN order_list ol ON oi.order_id = ol.id
                WHERE ol.status != 5 AND COALESCE(ol.delete_flag,0) = 0
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
        
        // Log admin action
        require_once 'ActivityLogger.php';
        $logger = new ActivityLogger();
        $doc_types = [];
        if($okOr) $doc_types[] = 'OR';
        if($okCr) $doc_types[] = 'CR';
        $doc_type_str = implode('/', $doc_types);
        $logger->logORCRUpload($client_id, $doc_type_str);
        
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

    /**
     * Recalculate client account balance based on invoice_financials
     */
    private function recalculateClientBalance($clientId) {
        $sum = $this->conn->query(
            "SELECT COALESCE(SUM(balance_remaining),0) as balance FROM invoice_financials WHERE customer_id = '{$clientId}'"
        )->fetch_assoc()['balance'] ?? 0;
        $this->conn->query(
            "UPDATE client_list SET account_balance = '{$sum}' WHERE id = '{$clientId}'"
        );
    }

    /**
     * Get all installment contracts
     */
    function get_all_installment_contracts() {
        $result = $this->conn->query(
            "SELECT ic.*, ip.plan_name, ip.number_of_installments,
                    c.firstname, c.lastname, c.email,
                    i.invoice_number, i.transaction_type
             FROM installment_contracts ic
             JOIN installment_plans ip ON ic.installment_plan_id = ip.id
             JOIN invoices i ON ic.invoice_id = i.id
             JOIN client_list c ON ic.customer_id = c.id
             ORDER BY ic.created_at DESC"
        );
        
        $contracts = [];
        while ($row = $result->fetch_assoc()) {
            $contracts[] = $row;
        }
        
        $resp['status'] = 'success';
        $resp['data'] = $contracts;
        return json_encode($resp);
    }

    /**
     * Get installment statistics
     */
    function get_installment_stats() {
        $stats = $this->conn->query(
            "SELECT 
                COUNT(CASE WHEN status = 'active' THEN 1 END) as active,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                SUM(CASE WHEN status = 'active' THEN remaining_balance ELSE 0 END) as total_balance
            FROM installment_contracts"
        )->fetch_assoc();
        
        // Get overdue count
        $overdue = $this->conn->query(
            "SELECT COUNT(*) as count
            FROM installment_schedule
            WHERE due_date < CURDATE() AND status IN ('pending', 'partial')"
        )->fetch_assoc()['count'] ?? 0;
        
        $stats['overdue'] = $overdue;
        
        $resp['status'] = 'success';
        $resp['data'] = $stats;
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
	case 'check_service_slot':
		echo $Master->check_service_slot_availability();
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
		// Ensure clean output
		ob_clean();
		header('Content-Type: application/json');
		echo $Master->place_order();
		exit;
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
	case 'get_admin_notifications':
		echo $Master->get_admin_notifications();
	break;
	case 'get_notification_history':
		echo $Master->get_notification_history();
	break;
	case 'get_admin_notification_history':
		echo $Master->get_notification_history();
	break;
	case 'mark_notification_read':
		echo $Master->markNotificationRead();
	break;
	case 'mark_admin_notification_read':
		echo $Master->mark_admin_notification_read();
	break;
	case 'mark_all_notifications_read':
		echo $Master->mark_all_notifications_read();
	break;
	case 'mark_all_admin_notifications_read':
		echo $Master->mark_all_admin_notifications_read();
	break;
	case 'get_admin_notifications_count':
		echo $Master->get_admin_notifications_count();
	break;
	case 'delete_admin_notification':
		echo $Master->delete_admin_notification();
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
		// Ensure clean output
		ob_clean();
		header('Content-Type: application/json');
		echo $Master->update_order_status();
		exit;
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
	case 'cancel_appointment':
		echo $Master->cancel_appointment();
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
	case 'get_all_installment_contracts':
		echo $Master->get_all_installment_contracts();
	break;
	case 'get_installment_stats':
		echo $Master->get_installment_stats();
	break;
	case 'restore_order':
		echo $Master->restore_order();
	break;
	case 'restore_invoice':
		echo $Master->restore_invoice();
	break;
	case 'restore_request':
		echo $Master->restore_request();
	break;
	case 'restore_appointment':
		echo $Master->restore_appointment();
	break;
	case 'restore_document':
		echo $Master->restore_document();
	break;
	case 'restore_stock':
		echo $Master->restore_stock();
	break;
	case 'restore_promo':
		echo $Master->restore_promo();
	break;
	case 'restore_customer':
		echo $Master->restore_customer();
	break;
	default:
		break;
}
