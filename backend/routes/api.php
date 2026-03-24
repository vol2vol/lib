<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\GenreController;
use App\Http\Controllers\API\AuthorController;
use App\Http\Controllers\API\PublisherController;
use App\Http\Controllers\API\FormatController;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\API\FavoriteController;

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

// ПУБЛИЧНЫЕ МАРШРУТЫ (работают без авторизации)

// Жанры
Route::get('/genres', [GenreController::class, 'index']);
Route::get('/genres/{id}', [GenreController::class, 'show']);

// Авторы
Route::get('/authors', [AuthorController::class, 'index']);
Route::get('/authors/{id}', [AuthorController::class, 'show']);

// Издательства
Route::get('/publishers', [PublisherController::class, 'index']);
Route::get('/publishers/{id}', [PublisherController::class, 'show']);

// Форматы
Route::get('/formats', [FormatController::class, 'index']);
Route::get('/formats/{id}', [FormatController::class, 'show']);

// КНИГИ
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);

Route::get('/covers/{filename}', [FileController::class, 'getCover']);

// Приватные маршруты
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/books/file/{fileId}/read', [FileController::class, 'readFile']);
    Route::get('/books/file/{fileId}/download', [FileController::class, 'downloadFile']);

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{book}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{book}', [FavoriteController::class, 'remove']);
});

