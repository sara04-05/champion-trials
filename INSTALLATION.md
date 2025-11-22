# City Care - Installation Guide

## Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL or MariaDB
- Web server (Apache/Nginx) or PHP built-in server

## Installation Steps

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Environment Configuration

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Edit `.env` and configure your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=citycare
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Generate Application Key

```bash
php artisan key:generate
```

### 4. Create Database

Create a MySQL database named `citycare` (or your preferred name).

### 5. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

This will:
- Create all database tables
- Seed roles (User, Admin, Construction Worker, etc.)
- Seed issue categories
- Seed badges

### 6. Create Storage Link

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for file uploads.

### 7. Build Frontend Assets

```bash
npm run build
```

Or for development:

```bash
npm run dev
```

### 8. Start Development Server

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Creating Admin User

After installation, you can create an admin user manually:

1. Register a regular user through the website
2. In your database, update the user's role:

```sql
-- Get the user ID and role ID
SELECT id FROM users WHERE email = 'admin@example.com';
SELECT id FROM roles WHERE slug = 'admin';

-- Attach admin role (replace USER_ID and ROLE_ID)
INSERT INTO user_roles (user_id, role_id, is_approved, created_at, updated_at) 
VALUES (USER_ID, ROLE_ID, 1, NOW(), NOW());
```

Or use Laravel Tinker:

```bash
php artisan tinker
```

```php
$user = App\Models\User::where('email', 'admin@example.com')->first();
$adminRole = App\Models\Role::where('slug', 'admin')->first();
$user->roles()->attach($adminRole->id, ['is_approved' => true]);
```

## Features Overview

- **Interactive Map**: Full-screen map with Leaflet.js showing all reported issues
- **Smart Issue Reporting**: AI-powered auto-categorization and urgency detection
- **Multi-Role System**: Support for different user roles (User, Worker, Admin, etc.)
- **Blog System**: Community blog for sharing insights
- **Gamification**: Points and badges system
- **Admin Dashboard**: Analytics and issue management
- **Accessibility**: Dark mode, high contrast, adjustable font size

## Troubleshooting

### Storage Link Issues

If images aren't displaying, ensure the storage link exists:

```bash
php artisan storage:link
```

### Permission Issues

On Linux/Mac, you may need to set permissions:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Database Connection Issues

Ensure your MySQL service is running and credentials in `.env` are correct.

## Next Steps

1. Customize issue categories in the database
2. Configure email settings in `.env` for notifications
3. Set up a production web server (Apache/Nginx)
4. Configure SSL certificate for HTTPS

