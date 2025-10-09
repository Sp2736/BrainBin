<?php
session_start();
require_once "db_connect.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Get material ID
$material_id = intval($_GET['id'] ?? 0);

if ($material_id <= 0) {
    die("Invalid material ID");
}

// Fetch material details
$stmt = $conn->prepare("SELECT file_name, file_path, file_size FROM study_materials WHERE id = ?");
$stmt->bind_param("i", $material_id);
$stmt->execute();
$stmt->bind_result($file_name, $file_path, $file_size);

if (!$stmt->fetch()) {
    $stmt->close();
    die("Material not found");
}
$stmt->close();

// Check if file exists
if (!file_exists($file_path)) {
    die("File not found on server");
}

// Update download count
$update_stmt = $conn->prepare("UPDATE study_materials SET download_count = download_count + 1 WHERE id = ?");
$update_stmt->bind_param("i", $material_id);
$update_stmt->execute();
$update_stmt->close();

// Log the download
$user_id = $_SESSION['user_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address) VALUES (?, 'download_material', 'study_materials', ?, ?)");
$log_stmt->bind_param("iis", $user_id, $material_id, $ip_address);
$log_stmt->execute();
$log_stmt->close();

$conn->close();

// Set headers for file download
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
header('Content-Length: ' . $file_size);
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Output file
readfile($file_path);
exit();
?>
