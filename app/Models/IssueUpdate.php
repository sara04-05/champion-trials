<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'user_id',
        'update_text',
        'status_change',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

