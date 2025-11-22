<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MapController extends Controller
{
    public function issues(Request $request): JsonResponse
    {
        $query = Issue::with(['category', 'user']);

        // Filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('urgency')) {
            $query->where('urgency', $request->urgency);
        }

        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        if ($request->has('state')) {
            $query->where('state', $request->state);
        }

        // Bounding box filter for map view
        if ($request->has(['north', 'south', 'east', 'west'])) {
            $query->whereBetween('latitude', [$request->south, $request->north])
                  ->whereBetween('longitude', [$request->west, $request->east]);
        }

        $issues = $query->get()->map(function ($issue) {
            return [
                'id' => $issue->id,
                'title' => $issue->title,
                'latitude' => $issue->latitude,
                'longitude' => $issue->longitude,
                'status' => $issue->status,
                'urgency' => $issue->urgency,
                'category' => [
                    'name' => $issue->category->name,
                    'color' => $issue->category->color,
                ],
                'upvotes' => $issue->upvotes,
                'reported_at' => $issue->reported_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json($issues);
    }

    public function heatmap(Request $request): JsonResponse
    {
        $issues = Issue::selectRaw('
                latitude,
                longitude,
                COUNT(*) as intensity
            ')
            ->groupBy('latitude', 'longitude')
            ->get();

        return response()->json($issues);
    }
}
