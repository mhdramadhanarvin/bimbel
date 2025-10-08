<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TelegramApiLog extends Model
{
    //
    public function user_payment(): BelongsTo
    {
        return $this->belongsTo(UserPayment::class, 'payment_id');
    }
}
