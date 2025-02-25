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
        'pay_date',
        'pay_method',
        'outstanding_balance',
        'invoice_number'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'cus_id');
    }
}
