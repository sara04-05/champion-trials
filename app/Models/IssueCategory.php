<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IssueCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'description',
    ];

    public function issues()
    {
        return $this->hasMany(Issue::class, 'category_id');
    }
}

