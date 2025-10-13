	<?php
require_once('../config.php');
Class Users extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_users(){
		extract($_POST);
		$data = '';
		$chk = $this->conn->query("SELECT * FROM `users` where username ='{$username}' ".($id>0? " and id!= '{$id}' " : ""))->num_rows;
		if($chk > 0){
			return 3;
			exit;
		}
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id','password'))){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(!empty($password)){
			$password = md5($password);
			if(!empty($data)) $data .=" , ";
			$data .= " `password` = '{$password}' ";
		}

		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
				$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
				if($move){
					$data .=" , avatar = '{$fname}' ";
					if(isset($_SESSION['userdata']['avatar']) && is_file('../'.$_SESSION['userdata']['avatar']) && $_SESSION['userdata']['id'] == $id)
						unlink('../'.$_SESSION['userdata']['avatar']);
				}
		}
		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO users set {$data}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully saved.');
				return 1;
			}else{
				return 2;
			}

		}else{
			$qry = $this->conn->query("UPDATE users set $data where id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully updated.');
				foreach($_POST as $k => $v){
					if($k != 'id'){
						if(!empty($data)) $data .=" , ";
						$this->settings->set_userdata($k,$v);
					}
				}
				if(isset($fname) && isset($move))
				$this->settings->set_userdata('avatar',$fname);

				return 1;
			}else{
				return "UPDATE users set $data where id = {$id}";
			}
			
		}
	}
	public function delete_users(){
		extract($_POST);
		$resp = array();
		$avatar = $this->conn->query("SELECT avatar FROM users where id = '{$id}'")->fetch_array()['avatar'];
		$qry = $this->conn->query("DELETE FROM users where id = $id");
		if($qry){
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			if(is_file(base_app.$avatar))
				unlink(base_app.$avatar);
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	public function save_client(){
		$resp = array();
		
		// Handle password encryption
		if(!empty($_POST['password']))
			$_POST['password'] = md5($_POST['password']);
		else
			unset($_POST['password']);
			
		// Handle old password verification for updates
		if(isset($_POST['oldpassword'])){
			if($this->settings->userdata('id') > 0 && $this->settings->userdata('login_type') == 2){
				$get = $this->conn->query("SELECT * FROM `client_list` where id = '{$this->settings->userdata('id')}'");
				$res = $get->fetch_array();
				if($res['password'] != md5($_POST['oldpassword'])){
					return  json_encode([
						'status' =>'failed',
						'msg'=>' Current Password is incorrect.'
					]);
				}
			}
			unset($_POST['oldpassword']);
		}
		
		// Handle avatar upload
		if(isset($_FILES['img']) && $_FILES['img']['error'] == 0){
			$upload_dir = "uploads/";
			if(!is_dir($upload_dir)){
				mkdir($upload_dir, 0777, true);
			}
			
			// Validate file type
			$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
			$file_extension = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
			
			if(in_array($file_extension, $allowed_types)){
				$file_name = time() . '_' . uniqid() . '.' . $file_extension;
				$file_path = $upload_dir . $file_name;
				$full_path = base_app . $file_path;
				
				if(move_uploaded_file($_FILES['img']['tmp_name'], $full_path)){
					$_POST['avatar'] = $file_path;
				} else {
					$resp['status'] = 'failed';
					$resp['msg'] = 'Failed to upload avatar image.';
					return json_encode($resp);
				}
			} else {
				$resp['status'] = 'failed';
				$resp['msg'] = 'Invalid file type. Please upload JPG, PNG, GIF, or WEBP images only.';
				return json_encode($resp);
			}
		}
		
		// Set default values for new registrations
		if(empty($_POST['id'])){
			$_POST['status'] = 1; // Active status
			$_POST['delete_flag'] = 0; // Not deleted
		}
		
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if(!empty($data)) $data .= ", ";
				$data .= " `{$k}` = '{$v}' ";
			}
		}
		
		// Check if email already exists
		$check = $this->conn->query("SELECT * FROM `client_list` where email = '{$email}' and delete_flag ='0' ".(is_numeric($id) && $id > 0 ? " and id != '{$id}'" : "")." ")->num_rows;
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = ' Email already exists in the database.';
		}else{
			if(empty($id)){
				$sql = "INSERT INTO `client_list` set $data";
			}else{
				$sql = "UPDATE `client_list` set $data where id = '{$id}'";
			}
			
			$save = $this->conn->query($sql);
			if($save){
				$resp['status'] = 'success';
				if(empty($id)){
					$resp['msg'] = " Account is successfully registered.";
					$resp['auto_login'] = true; // Flag for auto-login
					$resp['user_id'] = $this->conn->insert_id; // Get the new user ID
				}else if($this->settings->userdata('id') == $id && $this->settings->userdata('login_type') == 2){
					$resp['msg'] = " Account Details has been updated successfully.";
					foreach($_POST as $k => $v){
						if(!in_array($k,['password'])){
							$this->settings->set_userdata($k,$v);
						}
					}
				}else{
					$resp['msg'] = " Client's Account Details has been updated successfully.";
				}
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = " Database error: " . $this->conn->error;
				if(empty($id)){
					$resp['msg'] = " Account has failed to register: " . $this->conn->error;
				}else if($this->settings->userdata('id') == $id && $this->settings->userdata('login_type') == 2){
					$resp['msg'] = " Account Details has failed to update: " . $this->conn->error;
				}else{
					$resp['msg'] = " Client's Account Details has failed to update: " . $this->conn->error;
				}
			}
		}
		
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	} 

	function delete_client(){
		extract($_POST);
		$resp = array();
		$del = $this->conn->query("UPDATE `client_list` set delete_flag = 1 where id='{$id}'");
		if($del){
			$resp['status'] = 'success';
			$resp['msg'] = ' Client Account has been deleted successfully.';
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = " Client Account has failed to delete";
		}
		if($resp['status'] =='success')
		$this->settings->set_flashdata('success',$resp['msg']);
		return json_encode($resp);
	}
	
}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save':
		echo $users->save_users();
	break;
	case 'delete':
		echo $users->delete_users();
	break;
	case 'save_client':
		echo $users->save_client();
	break;
	case 'delete_client':
		echo $users->delete_client();
	break;
	default:
		// echo $sysset->index();
		break;
}