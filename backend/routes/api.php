s
<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::post('/user/picture', [AuthController::class, 'uploadPicture']);
    Route::get('/chatbot/conversations', [\App\Http\Controllers\ChatbotController::class, 'conversations']);
    Route::post('/chatbot/conversations', [\App\Http\Controllers\ChatbotController::class, 'startConversation']);
    Route::get('/chatbot/conversations/{id}/messages', [\App\Http\Controllers\ChatbotController::class, 'messages']);
    Route::post('/chatbot/conversations/{id}/messages', [\App\Http\Controllers\ChatbotController::class, 'sendMessage']);
});
