<?php
session_start();
require_once "db_connect.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Update session time
$_SESSION['login_time'] = time();

// Fetch admin info
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username);
$stmt->fetch();
$stmt->close();

// Fetch all events
$events_stmt = $conn->query("SELECT id, event_name, event_date, event_description, event_status, created_at FROM events ORDER BY event_date DESC");
$events = [];
while ($row = $events_stmt->fetch_assoc()) {
    $events[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="admin_style.css" />
    <link rel="icon" href="icon2.png" />
    <title>Manage Events | BrainBin</title>
    <style>
        .event-form {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.2);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .event-form h2 {
            text-align: center;
            color: #0b3d91;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #0b3d91;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #dbe9ff;
            border-radius: 8px;
            font-family: "Jost", sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0b3d91;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 15px;
            background-color: #0b3d91;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background-color: #164dba;
            transform: translateY(-2px);
        }
        
        .events-list {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(11, 61, 145, 0.2);
            padding: 30px;
        }
        
        .events-list h2 {
            text-align: center;
            color: #0b3d91;
            margin-bottom: 25px;
        }
        
        .event-card {
            background-color: #f8faff;
            border-left: 4px solid #0b3d91;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        
        .event-card:hover {
            box-shadow: 0 4px 12px rgba(11, 61, 145, 0.2);
        }
        
        .event-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .event-title {
            font-size: 1.2rem;
            color: #0b3d91;
            font-weight: 600;
        }
        
        .event-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .event-status.open {
            background-color: #4caf50;
            color: white;
        }
        
        .event-status.closed {
            background-color: #f44336;
            color: white;
        }
        
        .event-date {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .event-description {
            color: #333;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .event-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-edit,
        .btn-delete-event,
        .btn-toggle-status {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-edit {
            background-color: #2196f3;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #1976d2;
        }
        
        .btn-delete-event {
            background-color: #d32f2f;
            color: white;
        }
        
        .btn-delete-event:hover {
            background-color: #b71c1c;
        }
        
        .btn-toggle-status {
            background-color: #ff9800;
            color: white;
        }
        
        .btn-toggle-status:hover {
            background-color: #f57c00;
        }
        
        .no-events {
            text-align: center;
            color: #999;
            padding: 40px;
            font-style: italic;
        }
        
        #editModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .modal-content {
            background-color: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            color: #0b3d91;
            margin: 0;
        }
        
        .close-modal {
            font-size: 2rem;
            color: #999;
            cursor: pointer;
            border: none;
            background: none;
        }
        
        .close-modal:hover {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="navdiv">
            <header>
                <nav>
                    <a href="admin_dashboard.php" class="acard">Admin Dashboard</a>
                    <a href="dashboard.php" class="acard">User Dashboard</a>
                    <a href="index.html" class="acard">Go to Home</a>
                    <a href="logout.php" class="acard" style="background-color: #d32f2f;">Logout</a>
                </nav>
            </header>
        </div>

        <main>
            <h1>Manage Events <img src="icon1.png" /></h1>
            <p style="text-align:center; margin-bottom:30px;">
                Welcome, <strong><?php echo htmlspecialchars($admin_username); ?></strong>
            </p>

            <div id="messageBox" style="margin-bottom: 20px;"></div>

             Add New Event Form 
            <div class="event-form">
                <h2>Add New Event</h2>
                <form id="addEventForm">
                    <div class="form-group">
                        <label for="eventName">Event Name *</label>
                        <input type="text" id="eventName" name="eventName" required />
                    </div>
                    
                    <div class="form-group">
                        <label for="eventDate">Event Date *</label>
                        <input type="date" id="eventDate" name="eventDate" required />
                    </div>
                    
                    <div class="form-group">
                        <label for="eventDescription">Event Description</label>
                        <textarea id="eventDescription" name="eventDescription"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="eventStatus">Event Status *</label>
                        <select id="eventStatus" name="eventStatus" required>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-submit">Add Event</button>
                </form>
            </div>

             Events List 
            <div class="events-list">
                <h2>All Events</h2>
                
                <?php if (count($events) > 0): ?>
                    <div id="eventsContainer">
                        <?php foreach ($events as $event): ?>
                        <div class="event-card" data-event-id="<?php echo $event['id']; ?>">
                            <div class="event-header">
                                <div class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></div>
                                <span class="event-status <?php echo $event['event_status']; ?>">
                                    <?php echo ucfirst($event['event_status']); ?>
                                </span>
                            </div>
                            <div class="event-date">
                                <strong>Date:</strong> <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                            </div>
                            <?php if ($event['event_description']): ?>
                            <div class="event-description">
                                <?php echo nl2br(htmlspecialchars($event['event_description'])); ?>
                            </div>
                            <?php endif; ?>
                            <div class="event-actions">
                                <button class="btn-edit" onclick="openEditModal(<?php echo $event['id']; ?>, '<?php echo addslashes($event['event_name']); ?>', '<?php echo $event['event_date']; ?>', '<?php echo addslashes($event['event_description']); ?>', '<?php echo $event['event_status']; ?>')">
                                    Edit
                                </button>
                                <button class="btn-toggle-status" onclick="toggleEventStatus(<?php echo $event['id']; ?>, '<?php echo $event['event_status']; ?>')">
                                    <?php echo $event['event_status'] === 'open' ? 'Close Event' : 'Open Event'; ?>
                                </button>
                                <button class="btn-delete-event" onclick="deleteEvent(<?php echo $event['id']; ?>, '<?php echo addslashes($event['event_name']); ?>')">
                                    Delete
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-events">No events found. Add your first event above!</div>
                <?php endif; ?>
            </div>
        </main>
    </div>

     Edit Event Modal 
    <div id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Event</h2>
                <button class="close-modal" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editEventForm">
                <input type="hidden" id="editEventId" />
                
                <div class="form-group">
                    <label for="editEventName">Event Name *</label>
                    <input type="text" id="editEventName" name="editEventName" required />
                </div>
                
                <div class="form-group">
                    <label for="editEventDate">Event Date *</label>
                    <input type="date" id="editEventDate" name="editEventDate" required />
                </div>
                
                <div class="form-group">
                    <label for="editEventDescription">Event Description</label>
                    <textarea id="editEventDescription" name="editEventDescription"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="editEventStatus">Event Status *</label>
                    <select id="editEventStatus" name="editEventStatus" required>
                        <option value="open">Open</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                
                <button type="submit" class="btn-submit">Update Event</button>
            </form>
        </div>
    </div>

    <script src="manage_events.js"></script>
</body>
</html>
