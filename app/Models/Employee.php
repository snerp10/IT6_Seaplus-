<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'emp_id';

    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'birthdate',
        'contact_number',
        'email',
        'address',
        'position',
        'salary'
    ];

    public function salesReports()
    {
        return $this->hasMany(SalesReport::class, 'generated_by', 'emp_id');
    }
}
