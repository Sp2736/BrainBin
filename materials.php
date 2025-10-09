<?php
session_start();
require_once "db_connect.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Update session time
$_SESSION['login_time'] = time();

// Fetch user info
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $user_email);
$stmt->fetch();
$stmt->close();

// Get selected semester filter
$selected_semester = isset($_GET['semester']) ? intval($_GET['semester']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="dashstyle.css" />
    <link rel="icon" href="icon2.png" />
    <title>Study Materials | BrainBin</title>
    <style>
        .materials-page {
            max-width: 1400px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            font-size: 2.8rem;
            color: #0b3d91;
            margin-bottom: 10px;
        }
        
        .page-header p {
            font-size: 1.1rem;
            color: #666;
        }
        
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-section label {
            font-weight: 600;
            color: #0b3d91;
        }
        
        .filter-section select {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        
        .filter-section select:focus {
            outline: none;
            border-color: #0b3d91;
        }
        
        .semester-section {
            margin-bottom: 50px;
        }
        
        .semester-header {
            background: linear-gradient(135deg, #0b3d91 0%, #1e5bb8 100%);
            color: white;
            padding: 20px 30px;
            border-radius: 12px 12px 0 0;
            margin-bottom: 0;
        }
        
        .semester-header h2 {
            margin: 0;
            font-size: 1.8rem;
        }
        
        .subjects-container {
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .subject-row {
            border-bottom: 1px solid #f0f0f0;
            padding: 25px 30px;
            transition: background-color 0.3s;
        }
        
        .subject-row:last-child {
            border-bottom: none;
        }
        
        .subject-row:hover {
            background-color: #f8f9fa;
        }
        
        .subject-name {
            font-size: 1.4rem;
            font-weight: 700;
            color: #0b3d91;
            margin-bottom: 10px;
        }
        
        .subject-description {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .materials-links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .material-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #0b3d91 0%, #1e5bb8 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .material-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #0a3275 0%, #1a4d9a 100%);
        }
        
        .material-icon {
            font-size: 1.2rem;
        }
        
        .no-materials {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .access-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .access-granted {
            background: #e8f5e9;
            color: #388e3c;
        }
        
        .access-pending {
            background: #fff3e0;
            color: #f57c00;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="navdiv">
            <header>
                <nav>
                    <a href="dashboard.php" class="acard">Dashboard</a>
                    <a href="events.php" class="acard">Events</a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin_dashboard.php" class="acard">Admin Panel</a>
                    <?php endif; ?>
                    <a href="index.html" class="acard">Home</a>
                    <a href="logout.php" class="acard logout-btn">Logout</a>
                </nav>
            </header>
        </div>

        <main>
            <div class="materials-page">
                <div class="page-header">
                    <h1>Study Materials</h1>
                    <p>Access all your course materials organized by semester and subject</p>
                </div>

                <div class="filter-section">
                    <label for="semesterFilter">Filter by Semester:</label>
                    <select id="semesterFilter" onchange="filterBySemester(this.value)">
                        <option value="0">All Semesters</option>
                        <option value="1" <?php echo $selected_semester == 1 ? 'selected' : ''; ?>>1st Semester</option>
                        <option value="2" <?php echo $selected_semester == 2 ? 'selected' : ''; ?>>2nd Semester</option>
                        <option value="3" <?php echo $selected_semester == 3 ? 'selected' : ''; ?>>3rd Semester</option>
                        <option value="4" <?php echo $selected_semester == 4 ? 'selected' : ''; ?>>4th Semester</option>
                        <option value="5" <?php echo $selected_semester == 5 ? 'selected' : ''; ?>>5th Semester</option>
                        <option value="6" <?php echo $selected_semester == 6 ? 'selected' : ''; ?>>6th Semester</option>
                    </select>
                </div>

                <div id="materialsContent">
                    <p style="text-align: center; color: #999;">Loading materials...</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        function filterBySemester(semester) {
            window.location.href = `materials.php${semester > 0 ? '?semester=' + semester : ''}`;
        }
        
        async function loadMaterials() {
            try {
                const urlParams = new URLSearchParams(window.location.search);
                const semester = urlParams.get('semester') || '0';
                
                const response = await fetch(`get_drive_materials.php?semester=${semester}`);
                const data = await response.json();
                
                const content = document.getElementById('materialsContent');
                
                if (data.status === 'success' && Object.keys(data.materials).length > 0) {
                    let html = '';
                    
                    for (const [sem, subjects] of Object.entries(data.materials)) {
                        html += `
                            <div class="semester-section">
                                <div class="semester-header">
                                    <h2>Semester ${sem}</h2>
                                </div>
                                <div class="subjects-container">
                        `;
                        
                        for (const [subjectName, materials] of Object.entries(subjects)) {
                            const firstMaterial = materials[0];
                            html += `
                                <div class="subject-row">
                                    <div class="subject-name">${subjectName}</div>
                                    ${firstMaterial.description ? `<div class="subject-description">${firstMaterial.description}</div>` : ''}
                                    <div class="materials-links">
                            `;
                            
                            materials.forEach(material => {
                                const driveUrl = `https://drive.google.com/drive/folders/${material.drive_folder_id}`;
                                html += `
                                    <a href="${driveUrl}" target="_blank" class="material-link">
                                        <span class="material-icon">📁</span>
                                        <span>${material.folder_name}</span>
                                    </a>
                                `;
                            });
                            
                            html += `
                                    </div>
                                </div>
                            `;
                        }
                        
                        html += `
                                </div>
                            </div>
                        `;
                    }
                    
                    content.innerHTML = html;
                } else {
                    content.innerHTML = `
                        <div class="no-materials">
                            <h3>No materials available</h3>
                            <p>Materials will be added soon</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('[v0] Error loading materials:', error);
                document.getElementById('materialsContent').innerHTML = `
                    <div class="no-materials">
                        <h3>Error loading materials</h3>
                        <p>Please try again later</p>
                    </div>
                `;
            }
        }
        
        loadMaterials();
    </script>
</body>
</html>
