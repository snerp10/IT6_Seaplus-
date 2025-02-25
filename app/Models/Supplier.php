<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'supp_id';

    protected $fillable = [
        'company_name',
        'email',
        'contact_number',
        'address',
        'prod_type',
    ];
}
