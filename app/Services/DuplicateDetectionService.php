<?php

namespace App\Services;

use App\Models\Issue;

class DuplicateDetectionService
{
    private const RADIUS_KM = 0.5; // 500 meters

    public function findNearbyIssues(float $latitude, float $longitude, ?int $excludeIssueId = null): array
    {
        $issues = Issue::where('status', '!=', 'fixed')
            ->when($excludeIssueId, fn($q) => $q->where('id', '!=', $excludeIssueId))
            ->get();

        $nearby = [];
        foreach ($issues as $issue) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $issue->latitude,
                $issue->longitude
            );

            if ($distance <= self::RADIUS_KM) {
                $nearby[] = [
                    'issue' => $issue,
                    'distance' => round($distance * 1000, 0), // Convert to meters
                ];
            }
        }

        return $nearby;
    }

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

