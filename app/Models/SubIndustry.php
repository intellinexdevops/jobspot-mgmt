<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Industry;
class SubIndustry extends Model
{
    protected $table = 'sub_industries';

    protected $fillable = ['name', 'industry_id'];

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }
}
