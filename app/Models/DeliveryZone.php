<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'type',
        'polygon',
        'center_latitude',
        'center_longitude',
        'radius',
    ];

    protected $casts = [
        'polygon' => 'array', // automatically convert JSON to array
        'center_latitude' => 'decimal:7',
        'center_longitude' => 'decimal:7',
        'radius' => 'float', // radius is in km
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
