<?php

namespace App\Models;

use App\Enums\UserPaymentStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserPayment extends Model
{
    protected $guarded = [''];

    protected $casts = [
        'status' => UserPaymentStatusEnum::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
