<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'delivery_latitude',
        'delivery_longitude',
        'meta', // If any instructions are needed.
        'status'
    ];

    protected $casts = [
        'delivery_latitude' => 'decimal:7',
        'delivery_longitude' => 'decimal:7',
        'meta' => 'array',
    ];

    public function assignment()
    {
        return $this->hasOne(OrderAssignment::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
