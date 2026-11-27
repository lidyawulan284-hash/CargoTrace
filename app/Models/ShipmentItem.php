<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    use HasFactory;

    public const CATEGORY_CBU_EV = 'CBU_EV';

    public const CATEGORY_MOTORSPORT_AIR = 'MOTORSPORT_AIR';

    public const CATEGORY_CKD_PARTS = 'CKD_PARTS';

    public const CATEGORY_LUXURY_CAR = 'LUXURY_CAR';

    public const CATEGORIES = [
        self::CATEGORY_CBU_EV,
        self::CATEGORY_MOTORSPORT_AIR,
        self::CATEGORY_CKD_PARTS,
        self::CATEGORY_LUXURY_CAR,
    ];

    protected $fillable = [
        'shipment_id',
        'item_description',
        'vehicle_model',
        'vin_number',
        'cargo_category',
        'quantity',
        'weight_kg',
        'declared_value',
        'dimensions',
        'hs_code',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'weight_kg' => 'float',
            'declared_value' => 'float',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
