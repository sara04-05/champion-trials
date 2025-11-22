# City Care - Project Structure

## Overview

City Care is a comprehensive PHP Laravel web application for civic engagement, allowing users to report local issues, engage with the community through blogs, and track issue resolution.

## Technology Stack

- **Backend**: Laravel 10
- **Frontend**: Bootstrap 5, Leaflet.js, Chart.js
- **Database**: MySQL/MariaDB
- **Build Tool**: Vite
- **Map Library**: Leaflet.js

## Project Structure

```
city-care/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   └── MapController.php      # API endpoints for map data
│   │   │   ├── AdminController.php         # Admin dashboard & management
│   │   │   ├── AuthController.php          # Authentication
│   │   │   ├── BlogController.php          # Blog posts
│   │   │   ├── CommentController.php       # Comments system
│   │   │   ├── HomeController.php         # Home page
│   │   │   ├── IssueController.php         # Issue reporting & management
│   │   │   └── ProfileController.php      # User profiles
│   │   └── Middleware/
│   │       └── RoleMiddleware.php          # Role-based access control
│   ├── Models/
│   │   ├── Badge.php                       # Gamification badges
│   │   ├── BlogPost.php                    # Blog posts
│   │   ├── Comment.php                     # Comments (polymorphic)
│   │   ├── Issue.php                       # Reported issues
│   │   ├── IssueCategory.php               # Issue categories
│   │   ├── IssueImage.php                  # Issue images
│   │   ├── IssueUpdate.php                 # Issue status updates
│   │   ├── Notification.php                # User notifications
│   │   ├── Role.php                        # User roles
│   │   └── User.php                        # Users
│   └── Services/
│       ├── DuplicateDetectionService.php  # Detect nearby duplicate issues
│       ├── FixTimePredictionService.php    # Predict fix time
│       ├── GamificationService.php         # Points & badges
│       └── IssueCategorizationService.php  # AI categorization
├── database/
│   ├── migrations/                         # Database migrations
│   └── seeders/
│       └── DatabaseSeeder.php              # Seed data
├── resources/
│   ├── css/
│   │   └── app.css                         # Main stylesheet
│   ├── js/
│   │   ├── app.js                          # Main JavaScript
│   │   └── bootstrap.js                    # Axios setup
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php               # Main layout
│       ├── partials/
│       │   ├── navbar.blade.php            # Navigation bar
│       │   └── footer.blade.php            # Footer
│       ├── admin/
│       │   └── dashboard.blade.php         # Admin dashboard
│       ├── auth/
│       │   ├── login.blade.php             # Login page
│       │   └── register.blade.php          # Registration page
│       ├── blog/
│       │   ├── index.blade.php             # Blog listing
│       │   ├── create.blade.php            # Create blog post
│       │   └── show.blade.php              # Blog post detail
│       ├── issues/
│       │   ├── index.blade.php             # Issues listing
│       │   ├── create.blade.php            # Report issue
│       │   └── show.blade.php              # Issue detail
│       ├── profile/
│       │   ├── show.blade.php              # Profile view
│       │   └── edit.blade.php              # Edit profile
│       ├── about.blade.php                 # About page
│       ├── contact.blade.php               # Contact page
│       └── home.blade.php                  # Home/Map page
├── routes/
│   ├── web.php                             # Web routes
│   ├── api.php                             # API routes
│   └── console.php                         # Console routes
└── public/                                  # Public assets
```

## Key Features

### 1. Interactive Map
- Full-screen Leaflet.js map
- Color-coded issue pins by category
- Real-time issue filtering
- Heatmap visualization

### 2. Smart Issue Reporting
- **Auto-categorization**: AI detects issue type from description
- **Urgency detection**: Automatically assigns priority
- **Duplicate detection**: Warns about nearby similar issues
- **Fix-time prediction**: Estimates resolution time

### 3. User Roles
- Regular User
- Construction Worker
- Doctor
- Engineer
- Safety Inspector
- Environmental Officer
- Admin

### 4. Gamification
- Points system for actions:
  - Report issue: 10 points
  - Upvote issue: 2 points
  - Comment: 3 points
  - Blog post: 15 points
- Badges:
  - Active Citizen (10 points)
  - Road Saver (50 points)
  - Green City Hero (100 points)
  - Community Helper (200 points)

### 5. Blog System
- Community blog posts
- Image uploads
- Comments on posts
- Expert insights from role-based professionals

### 6. Admin Dashboard
- Issue statistics
- Charts (Chart.js)
- Issue management
- User management
- Analytics

### 7. Accessibility
- Dark mode toggle
- High contrast mode
- Adjustable font size
- Keyboard navigation
- ARIA labels

## Database Schema

### Core Tables
- `users` - User accounts
- `roles` - User roles
- `user_roles` - User-role relationships
- `issues` - Reported issues
- `issue_categories` - Issue types
- `issue_images` - Issue photos
- `issue_updates` - Status updates
- `blog_posts` - Blog entries
- `comments` - Comments (polymorphic)
- `notifications` - User notifications
- `badges` - Gamification badges
- `user_badges` - User badge assignments
- `issue_upvotes` - Issue upvotes

## API Endpoints

### Map API
- `GET /api/map/issues` - Get issues for map (with filters)
- `GET /api/map/heatmap` - Get heatmap data

## Security Features

- CSRF protection
- Password hashing
- Role-based access control
- File upload validation
- SQL injection prevention (Eloquent ORM)

## UI/UX Features

- Glassmorphism design
- Smooth transitions
- Responsive design
- Map-themed color palette
- Modern Bootstrap 5 components

## Next Steps for Enhancement

1. Email notifications
2. Real-time updates (WebSockets)
3. Mobile app (API ready)
4. Advanced analytics
5. Social media integration
6. Multi-language support
7. Advanced AI/ML integration
8. Worker assignment automation

