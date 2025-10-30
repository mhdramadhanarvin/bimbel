<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Telegram Payment Notification Bot for Laravel
 * Handles sending payment confirmation messages with images and inline buttons
 */
class TelegramAPI
{
    private string $botToken;
    private string $apiUrl;

    /**
     * Constructor
     * @param string|null $botToken Bot Token (defaults to config)
     */
    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->apiUrl = "https://api.telegram.org/bot{$this->botToken}/";
    }

    /**
     * Send payment confirmation message with image and buttons
     * @param int $chatId Admin's Telegram chat ID
     * @param string $imageUrl URL or file path of payment proof image
     * @param array $paymentData Payment details
     * @param string $webhookUrl Your webhook URL to receive callback
     * @return array API response
     */
    public function sendPaymentConfirmation(int $chatId, string $imageUrl, array $paymentData, string $webhookUrl): array
    {
        try {
            // Format message caption
            $caption = $this->formatPaymentMessage($paymentData);

            // Create inline keyboard with approve/reject buttons
            $keyboard = $this->createPaymentButtons($paymentData['payment_id'], $webhookUrl);

            // Send photo with caption and buttons
            $response = Http::timeout(30)
                ->post($this->apiUrl . 'sendPhoto', [
                    'chat_id' => $chatId,
                    'photo' => $imageUrl,
                    'caption' => $caption,
                    'parse_mode' => 'HTML',
                    'reply_markup' => json_encode($keyboard)
                ]);

            Log::debug($imageUrl);
            $result = $response->json();

            return [
                'success' => $response->successful() && ($result['ok'] ?? false),
                'data' => $result,
                'http_code' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Telegram sendPaymentConfirmation error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'http_code' => 500
            ];
        }
    }

    /**
     * Format payment details into message
     * @param array $paymentData
     * @return string Formatted message
     */
    private function formatPaymentMessage(array $paymentData): string
    {
        $message = "🔔 <b>Pendaftar Baru</b>\n\n";
        $message .= "👤 <b>Nama:</b> {$paymentData['name']}\n";
        $message .= "📧 <b>Email:</b> {$paymentData['email']}\n";
        $message .= "📧 <b>Program:</b> {$paymentData['programme']}\n";
        $message .= "💰 <b>Nominal:</b> {$paymentData['amount']}\n";
        $message .= "📅 <b>Tanggal Daftar:</b> {$paymentData['date']}\n";

        if (isset($paymentData['notes']) && !empty($paymentData['notes'])) {
            $message .= "📝 <b>Notes:</b> {$paymentData['notes']}\n";
        }

        $message .= "\n<i>Segera konfirmasi pembayaran ini.</i>";

        return $message;
    }

    /**
     * Create inline keyboard buttons
     * @param string $paymentId
     * @param string $webhookUrl
     * @return array Keyboard markup
     */
    private function createPaymentButtons(string $paymentId, string $webhookUrl): array
    {
        return [
            'inline_keyboard' => [
                [
                    [
                        'text' => '✅ Approve',
                        'callback_data' => "approve-$paymentId",
                    ],
                    [
                        'text' => '❌ Reject',
                        'callback_data' => "reject-$paymentId",
                    ]
                ]
            ]
        ];
    }

    /**
     * Handle webhook callback from Telegram
     * @param array|null $webhookData Data from request
     * @return array|null Processed callback data
     */
    public function handleCallback(?array $webhookData = null): ?array
    {
        try {
            if ($webhookData === null) {
                $webhookData = request()->all();
            }

            if (isset($webhookData['callback_query'])) {
                $callbackQuery = $webhookData['callback_query'];
                $callbackData = json_decode($callbackQuery['data'], true);

                // Answer callback query (remove loading state)
                $this->answerCallbackQuery($callbackQuery['id']);

                // Update message to show decision
                $this->updateMessageAfterDecision(
                    $callbackQuery['message']['chat']['id'],
                    $callbackQuery['message']['message_id'],
                    $callbackData['action'],
                    $callbackData['payment_id']
                );

                // Send callback to your webhook
                $this->sendToWebhook($callbackData);

                return $callbackData;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Telegram handleCallback error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Answer callback query to remove loading state
     * @param string $callbackQueryId
     */
    private function answerCallbackQuery(string $callbackQueryId): void
    {
        try {
            Http::timeout(10)->post($this->apiUrl . 'answerCallbackQuery', [
                'callback_query_id' => $callbackQueryId
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram answerCallbackQuery error: ' . $e->getMessage());
        }
    }

    /**
     * Send Plain Chat
     * @param array $replyChatData
     */
    public function sendPlainMessage(array $chatData): void
    {
        try {
            Http::timeout(10)->post($this->apiUrl . 'sendMessage', [
                'chat_id' => $chatData['chat_id'],
                'text' => $chatData['text'],
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram sendMessageReply error: ' . $e->getMessage());
        }
    }

    /**
     * Reply Chat
     * @param array $replyChatData
     */
    public function sendMessageReply(array $replyChatData): void
    {
        try {
            Http::timeout(10)->post($this->apiUrl . 'sendMessage', [
                'chat_id' => $replyChatData['chat_id'],
                'text' => $replyChatData['text'],
                'reply_to_message_id' => $replyChatData['reply_to_message_id']
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram sendMessageReply error: ' . $e->getMessage());
        }
    }

    /**
     * Update message after admin decision
     * @param int $chatId
     * @param int $messageId
     * @param string $action
     * @param string $paymentId
     */
    private function updateMessageAfterDecision(int $chatId, int $messageId, string $action, string $paymentId): void
    {
        try {
            $status = $action === 'approve' ? '✅ APPROVED' : '❌ REJECTED';
            $emoji = $action === 'approve' ? '✅' : '❌';

            $newCaption = "<b>{$status}</b>\n\n";
            $newCaption .= "Payment ID: {$paymentId}\n";
            $newCaption .= 'Decision made at: ' . now()->format('Y-m-d H:i:s');

            Http::timeout(10)->post($this->apiUrl . 'editMessageCaption', [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'caption' => $newCaption,
                'parse_mode' => 'HTML',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            [
                                'text' => "{$emoji} Decision: " . ucfirst($action),
                                'callback_data' => 'done'
                            ]
                        ]
                    ]
                ])
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram updateMessageAfterDecision error: ' . $e->getMessage());
        }
    }

    /**
     * Send callback data to your webhook
     * @param array $callbackData
     * @return bool Success status
     */
    private function sendToWebhook(array $callbackData): bool
    {
        try {
            if (!isset($callbackData['webhook'])) {
                return false;
            }

            $webhookUrl = $callbackData['webhook'];

            $response = Http::timeout(10)->post($webhookUrl, [
                'action' => $callbackData['action'],
                'payment_id' => $callbackData['payment_id'],
                'timestamp' => time()
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Telegram sendToWebhook error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Set webhook URL for receiving updates
     * @param string $webhookUrl Your webhook URL
     * @return array Response
     */
    public function setWebhook(string $webhookUrl): array
    {
        try {
            $response = Http::timeout(30)->post($this->apiUrl . 'setWebhook', [
                'url' => $webhookUrl
            ]);

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'http_code' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Telegram setWebhook error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'http_code' => 500
            ];
        }
    }

    /**
     * Get webhook info
     * @return array Response
     */
    public function getWebhookInfo(): array
    {
        try {
            $response = Http::timeout(10)->get($this->apiUrl . 'getWebhookInfo');

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'http_code' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Telegram getWebhookInfo error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'http_code' => 500
            ];
        }
    }

    /**
     * Delete webhook
     * @return array Response
     */
    public function deleteWebhook(): array
    {
        try {
            $response = Http::timeout(10)->post($this->apiUrl . 'deleteWebhook');

            return [
                'success' => $response->successful(),
                'data' => $response->json(),
                'http_code' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Telegram deleteWebhook error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'http_code' => 500
            ];
        }
    }
}

// ============================================
// LARAVEL USAGE EXAMPLE
// ============================================

/*
 * // 1. Configuration
 * // Add to config/services.php:
 * 'telegram' => [
 *     'bot_token' => env('TELEGRAM_BOT_TOKEN'),
 *     'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
 * ],
 *
 * // Add to .env:
 * TELEGRAM_BOT_TOKEN=your_bot_token_from_botfather
 * TELEGRAM_ADMIN_CHAT_ID=123456789
 *
 *
 * // 2. Controller Example (app/Http/Controllers/PaymentController.php)
 * namespace App\Http\Controllers;
 *
 * use App\Services\TelegramPaymentBot;
 * use Illuminate\Http\Request;
 *
 * class PaymentController extends Controller
 * {
 *     public function notifyAdmin(Request $request)
 *     {
 *         $bot = new TelegramPaymentBot();
 *
 *         $adminChatId = config('services.telegram.admin_chat_id');
 *         $imageUrl = $request->input('payment_proof_url');
 *         $webhookUrl = route('payment.webhook');
 *
 *         $paymentData = [
 *             'payment_id' => 'PAY-' . time(),
 *             'user_name' => $request->input('name'),
 *             'user_email' => $request->input('email'),
 *             'amount' => '$' . $request->input('amount'),
 *             'date' => now()->format('Y-m-d H:i:s'),
 *             'notes' => $request->input('notes', 'Bank Transfer')
 *         ];
 *
 *         $result = $bot->sendPaymentConfirmation($adminChatId, $imageUrl, $paymentData, $webhookUrl);
 *
 *         if ($result['success']) {
 *             return response()->json(['message' => 'Notification sent to admin']);
 *         }
 *
 *         return response()->json(['error' => 'Failed to send notification'], 500);
 *     }
 *
 *     // Webhook endpoint for processing approve/reject
 *     public function processWebhook(Request $request)
 *     {
 *         $action = $request->input('action'); // 'approve' or 'reject'
 *         $paymentId = $request->input('payment_id');
 *
 *         // Update database
 *         \DB::table('payments')
 *             ->where('payment_id', $paymentId)
 *             ->update(['status' => $action === 'approve' ? 'approved' : 'rejected']);
 *
 *         // Send email to user, etc.
 *
 *         return response()->json(['success' => true]);
 *     }
 * }
 *
 *
 * // 3. Telegram Webhook Handler (app/Http/Controllers/TelegramWebhookController.php)
 * namespace App\Http\Controllers;
 *
 * use App\Services\TelegramPaymentBot;
 * use Illuminate\Http\Request;
 *
 * class TelegramWebhookController extends Controller
 * {
 *     public function handle(Request $request)
 *     {
 *         $bot = new TelegramPaymentBot();
 *         $callbackData = $bot->handleCallback($request->all());
 *
 *         if ($callbackData) {
 *             // Telegram bot will automatically send to your webhook
 *             \Log::info('Payment decision received', $callbackData);
 *         }
 *
 *         return response()->json(['ok' => true]);
 *     }
 * }
 *
 *
 * // 4. Routes (routes/web.php or routes/api.php)
 * use App\Http\Controllers\PaymentController;
 * use App\Http\Controllers\TelegramWebhookController;
 *
 * Route::post('/payment/notify-admin', [PaymentController::class, 'notifyAdmin']);
 * Route::post('/payment/webhook', [PaymentController::class, 'processWebhook'])->name('payment.webhook');
 * Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle']);
 *
 *
 * // 5. Artisan Command for Setup (optional)
 * // php artisan make:command SetupTelegramWebhook
 *
 * namespace App\Console\Commands;
 *
 * use App\Services\TelegramPaymentBot;
 * use Illuminate\Console\Command;
 *
 * class SetupTelegramWebhook extends Command
 * {
 *     protected $signature = 'telegram:setup';
 *     protected $description = 'Setup Telegram webhook';
 *
 *     public function handle()
 *     {
 *         $bot = new TelegramPaymentBot();
 *         $webhookUrl = url('/telegram/webhook');
 *
 *         $result = $bot->setWebhook($webhookUrl);
 *
 *         if ($result['success']) {
 *             $this->info('Webhook set successfully!');
 *             $this->info('Webhook URL: ' . $webhookUrl);
 *         } else {
 *             $this->error('Failed to set webhook');
 *             $this->error(json_encode($result));
 *         }
 *     }
 * }
 *
 * // Run: php artisan telegram:setup
 *
 *
 * // 6. Service Provider (optional, for dependency injection)
 * // Add to app/Providers/AppServiceProvider.php:
 * public function register()
 * {
 *     $this->app->singleton(TelegramPaymentBot::class, function ($app) {
 *         return new TelegramPaymentBot();
 *     });
 * }
 *
 * // Then use in controllers:
 * public function __construct(private TelegramPaymentBot $bot) {}
 */

?>
