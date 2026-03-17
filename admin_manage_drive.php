<?php
session_start();
require_once "db_connect.php";
require_once "google_drive_config.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Update session time
$_SESSION['login_time'] = time();

// Fetch admin info
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username, $admin_email);
$stmt->fetch();
$stmt->close();

// Fetch all Drive resources
$resources_stmt = $conn->query("
    SELECT dr.*, u.username as created_by_name
    FROM drive_resources dr
    LEFT JOIN users u ON dr.created_by = u.id
    ORDER BY dr.semester, dr.subject, dr.material_type
");
$resources = [];
while ($row = $resources_stmt->fetch_assoc()) {
    $resources[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="admin_style.css" />
    <link rel="icon" href="icon2.png" />
    <title>Manage Drive Resources | BrainBin</title>
    <style>
        .drive-config-status {
            background: <?php echo GOOGLE_DRIVE_ENABLED ? '#e8f5e9' : '#fff3e0'; ?>;
            color: <?php echo GOOGLE_DRIVE_ENABLED ? '#388e3c' : '#f57c00'; ?>;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .add-resource-form {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
            color: #0b3d91;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #0b3d91;
        }
        
        .resources-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .resources-table th {
            background: linear-gradient(135deg, #0b3d91 0%, #1e5bb8 100%);
            color: white;
            padding: 15px;
            text-align: left;
        }
        
        .resources-table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .resources-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn-add {
            background: linear-gradient(135deg, #0b3d91 0%, #1e5bb8 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .drive-link {
            color: #0b3d91;
            text-decoration: none;
            font-weight: 600;
        }
        
        .drive-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="navdiv">
            <header>
                <nav>
                    <a href="admin_dashboard.php" class="acard">Admin Dashboard</a>
                    <a href="dashboard.php" class="acard">Student View</a>
                    <a href="materials.php" class="acard">View Materials</a>
                    <a href="index.html" class="acard">Home</a>
                    <a href="logout.php" class="acard logout-btn">Logout</a>
                </nav>
            </header>
        </div>

        <main>
            <div class="welcome-header">
                <h1>Manage Google Drive Resources <img src="icon1.png" alt="BrainBin Logo" /></h1>
            </div>

            <div class="drive-config-status">
                <?php if (GOOGLE_DRIVE_ENABLED): ?>
                    ✅ Google Drive API is configured and active
                <?php else: ?>
                    ⚠️ Google Drive API is not configured. See google_drive_config.php for setup instructions.
                <?php endif; ?>
            </div>

            <div class="add-resource-form">
                <h2>Add New Drive Resource</h2>
                <form id="addDriveResourceForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="semester">Semester *</label>
                            <select id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1">1st Semester</option>
                                <option value="2">2nd Semester</option>
                                <option value="3">3rd Semester</option>
                                <option value="4">4th Semester</option>
                                <option value="5">5th Semester</option>
                                <option value="6">6th Semester</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <input type="text" id="subject" name="subject" placeholder="e.g., Engineering Mathematics - 1" required />
                        </div>
                        <div class="form-group">
                            <label for="materialType">Material Type *</label>
                            <input type="text" id="materialType" name="materialType" placeholder="e.g., Reading Notes, Assignments" required />
                        </div>
                        <div class="form-group">
                            <label for="folderName">Folder Name *</label>
                            <input type="text" id="folderName" name="folderName" placeholder="Display name for the folder" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="driveFolderId">Google Drive Folder ID *</label>
                        <input type="text" id="driveFolderId" name="driveFolderId" placeholder="Extract from Drive URL: /folders/[THIS_PART]" required />
                        <small style="color: #666; margin-top: 5px;">Example: From https://drive.google.com/drive/folders/1ABC123xyz, use "1ABC123xyz"</small>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3" placeholder="Optional description of the subject or materials"></textarea>
                    </div>
                    <button type="submit" class="btn-add">Add Drive Resource</button>
                </form>
            </div>

            <div id="messageBox" style="margin-bottom: 15px;"></div>

            <h2>Existing Drive Resources</h2>
            <table class="resources-table">
                <thead>
                    <tr>
                        <th>Semester</th>
                        <th>Subject</th>
                        <th>Material Type</th>
                        <th>Folder Name</th>
                        <th>Drive Link</th>
                        <th>Created By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($resources) > 0): ?>
                        <?php foreach ($resources as $resource): ?>
                        <tr>
                            <td><?php echo $resource['semester']; ?></td>
                            <td><?php echo htmlspecialchars($resource['subject']); ?></td>
                            <td><?php echo htmlspecialchars($resource['material_type']); ?></td>
                            <td><?php echo htmlspecialchars($resource['folder_name']); ?></td>
                            <td>
                                <a href="https://drive.google.com/drive/folders/<?php echo htmlspecialchars($resource['drive_folder_id']); ?>" 
                                   target="_blank" class="drive-link">
                                    View Folder
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($resource['created_by_name'] ?? 'Unknown'); ?></td>
                            <td>
                                <button class="btn-delete" onclick="deleteResource(<?php echo $resource['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #999;">No Drive resources added yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>
    </div>

    <script src="admin_drive.js"></script>
</body>
</html>
