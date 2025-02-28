<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'customers';
    protected $guarded = ['id'];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'cus_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
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
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the orders for the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'cus_id', 'cus_id');
    }

    /**
     * Get the payments for the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'cus_id', 'cus_id');
    }

    /**
     * Get the user that owns the customer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the customer's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->fname . ' ' . ($this->mname ? $this->mname . ' ' : '') . $this->lname;
    }
}

