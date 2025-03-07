<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Order Model
 * 
 * Important note about status fields:
 * - order_status: Database column representing the order's lifecycle status (Pending, Processing, Completed, etc.)
 * - pay_status: Dynamic accessor that calculates payment status based on related payments (Paid, Partially Paid, Unpaid)
 */
class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'cus_id',
        'order_date',
        'total_amount',
        'order_status',
        'order_type',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'cus_id', 'cus_id');
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class, 'order_id', 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id', 'order_id');
    }

    /**
     * Check if the order is a bulk order
     */
    public function isBulkOrder()
    {
        return $this->order_type === 'bulk';
    }

    /**
     * Get the payment status of the order through its payments relationship
     * 
     * @return string
     */
    public function getPayStatusAttribute(): string
    {
        $payments = $this->payments;
        
        if ($payments->isEmpty()) {
            return 'Unpaid';
        }
        
        $totalAmount = $this->total_amount + ($this->delivery ? $this->delivery->delivery_cost : 0);
        $totalPaid = $payments->where('pay_status', 'Paid')->sum('amount_paid');
        
        if ($totalPaid >= $totalAmount) {
            return 'Paid';
        } elseif ($totalPaid > 0) {
            return 'Partially Paid';
        } else {
            return 'Unpaid';
        }
    }
    
    /**
     * Scope a query to only include orders with a specific payment status
     */
    public function scopeWithPaymentStatus($query, $status)
    {
        if ($status === 'Paid') {
            return $query->whereHas('payments', function ($subquery) {
                $subquery->selectRaw('SUM(amount_paid) as total_paid, order_id')
                    ->where('pay_status', 'Paid')
                    ->groupBy('order_id')
                    ->havingRaw('total_paid >= orders.total_amount');
            });
        } elseif ($status === 'Partially Paid') {
            return $query->whereHas('payments', function ($subquery) {
                $subquery->selectRaw('SUM(amount_paid) as total_paid, order_id')
                    ->where('pay_status', 'Paid')
                    ->groupBy('order_id')
                    ->havingRaw('total_paid > 0')
                    ->havingRaw('total_paid < orders.total_amount');
            });
        } elseif ($status === 'Unpaid') {
            return $query->whereDoesntHave('payments', function ($subquery) {
                $subquery->where('pay_status', 'Paid');
            })->orWhereHas('payments', function ($subquery) {
                $subquery->selectRaw('SUM(amount_paid) as total_paid, order_id')
                    ->where('pay_status', 'Paid')
                    ->groupBy('order_id')
                    ->havingRaw('total_paid = 0');
            });
        }
        
        return $query;
    }
    
    /**
     * Calculate the total amount paid for this order
     * 
     * @return float
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments->where('pay_status', 'Paid')->sum('amount_paid');
    }
    
    /**
     * Calculate the outstanding balance for this order
     * 
     * @return float
     */
    public function getOutstandingBalanceAttribute(): float
    {
        $totalAmount = $this->total_amount + ($this->delivery ? $this->delivery->delivery_cost : 0);
        return max(0, $totalAmount - $this->total_paid);
    }
}


