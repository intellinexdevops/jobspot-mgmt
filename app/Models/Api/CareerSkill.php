<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;
use App\Models\Career;
use App\Models\Skill;

class CareerSkill extends Model
{
    protected $table = 'career_skills';
    protected $fillable = ['post_id', 'skill_id'];

    public function career()
    {
        return $this->belongsTo(Career::class, 'post_id', 'id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'id');
    }
}
