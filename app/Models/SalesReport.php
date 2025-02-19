<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;

    protected $primaryKey = 'report_id';

    protected $fillable = [
        'date_generated',
        'total_sales',
        'total_expenses',
        'net_profit',
        'report_type',
        'generated_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
