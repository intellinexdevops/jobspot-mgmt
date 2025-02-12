<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploymentType extends Model
{
    protected $table = 'employment_type';
    protected $fillable = [
        'title',
        'description'
    ];
}
