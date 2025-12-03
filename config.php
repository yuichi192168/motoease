<?php
ob_start();
ini_set('date.timezone','Asia/Manila');
date_default_timezone_set('Asia/Manila');
session_start();

require_once('initialize.php');
require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');
$db = new DBConnection;
$conn = $db->conn;

function redirect($url=''){
	if(!empty($url))
	echo '<script>location.href="'.base_url .$url.'"</script>';
}
function validate_image($file){
    $file = explode("?",$file)[0];
	if(!empty($file)){
			 //exit;
		if(is_file(base_app.$file)){
			return base_url.$file;
		}else{
			return base_url.'dist/img/no-image-available.png';
		}
	}else{
		return base_url.'dist/img/no-image-available.png';
	}
}
function isMobileDevice(){
    $aMobileUA = array(
        '/iphone/i' => 'iPhone', 
        '/ipod/i' => 'iPod', 
        '/ipad/i' => 'iPad', 
        '/android/i' => 'Android', 
        '/blackberry/i' => 'BlackBerry', 
        '/webos/i' => 'Mobile'
    );

    //Return true if Mobile User Agent is detected
    foreach($aMobileUA as $sMobileKey => $sMobileOS){
        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])){
            return true;
        }
    }
    //Otherwise return false..  
    return false;
}

/**
 * Check if user has permission to access a module
 * @param string $permission_key The permission key to check (e.g., 'user_management', 'inventory_management')
 * @param object $settings The SystemSettings object
 * @param object $conn Database connection
 * @return bool True if user has permission, false otherwise
 */
function hasPermission($permission_key, $settings = null, $conn = null){
    global $_settings, $conn;
    
    if($settings === null) $settings = $_settings;
    
    // Admin always has all permissions
    $role_type = $settings->userdata('role_type');
    if($role_type === 'admin'){
        return true;
    }
    
    // Get user permissions from database (cache per user for current request)
    static $permission_cache = [];
    $user_id = $settings->userdata('id');
    if(empty($user_id)) return false;
    
    if(!isset($permission_cache[$user_id])){
        if($conn === null) global $conn;
        $permission_cache[$user_id] = [];
        $user = $conn->query("SELECT permissions FROM users WHERE id = '{$user_id}'");
        if($user && $user->num_rows > 0){
            $user_data = $user->fetch_assoc();
            $permissions = $user_data['permissions'];
            
            if(!empty($permissions)){
                $permissions_array = json_decode($permissions, true);
                if(is_array($permissions_array)){
                    $permission_cache[$user_id] = $permissions_array;
                }
            }
        }
    }
    
    if(in_array($permission_key, $permission_cache[$user_id])){
        return true;
    }
    
    // Map page names to permission keys for backward compatibility
    $page_to_permission_map = [
        'user' => 'user_management',
        'user/list' => 'user_management',
        'user/manage_user' => 'user_management',
        'clients' => 'customer_management',
        'products' => 'product_management',
        'inventory' => 'inventory_management',
        'inventory/abc_analysis' => 'inventory_management',
        'service_requests' => 'service_requests',
        'promo_images' => 'promo_images',
        'customer_images' => 'customer_images',
        'orders' => 'order_management',
        'invoices' => 'invoice_management',
        'customer_account_balances' => 'customer_accounts',
        'orcr_documents' => 'orcr_documents',
        'report' => 'reports',
        'system_info' => 'system_settings',
        'mechanics' => 'mechanics'
    ];
    
    // If permission key not found, try to map from page name
    if(isset($page_to_permission_map[$permission_key])){
        $mapped_permission = $page_to_permission_map[$permission_key];
        if($mapped_permission === $permission_key){
            return false; // prevent infinite recursion if mapping points to itself
        }
        return hasPermission($mapped_permission, $settings, $conn);
    }
    
    return false;
}

/**
 * Get all permissions for current user
 * @param object $settings The SystemSettings object
 * @param object $conn Database connection
 * @return array Array of permission keys
 */
function getUserPermissions($settings = null, $conn = null){
    global $_settings, $conn;
    
    if($settings === null) $settings = $_settings;
    
    // Admin has all permissions
    $role_type = $settings->userdata('role_type');
    if($role_type === 'admin'){
        return ['all']; // Return special 'all' flag for admin
    }
    
    $user_id = $settings->userdata('id');
    if(empty($user_id)) return [];
    
    if($conn === null) global $conn;
    
    $user = $conn->query("SELECT permissions FROM users WHERE id = '{$user_id}'");
    if($user && $user->num_rows > 0){
        $user_data = $user->fetch_assoc();
        $permissions = $user_data['permissions'];
        
        if(!empty($permissions)){
            $permissions_array = json_decode($permissions, true);
            if(is_array($permissions_array)){
                return $permissions_array;
            }
        }
    }
    
    return [];
}

if(!function_exists('get_product_stock_levels')){
	function get_product_stock_levels($conn, $product_id){
		$snapshot = [
			'stock_in' => 0,
			'stock_out' => 0,
			'current_stock' => 0,
			'reserved_orders' => 0,
			'available_stock' => 0
		];
		if(!isset($conn) || !$conn) return $snapshot;
		$product_id = (int)$product_id;
		if($product_id <= 0) return $snapshot;

		$stock_sql = "
			SELECT 
				COALESCE(SUM(CASE WHEN type = 1 THEN quantity ELSE 0 END), 0) as total_in,
				COALESCE(SUM(CASE WHEN type = 2 THEN quantity ELSE 0 END), 0) as total_out
			FROM stock_list
			WHERE product_id = {$product_id}
			  AND COALESCE(delete_flag, 0) = 0
		";
		if($stock_row = $conn->query($stock_sql)){
			$data = $stock_row->fetch_assoc();
			$snapshot['stock_in'] = (float)($data['total_in'] ?? 0);
			$snapshot['stock_out'] = (float)($data['total_out'] ?? 0);
		}
		$snapshot['current_stock'] = max(0.0, $snapshot['stock_in'] - $snapshot['stock_out']);

		$reserved_sql = "
			SELECT COALESCE(SUM(oi.quantity), 0) as reserved
			FROM order_items oi
			INNER JOIN order_list ol ON oi.order_id = ol.id
			WHERE oi.product_id = {$product_id}
			  AND (ol.status IS NULL OR ol.status != 5)
			  AND COALESCE(ol.delete_flag, 0) = 0
		";
		if($reserved_row = $conn->query($reserved_sql)){
			$snapshot['reserved_orders'] = (float)($reserved_row->fetch_assoc()['reserved'] ?? 0);
		}

		$snapshot['available_stock'] = max(0.0, $snapshot['current_stock'] - $snapshot['reserved_orders']);
		return $snapshot;
	}
}
ob_end_flush();
?>
<?php
// SMTP / Mail configuration (fill these in for PHPMailer)
if(!defined('SMTP_HOST')) define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.mailtrap.io');
if(!defined('SMTP_PORT')) define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
if(!defined('SMTP_USER')) define('SMTP_USER', getenv('SMTP_USER') ?: '');
if(!defined('SMTP_PASS')) define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
if(!defined('SMTP_SECURE')) define('SMTP_SECURE', getenv('SMTP_SECURE') ?: 'tls'); // 'tls' or 'ssl'
if(!defined('MAIL_FROM')) define('MAIL_FROM', getenv('MAIL_FROM') ?: 'no-reply@example.com');
if(!defined('MAIL_FROM_NAME')) define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: ($_settings->info('name') ?: 'MotoEase'));
?>