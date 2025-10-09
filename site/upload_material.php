<?php
session_start();
require_once "db_connect.php";

header('Content-Type: application/json');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

// Check if file was uploaded
if (!isset($_FILES['materialFile']) || $_FILES['materialFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded or upload error occurred']);
    exit();
}

// Get form data
$semester = intval($_POST['semester']);
$subject = trim($_POST['subject']);
$materialType = $_POST['materialType'];
$description = trim($_POST['description'] ?? '');

// Validate inputs
if ($semester < 1 || $semester > 6) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid semester']);
    exit();
}

if (empty($subject)) {
    echo json_encode(['status' => 'error', 'message' => 'Subject is required']);
    exit();
}

if (!in_array($materialType, ['notes', 'assignment', 'practical', 'reference'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid material type']);
    exit();
}

// File validation
$file = $_FILES['materialFile'];
$fileName = $file['name'];
$fileTmpName = $file['tmp_name'];
$fileSize = $file['size'];
$fileError = $file['error'];

// Get file extension
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip'];

if (!in_array($fileExt, $allowedExtensions)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Allowed: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP']);
    exit();
}

// Check file size (max 10MB)
if ($fileSize > 10 * 1024 * 1024) {
    echo json_encode(['status' => 'error', 'message' => 'File size exceeds 10MB limit']);
    exit();
}

// Create uploads directory structure if it doesn't exist
$uploadBaseDir = 'uploads';
$semesterDir = $uploadBaseDir . '/semester_' . $semester;

if (!file_exists($uploadBaseDir)) {
    mkdir($uploadBaseDir, 0755, true);
}

if (!file_exists($semesterDir)) {
    mkdir($semesterDir, 0755, true);
}

// Generate unique filename to prevent overwrites
$uniqueFileName = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
$filePath = $semesterDir . '/' . $uniqueFileName;

// Move uploaded file
if (!move_uploaded_file($fileTmpName, $filePath)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save file']);
    exit();
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO study_materials (semester, subject, material_type, file_name, file_path, file_size, description, uploaded_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$uploaded_by = $_SESSION['user_id'];
$stmt->bind_param("issssssi", $semester, $subject, $materialType, $fileName, $filePath, $fileSize, $description, $uploaded_by);

if ($stmt->execute()) {
    $material_id = $stmt->insert_id;
    
    // Log the action
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $log_stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address) VALUES (?, 'upload_material', 'study_materials', ?, ?)");
    $log_stmt->bind_param("iis", $uploaded_by, $material_id, $ip_address);
    $log_stmt->execute();
    $log_stmt->close();
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Material uploaded successfully!',
        'data' => [
            'id' => $material_id,
            'file_name' => $fileName,
            'file_size' => round($fileSize / 1024, 2) . ' KB'
        ]
    ]);
} else {
    // Delete the uploaded file if database insert fails
    unlink($filePath);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save material information']);
}

$stmt->close();
$conn->close();
?>
