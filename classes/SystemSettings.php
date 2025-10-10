<?php
if(!class_exists('DBConnection')){
	require_once('../config.php');
	require_once('DBConnection.php');
}
class SystemSettings extends DBConnection{
	public function __construct(){
		parent::__construct();
	}
	function check_connection(){
		return($this->conn);
	}
	function load_system_info(){
		 //if(!isset($_SESSION['system_info'])){
			$sql = "SELECT * FROM system_info";
			$qry = $this->conn->query($sql);
				while($row = $qry->fetch_assoc()){
					$_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
				}
		// }
	}
	function update_system_info(){
		$sql = "SELECT * FROM system_info";
		$qry = $this->conn->query($sql);
			while($row = $qry->fetch_assoc()){
				if(isset($_SESSION['system_info'][$row['meta_field']]))unset($_SESSION['system_info'][$row['meta_field']]);
				$_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
			}
		return true;
	}
	function update_settings_info(){
		$data = "";
		foreach ($_POST as $key => $value) {
			if(!in_array($key,array("about_us","privacy_policy")))
			if(isset($_SESSION['system_info'][$key])){
				$value = str_replace("'", "&apos;", $value);
				$qry = $this->conn->query("UPDATE system_info set meta_value = '{$value}' where meta_field = '{$key}' ");
			}else{
				$qry = $this->conn->query("INSERT into system_info set meta_value = '{$value}', meta_field = '{$key}' ");
			}
		}
		if(isset($_POST['about_us'])){
			file_put_contents('../about.html',$_POST['about_us']);
		}
		if(isset($_POST['privacy_policy'])){
			file_put_contents('../privacy_policy.html',$_POST['privacy_policy']);
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
			if(isset($_SESSION['system_info']['logo'])){
				$qry = $this->conn->query("UPDATE system_info set meta_value = '{$fname}' where meta_field = 'logo' ");
				if(is_file('../'.$_SESSION['system_info']['logo'])) unlink('../'.$_SESSION['system_info']['logo']);
			}else{
				$qry = $this->conn->query("INSERT into system_info set meta_value = '{$fname}',meta_field = 'logo' ");
			}
		}
		if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['cover']['name'];
			$move = move_uploaded_file($_FILES['cover']['tmp_name'],'../'. $fname);
			if(isset($_SESSION['system_info']['cover'])){
				$qry = $this->conn->query("UPDATE system_info set meta_value = '{$fname}' where meta_field = 'cover' ");
				if(is_file('../'.$_SESSION['system_info']['cover'])) unlink('../'.$_SESSION['system_info']['cover']);
			}else{
				$qry = $this->conn->query("INSERT into system_info set meta_value = '{$fname}',meta_field = 'cover' ");
			}
		}
		if(isset($_FILES['main_logo']) && $_FILES['main_logo']['tmp_name'] != ''){
			$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['main_logo']['name'];
			$move = move_uploaded_file($_FILES['main_logo']['tmp_name'],'../'. $fname);
			if(isset($_SESSION['system_info']['main_logo'])){
				$qry = $this->conn->query("UPDATE system_info set meta_value = '{$fname}' where meta_field = 'main_logo' ");
				if(is_file('../'.$_SESSION['system_info']['main_logo'])) unlink('../'.$_SESSION['system_info']['main_logo']);
			}else{
				$qry = $this->conn->query("INSERT into system_info set meta_value = '{$fname}',meta_field = 'main_logo' ");
			}
		}
		if(isset($_FILES['secondary_logo']) && $_FILES['secondary_logo']['tmp_name'] != ''){
			$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['secondary_logo']['name'];
			$move = move_uploaded_file($_FILES['secondary_logo']['tmp_name'],'../'. $fname);
			if(isset($_SESSION['system_info']['secondary_logo'])){
				$qry = $this->conn->query("UPDATE system_info set meta_value = '{$fname}' where meta_field = 'secondary_logo' ");
				if(is_file('../'.$_SESSION['system_info']['secondary_logo'])) unlink('../'.$_SESSION['system_info']['secondary_logo']);
			}else{
				$qry = $this->conn->query("INSERT into system_info set meta_value = '{$fname}',meta_field = 'secondary_logo' ");
			}
		}
		
		// Handle promo image uploads
		if(isset($_FILES['promo_images']) && !empty($_FILES['promo_images']['name'][0])){
			foreach($_FILES['promo_images']['name'] as $key => $filename){
				if($_FILES['promo_images']['tmp_name'][$key] != ''){
					$fname = 'uploads/promos/'.strtotime(date('y-m-d H:i')).'_'.$filename;
					$upload_dir = '../uploads/promos/';
					if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
					$move = move_uploaded_file($_FILES['promo_images']['tmp_name'][$key], '../'.$fname);
					if($move){
						$title = isset($_POST['promo_titles'][$key]) ? $_POST['promo_titles'][$key] : 'Promo Image';
						$description = isset($_POST['promo_descriptions'][$key]) ? $_POST['promo_descriptions'][$key] : '';
						$qry = $this->conn->query("INSERT into promo_images set title = '{$title}', description = '{$description}', image_path = '{$fname}' ");
					}
				}
			}
		}
		
		// Handle customer purchase image uploads
		if(isset($_FILES['customer_images']) && !empty($_FILES['customer_images']['name'][0])){
			foreach($_FILES['customer_images']['name'] as $key => $filename){
				if($_FILES['customer_images']['tmp_name'][$key] != ''){
					$fname = 'uploads/customers/'.strtotime(date('y-m-d H:i')).'_'.$filename;
					$upload_dir = '../uploads/customers/';
					if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
					$move = move_uploaded_file($_FILES['customer_images']['tmp_name'][$key], '../'.$fname);
					if($move){
						$customer_name = isset($_POST['customer_names'][$key]) ? $_POST['customer_names'][$key] : 'Customer';
						$motorcycle_model = isset($_POST['motorcycle_models'][$key]) ? $_POST['motorcycle_models'][$key] : 'Motorcycle';
						$testimonial = isset($_POST['customer_testimonials'][$key]) ? $_POST['customer_testimonials'][$key] : '';
						$purchase_date = isset($_POST['purchase_dates'][$key]) ? $_POST['purchase_dates'][$key] : date('Y-m-d');
						$qry = $this->conn->query("INSERT into customer_purchase_images set customer_name = '{$customer_name}', motorcycle_model = '{$motorcycle_model}', testimonial = '{$testimonial}', purchase_date = '{$purchase_date}', image_path = '{$fname}' ");
					}
				}
			}
		}
		
		$update = $this->update_system_info();
		$flash = $this->set_flashdata('success','System Info Successfully Updated.');
		if($update && $flash){
			//var_dump($_SESSION);
			return true;
		}
	}
	function set_userdata($field='',$value=''){
		if(!empty($field) && !empty($value)){
			$_SESSION['userdata'][$field]= $value;
		}
	}
	function userdata($field = ''){
		if(!empty($field)){
			if(isset($_SESSION['userdata'][$field]))
				return $_SESSION['userdata'][$field];
			else
				return null;
		}else{
			return false;
		}
	}
	function set_flashdata($flash='',$value=''){
		if(!empty($flash) && !empty($value)){
			$_SESSION['flashdata'][$flash]= $value;
		return true;
		}
	}
	function chk_flashdata($flash = ''){
		if(isset($_SESSION['flashdata'][$flash])){
			return true;
		}else{
			return false;
		}
	}
	function flashdata($flash = ''){
		if(!empty($flash)){
			$_tmp = $_SESSION['flashdata'][$flash];
			unset($_SESSION['flashdata']);
			return $_tmp;
		}else{
			return false;
		}
	}
	function sess_des(){
		if(isset($_SESSION['userdata'])){
				unset($_SESSION['userdata']);
			return true;
		}
			return true;
	}
	function info($field=''){
		if(!empty($field)){
			if(isset($_SESSION['system_info'][$field]))
				return $_SESSION['system_info'][$field];
			else
				return false;
		}else{
			return false;
		}
	}
	function set_info($field='',$value=''){
		if(!empty($field) && !empty($value)){
			$_SESSION['system_info'][$field] = $value;
		}
	}
}
$_settings = new SystemSettings();
$_settings->load_system_info();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'update_settings':
		echo $sysset->update_settings_info();
		break;
	default:
		// echo $sysset->index();
		break;
}
?>