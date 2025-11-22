<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'surname',
        'username',
        'email',
        'password',
        'state',
        'city',
        'avatar',
        'points',
        'is_approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_approved' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('is_approved')
            ->withTimestamps();
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function assignedIssues()
    {
        return $this->hasMany(Issue::class, 'assigned_worker_id');
    }

    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')->withTimestamps();
    }

    public function upvotedIssues()
    {
        return $this->belongsToMany(Issue::class, 'issue_upvotes')->withTimestamps();
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }
        return $this->roles->contains('id', $role->id);
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function addPoints($points)
    {
        $this->increment('points', $points);
        $this->checkBadges();
    }

    public function checkBadges()
    {
        $badges = Badge::where('points_required', '<=', $this->points)->get();
        foreach ($badges as $badge) {
            if (!$this->badges->contains($badge->id)) {
                $this->badges()->attach($badge->id);
            }
        }
    }
}

