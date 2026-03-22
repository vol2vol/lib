<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->per_page ?? 15;

        $favorites = $user->favoriteBooks()
            ->with([
                'genres:genre_id,genre_name',
                'authors:author_id,last_name,first_name,middle_name',
                'publisher:publisher_id,publisher_name',
                'files' => fn($q) => $q->with('format:format_id,format_name')
            ])
            ->paginate($perPage);

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
    }

    public function store($bookId)
    {
        $book = Book::findOrFail($bookId);
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
    }

    public function remove($bookId)
    {
        $book = Book::findOrFail($bookId);
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
    }
}
