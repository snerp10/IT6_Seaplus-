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
        'prod_type'
    ];

    // Direct one-to-many relationship with products
    public function products()
    {
        return $this->hasMany(Product::class, 'supp_id', 'supp_id');
    }
}
