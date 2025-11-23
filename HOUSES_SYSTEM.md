# Houses System Documentation

## Overview
The Houses System allows users to choose from 4 different houses, each with its own theme colors and logo. The selected house affects the entire website's appearance.

## Houses Available

1. **Engineers** 🏗️
   - Colors: Green (#4CAF50), Black, White
   - Description: Builders and innovators

2. **Shadows** 🌙
   - Colors: Blue (#2196F3), Black, White
   - Description: Mysterious and strategic

3. **Hipsters** 🎨
   - Colors: Purple (#9C27B0), Black, White
   - Description: Creative and unique

4. **Speedsters** ⚡
   - Colors: Red (#f44336), Black, White
   - Description: Fast and energetic

## Database Setup

Run this SQL to add the house fields to your database:

```sql
ALTER TABLE users ADD COLUMN IF NOT EXISTS house VARCHAR(50) DEFAULT NULL;
ALTER TABLE users ADD COLUMN IF NOT EXISTS house_logo VARCHAR(255) DEFAULT NULL;
```

Or run: `database/add_house_field.sql`

## How It Works

### 1. House Selection
- Users can select their house from the "Pick Your House" section in `profile.php`
- Selection is saved to the database and session
- Theme applies immediately after selection

### 2. Theme Application
- Theme is loaded via `includes/theme-loader.php`
- Add `<?php include 'includes/theme-loader.php'; ?>` to any page's `<head>` section
- Theme CSS variables override default colors

### 3. Profile Picture
- When a house is selected, the profile picture changes to the house logo
- Logo appears in navbar, profile page, and anywhere profile picture is shown

## Adding Theme to New Pages

To add house theme support to any page:

1. Include the theme loader in the `<head>` section:
```php
<?php include 'includes/theme-loader.php'; ?>
```

2. Make sure the page includes:
   - `config/config.php`
   - `includes/auth.php` (if user authentication is needed)

## Files Modified/Created

### New Files:
- `includes/houses.php` - House management functions
- `includes/theme-loader.php` - Global theme CSS loader
- `api/houses.php` - API endpoint for house selection
- `database/add_house_field.sql` - Database migration

### Modified Files:
- `config/config.php` - Added HOUSES constant
- `profile.php` - Added house selection UI
- `includes/navbar.php` - Shows house logo
- `includes/auth.php` - Loads house data on login
- `index.php`, `about.php`, `blog.php` - Added theme loader

## API Endpoints

### Select House
```
POST api/houses.php?action=select
Body: { "house": "engineers" }
```

### Get Current House
```
GET api/houses.php?action=current
Response: { "success": true, "house": "engineers" }
```

## Extending the System

### Adding a New House

1. Add to `config/config.php` HOUSES array:
```php
'newhouse' => [
    'name' => 'New House',
    'colors' => ['primary' => '#COLOR', 'secondary' => '#000000', 'accent' => '#FFFFFF'],
    'logo' => '🎯',
    'description' => 'House description'
]
```

2. The system will automatically pick it up!

### Customizing Theme Colors

Edit `includes/theme-loader.php` to add more CSS rules or modify existing ones.

## Badge System Fix

Also fixed badge display issue:
- Badges now properly query from database
- Added points-based badge (10+ points)
- Badges checked after all activities (issues, comments, blog posts)

