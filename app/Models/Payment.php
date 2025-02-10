<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'payment_id';
    
    protected $fillable = [
        'customer_id',
        'order_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'outstanding_balance',
        'invoice_number'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
