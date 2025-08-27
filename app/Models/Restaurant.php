<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Services\GeoService;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'address',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function zones()
    {
        return $this->hasMany(DeliveryZone::class);
    }

    /**
     * Check if point is within any delivery zone
     */
    public function isWithinDeliveryZone(float $lat, float $lng): bool
    {
        foreach ($this->zones as $zone) {

            // Polygon zone
            if ($zone->type === 'polygon' && is_array($zone->polygon)) {
                if (GeoService::pointInPolygon($lat, $lng, $zone->polygon)) {
                    return true;
                }
            }

            // Radius zone
            if ($zone->type === 'radius' && $zone->center_latitude !== null && $zone->center_longitude !== null && $zone->radius !== null) {
                $distance = GeoService::haversineDistance(
                    $lat,
                    $lng,
                    (float) $zone->center_latitude,
                    (float) $zone->center_longitude
                );

                if ($distance <= $zone->radius) { // radius is in km
                    return true;
                }
            }
        }

        return false;
    }
}
