<?php
require_once dirname(__DIR__) . '/config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	
	private function checkAccountLock($table, $field, $value) {
		$check = $this->conn->query("SELECT is_locked, locked_until FROM {$table} WHERE {$field} = '{$value}'");
		if($check->num_rows > 0) {
			$data = $check->fetch_assoc();
			if($data['is_locked'] == 1) {
				if($data['locked_until'] && strtotime($data['locked_until']) > time()) {
					$lockedUntilTs = strtotime($data['locked_until']);
					return array(
						'msg' => "Account is locked until " . date('M d, Y H:i', $lockedUntilTs),
						'locked_until' => $data['locked_until'],
						'locked_until_ts' => $lockedUntilTs
					);
				} else {
					// Unlock account if lock period has expired
					$this->conn->query("UPDATE {$table} SET is_locked = 0, locked_until = NULL, login_attempts = 0 WHERE {$field} = '{$value}'");
				}
			}
		}
		return false;
	}
	
    private function updateLoginAttempts($table, $field, $value, $success = false) {
        if($success) {
            $this->conn->query("UPDATE {$table} SET login_attempts = 0, is_locked = 0, locked_until = NULL WHERE {$field} = '{$value}'");
            return false;
        } else {
            $this->conn->query("UPDATE {$table} SET login_attempts = login_attempts + 1 WHERE {$field} = '{$value}'");
            
            // Check if account should be locked (after 3 failed attempts)
            $attemptsRow = $this->conn->query("SELECT login_attempts FROM {$table} WHERE {$field} = '{$value}'")->fetch_assoc();
            $attempts = isset($attemptsRow['login_attempts']) ? (int)$attemptsRow['login_attempts'] : 0;
            if($attempts >= 3) {
                // Lock for 1 minute as requested
                $lock_until = date('Y-m-d H:i:s', strtotime('+1 minute'));
                $this->conn->query("UPDATE {$table} SET is_locked = 1, locked_until = '{$lock_until}' WHERE {$field} = '{$value}'");
                return true;
            }
            return false;
        }
    }
	
	public function login(){
		extract($_POST);
		$password = md5($password);
		
		// Check if account is locked
		$lock_check = $this->checkAccountLock('users', 'username', $username);
		if($lock_check) {
			return json_encode(array('status'=>'locked', 'msg'=> is_array($lock_check) ? $lock_check['msg'] : $lock_check, 'locked_until' => is_array($lock_check) ? $lock_check['locked_until'] : null, 'locked_until_ts' => is_array($lock_check) ? $lock_check['locked_until_ts'] : null));
		}
		
		$stmt = $this->conn->prepare("SELECT * from users where username = ? and `password` = ? ");
		$stmt->bind_param("ss",$username,$password);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0){
			// Reset login attempts on successful login
			$this->updateLoginAttempts('users', 'username', $username, true);
			
			foreach($result->fetch_array() as $k => $v){
				if(!is_numeric($k) && $k != 'password'){
					$this->settings->set_userdata($k,$v);
				}
			}
			$this->settings->set_userdata('login_type',1);
			
			// Update last login
			$this->conn->query("UPDATE users SET last_login = NOW() WHERE username = '{$username}'");
			
		return json_encode(array('status'=>'success'));
        }else{
            // Increment failed login attempts and detect if locked now
			$lockedNow = $this->updateLoginAttempts('users', 'username', $username, false);
			if ($lockedNow) {
				$lockInfo = $this->checkAccountLock('users', 'username', $username);
				return json_encode(array(
					'status' => 'locked',
					'msg' => is_array($lockInfo) ? $lockInfo['msg'] : ($lockInfo ?: 'Account is locked.'),
					'locked_until' => is_array($lockInfo) ? $lockInfo['locked_until'] : null,
					'locked_until_ts' => is_array($lockInfo) ? $lockInfo['locked_until_ts'] : null
				));
			}
            
            return json_encode(array('status'=>'incorrect','last_qry'=>"SELECT * from users where username = '$username' and `password` = md5('$password') "));
        }
	}
	public function logout(){
		if($this->settings->sess_des()){
			redirect('admin/login.php');
		}
	}
	public function login_client(){
		$resp = array();
		
		try {
			extract($_POST);
			$password = md5($password);
			
			// Check if account is locked
			$lock_check = $this->checkAccountLock('client_list', 'email', $email);
			if($lock_check) {
				return json_encode(array('status'=>'locked', 'msg'=> is_array($lock_check) ? $lock_check['msg'] : $lock_check, 'locked_until' => is_array($lock_check) ? $lock_check['locked_until'] : null, 'locked_until_ts' => is_array($lock_check) ? $lock_check['locked_until_ts'] : null));
			}
			
			$stmt = $this->conn->prepare("SELECT * from client_list where email = ? and `password` =? and delete_flag = ?  ");
			$delete_flag = 0;
			$stmt->bind_param("ssi",$email,$password,$delete_flag);
			$stmt->execute();
			$result = $stmt->get_result();
			
			if($result->num_rows > 0){
				$data = $result->fetch_array();
				if($data['status'] == 1){
					// Reset login attempts on successful login
					$this->updateLoginAttempts('client_list', 'email', $email, true);
					
					// Debug logging
					error_log("Login successful for user: " . $email);
					error_log("User data: " . print_r($data, true));
					
					// Clear any existing session data
					unset($_SESSION['userdata']);
					
					// Set user data in session
					foreach($data as $k => $v){
						if(!is_numeric($k) && $k != 'password'){
							$this->settings->set_userdata($k,$v);
						}
					}
					$this->settings->set_userdata('login_type',2);
					
					// Force session write
					session_write_close();
					session_start();
					
					// Debug: Check if session data is set
					error_log("Session data after login: " . print_r($_SESSION['userdata'], true));
					
					// Update last login
					$this->conn->query("UPDATE client_list SET last_login = NOW() WHERE email = '{$email}'");
					
					$resp['status'] = 'success';
					$resp['msg'] = 'Login successful';
					$resp['user_id'] = $data['id'];
					$resp['login_type'] = 2;
				}else{
					$resp['status'] = 'failed';
					$resp['msg'] = ' Your Account has been blocked by the management.';
				}
			}else{
				// Increment failed login attempts and detect if locked now
				$lockedNow = $this->updateLoginAttempts('client_list', 'email', $email, false);
				if ($lockedNow) {
					$lockInfo = $this->checkAccountLock('client_list', 'email', $email);
					$resp['status'] = 'locked';
					$resp['msg'] = is_array($lockInfo) ? $lockInfo['msg'] : ($lockInfo ?: 'Account is locked.');
					$resp['locked_until'] = is_array($lockInfo) ? $lockInfo['locked_until'] : null;
					$resp['locked_until_ts'] = is_array($lockInfo) ? $lockInfo['locked_until_ts'] : null;
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = ' Incorrect Email or Password.';
				}
			}
		} catch (Exception $e) {
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occurred during login: ' . $e->getMessage();
		}
		
		return json_encode($resp);
	}
	public function logout_client(){
		if($this->settings->sess_des()){
			redirect('?');
		}
	}
	
	public function reset_password() {
		extract($_POST);
		
        // Throttle: limit to 1 request per 30 seconds per session
        if(isset($_SESSION['last_reset_req']) && (time() - (int)$_SESSION['last_reset_req']) < 30){
            $resp['status'] = 'success';
            $resp['msg'] = 'If the details match our records, a reset option is now available.';
            return json_encode($resp);
        }
        $_SESSION['last_reset_req'] = time();

        // Check if email exists and verify additional factors
        $emailEsc = $this->conn->real_escape_string($email);
        $lnameEsc = isset($lastname) ? $this->conn->real_escape_string(trim($lastname)) : '';
        $last4 = isset($contact_last4) ? preg_replace('/[^0-9]/', '', $contact_last4) : '';
        $check = $this->conn->query("SELECT id, firstname, lastname, contact FROM client_list WHERE email = '{$emailEsc}' AND delete_flag = 0");
		if($check->num_rows > 0) {
			$user = $check->fetch_assoc();
            // Verify lastname (case-insensitive) and last 4 digits of contact
            $lnameOk = empty($lnameEsc) ? false : (strcasecmp($lnameEsc, $user['lastname']) === 0);
            $contactDigits = preg_replace('/[^0-9]/', '', (string)$user['contact']);
            $contactOk = !empty($last4) && strlen($contactDigits) >= 4 && substr($contactDigits, -4) === $last4;
            if(!$lnameOk || !$contactOk){
                // Always reply generically to avoid user enumeration
                $resp['status'] = 'success';
                $resp['msg'] = 'If the details match our records, a reset option is now available.';
                return json_encode($resp);
            }
			
			// Generate reset token
			$token = bin2hex(random_bytes(32));
			$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
			
			// Store reset token (you might want to create a password_resets table)
			$this->conn->query("UPDATE client_list SET reset_token = '{$token}', reset_expires = '{$expires}' WHERE id = '{$user['id']}'");
			// Alternative to email: return the token directly (for SMS or manual copy)
			// In production, prefer SMS OTP or admin-issued reset links.
			$resp['status'] = 'success';
			$resp['msg'] = 'Password reset token has been generated.';
			$resp['reset_link'] = base_url . "reset_password.php?token=" . $token;
			$resp['token'] = $token;
		} else {
            // Generic response to avoid email enumeration
            $resp['status'] = 'success';
            $resp['msg'] = 'If the details match our records, a reset option is now available.';
		}
		
		return json_encode($resp);
	}

	public function apply_password_reset(){
		$resp = array();
		try{
			extract($_POST);
			$token = isset($token) ? trim($token) : '';
			$new_password = isset($password) ? trim($password) : '';
			if(empty($token) || empty($new_password)){
				$resp['status'] = 'failed';
				$resp['msg'] = 'Missing token or password.';
				return json_encode($resp);
			}
			$tokenEsc = $this->conn->real_escape_string($token);
			$now = date('Y-m-d H:i:s');
			$q = $this->conn->query("SELECT id FROM client_list WHERE reset_token = '{$tokenEsc}' AND reset_expires IS NOT NULL AND reset_expires > '{$now}' AND delete_flag = 0 LIMIT 1");
			if(!$q || $q->num_rows === 0){
				$resp['status'] = 'failed';
				$resp['msg'] = 'Invalid or expired token.';
				return json_encode($resp);
			}
			$row = $q->fetch_assoc();
			$client_id = (int)$row['id'];
			$hashed = md5($new_password);
			$upd = $this->conn->query("UPDATE client_list SET password = '{$hashed}', reset_token = NULL, reset_expires = NULL WHERE id = '{$client_id}'");
			if($upd){
				$resp['status'] = 'success';
				$resp['msg'] = 'Password has been reset successfully.';
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = 'Failed to update password.';
				$resp['error'] = $this->conn->error;
			}
		}catch(Exception $e){
			$resp['status'] = 'failed';
			$resp['msg'] = 'An error occurred: ' . $e->getMessage();
		}
		return json_encode($resp);
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	case 'login_client':
		echo $auth->login_client();
		break;
	case 'logout_client':
		echo $auth->logout_client();
		break;
	case 'reset_password':
		echo $auth->reset_password();
		break;
	case 'apply_password_reset':
		echo $auth->apply_password_reset();
		break;
	default:
		echo $auth->index();
		break;
}

