<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_men_id',
        'status'
    ];

    public function deliveryMen()
    {
        return $this->belongsTo(User::class, 'delivery_men_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
