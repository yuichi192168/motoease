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
				
				foreach($data as $k => $v){
					if(!is_numeric($k) && $k != 'password'){
						$this->settings->set_userdata($k,$v);
					}
				}
				$this->settings->set_userdata('login_type',2);
				
				// Update last login
				$this->conn->query("UPDATE client_list SET last_login = NOW() WHERE email = '{$email}'");
				
				$resp['status'] = 'success';
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
                $resp['error'] = $this->conn->error;
                $resp['res'] = $result;
            }
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
		
		// Check if email exists
		$check = $this->conn->query("SELECT id, firstname, lastname FROM client_list WHERE email = '{$email}' AND delete_flag = 0");
		if($check->num_rows > 0) {
			$user = $check->fetch_assoc();
			
			// Generate reset token
			$token = bin2hex(random_bytes(32));
			$expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
			
			// Store reset token (you might want to create a password_resets table)
			$this->conn->query("UPDATE client_list SET reset_token = '{$token}', reset_expires = '{$expires}' WHERE id = '{$user['id']}'");
			
			// Send email (implement your email sending logic here)
			$reset_link = base_url . "reset_password.php?token=" . $token;
			
			$resp['status'] = 'success';
			$resp['msg'] = 'Password reset instructions have been sent to your email.';
		} else {
			$resp['status'] = 'failed';
			$resp['msg'] = 'Email address not found.';
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
	default:
		echo $auth->index();
		break;
}

