<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    AuthController,
    UserController,
    QuizController,
    HistoryController,
    CategoryController,
    CountDataController,
};

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/user', fn (Request $request) => $request->user());
        Route::get('/logout', [AuthController::class, 'logout']);
    });

    Route::resource('user', UserController::class);
    Route::resource('quiz', QuizController::class);
    Route::resource('history', HistoryController::class);
    Route::resource('category', CategoryController::class);

    Route::get('get-count-data', CountDataController::class);
    Route::get('quiz-hidden-answer', [QuizController::class, 'hiddenAnswer']);
    Route::get('quiz-list-hidden-answer', [QuizController::class, 'listHiddenAnswer']);
});
