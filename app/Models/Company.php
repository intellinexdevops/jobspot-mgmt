<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Company extends Model
{
    protected $table = "companies";
    protected $fillable = [
        "company_name",
        "user_id",
        "industry_id",
        "location_id",
        "profile",
        "bio",
        "employee_count",
        "since",
        "website",
        "follower",
        "gallery_images",
        "status",
        "employment_size_id"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
