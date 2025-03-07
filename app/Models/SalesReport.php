<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'name',
        'description',
        'date_from',
        'date_to',
        'parameters'
    ];

    protected $casts = [
        'date_generated' => 'datetime',
        'date_from' => 'date',
        'date_to' => 'date',
        'total_sales' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'parameters' => 'json'
    ];

    /**
     * Get the employee who generated the report
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'generated_by', 'emp_id');
    }

    /**
     * Get the user who created the report through the employee relation
     */
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Employee::class,
            'emp_id', // Foreign key on employees table
            'id', // Foreign key on users table
            'generated_by', // Local key on sales_reports table
            'user_id' // Local key on employees table
        );
    }
}
