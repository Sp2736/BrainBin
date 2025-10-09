<?php
require_once "db_connect.php";
session_start();

header('Content-Type: application/json');

$response = ["status" => "error", "message" => "An unknown error occurred."];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password_input = trim($_POST['password']);
    $remember = isset($_POST['remember']);
    
    // Validate inputs
    if (empty($username) || empty($password_input)) {
        $response = ["status" => "error", "message" => "Username and password are required."];
        echo json_encode($response);
        exit();
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, role, status FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $db_username, $password_hash, $role, $status);
        $stmt->fetch();
        
        // Check if account is active
        if ($status !== 'active') {
            $response = ["status" => "error", "message" => "Your account has been deactivated. Contact admin."];
            $stmt->close();
            echo json_encode($response);
            exit();
        }
        
        if (password_verify($password_input, $password_hash)) {
            // Update last login
            $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $update_stmt->bind_param("i", $id);
            $update_stmt->execute();
            $update_stmt->close();
            
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $db_username;
            $_SESSION['role'] = $role;
            $_SESSION['login_time'] = time();
            
            // Set remember me cookie
            if ($remember) {
                setcookie("username", $db_username, time() + (7 * 24 * 60 * 60), "/", "", false, true);
            }
            
            // Log the action
            $ip_address = $_SERVER['REMOTE_ADDR'];
            $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, ip_address) VALUES (?, 'login', ?)");
            $log_stmt->bind_param("is", $id, $ip_address);
            $log_stmt->execute();
            $log_stmt->close();
            
            $response = ["status" => "success", "message" => "Login Successful!", "role" => $role];
        } else {
            $response = ["status" => "error", "message" => "Invalid password"];
        }
    } else {
        $response = ["status" => "error", "message" => "user_not_found"];
    }
    
    $stmt->close();
}
$conn->close();

echo json_encode($response);
?>
