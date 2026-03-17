<?php
define('GOOGLE_DRIVE_ENABLED', true); // Set to true after configuration
define('GOOGLE_SERVICE_ACCOUNT_FILE', __DIR__ . '/credentials/service-account.json');
define('GOOGLE_DRIVE_SCOPES', ['https://www.googleapis.com/auth/drive']);

// Function to get Google Drive client
function getGoogleDriveClient() {
    if (!GOOGLE_DRIVE_ENABLED) {
        return null;
    }
    
    if (!file_exists(GOOGLE_SERVICE_ACCOUNT_FILE)) {
        error_log("Google Drive service account file not found");
        return null;
    }
    
    try {
        require_once __DIR__ . '/vendor/autoload.php';
        
        $client = new Google_Client();
        $client->setAuthConfig(GOOGLE_SERVICE_ACCOUNT_FILE);
        $client->addScope(GOOGLE_DRIVE_SCOPES);
        $client->setApplicationName('BrainBin Study Portal');
        
        return $client;
    } catch (Exception $e) {
        error_log("Error creating Google Drive client: " . $e->getMessage());
        return null;
    }
}

// Function to grant folder access to user email
function grantDriveFolderAccess($folderId, $userEmail) {
    $client = getGoogleDriveClient();
    if (!$client) {
        return ['success' => false, 'error' => 'Google Drive not configured'];
    }
    
    try {
        $driveService = new Google_Service_Drive($client);
        
        $permission = new Google_Service_Drive_Permission();
        $permission->setType('user');
        $permission->setRole('reader');
        $permission->setEmailAddress($userEmail);
        
        $result = $driveService->permissions->create(
            $folderId,
            $permission,
            ['sendNotificationEmail' => false]
        );
        
        return [
            'success' => true,
            'permission_id' => $result->getId()
        ];
    } catch (Exception $e) {
        error_log("Error granting Drive access: " . $e->getMessage());
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Function to revoke folder access
function revokeDriveFolderAccess($folderId, $permissionId) {
    $client = getGoogleDriveClient();
    if (!$client) {
        return false;
    }
    
    try {
        $driveService = new Google_Service_Drive($client);
        $driveService->permissions->delete($folderId, $permissionId);
        return true;
    } catch (Exception $e) {
        error_log("Error revoking Drive access: " . $e->getMessage());
        return false;
    }
}

// Function to grant access to all folders for a user
function grantAllFoldersAccess($userId, $userEmail, $conn) {
    $stmt = $conn->prepare("SELECT id, drive_folder_id FROM drive_resources WHERE access_scope = 'individual'");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $granted = 0;
    $failed = 0;
    
    while ($row = $result->fetch_assoc()) {
        $accessResult = grantDriveFolderAccess($row['drive_folder_id'], $userEmail);
        
        $status = $accessResult['success'] ? 'granted' : 'failed';
        $permissionId = $accessResult['permission_id'] ?? null;
        $errorMsg = $accessResult['error'] ?? null;
        
        $insertStmt = $conn->prepare("
            INSERT INTO drive_permissions (user_id, drive_resource_id, permission_id, status, error_message)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                permission_id = VALUES(permission_id),
                status = VALUES(status),
                error_message = VALUES(error_message),
                granted_at = CURRENT_TIMESTAMP
        ");
        $insertStmt->bind_param("iisss", $userId, $row['id'], $permissionId, $status, $errorMsg);
        $insertStmt->execute();
        $insertStmt->close();
        
        if ($accessResult['success']) {
            $granted++;
        } else {
            $failed++;
        }
    }
    
    $stmt->close();
    
    return [
        'granted' => $granted,
        'failed' => $failed
    ];
}
?>
