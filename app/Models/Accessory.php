<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accessory extends Model
{
    protected $fillable = [
        'device_id',
        'name',
        'condition',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}