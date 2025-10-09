-- Seed initial admin user
-- Password: admin123 (hashed)
INSERT INTO users (username, email, phone, dob, password, role, status) 
VALUES (
    'admin',
    'admin@brainbin.com',
    '9999999999',
    '2000-01-01',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin',
    'active'
) ON DUPLICATE KEY UPDATE role='admin';

-- Seed some sample events
INSERT INTO events (event_name, event_date, event_description, event_status, created_by) 
VALUES 
    ('Tech Fest 2025', '2025-03-15', 'Annual technical festival with coding competitions, hackathons, and tech talks.', 'open', 1),
    ('Cultural Night', '2025-04-20', 'Evening of music, dance, and cultural performances by students.', 'open', 1),
    ('Sports Day', '2025-05-10', 'Inter-department sports competition including cricket, football, and athletics.', 'open', 1),
    ('Workshop: Web Development', '2025-02-28', 'Hands-on workshop covering HTML, CSS, JavaScript, and PHP fundamentals.', 'open', 1),
    ('Alumni Meet 2025', '2025-06-15', 'Annual gathering of alumni to network and share experiences.', 'open', 1)
ON DUPLICATE KEY UPDATE event_name=event_name;
