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

if ($semester < 1 || $semester > 6) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid semester']);
    exit();
}

// Fetch materials for the semester
$stmt = $conn->prepare("
    SELECT sm.id, sm.subject, sm.material_type, sm.file_name, sm.file_size, 
           sm.description, sm.upload_date, sm.download_count, u.username as uploaded_by
    FROM study_materials sm
    LEFT JOIN users u ON sm.uploaded_by = u.id
    WHERE sm.semester = ?
    ORDER BY sm.upload_date DESC
");
$stmt->bind_param("i", $semester);
$stmt->execute();
$result = $stmt->get_result();

$materials = [];
while ($row = $result->fetch_assoc()) {
    $materials[] = [
        'id' => $row['id'],
        'subject' => $row['subject'],
        'material_type' => ucfirst($row['material_type']),
        'file_name' => $row['file_name'],
        'file_size' => round($row['file_size'] / 1024, 2) . ' KB',
        'description' => $row['description'],
        'upload_date' => date('M j, Y', strtotime($row['upload_date'])),
        'download_count' => $row['download_count'],
        'uploaded_by' => $row['uploaded_by']
    ];
}

$stmt->close();
$conn->close();

echo json_encode(['status' => 'success', 'materials' => $materials]);
?>
