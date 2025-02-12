<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $table = 'skills';
    protected $fillable = [
        'title'
    ];

    public function careers()
    {
        return $this->belongsToMany(Career::class, 'career_skills', 'skill_id', 'post_id');
    }
}
