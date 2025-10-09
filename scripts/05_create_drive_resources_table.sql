-- Create Google Drive resources table
USE brainbin_data;

CREATE TABLE IF NOT EXISTS drive_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    material_type VARCHAR(100) NOT NULL,
    drive_folder_id VARCHAR(255) NOT NULL,
    folder_name VARCHAR(255) NOT NULL,
    description TEXT,
    resource_type ENUM('file', 'folder') DEFAULT 'folder',
    access_scope ENUM('domain', 'individual') DEFAULT 'individual',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_semester (semester),
    INDEX idx_subject (subject),
    INDEX idx_material_type (material_type)
);

-- Create table to track Drive permissions granted to users
CREATE TABLE IF NOT EXISTS drive_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    drive_resource_id INT NOT NULL,
    permission_id VARCHAR(255),
    granted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'granted', 'failed') DEFAULT 'pending',
    error_message TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (drive_resource_id) REFERENCES drive_resources(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_resource (user_id, drive_resource_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
);
