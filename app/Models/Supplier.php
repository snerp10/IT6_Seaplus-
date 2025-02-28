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
        'email',
        'contact_number',
        'street',
        'city',
        'province',
        'prod_type',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'supp_id', 'supp_id');
    }
    
    public function supplierProducts()
    {
        return $this->hasMany(SupplierProduct::class, 'supp_id', 'supp_id');
    }
    
    public function getAddressAttribute()
    {
        return $this->street . ', ' . $this->city . ', ' . $this->province;
    }
}
