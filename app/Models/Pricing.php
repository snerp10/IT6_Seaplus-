<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $primaryKey = 'price_id';
    protected $table = 'pricing';

    protected $fillable = [
        'prod_id',
        'original_price',
        'selling_price',
        'markup',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function products()
    {
        return $this->belongsTo(Product::class, 'prod_id', 'prod_id');
    }
}
