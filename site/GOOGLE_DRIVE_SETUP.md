# Google Drive API Integration Setup Guide

This guide will walk you through setting up Google Drive API integration for BrainBin to automatically grant students access to study material folders.

## 📋 Prerequisites

- Google Account with access to Google Cloud Console
- Admin access to your Google Drive folders
- Composer installed on your system
- BrainBin portal already set up and running

## 🚀 Step-by-Step Setup

### Step 1: Install Google API PHP Client

Run this command in your project root directory:

\`\`\`bash
composer install
\`\`\`

This will install the Google API PHP Client library required for Drive integration.

### Step 2: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click **"Select a project"** → **"New Project"**
3. Enter project name: `BrainBin Portal`
4. Click **"Create"**

### Step 3: Enable Google Drive API

1. In your project dashboard, go to **"APIs & Services"** → **"Library"**
2. Search for **"Google Drive API"**
3. Click on it and press **"Enable"**

### Step 4: Create Service Account

1. Go to **"APIs & Services"** → **"Credentials"**
2. Click **"Create Credentials"** → **"Service Account"**
3. Fill in the details:
   - **Service account name**: `brainbin-drive-service`
   - **Service account ID**: (auto-generated)
   - **Description**: `Service account for BrainBin Drive access`
4. Click **"Create and Continue"**
5. Grant role: **"Editor"** (or create custom role with Drive permissions)
6. Click **"Continue"** → **"Done"**

### Step 5: Generate Service Account Key

1. In **"Credentials"**, find your service account
2. Click on the service account email
3. Go to **"Keys"** tab
4. Click **"Add Key"** → **"Create new key"**
5. Select **"JSON"** format
6. Click **"Create"**
7. The JSON key file will download automatically

### Step 6: Configure BrainBin

1. Create a `credentials` folder in your project root:
   \`\`\`bash
   mkdir credentials
   \`\`\`

2. Move the downloaded JSON file to `credentials/service-account.json`

3. Update `google_drive_config.php`:
   \`\`\`php
   define('GOOGLE_DRIVE_ENABLED', true); // Change to true
   \`\`\`

4. Set proper permissions:
   \`\`\`bash
   chmod 600 credentials/service-account.json
   \`\`\`

### Step 7: Share Drive Folders with Service Account

**IMPORTANT**: For the service account to grant permissions, it must have access to your folders.

1. Open your JSON key file and copy the `client_email` value
   - It looks like: `brainbin-drive-service@project-id.iam.gserviceaccount.com`

2. For each Google Drive folder containing study materials:
   - Right-click the folder → **"Share"**
   - Paste the service account email
   - Set permission to **"Editor"**
   - Uncheck **"Notify people"**
   - Click **"Share"**

### Step 8: Add Drive Resources to Database

1. Run the database migration:
   \`\`\`sql
   -- Execute in phpMyAdmin
   SOURCE scripts/05_create_drive_resources_table.sql;
   \`\`\`

2. Log in to BrainBin as admin

3. Go to **"Manage Drive"** from the admin dashboard

4. Add your Drive folders:
   - **Semester**: Select 1-6
   - **Subject**: e.g., "Engineering Mathematics - 1"
   - **Material Type**: e.g., "Reading Notes", "Assignments"
   - **Folder Name**: Display name for students
   - **Drive Folder ID**: Extract from Drive URL
   - **Description**: Optional subject description

### Step 9: Extract Drive Folder ID

From a Google Drive folder URL:
\`\`\`
https://drive.google.com/drive/folders/1ABC123xyz456DEF789
\`\`\`

The Folder ID is: `1ABC123xyz456DEF789`

Copy only this part when adding resources.

### Step 10: Test the Integration

1. Create a new test user account
2. Check the PHP error logs for Drive API calls
3. Verify the user receives access to folders
4. Check `drive_permissions` table in database

## 🔍 Verification

### Check if API is Working

Create a test script `test_drive.php`:

\`\`\`php
<?php
require_once 'google_drive_config.php';

$client = getGoogleDriveClient();

if ($client) {
    echo "✅ Google Drive API is configured correctly!\n";
    echo "Service account email: " . $client->getClientId() . "\n";
} else {
    echo "❌ Google Drive API configuration failed.\n";
    echo "Check your service-account.json file.\n";
}
?>
\`\`\`

Run: `php test_drive.php`

### Check Database Permissions

\`\`\`sql
SELECT u.username, u.email, dr.subject, dr.folder_name, dp.status
FROM drive_permissions dp
JOIN users u ON dp.user_id = u.id
JOIN drive_resources dr ON dp.drive_resource_id = dr.id
ORDER BY dp.granted_at DESC
LIMIT 10;
\`\`\`

## 🐛 Troubleshooting

### Error: "Service account file not found"
- Verify `credentials/service-account.json` exists
- Check file path in `google_drive_config.php`

### Error: "Permission denied"
- Ensure service account email is shared with all Drive folders
- Check service account has "Editor" role in Cloud Console

### Error: "API not enabled"
- Go to Cloud Console → APIs & Services → Library
- Search "Google Drive API" and enable it

### Users not getting access
- Check `drive_permissions` table for error messages
- Verify user email is correct in database
- Check PHP error logs: `/var/log/apache2/error.log` or XAMPP logs

### Rate limiting errors
- Google Drive API has quotas (10,000 requests/day for free tier)
- Implement caching or batch operations if needed

## 📊 Monitoring

### Check Permission Status

Admin dashboard shows:
- Total Drive resources added
- Permissions granted/failed per user
- Recent Drive access logs

### View Logs

\`\`\`sql
SELECT * FROM audit_log 
WHERE action LIKE '%drive%' 
ORDER BY created_at DESC 
LIMIT 20;
\`\`\`

## 🔒 Security Best Practices

1. **Never commit credentials to Git**:
   \`\`\`bash
   echo "credentials/" >> .gitignore
   \`\`\`

2. **Restrict service account permissions**:
   - Only grant necessary Drive scopes
   - Use custom IAM roles if possible

3. **Monitor API usage**:
   - Check Cloud Console → APIs & Services → Dashboard
   - Set up billing alerts

4. **Rotate keys periodically**:
   - Generate new service account keys every 90 days
   - Delete old keys after rotation

## 📈 Scaling Considerations

### For Large Student Base (500+ users)

1. **Batch permission granting**:
   - Grant permissions in chunks of 50 users
   - Add delays between batches to avoid rate limits

2. **Use domain-wide delegation**:
   - If all students have @yourdomain.edu emails
   - Share folders with entire domain instead of individuals

3. **Implement queue system**:
   - Use background jobs for permission granting
   - Process new signups asynchronously

## 🎯 Alternative: Domain-Wide Sharing

If all students have institutional emails (e.g., @charusat.edu.in):

1. Share folders with entire domain:
   - Right-click folder → Share
   - Enter: `charusat.edu.in`
   - Set to "Viewer"

2. Update `google_drive_config.php`:
   \`\`\`php
   define('USE_DOMAIN_SHARING', true);
   define('INSTITUTION_DOMAIN', 'charusat.edu.in');
   \`\`\`

3. Skip individual permission granting (faster and simpler)

## 📞 Support

For issues with Google Drive API:
- [Google Drive API Documentation](https://developers.google.com/drive/api/guides/about-sdk)
- [Stack Overflow - google-drive-api tag](https://stackoverflow.com/questions/tagged/google-drive-api)
- [Google Cloud Support](https://cloud.google.com/support)

---

**Setup Complete!** Students will now automatically receive access to Drive folders when they sign up.
