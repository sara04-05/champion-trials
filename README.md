# City Care - Civic Engagement Platform

A comprehensive PHP Laravel web application for reporting and managing local city issues.

## Features

- 🗺️ Interactive map with Leaflet.js
- 📝 Smart issue reporting with auto-categorization
- 👥 Multi-role user system (Users, Workers, Admins)
- 📰 Blog system for community engagement
- 🏆 Gamification with points and badges
- 📊 Admin dashboard with analytics
- ♿ Full accessibility support
- 🎨 Modern glassmorphism UI design

## Installation

1. Install dependencies:
```bash
composer install
npm install
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Configure database in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=citycare
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations:
```bash
php artisan migrate --seed
```

6. Start development server:
```bash
php artisan serve
npm run dev
```

## Tech Stack

- **Backend**: Laravel 10
- **Frontend**: Bootstrap 5, Leaflet.js, Chart.js
- **Database**: MySQL/MariaDB
- **Build Tool**: Vite

## User Roles

- Regular User
- Construction Worker
- Doctor
- Engineer
- Safety Inspector
- Environmental Officer
- Admin

## License

MIT

