<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidence extends Model
{
    protected $fillable = [
        'device_id',
        'file_path',
        'file_name',
        'file_type',
        'evidence_type',
        'description',
        'file_hash',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}