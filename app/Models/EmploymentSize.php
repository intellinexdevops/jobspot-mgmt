<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
class EmploymentSize extends Model
{
    protected $table = 'employment_size';
    protected $fillable = ['name'];

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
