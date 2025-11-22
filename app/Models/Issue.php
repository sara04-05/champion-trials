<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'latitude',
        'longitude',
        'state',
        'city',
        'address',
        'urgency',
        'status',
        'assigned_worker_id',
        'estimated_fix_days',
        'reported_at',
        'fixed_at',
        'upvotes',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'reported_at' => 'datetime',
        'fixed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(IssueCategory::class, 'category_id');
    }

    public function assignedWorker()
    {
        return $this->belongsTo(User::class, 'assigned_worker_id');
    }

    public function images()
    {
        return $this->hasMany(IssueImage::class);
    }

    public function updates()
    {
        return $this->hasMany(IssueUpdate::class);
    }

    public function upvoters()
    {
        return $this->belongsToMany(User::class, 'issue_upvotes')->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}

