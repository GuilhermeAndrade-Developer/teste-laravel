<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\JsonResponseMiddleware;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => [JsonResponseMiddleware::class, 'jwt.auth']], function () {
    // Profile
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Author
    Route::get('authors', [AuthorController::class, 'index']);
    Route::post('authors', [AuthorController::class, 'store']);
    Route::get('authors/show', [AuthorController::class, 'show']);
    Route::put('authors/update', [AuthorController::class, 'update']);
    Route::delete('authors/delete', [AuthorController::class, 'destroy']);
    Route::post('authors/restore', [AuthorController::class, 'restore']);

    // Book
    Route::get('books', [BookController::class, 'index']);
    Route::post('books', [BookController::class, 'store']);
    Route::get('books/show', [BookController::class, 'show']);
    Route::put('books/update', [BookController::class, 'update']);
    Route::delete('books/delete', [BookController::class, 'destroy']);

    // Loan
    Route::get('loans', [LoanController::class, 'index']);
    Route::post('loans', [LoanController::class, 'store']);
    Route::post('loans/return', [LoanController::class, 'returnBook']);
});
