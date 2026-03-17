<?php
session_start();
require_once "db_connect.php";

header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'change_role') {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['new_role'];
    
    // Validate role
    if (!in_array($new_role, ['user', 'admin'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid role']);
        exit();
    }
    
    // Prevent admin from changing their own role
    if ($user_id === $_SESSION['user_id']) {
        echo json_encode(['status' => 'error', 'message' => 'You cannot change your own role']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    
    if ($stmt->execute()) {
        $admin_id = $_SESSION['user_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address) VALUES (?, 'change_role', 'users', ?, ?)");
        $log_stmt->bind_param("iis", $admin_id, $user_id, $ip_address);
        $log_stmt->execute();
        $log_stmt->close();
        
        $action_text = $new_role === 'admin' ? 'promoted to admin' : 'demoted to user';
        echo json_encode(['status' => 'success', 'message' => "User successfully $action_text"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user role']);
    }
    $stmt->close();
    
} elseif ($action === 'delete_user') {
    $user_id = intval($_POST['user_id']);
    
    // Prevent admin from deleting themselves
    if ($user_id === $_SESSION['user_id']) {
        echo json_encode(['status' => 'error', 'message' => 'You cannot delete your own account']);
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $admin_id = $_SESSION['user_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address) VALUES (?, 'delete_user', 'users', ?, ?)");
        $log_stmt->bind_param("iis", $admin_id, $user_id, $ip_address);
        $log_stmt->execute();
        $log_stmt->close();
        
        echo json_encode(['status' => 'success', 'message' => 'User successfully deleted']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete user']);
    }
    $stmt->close();
    
} elseif ($action === 'toggle_status') {
    $user_id = intval($_POST['user_id']);
    $new_status = $_POST['new_status'];
    
    // Validate status
    if (!in_array($new_status, ['active', 'inactive'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
        exit();
    }
    
    // Prevent admin from changing their own status
    if ($user_id === $_SESSION['user_id']) {
        echo json_encode(['status' => 'error', 'message' => 'You cannot change your own status']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $user_id);
    
    if ($stmt->execute()) {
        $admin_id = $_SESSION['user_id'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address) VALUES (?, 'toggle_status', 'users', ?, ?)");
        $log_stmt->bind_param("iis", $admin_id, $user_id, $ip_address);
        $log_stmt->execute();
        $log_stmt->close();
        
        $action_text = $new_status === 'active' ? 'activated' : 'deactivated';
        echo json_encode(['status' => 'success', 'message' => "User successfully $action_text"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update user status']);
    }
    $stmt->close();
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

$conn->close();
?>
