<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryMen extends Model
{
    use Notifiable, HasFactory;

    protected $table = 'delivery_men';

    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignments()
    {
        return $this->hasMany(OrderAssignment::class,'delivery_men_id');
    }

    public function notify($instance)
    {
        return $this->user->notify($instance);
    }

}
