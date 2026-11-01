<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'event_type',
        'status',
        'location_name',
        'location_id',
        'latitude',
        'longitude',
        'event_time',
        'description',
        'is_exception',
        'created_by',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_exception' => 'boolean',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exception(): HasOne
    {
        return $this->hasOne(ShipmentException::class);
    }
}
