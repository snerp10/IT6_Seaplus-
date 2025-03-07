<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Delivery extends Model
{
    use HasFactory;

    protected $primaryKey = 'delivery_id'; // Change to your actual primary key

    protected $casts = [
        'delivery_date' => 'date',
    ];
    
    protected $fillable = [
        'order_id',
        'delivery_date',
        'street',
        'city',
        'province',
        'special_instructions',
        'delivery_status',
        'delivery_cost',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    // Add a scope for active deliveries
    public function scopeActive($query)
    {
        return $query->whereIn('delivery_status', ['Pending', 'Scheduled', 'Out for Delivery']);
    }
}
