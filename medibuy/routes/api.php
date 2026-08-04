<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CamionetaApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\WhatsappWebhookController;

Route::get('/webhooks/whatsapp', [WhatsappWebhookController::class, 'verify']);
Route::post('/webhooks/whatsapp', [WhatsappWebhookController::class, 'receive']);
Route::post('/whatsapp/close/{msisdn}', [WhatsappWebhookController::class, 'closeByAgent'])
     ->name('wa.chat.close');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/camionetas', [CamionetaApiController::class, 'index']);
    Route::get('/camionetas/{id}', [CamionetaApiController::class, 'show']);
    Route::post('/camionetas', [CamionetaApiController::class, 'store']);
    Route::put('/camionetas/{id}', [CamionetaApiController::class, 'update']);
    Route::delete('/camionetas/{id}', [CamionetaApiController::class, 'destroy']);
});
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

