<?php

namespace App\Http\Controllers;

use App\Enums\UserPaymentStatusEnum;
use App\Models\TelegramApiLog;
use App\Services\TelegramAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Error;

class WebhookController extends Controller
{
    public function index(Request $request)
    {
        DB::beginTransaction();
        $telegramApi = new TelegramAPI;
        try {
            if (isset($request->callback_query)) {
                $callback_data = explode('-', $request->callback_query['data']);
                $telegramApiLog = TelegramApiLog::with('user_payment')->where('payment_id', $callback_data[1])->first();
                $adminChatId = config('services.telegram.admin_chat_id');
                if ($telegramApiLog->user_payment->status != UserPaymentStatusEnum::WAITING_CONFIRMATION) {
                    $telegramApi->sendMessageReply([
                        'chat_id' => $adminChatId,
                        'text' => 'Pembayaran sudah di konfirmasi, dan tidak dapat dikonfirmasi ulang',
                        'reply_to_message_id' => $telegramApiLog->message_id
                    ]);
                    throw new Error('payment has been process');
                }

                if ($callback_data[0] == 'approve') {
                    $telegramApiLog->user_payment->update([
                        'status' => UserPaymentStatusEnum::APPROVED
                    ]);
                    $telegramApi->sendMessageReply([
                        'chat_id' => $adminChatId,
                        'text' => 'Pembayaran diterima',
                        'reply_to_message_id' => $telegramApiLog->message_id
                    ]);
                } else if ($callback_data[0] == 'reject') {
                    $telegramApiLog->user_payment->update([
                        'status' => UserPaymentStatusEnum::REJECTED
                    ]);
                    $telegramApi->sendMessageReply([
                        'chat_id' => $adminChatId,
                        'text' => 'Pembayaran ditolak',
                        'reply_to_message_id' => $telegramApiLog->message_id
                    ]);
                }

                DB::commit();
            } else if (isset($request->message) && $request->message['text'] == '/start') {
                $chatID = $request->message['from']['id'];
                $telegramApi->sendPlainMessage([
                    'chat_id' => $chatID,
                    'text' => "Chat ID adalah $chatID"
                ]);
                throw new Error('callback stared');
            } else {
                throw new Error('callback not found');
            }
        } catch (\Throwable $error) {
            DB::rollBack();
            Log::error($error->getMessage());
        }

        return response()->json($request->all());
    }
}
