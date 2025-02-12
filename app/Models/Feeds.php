<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;

class Feeds extends Model
{
    protected $table = 'feeds';
    protected $fillable = [
        'company_id',
        'title',
        'description',
        'assets',
        'reaction_count',
        'comment_count'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
