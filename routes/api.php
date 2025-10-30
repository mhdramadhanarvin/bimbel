<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('webhook', [WebhookController::class, 'index']);
Route::get('webhook', [WebhookController::class, 'index']);

Route::get('queue', function (Request $request) {
    Artisan::call('queue:work --stop-when-empty');

    $output = Artisan::output();
    return json_encode($output);
});
