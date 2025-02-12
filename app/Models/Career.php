<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Skill;
use App\Models\Company;
use App\Models\Workspace;
use App\Models\Location;
use App\Models\EmploymentType;

class Career extends Model
{
    protected $table = 'posts';
    protected $fillable = [
        'company_id',
        'workspace_id',
        'location_id',
        'employment_type_id',
        'title',
        'description',
        'requirement',
        'facilities',
        'deadline',
        'status',
        'salary',
        'unit',
        'experience_level_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }


    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'career_skills', 'post_id', 'skill_id');
    }

    public function experienceLevel()
    {
        return $this->belongsTo(ExperienceLevel::class, 'experience_level_id');
    }
}
