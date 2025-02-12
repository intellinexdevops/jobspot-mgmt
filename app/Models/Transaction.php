<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'tran_id',
        'amount',
        'currency',
        'status',
        'payment_option',
        'type',
        'items',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
