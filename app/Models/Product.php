<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'prod_id';

    protected $fillable = [
        'name',
        'category',
        'unit',
        'status',
        'supp_id',
        'description',
        'image'
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'prod_id', 'prod_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supp_id', 'supp_id');
    }

    public function pricing()
    {
        return $this->hasMany(Pricing::class, 'prod_id', 'prod_id');
    }
    
    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'prod_id', 'prod_id');
    }
    
    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class, 'prod_id', 'prod_id');
    }

    public function getPriceAttribute()
    {
        // Get the current active pricing record
        $pricing = $this->pricing()
            ->where(function ($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->latest('start_date')
            ->first();
            
        return $pricing ? $pricing->selling_price : 0;
    }
    
    public function getStockAttribute()
    {
        return $this->inventories()->orderBy('inv_id', 'desc')->first()?->curr_stock ?? 0;
    }

}