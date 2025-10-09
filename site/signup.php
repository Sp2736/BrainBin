<?php
require_once "db_connect.php";
session_start();

header('Content-Type: application/json');

$response = ["status" => "error", "message" => "An unknown error occurred."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $password_input = trim($_POST['password']);
    
    // Validation
    $errors = [];
    
    // Username validation
    if (empty($username) || !preg_match('/^[A-Za-z0-9]{3,50}$/', $username)) {
        $errors[] = "Username must be 3-50 alphanumeric characters.";
    }
    
    // Email validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    
    // Phone validation (optional but if provided must be valid)
    if (!empty($phone) && !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }
    
    // Password validation
    if (strlen($password_input) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }
    
    if (!empty($errors)) {
        $response = ["status" => "error", "message" => implode(" ", $errors)];
        echo json_encode($response);
        exit();
    }
    
    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $response = ["status" => "error", "message" => "Username or email already exists."];
        $stmt->close();
        echo json_encode($response);
        exit();
    }
    $stmt->close();
    
    // Hash password
    $password_hash = password_hash($password_input, PASSWORD_DEFAULT);
    
    // Insert into database with all fields
    $stmt = $conn->prepare("INSERT INTO users (username, email, phone, dob, password, role) VALUES (?, ?, ?, ?, ?, 'user')");
    $stmt->bind_param("sssss", $username, $email, $phone, $dob, $password_hash);
    
    if ($stmt->execute()) {
        // Auto-login after signup
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'user';
        $_SESSION['login_time'] = time();
        
        // Log the action
        $user_id = $stmt->insert_id;
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address) VALUES (?, 'signup', 'users', ?, ?)");
        $log_stmt->bind_param("iis", $user_id, $user_id, $ip_address);
        $log_stmt->execute();
        $log_stmt->close();
        
        // require_once "google_drive_config.php";
        // if (GOOGLE_DRIVE_ENABLED) {
        //     $driveResult = grantAllFoldersAccess($user_id, $email, $conn);
        //     error_log("Drive access granted for user $user_id: {$driveResult['granted']} folders, {$driveResult['failed']} failed");
        // }
        
        $response = ["status" => "success", "message" => "Registration successful!"];
    } else {
        $response = ["status" => "error", "message" => "Registration failed. Please try again."];
    }
    
    $stmt->close();
}

$conn->close();
echo json_encode($response);
exit;
?>
