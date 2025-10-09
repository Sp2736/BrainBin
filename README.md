# BrainBin - CHARUSAT Student Portal

A comprehensive academic portal for students to access semester-wise study materials, manage events, and interact with the university system.

## 🚀 Features

### For Students
- **Semester-wise Materials**: Access notes, assignments, and resources for semesters 1-6
- **Collective Materials Page**: Browse all study materials organized by semester and subject
- **Google Drive Integration**: Automatic access to Drive folders upon signup
- **Event Management**: View upcoming college events and activities
- **User Dashboard**: Personalized dashboard with quick access to all resources
- **Responsive Design**: Works seamlessly on desktop, tablet, and mobile devices

### For Administrators
- **User Management**: Promote/demote users, activate/deactivate accounts
- **Event Management**: Create, edit, and manage college events
- **Google Drive Management**: Link Drive folders to semesters/subjects with dropdown selections
- **Study Materials Upload**: Upload and organize semester-wise study materials (server-based)
- **Analytics Dashboard**: View system statistics and recent activity logs
- **Audit Logging**: Track all administrative actions for security
- **Automatic Access Granting**: New users automatically get Drive folder permissions

## 🛠️ Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Server**: XAMPP (Apache + MySQL)
- **Cloud Integration**: Google Drive API (via Service Account)
- **Dependencies**: Google API PHP Client (via Composer)

## 📋 Prerequisites

- XAMPP (or similar LAMP/WAMP stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer (for Google API dependencies)
- Google Cloud account (for Drive integration)
- Modern web browser (Chrome, Firefox, Safari, Edge)

## 🔧 Installation

### 1. Clone or Download the Project
\`\`\`bash
git clone <repository-url>
cd brainbin-portal
\`\`\`

### 2. Install Dependencies
\`\`\`bash
composer install
\`\`\`

### 3. Set Up Database

1. Start XAMPP and ensure Apache and MySQL are running
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Execute the SQL scripts in order:
   \`\`\`
   scripts/01_create_database.sql
   scripts/02_seed_data.sql
   scripts/04_create_materials_table.sql
   scripts/05_create_drive_resources_table.sql
   \`\`\`

### 4. Configure Admin User

1. Generate password hash:
   \`\`\`bash
   php hash_password.php
   \`\`\`
2. Copy the generated hash
3. Update `scripts/03_add_admin_user.sql` with the hash
4. Execute the script in phpMyAdmin

### 5. Configure Database Connection

Edit `db_connect.php` with your database credentials:
\`\`\`php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brainbin_data";
\`\`\`

### 6. Set Up Google Drive Integration (Optional but Recommended)

Follow the detailed guide in `GOOGLE_DRIVE_SETUP.md` to:
1. Create Google Cloud project
2. Enable Drive API
3. Create service account
4. Configure credentials
5. Share Drive folders with service account

**Quick Setup:**
- Place service account JSON in `credentials/service-account.json`
- Set `GOOGLE_DRIVE_ENABLED = true` in `google_drive_config.php`
- Share your Drive folders with the service account email

### 7. Start the Application

1. Move the project folder to XAMPP's `htdocs` directory
2. Access the portal at: `http://localhost/brainbin-portal/`

## 👤 Default Credentials

### Admin Account
- **Username**: 24DCS088
- **Email**: 24dcs088@charusat.edu.in
- **Password**: swayam088

### Test User Account
- **Username**: admin
- **Email**: admin@brainbin.com
- **Password**: admin123

## 📁 Project Structure

\`\`\`
brainbin-portal/
├── index.html              # Landing page
├── login.html              # Login page
├── signup.html             # Registration page
├── dashboard.php           # Student dashboard
├── materials.php           # Collective materials page (NEW)
├── admin_dashboard.php     # Admin dashboard
├── admin_manage_drive.php  # Drive resources management (NEW)
├── manage_events.php       # Event management
├── login.php               # Login handler
├── singup.php             # Registration handler (with Drive integration)
├── admin_actions.php       # Admin operations handler
├── admin_drive_actions.php # Drive management handler (NEW)
├── get_drive_materials.php # API for fetching Drive resources (NEW)
├── google_drive_config.php # Google Drive API configuration (NEW)
├── db_connect.php          # Database connection
├── logout.php              # Logout handler
├── composer.json           # PHP dependencies (NEW)
├── GOOGLE_DRIVE_SETUP.md   # Drive setup guide (NEW)
├── credentials/
│   └── service-account.json # Google service account key (gitignored)
├── scripts/
│   ├── 01_create_database.sql
│   ├── 02_seed_data.sql
│   ├── 03_add_admin_user.sql
│   ├── 04_create_materials_table.sql
│   └── 05_create_drive_resources_table.sql (NEW)
├── first.html - sixth.html # Semester pages
├── *.css                   # Stylesheets
└── *.js                    # JavaScript files
\`\`\`

## 🎨 Features Breakdown

### Google Drive Integration (NEW)

**How It Works:**
1. Admin adds Drive folder links via "Manage Drive" page
2. Admin selects semester, subject, material type from dropdowns
3. System stores folder IDs in database
4. When new user signs up, system automatically grants them access to all folders
5. Students see organized materials on "All Study Materials" page

**Benefits:**
- No manual sharing of folders with each student
- Centralized material management
- Automatic access provisioning
- Organized by semester and subject
- No storage limitations (uses Google Drive)

### Collective Materials Page

- Browse all semesters in one place
- Filter by specific semester
- Materials organized by subject
- Direct links to Google Drive folders
- Clean, card-based interface

### Authentication System
- Secure password hashing using PHP's `password_hash()`
- Session management with timeout (30 minutes)
- Remember me functionality (7 days)
- Audit logging for all login attempts
- Automatic Drive access on signup

### User Dashboard
- Quick access to semester materials
- New "All Study Materials" card for collective page
- Upcoming events display
- FAQ section
- Responsive navigation

### Admin Dashboard
- **Statistics Cards**: Total users, admins, events
- **Quick Actions**: Fast access to common tasks including Drive management
- **Drive Management**: Add/remove Drive folder links with dropdown selections
- **User Management**: 
  - Search and filter users
  - Promote/demote users
  - Activate/deactivate accounts
  - Delete users
- **Study Materials Management**: Upload semester-wise materials (server-based option)
- **Analytics**: Recent activity logs and user growth trends
- **Event Management**: Full CRUD operations for events

## 🔒 Security Features

- SQL injection prevention using prepared statements
- XSS protection with `htmlspecialchars()`
- CSRF protection (session-based)
- Password strength requirements
- Audit logging for administrative actions
- Session timeout and management
- Service account credentials secured (gitignored)
- Drive API scopes limited to necessary permissions

## 📱 Responsive Design

The portal is fully responsive and optimized for:
- Desktop (1920px and above)
- Laptop (1024px - 1919px)
- Tablet (768px - 1023px)
- Mobile (320px - 767px)

## 🐛 Troubleshooting

### Google Drive Integration Issues

**Problem**: Users not getting Drive access
- Check `drive_permissions` table for error messages
- Verify service account email is shared with folders
- Check PHP error logs

**Problem**: "Service account file not found"
- Ensure `credentials/service-account.json` exists
- Verify path in `google_drive_config.php`

**Problem**: API rate limits
- Google Drive API has 10,000 requests/day limit (free tier)
- Consider domain-wide sharing for large user bases

See `GOOGLE_DRIVE_SETUP.md` for detailed troubleshooting.

## 🚀 Future Enhancements

- [x] Google Drive integration with automatic access
- [x] Collective materials page
- [x] Admin Drive management interface
- [ ] Real-time notifications system
- [ ] Advanced analytics with charts
- [ ] Email verification for new users
- [ ] Password reset functionality
- [ ] Discussion forums for each semester
- [ ] Assignment submission system
- [ ] Grade management system
- [ ] Batch permission granting for existing users

## 📝 Academic Information

**Project**: Web Development Frameworks (ITUE203)  
**Institution**: CHARUSAT - Faculty of Technology and Engineering  
**Semester**: 3, 2025-26 (Odd)  
**Practical Coverage**: 1-15 (Integrated)

## 🤝 Contributing

This is an academic project. For suggestions or improvements:
1. Contact the admin through the portal
2. Submit feedback via the Contact Us page

## 📄 License

This project is created for academic purposes as part of the Web Development Frameworks course at CHARUSAT University.

## 👨‍💻 Developer

**Swayam Patel**  
Student ID: 24DCS088  
Email: 24dcs088@charusat.edu.in

## 📞 Support

For technical support or queries:
- Use the "Contact Us" page in the portal
- Email: admin@brainbin.com
- Visit the admin dashboard for system status
- Check `GOOGLE_DRIVE_SETUP.md` for Drive integration help

---

**Note**: This portal supports both server-based file uploads and Google Drive integration. The Drive integration approach is recommended for larger deployments with many students and materials.
