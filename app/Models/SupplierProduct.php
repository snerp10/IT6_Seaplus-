<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierProduct extends Model
{
    use HasFactory;

    protected $primaryKey = 'sup_prod_id';

    protected $fillable = [
        'supp_id',
        'prod_id',
        'min_order_qty',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supp_id', 'supp_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'prod_id', 'prod_id');
    }
}
