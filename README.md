# City Care - PHP Web Application

A comprehensive PHP-based web application for reporting and managing local city issues. Built with PHP, MySQL, HTML, CSS, JavaScript, and Google Maps API.

## Features

### 🎨 Landing Page
- Full-screen interactive Google Maps integration
- Glassmorphism modal design
- State and city selection
- Map-themed color palette (Green, White, Black, Red, Blue)

### 🔐 Authentication System
- User registration with state/city selection
- Secure login with session management
- Role-based access control

### 🏙️ Issue Reporting System
- **Smart Issue Categorization**: Automatically categorizes issues based on description
- **Auto-assign Urgency Level**: Determines urgency (low, medium, high) automatically
- **Duplicate Detection**: Warns users about similar issues nearby
- **Fix-Time Prediction**: Estimates resolution time based on issue type
- **Photo Upload**: Support for before/after photos
- **Status Tracking**: Pending → In Progress → Fixed

### 👥 User Roles
- Regular User
- Construction Worker
- Doctor
- Engineer
- Safety Inspector
- Environmental Officer
- Admin (with special privileges)

### 🗺️ Map Features
- Interactive pins for reported issues
- Category-based color coding
- Hover tooltips with issue information
- Click to view detailed issue information
- Filters by category, status, urgency, and date

### 📰 Blog System ("Make Your City Better")
- Create and view blog posts
- Upload images
- Comment on posts
- Role-based professional insights

### 🏆 Gamification System
- Points for reporting issues, commenting, posting blogs, and upvoting
- Badges: Active Citizen, Road Saver, Green City Hero, Community Helper
- Points history tracking

### 📊 Admin Dashboard
- View all reported issues
- Filter by type, city, severity, status
- Analytics charts (Chart.js)
- Most active users
- Issue resolution statistics
- Manage issue statuses
- Assign workers to issues

### ♿ Accessibility Features
- Dark Mode
- High Contrast Mode
- Adjustable Font Size
- Keyboard Navigation
- ARIA Labels for screen readers

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Google Maps API Key

### Setup Steps

1. **Clone or download the project**
   ```bash
   cd C:\xampp\htdocs\GitHub\champion-trials
   ```

2. **Create the database**
   - Open phpMyAdmin or MySQL command line
   - Import `database/schema.sql` to create the database and tables

3. **Configure the application**
   - Edit `config/config.php` and add your Google Maps API Key:
     ```php
     define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY_HERE');
     ```
   - Update database credentials in `config/database.php` if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'city_care');
     ```

4. **Set up file permissions**
   - Ensure the `uploads/` directory is writable:
     ```bash
     mkdir uploads/issues uploads/blog
     chmod 777 uploads -R
     ```

5. **Access the application**
   - Open your browser and navigate to:
     ```
     http://localhost/GitHub/champion-trials/
     ```

## Default Admin Account

- **Username**: admin
- **Password**: admin123
- **Email**: admin@citycare.com

**⚠️ Important**: Change the default admin password after first login!

## Project Structure

```
champion-trials/
├── admin/
│   └── dashboard.php          # Admin dashboard
├── api/
│   ├── auth.php               # Authentication API
│   ├── issues.php             # Issues API
│   └── blog.php               # Blog API
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       ├── main.js            # Main JavaScript
│       └── map.js             # Map functionality
├── config/
│   ├── config.php             # Application configuration
│   └── database.php           # Database connection
├── database/
│   └── schema.sql             # Database schema
├── includes/
│   ├── auth.php               # Authentication functions
│   ├── issues.php             # Issue management functions
│   ├── blog.php               # Blog functions
│   └── navbar.php             # Navigation bar component
├── uploads/                   # Uploaded files (created automatically)
│   ├── issues/                # Issue photos
│   └── blog/                  # Blog images
├── index.php                  # Landing page
├── report.php                 # Report issue page
├── blog.php                   # Blog listing page
├── profile.php                # User profile
├── my-reports.php             # User's reported issues
├── notifications.php          # Notifications page
├── issue-details.php          # Issue details page
└── README.md                  # This file
```

## API Endpoints

### Authentication
- `POST api/auth.php?action=register` - Register new user
- `POST api/auth.php?action=login` - Login user
- `POST api/auth.php?action=logout` - Logout user
- `GET api/auth.php?action=check` - Check authentication status

### Issues
- `GET api/issues.php?action=all` - Get all issues (with filters)
- `GET api/issues.php?action=single&id={id}` - Get single issue
- `GET api/issues.php?action=duplicates&lat={lat}&lng={lng}` - Check for duplicates
- `POST api/issues.php?action=report` - Report new issue
- `POST api/issues.php?action=comment` - Add comment to issue
- `POST api/issues.php?action=upvote` - Upvote issue
- `POST api/issues.php?action=update_status` - Update issue status (Admin only)

### Blog
- `GET api/blog.php?action=all` - Get all blog posts
- `GET api/blog.php?action=single&id={id}` - Get single blog post
- `POST api/blog.php?action=create` - Create blog post
- `POST api/blog.php?action=comment` - Add comment to blog post

## Smart Features

### Auto-Categorization
The system automatically categorizes issues based on keywords in the description:
- **Pothole**: pothole, hole, road, pavement, asphalt
- **Broken Light**: light, lamp, streetlight, bulb, dark
- **Traffic**: traffic, jam, congestion, accident, crash
- **Trash**: trash, garbage, waste, dump, overflow
- **Environmental**: environment, pollution, hazard, toxic, chemical
- **Safety**: safety, danger, unsafe, hazard, risk

### Fix-Time Prediction
- Potholes: 3-7 days
- Broken streetlights: 1-3 days
- Trash overflow: Same day
- Traffic: Real-time only
- Environmental: 3 days
- Safety: 2 days

### Duplicate Detection
Warns users if a new report is within 100 meters of an existing report.

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Maps**: Google Maps JavaScript API
- **Charts**: Chart.js
- **UI Framework**: Bootstrap 5
- **Icons**: Font Awesome 6

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Security Features

- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- XSS protection (htmlspecialchars)
- Session management
- Role-based access control
- File upload validation

## Contributing

This is a project for learning and demonstration purposes. Feel free to fork and modify as needed.

## License

This project is open source and available for educational purposes.

## Support

For issues or questions, please check the code comments or create an issue in the repository.

---

**Built with ❤️ for better cities**

