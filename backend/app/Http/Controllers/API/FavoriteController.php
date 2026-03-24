<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться'
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $user = auth()->user();

            $perPage = $request->per_page ?? 15;
            $favorites;

            if ($request->boolean('all')) {
                $favorites = $user->favoriteBooks()
                ->with([
                    'genres:genre_id,genre_name',
                    'authors:author_id,last_name,first_name,middle_name',
                    'publisher:publisher_id,publisher_name',
                    'files' => fn($q) => $q->with('format:format_id,format_name')
                ])
                ->paginate($user->favoriteBooks()->count());
            } else {
                $favorites = $user->favoriteBooks()
                ->with([
                    'genres:genre_id,genre_name',
                    'authors:author_id,last_name,first_name,middle_name',
                    'publisher:publisher_id,publisher_name',
                    'files' => fn($q) => $q->with('format:format_id,format_name')
                ])
                ->paginate($perPage);
            }

            $favorites->getCollection()->transform(function ($book) {
                return [
                    'book_id' => $book->book_id,
                    'book_title' => $book->book_title,
                    'published_year' => $book->published_year,
                    'cover_url' => $book->cover_path ? '/api/covers/' . basename($book->cover_path) : null,
                    'genres' => $book->genres->map(fn($genre) => [
                        'genre_id' => $genre->genre_id,
                        'genre_name' => $genre->genre_name
                    ]),
                    'authors' => $book->authors->map(fn($author) => [
                        'author_id' => $author->author_id,
                        'last_name' => $author->last_name,
                        'first_name' => $author->first_name,
                        'middle_name' => $author->middle_name
                    ]),
                    'publisher' => $book->publisher?->publisher_name,
                    'files_count' => $book->files->count()
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $favorites
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Error in favorites index: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении избранного'
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function store($bookId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться'
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $book = Book::find($bookId);

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Книга не найдена'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $user = auth()->user();

            if ($user->favoriteBooks()->where('favorite_books.book_id', $bookId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Книга уже в избранном'
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            $user->favoriteBooks()->attach($bookId);

            return response()->json([
                'success' => true,
                'message' => 'Книга добавлена в избранное'
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Error adding to favorites: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении в избранное'
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function remove($bookId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться'
                ], 401, [], JSON_UNESCAPED_UNICODE);
            }

            $book = Book::find($bookId);

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Книга не найдена'
                ], 404, [], JSON_UNESCAPED_UNICODE);
            }

            $user = auth()->user();

            if (!$user->favoriteBooks()->where('favorite_books.book_id', $bookId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Книги нет в избранном'
                ], 400, [], JSON_UNESCAPED_UNICODE);
            }

            $user->favoriteBooks()->detach($bookId);

            return response()->json([
                'success' => true,
                'message' => 'Книга удалена из избранного'
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            \Log::error('Error removing from favorites: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении из избранного'
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }
}