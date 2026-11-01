<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentException extends Model
{
    use HasFactory;

    public const TYPE_CUSTOMS_HOLD = 'CUSTOMS_HOLD';
    public const TYPE_WEATHER_DELAY = 'WEATHER_DELAY';
    public const TYPE_PORT_CONGESTION = 'PORT_CONGESTION';
    public const TYPE_DAMAGED = 'DAMAGED';
    public const TYPE_MISROUTED = 'MISROUTED';
    public const TYPE_DOCUMENTATION_ISSUE = 'DOCUMENTATION_ISSUE';

    public const TYPES = [
        self::TYPE_CUSTOMS_HOLD,
        self::TYPE_WEATHER_DELAY,
        self::TYPE_PORT_CONGESTION,
        self::TYPE_DAMAGED,
        self::TYPE_MISROUTED,
        self::TYPE_DOCUMENTATION_ISSUE,
    ];

    public const STATUS_OPEN = 'OPEN';
    public const STATUS_INVESTIGATING = 'INVESTIGATING';
    public const STATUS_RESOLVED = 'RESOLVED';

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_INVESTIGATING,
        self::STATUS_RESOLVED,
    ];

    public const SEVERITY_LOW = 'LOW';
    public const SEVERITY_MEDIUM = 'MEDIUM';
    public const SEVERITY_HIGH = 'HIGH';
    public const SEVERITY_CRITICAL = 'CRITICAL';

    protected $fillable = [
        'shipment_id',
        'tracking_event_id',
        'exception_type',
        'severity',
        'status',
        'description',
        'delay_impact_hours',
        'detected_at',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
        'delay_impact_hours' => 'integer',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function trackingEvent(): BelongsTo
    {
        return $this->belongsTo(TrackingEvent::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_INVESTIGATING]);
    }

    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }
}
