<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Company;
use App\Models\Career;

class Application extends Model
{
    protected $table = "application";
    protected $fillable = [
        'user_id',
        'post_id',
        'company_id',
        'status'
    ];

    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
    // public function company()
    // {
    //     return $this->hasMany(Job::class);
    // }
}
