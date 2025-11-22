<?php

namespace App\Services;

use App\Models\IssueCategory;

class IssueCategorizationService
{
    private $keywords = [
        'pothole' => ['pothole', 'hole', 'road damage', 'crack', 'bump', 'uneven'],
        'broken_light' => ['light', 'lamp', 'streetlight', 'dark', 'broken light', 'out', 'flickering'],
        'traffic' => ['traffic', 'congestion', 'jam', 'accident', 'collision', 'blocked'],
        'trash' => ['trash', 'garbage', 'litter', 'waste', 'overflow', 'dump', 'rubbish'],
        'environmental' => ['pollution', 'smoke', 'smell', 'hazard', 'chemical', 'toxic', 'air quality'],
        'safety' => ['dangerous', 'unsafe', 'hazard', 'risk', 'injury', 'fall'],
        'water' => ['water', 'leak', 'flood', 'drainage', 'sewer', 'pipe'],
        'vandalism' => ['vandalism', 'graffiti', 'damaged', 'broken', 'destroyed'],
    ];

    public function categorize(string $title, string $description): ?IssueCategory
    {
        $text = strtolower($title . ' ' . $description);
        
        $matches = [];
        foreach ($this->keywords as $categorySlug => $keywords) {
            $count = 0;
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    $count++;
                }
            }
            if ($count > 0) {
                $matches[$categorySlug] = $count;
            }
        }

        if (empty($matches)) {
            return IssueCategory::where('slug', 'other')->first() 
                ?? IssueCategory::first();
        }

        $bestMatch = array_search(max($matches), $matches);
        return IssueCategory::where('slug', $bestMatch)->first() 
            ?? IssueCategory::first();
    }

    public function detectUrgency(string $title, string $description): string
    {
        $text = strtolower($title . ' ' . $description);
        
        $highUrgencyKeywords = ['emergency', 'urgent', 'dangerous', 'accident', 'injury', 'hazard', 'toxic'];
        $lowUrgencyKeywords = ['minor', 'small', 'cosmetic', 'suggestion'];
        
        foreach ($highUrgencyKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return 'high';
            }
        }
        
        foreach ($lowUrgencyKeywords as $keyword) {
            if (str_contains($text, $keyword)) {
                return 'low';
            }
        }
        
        return 'medium';
    }
}

