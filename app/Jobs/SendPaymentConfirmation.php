<?php

namespace App\Jobs;

use App\Models\TelegramApiLog;
use App\Models\UserPayment;
use App\Services\TelegramAPI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SendPaymentConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;
    public $timeout = 60;
    public $failOnTimeout = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public UserPayment $payment
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramAPI $bot): void
    {
        try {
            DB::beginTransaction();
            $payment = UserPayment::with('user')->find($this->payment->id);
            $adminChatId = config('services.telegram.admin_chat_id');
            $imageUrl = Str::remove('public/', asset('storage/' . $payment->proof_of_payment));
            $webhookUrl = config('services.telegram.webhook_url');
            $programme = $payment->user->programme;
            $paymentData = [
                'payment_id' => $payment->id,
                'name' => $payment->user->name,
                'email' => $payment->user->email,
                'programme' => $programme->getLabel(),
                'amount' => 'IDR Rp. 6.150.000',
                'date' => now()->format('Y-m-d H:i:s'),
            ];

            $result = $bot->sendPaymentConfirmation($adminChatId, $imageUrl, $paymentData, $webhookUrl);

            Log::debug($result);
            TelegramApiLog::insert([
                'message_id' => $result['data']['result']['message_id'],
                'payment_id' => $this->payment->id
            ]);
            $payment->update([
                'is_notify' => 1
            ]);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('SendPaymentConfirmation :' . $e->getMessage());
            $this->release();
        }
    }
}
