<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'contact_number',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}
