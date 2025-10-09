<?php
session_start();
require_once "db_connect.php";

header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$action = $_GET['action'] ?? '';
$admin_id = $_SESSION['user_id'];

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $semester = intval($_POST['semester']);
    $subject = trim($_POST['subject']);
    $materialType = trim($_POST['materialType']);
    $folderName = trim($_POST['folderName']);
    $driveFolderId = trim($_POST['driveFolderId']);
    $description = trim($_POST['description'] ?? '');
    
    // Validation
    if ($semester < 1 || $semester > 6) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid semester']);
        exit();
    }
    
    if (empty($subject) || empty($materialType) || empty($folderName) || empty($driveFolderId)) {
        echo json_encode(['status' => 'error', 'message' => 'All required fields must be filled']);
        exit();
    }
    
    // Insert into database
    $stmt = $conn->prepare("
        INSERT INTO drive_resources (semester, subject, material_type, drive_folder_id, folder_name, description, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssssi", $semester, $subject, $materialType, $driveFolderId, $folderName, $description, $admin_id);
    
    if ($stmt->execute()) {
        // Log the action
        $resource_id = $stmt->insert_id;
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address) VALUES (?, 'add_drive_resource', 'drive_resources', ?, ?)");
        $log_stmt->bind_param("iis", $admin_id, $resource_id, $ip_address);
        $log_stmt->execute();
        $log_stmt->close();
        
        echo json_encode(['status' => 'success', 'message' => 'Drive resource added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add Drive resource']);
    }
    
    $stmt->close();
}
elseif ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_GET['id'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid resource ID']);
        exit();
    }
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM drive_resources WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Log the action
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address) VALUES (?, 'delete_drive_resource', 'drive_resources', ?, ?)");
        $log_stmt->bind_param("iis", $admin_id, $id, $ip_address);
        $log_stmt->execute();
        $log_stmt->close();
        
        echo json_encode(['status' => 'success', 'message' => 'Drive resource deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete Drive resource']);
    }
    
    $stmt->close();
}
else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

$conn->close();
?>
