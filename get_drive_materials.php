<?php
session_start();
require_once "db_connect.php";

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$semester = intval($_GET['semester'] ?? 0);

// Build query based on semester filter
if ($semester > 0 && $semester <= 6) {
    $stmt = $conn->prepare("
        SELECT dr.id, dr.semester, dr.subject, dr.material_type, dr.drive_folder_id, 
               dr.folder_name, dr.description, dr.resource_type
        FROM drive_resources dr
        WHERE dr.semester = ?
        ORDER BY dr.semester, dr.subject, dr.material_type
    ");
    $stmt->bind_param("i", $semester);
} else {
    $stmt = $conn->prepare("
        SELECT dr.id, dr.semester, dr.subject, dr.material_type, dr.drive_folder_id, 
               dr.folder_name, dr.description, dr.resource_type
        FROM drive_resources dr
        ORDER BY dr.semester, dr.subject, dr.material_type
    ");
}

$stmt->execute();
$result = $stmt->get_result();

// Organize materials by semester and subject
$materials = [];
while ($row = $result->fetch_assoc()) {
    $sem = $row['semester'];
    $subject = $row['subject'];
    
    if (!isset($materials[$sem])) {
        $materials[$sem] = [];
    }
    
    if (!isset($materials[$sem][$subject])) {
        $materials[$sem][$subject] = [];
    }
    
    $materials[$sem][$subject][] = [
        'id' => $row['id'],
        'material_type' => $row['material_type'],
        'drive_folder_id' => $row['drive_folder_id'],
        'folder_name' => $row['folder_name'],
        'description' => $row['description'],
        'resource_type' => $row['resource_type']
    ];
}

$stmt->close();
$conn->close();

echo json_encode(['status' => 'success', 'materials' => $materials]);
?>
