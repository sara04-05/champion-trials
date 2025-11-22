<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\IssueCategory;
use App\Models\Badge;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            ['name' => 'User', 'slug' => 'user', 'description' => 'Regular user'],
            ['name' => 'Construction Worker', 'slug' => 'construction_worker', 'description' => 'Construction worker'],
            ['name' => 'Doctor', 'slug' => 'doctor', 'description' => 'Medical professional'],
            ['name' => 'Engineer', 'slug' => 'engineer', 'description' => 'Engineering professional'],
            ['name' => 'Safety Inspector', 'slug' => 'safety_inspector', 'description' => 'Safety inspection professional'],
            ['name' => 'Environmental Officer', 'slug' => 'environmental_officer', 'description' => 'Environmental professional'],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Administrator'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Issue Categories
        $categories = [
            ['name' => 'Pothole', 'slug' => 'pothole', 'color' => '#FF6B6B', 'icon' => '🚧'],
            ['name' => 'Broken Light', 'slug' => 'broken_light', 'color' => '#FFD93D', 'icon' => '💡'],
            ['name' => 'Traffic', 'slug' => 'traffic', 'color' => '#6BCF7F', 'icon' => '🚦'],
            ['name' => 'Trash', 'slug' => 'trash', 'color' => '#4ECDC4', 'icon' => '🗑️'],
            ['name' => 'Environmental', 'slug' => 'environmental', 'color' => '#95E1D3', 'icon' => '🌱'],
            ['name' => 'Safety', 'slug' => 'safety', 'color' => '#F38181', 'icon' => '⚠️'],
            ['name' => 'Water', 'slug' => 'water', 'color' => '#3498DB', 'icon' => '💧'],
            ['name' => 'Vandalism', 'slug' => 'vandalism', 'color' => '#9B59B6', 'icon' => '🎨'],
            ['name' => 'Other', 'slug' => 'other', 'color' => '#95A5A6', 'icon' => '📍'],
        ];

        foreach ($categories as $category) {
            IssueCategory::create($category);
        }

        // Badges
        $badges = [
            ['name' => 'Active Citizen', 'slug' => 'active_citizen', 'description' => 'Reported your first issue', 'points_required' => 10, 'icon' => '🏅'],
            ['name' => 'Road Saver', 'slug' => 'road_saver', 'description' => 'Reported 5 road issues', 'points_required' => 50, 'icon' => '🛣️'],
            ['name' => 'Green City Hero', 'slug' => 'green_city_hero', 'description' => 'Reported 10 environmental issues', 'points_required' => 100, 'icon' => '🌳'],
            ['name' => 'Community Helper', 'slug' => 'community_helper', 'description' => 'Helped the community with 20 reports', 'points_required' => 200, 'icon' => '🤝'],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}

