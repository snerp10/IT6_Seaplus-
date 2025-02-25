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
        'price',
        'unit',
        'stock',
        'supp_id',
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'prod_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supp_id');
    }

    public function getCategoryProducts($category)
    {
        return $this->where('category', $category)->get();
    }
}

