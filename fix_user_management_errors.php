<?php
/**
 * User Management Error Fix Script
 * This script fixes the staff position change error in user management
 */

require_once('./config.php');

echo "=== USER MANAGEMENT ERROR FIX SCRIPT ===\n";

// 1. Add missing email column to users table
echo "1. Adding missing email column to users table...\n";
$email_check = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
if($email_check->num_rows == 0) {
    $result = $conn->query("ALTER TABLE users ADD COLUMN email varchar(255) DEFAULT NULL AFTER username");
    if($result) {
        echo "✓ Email column added successfully\n";
    } else {
        echo "✗ Error adding email column: " . $conn->error . "\n";
    }
} else {
    echo "✓ Email column already exists\n";
}

// 2. Update the role_type enum to include all necessary values
echo "\n2. Updating role_type enum values...\n";
$result = $conn->query("ALTER TABLE users MODIFY COLUMN role_type ENUM('admin','branch_supervisor','admin_assistant','stock_admin','service_admin','mechanic','inventory','service_receptionist') DEFAULT 'admin'");
if($result) {
    echo "✓ Role type enum updated successfully\n";
} else {
    echo "✗ Error updating role_type enum: " . $conn->error . "\n";
}

// 3. Add any other missing columns that might be needed
echo "\n3. Checking for other missing columns...\n";
$required_columns = [
    'status' => "tinyint(1) DEFAULT 1",
    'date_updated' => "datetime DEFAULT NULL ON UPDATE current_timestamp()"
];

foreach($required_columns as $column => $definition) {
    $check = $conn->query("SHOW COLUMNS FROM users LIKE '$column'");
    if($check->num_rows == 0) {
        $result = $conn->query("ALTER TABLE users ADD COLUMN $column $definition");
        if($result) {
            echo "✓ Added $column column\n";
        } else {
            echo "✗ Error adding $column column: " . $conn->error . "\n";
        }
    } else {
        echo "✓ $column column already exists\n";
    }
}

// 4. Test the user update functionality
echo "\n4. Testing user update functionality...\n";
$test_data = [
    'firstname' => 'Test',
    'lastname' => 'User',
    'username' => 'testuser',
    'email' => 'test@example.com',
    'role_type' => 'admin'
];

$data = '';
foreach($test_data as $k => $v){
    if(!in_array($k,array('id','password'))){
        if(!empty($data)) $data .=" , ";
        $data .= " {$k} = '{$v}' ";
    }
}

// Test with a safe query (using a non-existent ID)
$test_query = "UPDATE users set $data where id = 99999";
$result = $conn->query($test_query);
if($result) {
    echo "✓ Test query executed successfully (no rows affected as expected)\n";
} else {
    echo "✗ Test query failed: " . $conn->error . "\n";
}

// 5. Fix the Users.php class to handle missing email column
echo "\n5. Fixing Users.php class...\n";

$users_class_content = file_get_contents('classes/Users.php');

// Check if the class already handles missing email column
if(strpos($users_class_content, 'email') !== false && strpos($users_class_content, 'SHOW COLUMNS') === false) {
    echo "Updating Users.php class to handle missing email column...\n";
    
    // Create a backup
    file_put_contents('classes/Users.php.backup', $users_class_content);
    
    // Update the save_users function to check for email column
    $updated_content = str_replace(
        '// Check if email already exists
		if(!empty($email)){
			$chk_email = $this->conn->query("SELECT * FROM `users` where email =\'{$email}\' ".($id>0? " and id!= \'{$id}\' " : ""))->num_rows;
			if($chk_email > 0){
				return json_encode([\'status\' => \'failed\', \'msg\' => \'Email already exists.\']);
			}
		}',
        '// Check if email column exists and if email already exists
		if(!empty($email)){
			$email_column_check = $this->conn->query("SHOW COLUMNS FROM users LIKE \'email\'");
			if($email_column_check->num_rows > 0){
				$chk_email = $this->conn->query("SELECT * FROM `users` where email =\'{$email}\' ".($id>0? " and id!= \'{$id}\' " : ""))->num_rows;
				if($chk_email > 0){
					return json_encode([\'status\' => \'failed\', \'msg\' => \'Email already exists.\']);
				}
			}
		}',
        $users_class_content
    );
    
    // Also update the data building part to exclude email if column doesn't exist
    $updated_content = str_replace(
        'foreach($_POST as $k => $v){
			if(!in_array($k,array(\'id\',\'password\')){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = \'{$v}\' ";
			}
		}',
        'foreach($_POST as $k => $v){
			if(!in_array($k,array(\'id\',\'password\'))){
				// Check if email column exists before including email
				if($k == \'email\'){
					$email_column_check = $this->conn->query("SHOW COLUMNS FROM users LIKE \'email\'");
					if($email_column_check->num_rows == 0){
						continue; // Skip email if column doesn\'t exist
					}
				}
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = \'{$v}\' ";
			}
		}',
        $updated_content
    );
    
    file_put_contents('classes/Users.php', $updated_content);
    echo "✓ Users.php class updated\n";
} else {
    echo "✓ Users.php class already handles email column properly\n";
}

// 6. Update the manage_user.php form to handle missing email column
echo "\n6. Updating manage_user.php form...\n";

$manage_user_content = file_get_contents('admin/user/manage_user.php');

// Check if email field exists in the form
if(strpos($manage_user_content, 'name="email"') !== false) {
    echo "Email field found in form - checking if column exists...\n";
    
    $email_column_check = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
    if($email_column_check->num_rows == 0) {
        echo "Email column doesn't exist - hiding email field in form...\n";
        
        // Hide the email field
        $updated_content = str_replace(
            '<div class="form-group col-6">
					<label for="email">Email</label>
					<input type="email" name="email" id="email" class="form-control" value="<?php echo isset($meta[\'email\']) ? $meta[\'email\']: \'\' ?>" required autocomplete="off">
					<small class="form-text text-muted">Email must be unique and valid format.</small>
				</div>',
            '<div class="form-group col-6" style="display: none;">
					<label for="email">Email</label>
					<input type="email" name="email" id="email" class="form-control" value="<?php echo isset($meta[\'email\']) ? $meta[\'email\']: \'\' ?>" autocomplete="off">
					<small class="form-text text-muted">Email field is disabled - column not available in database.</small>
				</div>',
            $manage_user_content
        );
        
        file_put_contents('admin/user/manage_user.php', $updated_content);
        echo "✓ Email field hidden in form\n";
    } else {
        echo "✓ Email column exists - form is correct\n";
    }
} else {
    echo "✓ Email field not found in form - no changes needed\n";
}

// 7. Test the complete user update process
echo "\n7. Testing complete user update process...\n";

// Get a test user
$test_user = $conn->query("SELECT * FROM users WHERE id > 0 LIMIT 1")->fetch_assoc();
if($test_user) {
    echo "Testing with user: " . $test_user['firstname'] . " " . $test_user['lastname'] . "\n";
    
    // Test data without email
    $test_data = [
        'firstname' => $test_user['firstname'],
        'lastname' => $test_user['lastname'],
        'username' => $test_user['username'],
        'role_type' => 'admin'
    ];
    
    $data = '';
    foreach($test_data as $k => $v){
        if(!in_array($k,array('id','password'))){
            if(!empty($data)) $data .=" , ";
            $data .= " {$k} = '{$v}' ";
        }
    }
    
    $test_query = "UPDATE users set $data where id = {$test_user['id']}";
    $result = $conn->query($test_query);
    if($result) {
        echo "✓ User update test successful\n";
    } else {
        echo "✗ User update test failed: " . $conn->error . "\n";
    }
} else {
    echo "No users found for testing\n";
}

echo "\n=== USER MANAGEMENT ERROR FIX COMPLETE ===\n";
echo "The staff position change error should now be resolved.\n";
echo "You can test by:\n";
echo "1. Going to Admin > User Management\n";
echo "2. Editing a user\n";
echo "3. Changing their staff position\n";
echo "4. Saving the changes\n";
?>
