<?php

namespace App\Services;

use App\Models\IssueCategory;

class FixTimePredictionService
{
    private $predictions = [
        'pothole' => ['min' => 3, 'max' => 7],
        'broken_light' => ['min' => 1, 'max' => 3],
        'trash' => ['min' => 0, 'max' => 1],
        'traffic' => ['min' => 0, 'max' => 0], // Real-time only
        'water' => ['min' => 2, 'max' => 5],
        'vandalism' => ['min' => 1, 'max' => 4],
        'environmental' => ['min' => 3, 'max' => 10],
        'safety' => ['min' => 1, 'max' => 3],
    ];

    public function predict(IssueCategory $category, string $urgency = 'medium'): int
    {
        $slug = $category->slug;
        
        if (!isset($this->predictions[$slug])) {
            return 5; // Default
        }

        $range = $this->predictions[$slug];
        
        // Adjust based on urgency
        $multiplier = match($urgency) {
            'high' => 0.7,
            'low' => 1.3,
            default => 1.0,
        };

        $min = max(0, (int)($range['min'] * $multiplier));
        $max = max($min, (int)($range['max'] * $multiplier));

        return (int)(($min + $max) / 2);
    }
}

