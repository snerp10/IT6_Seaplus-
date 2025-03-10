<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $primaryKey = 'inv_id';

    protected $fillable = [
        'prod_id',
        'curr_stock',
        'move_type',
        'stock_in',
        'stock_out',
        'move_date',
    ];

    protected $casts = [
        'move_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'prod_id', 'prod_id');
    }
}

