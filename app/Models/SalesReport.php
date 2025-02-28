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

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'generated_by', 'emp_id');
    }
}
