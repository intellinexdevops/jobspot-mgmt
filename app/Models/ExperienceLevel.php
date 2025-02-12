<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExperienceLevel extends Model
{
    protected $table = "experience_level";
    protected $fillable = [
        "name",
        'description'
    ];
}
