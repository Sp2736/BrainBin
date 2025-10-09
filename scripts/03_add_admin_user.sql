-- Add the specific admin user with credentials
-- Username: 24DCS088
-- Email: 24dcs088@charusat.edu.in
-- Password: swayam088

-- Note: Run hash_password.php first to generate the correct hash
-- Then replace the hash below with the generated one

-- Temporary hash (you must run hash_password.php and update this)
INSERT INTO users (username, email, phone, dob, password, role, status) 
VALUES (
    '24DCS088',
    '24dcs088@charusat.edu.in',
    '9876543210',
    '2005-01-01',
    '$2y$10$YourHashWillGoHere',
    'admin',
    'active'
) ON DUPLICATE KEY UPDATE 
    email = '24dcs088@charusat.edu.in',
    role = 'admin',
    status = 'active';

-- Instructions:
-- 1. Run: php hash_password.php
-- 2. Copy the generated hash
-- 3. Replace '$2y$10$YourHashWillGoHere' with the actual hash
-- 4. Execute this SQL script
