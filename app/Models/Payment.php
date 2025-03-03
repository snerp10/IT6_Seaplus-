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

    /**
     * This method checks if a payment has been initialized but not completed
     */
    public function isInitialized()
    {
        return $this->exists && 
               $this->order_id && 
               (!$this->pay_status || $this->pay_status != 'Paid');
    }

    /**
     * Scope a query to only include initialized but incomplete payments
     */
    public function scopeInitialized($query)
    {
        return $query->whereNotNull('order_id')
                    ->where(function($q) {
                        $q->whereNull('pay_status')
                          ->orWhere('pay_status', '!=', 'Paid');
                    });
    }
}
