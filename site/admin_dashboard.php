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
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$stmt->bind_result($admin_username, $admin_email);
$stmt->fetch();
$stmt->close();

// Get statistics
$total_users_stmt = $conn->query("SELECT COUNT(*) as count FROM users");
$total_users = $total_users_stmt->fetch_assoc()['count'];

$total_admins_stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
$total_admins = $total_admins_stmt->fetch_assoc()['count'];

$total_events_stmt = $conn->query("SELECT COUNT(*) as count FROM events");
$total_events = $total_events_stmt->fetch_assoc()['count'];

$open_events_stmt = $conn->query("SELECT COUNT(*) as count FROM events WHERE event_status = 'open'");
$open_events = $open_events_stmt->fetch_assoc()['count'];

$recent_logs_stmt = $conn->query("SELECT al.*, u.username FROM audit_log al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 10");
$recent_logs = [];
while ($row = $recent_logs_stmt->fetch_assoc()) {
    $recent_logs[] = $row;
}

// Fetch all users
$users_stmt = $conn->query("SELECT id, username, email, phone, dob, role, status, created_at, last_login FROM users ORDER BY created_at DESC");
$users = [];
while ($row = $users_stmt->fetch_assoc()) {
    $users[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="admin_style.css" />
    <link rel="icon" href="icon2.png" />
    <title>Admin Dashboard | BrainBin</title>
</head>
<body>
    <div class="container">
        <div id="navdiv">
            <header>
                <nav>
                    <a href="dashboard.php" class="acard">Student View</a>
                    <a href="manage_events.php" class="acard">Manage Events</a>
                    <a href="admin_manage_drive.php" class="acard">Manage Drive</a>
                    <a href="#materials" class="acard" onclick="scrollToSection('materials')">Study Materials</a>
                    <a href="#analytics" class="acard" onclick="scrollToSection('analytics')">Analytics</a>
                    <a href="index.html" class="acard">Home</a>
                    <a href="logout.php" class="acard logout-btn">Logout</a>
                </nav>
            </header>
        </div>

        <main>
            <div class="welcome-header">
                <h1>Admin Dashboard <img src="icon1.png" alt="BrainBin Logo" /></h1>
                <p class="admin-welcome">
                    Welcome, <strong><?php echo htmlspecialchars($admin_username); ?></strong>
                    <span class="admin-email"><?php echo htmlspecialchars($admin_email); ?></span>
                </p>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-container">
                <div class="stat-card stat-users">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <h3>Total Users</h3>
                        <p class="stat-number"><?php echo $total_users; ?></p>
                        <span class="stat-label">Registered Students</span>
                    </div>
                </div>
                <div class="stat-card stat-admins">
                    <div class="stat-icon">🛡️</div>
                    <div class="stat-content">
                        <h3>Total Admins</h3>
                        <p class="stat-number"><?php echo $total_admins; ?></p>
                        <span class="stat-label">System Administrators</span>
                    </div>
                </div>
                <div class="stat-card stat-events">
                    <div class="stat-icon">📅</div>
                    <div class="stat-content">
                        <h3>Total Events</h3>
                        <p class="stat-number"><?php echo $total_events; ?></p>
                        <span class="stat-label">All Events</span>
                    </div>
                </div>
                <div class="stat-card stat-open">
                    <div class="stat-icon">✅</div>
                    <div class="stat-content">
                        <h3>Open Events</h3>
                        <p class="stat-number"><?php echo $open_events; ?></p>
                        <span class="stat-label">Active Events</span>
                    </div>
                </div>
            </div>

            <!-- Added Quick Actions Section -->
            <section class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-grid">
                    <button class="action-btn" onclick="window.location.href='manage_events.php'">
                        <span class="action-icon">📅</span>
                        <span class="action-text">Manage Events</span>
                    </button>
                    <button class="action-btn" onclick="window.location.href='admin_manage_drive.php'">
                        <span class="action-icon">☁️</span>
                        <span class="action-text">Manage Drive</span>
                    </button>
                    <button class="action-btn" onclick="scrollToSection('materials')">
                        <span class="action-icon">📚</span>
                        <span class="action-text">Upload Materials</span>
                    </button>
                    <button class="action-btn" onclick="scrollToSection('users')">
                        <span class="action-icon">👥</span>
                        <span class="action-text">View Users</span>
                    </button>
                    <button class="action-btn" onclick="exportData()">
                        <span class="action-icon">📊</span>
                        <span class="action-text">Export Data</span>
                    </button>
                </div>
            </section>

            <!-- Added Study Materials Management Section -->
            <section id="materials" class="materials-section">
                <h2>Study Materials Management</h2>
                <div class="materials-upload">
                    <form id="uploadMaterialForm" class="upload-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="semester">Semester</label>
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
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" placeholder="e.g., Web Development" required />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="materialType">Material Type</label>
                                <select id="materialType" name="materialType" required>
                                    <option value="">Select Type</option>
                                    <option value="notes">Notes</option>
                                    <option value="assignment">Assignment</option>
                                    <option value="practical">Practical</option>
                                    <option value="reference">Reference Material</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="materialFile">Upload File</label>
                                <input type="file" id="materialFile" name="materialFile" accept=".pdf,.doc,.docx,.ppt,.pptx" required />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3" placeholder="Brief description of the material"></textarea>
                        </div>
                        <button type="submit" class="btn-upload">Upload Material</button>
                    </form>
                </div>
            </section>

            <!-- Added Analytics Section -->
            <section id="analytics" class="analytics-section">
                <h2>System Analytics</h2>
                <div class="analytics-grid">
                    <div class="analytics-card">
                        <h3>Recent Activity</h3>
                        <div class="activity-list">
                            <?php if (count($recent_logs) > 0): ?>
                                <?php foreach ($recent_logs as $log): ?>
                                <div class="activity-item">
                                    <span class="activity-user"><?php echo htmlspecialchars($log['username'] ?? 'Unknown'); ?></span>
                                    <span class="activity-action"><?php echo htmlspecialchars($log['action']); ?></span>
                                    <span class="activity-time"><?php echo date('M j, g:i A', strtotime($log['created_at'])); ?></span>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-data">No recent activity</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="analytics-card">
                        <h3>User Growth</h3>
                        <div class="chart-placeholder">
                            <p>📈 User registration trend</p>
                            <p class="chart-note">Chart visualization coming soon</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- User Management Section -->
            <section id="users" class="user-management">
                <h2>User Management</h2>
                <div id="messageBox" style="margin-bottom: 15px;"></div>
                
                <!-- Added search and filter -->
                <div class="table-controls">
                    <input type="text" id="searchUsers" placeholder="Search users..." class="search-input" onkeyup="filterUsers()" />
                    <select id="filterRole" class="filter-select" onchange="filterUsers()">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <select id="filterStatus" class="filter-select" onchange="filterUsers()">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="table-container">
                    <table class="user-table" id="userTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Date of Birth</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr data-user-id="<?php echo $user['id']; ?>" data-role="<?php echo $user['role']; ?>" data-status="<?php echo $user['status']; ?>">
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo htmlspecialchars($user['dob']); ?></td>
                                <td>
                                    <span class="role-badge <?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $user['status']; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $user['last_login'] ? date('M j, g:i A', strtotime($user['last_login'])) : 'Never'; ?></td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <?php if ($user['id'] != $admin_id): ?>
                                        <?php if ($user['role'] === 'user'): ?>
                                            <button class="btn-promote" onclick="changeRole(<?php echo $user['id']; ?>, 'admin')">
                                                Promote
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-demote" onclick="changeRole(<?php echo $user['id']; ?>, 'user')">
                                                Demote
                                            </button>
                                        <?php endif; ?>
                                        <?php if ($user['status'] === 'active'): ?>
                                            <button class="btn-deactivate" onclick="toggleStatus(<?php echo $user['id']; ?>, 'inactive')">
                                                Deactivate
                                            </button>
                                        <?php else: ?>
                                            <button class="btn-activate" onclick="toggleStatus(<?php echo $user['id']; ?>, 'active')">
                                                Activate
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn-delete" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                            Delete
                                        </button>
                                    <?php else: ?>
                                        <span style="color: #999; font-style: italic;">You</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script src="admin_dashboard.js"></script>
</body>
</html>
