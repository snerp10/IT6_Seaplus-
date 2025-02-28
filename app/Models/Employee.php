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
        'gender',
        'birthdate',
        'contact_number',
        'email',
        'street',
        'barangay',
        'city',
        'province',
        'postal_code',
        'country',
        'position',
        'salary',
        'hired_date',
        'status',
    ];

    public function salesReports()
    {
        return $this->hasMany(SalesReport::class, 'generated_by', 'emp_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function getFullNameAttribute()
    {
        return $this->fname . ' ' . ($this->mname ? $this->mname . ' ' : '') . $this->lname;
    }
}
