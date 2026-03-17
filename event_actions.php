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

if ($action === 'add_event') {
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];
    $event_description = trim($_POST['event_description']);
    $event_status = $_POST['event_status'];
    
    // Validate inputs
    if (empty($event_name) || empty($event_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Event name and date are required']);
        exit();
    }
    
    if (!in_array($event_status, ['open', 'closed'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event status']);
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO events (event_name, event_date, event_description, event_status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $event_name, $event_date, $event_description, $event_status);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add event']);
    }
    $stmt->close();
    
} elseif ($action === 'edit_event') {
    $event_id = intval($_POST['event_id']);
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];
    $event_description = trim($_POST['event_description']);
    $event_status = $_POST['event_status'];
    
    // Validate inputs
    if (empty($event_name) || empty($event_date)) {
        echo json_encode(['status' => 'error', 'message' => 'Event name and date are required']);
        exit();
    }
    
    if (!in_array($event_status, ['open', 'closed'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event status']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_date = ?, event_description = ?, event_status = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $event_name, $event_date, $event_description, $event_status, $event_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update event']);
    }
    $stmt->close();
    
} elseif ($action === 'toggle_status') {
    $event_id = intval($_POST['event_id']);
    $new_status = $_POST['new_status'];
    
    if (!in_array($new_status, ['open', 'closed'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid event status']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE events SET event_status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $event_id);
    
    if ($stmt->execute()) {
        $status_text = $new_status === 'open' ? 'opened' : 'closed';
        echo json_encode(['status' => 'success', 'message' => "Event successfully $status_text"]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update event status']);
    }
    $stmt->close();
    
} elseif ($action === 'delete_event') {
    $event_id = intval($_POST['event_id']);
    
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Event deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete event']);
    }
    $stmt->close();
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

$conn->close();
?>
