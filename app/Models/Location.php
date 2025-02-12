<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'latlng',
        'full_address'
    ];

    public function locations()
    {
        return $this->belongsTo(Location::class);
    }
}
