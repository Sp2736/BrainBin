-- Create study materials table
USE brainbin_data;

CREATE TABLE IF NOT EXISTS study_materials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester INT NOT NULL,
    subject VARCHAR(200) NOT NULL,
    material_type ENUM('notes', 'assignment', 'practical', 'reference') NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    description TEXT,
    uploaded_by INT NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    download_count INT DEFAULT 0,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_semester (semester),
    INDEX idx_subject (subject),
    INDEX idx_material_type (material_type)
);
