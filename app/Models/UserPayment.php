<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model
{
    protected $fillable = [
        'proof_of_payment',
        'registration_number'
    ];
}
