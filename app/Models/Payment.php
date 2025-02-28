<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $primaryKey = 'pay_id';
    
    protected $fillable = [
        'cus_id',
        'order_id',
        'amount_paid',
        'change_amount',
        'outstanding_balance',
        'pay_date',
        'pay_method',
        'reference_number',
        'invoice_number',
        'pay_status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cus_id', 'cus_id');
    }
}
