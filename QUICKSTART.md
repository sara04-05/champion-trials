# City Care - Quick Start Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `city_care`
3. Import `database/schema.sql` into the database
   - OR run `setup.php` in your browser: http://localhost/GitHub/champion-trials/setup.php

### Step 2: Configuration
1. Open `config/config.php`
2. Add your Google Maps API Key:
   ```php
   define('GOOGLE_MAPS_API_KEY', 'YOUR_API_KEY_HERE');
   ```
   - Get your API key from: https://console.cloud.google.com/google/maps-apis

3. (Optional) Update database credentials in `config/database.php` if needed

### Step 3: File Permissions
Ensure the `uploads/` directory is writable:
- The application will create this automatically
- If issues occur, manually create: `uploads/issues/` and `uploads/blog/`

### Step 4: Access the Application
1. Open your browser
2. Navigate to: `http://localhost/GitHub/champion-trials/`
3. Login with default admin account:
   - Username: `admin`
   - Password: `admin123`

## 📋 Default Accounts

### Admin Account
- **Username**: admin
- **Password**: admin123
- **Email**: admin@citycare.com
- **Role**: Admin

⚠️ **IMPORTANT**: Change the admin password immediately after first login!

## 🎯 First Steps

1. **Login** with the admin account
2. **Explore the map** - You'll see the interactive Google Maps interface
3. **Report an issue** - Click "Report an Issue" in the navigation
4. **View the dashboard** - Check the admin dashboard for analytics
5. **Create a blog post** - Share insights in "Make Your City Better"

## 🔧 Troubleshooting

### Google Maps not loading?
- Check that your API key is correct in `config/config.php`
- Ensure the Maps JavaScript API is enabled in Google Cloud Console
- Check browser console for API errors

### Database connection errors?
- Verify MySQL is running (XAMPP Control Panel)
- Check database credentials in `config/database.php`
- Ensure the `city_care` database exists

### File upload not working?
- Check that `uploads/` directory exists and is writable
- Verify PHP upload settings in `.htaccess` or `php.ini`
- Check file permissions (should be 755 or 777)

### Session errors?
- Ensure PHP sessions are enabled
- Check that the session directory is writable
- Clear browser cookies and try again

## 📚 Next Steps

- Read the full [README.md](README.md) for detailed documentation
- Customize the color scheme in `assets/css/style.css`
- Add more states/cities in `assets/js/main.js`
- Configure email notifications (if needed)

## 🆘 Need Help?

- Check the [README.md](README.md) for detailed information
- Review code comments in PHP files
- Check browser console for JavaScript errors
- Review PHP error logs

---

**Happy Coding! 🎉**

