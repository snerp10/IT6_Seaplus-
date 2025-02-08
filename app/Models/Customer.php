<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'user_id',
        'address',
        'customer_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}