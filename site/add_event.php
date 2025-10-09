<?php
require_once "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];

    $stmt = $conn->prepare("INSERT INTO events (event_name, event_date) VALUES (?, ?)");
    $stmt->bind_param("ss", $event_name, $event_date);

    if ($stmt->execute()) {
        echo "Event added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
</head>
<body>
    <h2>Add New Event</h2>
    <form method="POST" action="">
        <label>Event Name:</label><br>
        <input type="text" name="event_name" required><br><br>
        
        <label>Event Date:</label><br>
        <input type="date" name="event_date" required><br><br>
        
        <button type="submit">Add Event</button>
    </form>
</body>
</html>
