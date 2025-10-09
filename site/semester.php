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

// Get semester from URL
$semester = intval($_GET['sem'] ?? 0);

if ($semester < 1 || $semester > 6) {
    header("Location: dashboard.php");
    exit();
}

// Fetch user info
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="dashstyle.css" />
    <link rel="icon" href="icon2.png" />
    <title>Semester <?php echo $semester; ?> | BrainBin</title>
    <style>
        .materials-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        
        .materials-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .materials-header h2 {
            font-size: 2.5rem;
            color: #0b3d91;
            margin-bottom: 10px;
        }
        
        .materials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .material-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .material-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }
        
        .material-type {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .material-type.notes { background: #e3f2fd; color: #1976d2; }
        .material-type.assignment { background: #fff3e0; color: #f57c00; }
        .material-type.practical { background: #e8f5e9; color: #388e3c; }
        .material-type.reference { background: #f3e5f5; color: #7b1fa2; }
        
        .material-subject {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0b3d91;
            margin: 10px 0;
        }
        
        .material-filename {
            font-size: 0.9rem;
            color: #666;
            margin: 8px 0;
            word-break: break-word;
        }
        
        .material-description {
            font-size: 0.9rem;
            color: #555;
            margin: 10px 0;
            line-height: 1.5;
        }
        
        .material-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 0.85rem;
            color: #888;
        }
        
        .download-btn {
            background: linear-gradient(135deg, #0b3d91 0%, #1e5bb8 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 15px;
            width: 100%;
        }
        
        .download-btn:hover {
            background: linear-gradient(135deg, #0a3275 0%, #1a4d9a 100%);
            transform: scale(1.02);
        }
        
        .no-materials {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .no-materials h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
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
            <div class="materials-container">
                <div class="materials-header">
                    <h2>Semester <?php echo $semester; ?> Study Materials</h2>
                    <p>Download notes, assignments, practicals, and reference materials</p>
                </div>

                <div id="materialsGrid" class="materials-grid">
                    <p style="text-align: center; color: #999;">Loading materials...</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        const semester = <?php echo $semester; ?>;
        
        async function loadMaterials() {
            try {
                const response = await fetch(`get_materials.php?semester=${semester}`);
                const data = await response.json();
                
                const grid = document.getElementById('materialsGrid');
                
                if (data.status === 'success' && data.materials.length > 0) {
                    grid.innerHTML = data.materials.map(material => `
                        <div class="material-card">
                            <span class="material-type ${material.material_type.toLowerCase()}">${material.material_type}</span>
                            <h3 class="material-subject">${material.subject}</h3>
                            <p class="material-filename">📄 ${material.file_name}</p>
                            ${material.description ? `<p class="material-description">${material.description}</p>` : ''}
                            <div class="material-meta">
                                <span>📥 ${material.download_count} downloads</span>
                                <span>${material.file_size}</span>
                            </div>
                            <div class="material-meta">
                                <span>Uploaded: ${material.upload_date}</span>
                            </div>
                            <button class="download-btn" onclick="downloadMaterial(${material.id}, '${material.file_name}')">
                                Download Material
                            </button>
                        </div>
                    `).join('');
                } else {
                    grid.innerHTML = `
                        <div class="no-materials">
                            <h3>No materials available yet</h3>
                            <p>Check back later for study materials</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading materials:', error);
                document.getElementById('materialsGrid').innerHTML = `
                    <div class="no-materials">
                        <h3>Error loading materials</h3>
                        <p>Please try again later</p>
                    </div>
                `;
            }
        }
        
        function downloadMaterial(id, filename) {
            window.location.href = `download_material.php?id=${id}`;
        }
        
        // Load materials on page load
        loadMaterials();
    </script>
</body>
</html>
