<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BookController;

Route::prefix('admin')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::middleware(['admin'])->group(function () {

            Route::apiResource('books', BookController::class);
            Route::apiResource('authors', App\Http\Controllers\Admin\AuthorController::class);
            Route::apiResource('genres', App\Http\Controllers\Admin\GenreController::class);
            Route::apiResource('publishers', App\Http\Controllers\Admin\PublisherController::class);
            Route::apiResource('formats', App\Http\Controllers\Admin\FormatController::class);

        });
    });
});
