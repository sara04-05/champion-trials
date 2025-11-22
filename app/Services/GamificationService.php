<?php

namespace App\Services;

use App\Models\User;

class GamificationService
{
    private $pointValues = [
        'issue_reported' => 10,
        'issue_upvoted' => 2,
        'comment_posted' => 3,
        'blog_posted' => 15,
        'issue_fixed' => 50,
    ];

    public function awardPoints(User $user, string $action, ?int $customPoints = null): void
    {
        $points = $customPoints ?? ($this->pointValues[$action] ?? 5);
        $user->addPoints($points);
    }
}

