<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'supp_id';
    
    protected $fillable = [
        'company_name',
        'contact_number',
        'email',
        'street',
        'city',
        'province',
        'postal_code',
        'prod_type'
    ];

    // Get all products associated with this supplier through the junction table
    public function products()
{
    return $this->belongsToMany(Product::class, 'supplier_products', 'supp_id', 'prod_id')
        ->withPivot('min_order_qty')
        ->withTimestamps();
}

    // Get all supplier-product relationships
    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class, 'supp_id', 'supp_id');
    }
}
