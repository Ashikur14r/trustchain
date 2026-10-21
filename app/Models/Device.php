<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'transaction_id',
        'device_type',
        'brand',
        'model',
        'serial_number',
        'imei',
        'condition_description',
        'agreed_price',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class);
    }

    public function accessories(): HasMany
    {
        return $this->hasMany(Accessory::class);
    }
}