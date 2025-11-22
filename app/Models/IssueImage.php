<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'image_path',
        'type',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}

