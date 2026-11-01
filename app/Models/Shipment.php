<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_CREATED = 'CREATED';
    public const STATUS_BOOKED = 'BOOKED';
    public const STATUS_PICKED_UP = 'PICKED_UP';
    public const STATUS_IN_TRANSIT = 'IN_TRANSIT';
    public const STATUS_DELIVERED = 'DELIVERED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public const STATUSES = [
        self::STATUS_CREATED,
        self::STATUS_BOOKED,
        self::STATUS_PICKED_UP,
        self::STATUS_IN_TRANSIT,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    // Status transition rules (Slide 6)
    public const TRANSITIONS = [
        self::STATUS_CREATED => [self::STATUS_BOOKED, self::STATUS_CANCELLED],
        self::STATUS_BOOKED => [self::STATUS_PICKED_UP, self::STATUS_CANCELLED],
        self::STATUS_PICKED_UP => [self::STATUS_IN_TRANSIT],
        self::STATUS_IN_TRANSIT => [self::STATUS_DELIVERED],
        self::STATUS_DELIVERED => [],
        self::STATUS_CANCELLED => [],
    ];

    protected $fillable = [
        'shipment_number',
        'customer_id',
        'carrier_id',
        'origin_id',
        'destination_id',
        'cargo_type',
        'total_weight_kg',
        'total_volume_cbm',
        'expected_departure',
        'expected_arrival',
        'actual_departure',
        'actual_arrival',
        'estimated_arrival',
        'status',
        'is_delayed',
        'delay_hours',
        'delay_severity',
        'notes',
    ];

    protected $casts = [
        'expected_departure' => 'datetime',
        'expected_arrival' => 'datetime',
        'actual_departure' => 'datetime',
        'actual_arrival' => 'datetime',
        'estimated_arrival' => 'datetime',
        'total_weight_kg' => 'float',
        'total_volume_cbm' => 'float',
        'is_delayed' => 'boolean',
        'delay_hours' => 'integer',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class)->orderBy('event_time', 'asc');
    }

    public function latestTrackingEvent(): HasOne
    {
        return $this->hasOne(TrackingEvent::class)->latestOfMany('event_time');
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class)->orderBy('detected_at', 'desc');
    }

    // Scopes
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->canManageShipments()) {
            return $query;
        }
        return $query->where('customer_id', $user->id);
    }

    public function scopeDelayed(Builder $query): Builder
    {
        return $query->where('is_delayed', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_DELIVERED, self::STATUS_CANCELLED]);
    }

    // Business Logic Helpers
    public function canTransitionTo(string $targetStatus): bool
    {
        $allowed = self::TRANSITIONS[$this->status] ?? [];
        return in_array($targetStatus, $allowed, true);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [self::STATUS_DELIVERED, self::STATUS_CANCELLED], true);
    }

    public function getCurrentCoordinates(): ?array
    {
        $latest = $this->latestTrackingEvent;
        if ($latest && $latest->latitude && $latest->longitude) {
            return [
                'lat' => (float)$latest->latitude,
                'lng' => (float)$latest->longitude,
                'location' => $latest->location_name,
            ];
        }
        if ($this->origin) {
            return [
                'lat' => (float)$this->origin->latitude,
                'lng' => (float)$this->origin->longitude,
                'location' => $this->origin->name,
            ];
        }
        return null;
    }
}
