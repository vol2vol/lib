<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with([
            'genre:genre_id,genre_name',
            'authors:author_id,last_name,first_name,middle_name',
            'publisher:publisher_id,publisher_name'
        ])->get();

        return response()->json(
            $this->formatBooksList($books)
        );
    }

    public function show($id)
    {
        $book = Book::with([
            'genre',
            'authors',
            'publisher',
            'format'
        ])->findOrFail($id);

        return response()->json(
            $this->formatBookDetail($book)
        );
    }

    private function formatBooksList(Collection $books)
    {
        return $books->map(function ($book) {
            return [
                'book_id' => $book->book_id,
                'book_title' => $book->book_title,
                'genre' => $book->genre,
                'authors' => $book->authors->map(function ($author) {
                    return [
                        'author_id' => $author->author_id,
                        'last_name' => $author->last_name,
                        'first_name' => $author->first_name,
                        'middle_name' => $author->middle_name,
                    ];
                }),
                'publisher' => $book->publisher,
            ];
        });
    }

    private function formatBookDetail($book)
    {
        return [
            'book_id' => $book->book_id,
            'book_title' => $book->book_title,
            'description' => $book->description,
            'published_year' => $book->published_year,
            'isbn' => $book->isbn,
            'page_count' => $book->page_count,
            'language' => $book->language,
            'file_path' => $book->file_path,
            'file_size_bytes' => $book->file_size_bytes,
            'file_size_mb' => round($book->file_size_bytes / 1048576, 2),
            'genre' => $book->genre,
            'authors' => $book->authors->map(function ($author) {
                return [
                    'author_id' => $author->author_id,
                    'last_name' => $author->last_name,
                    'first_name' => $author->first_name,
                    'middle_name' => $author->middle_name,
                ];
            }),
            'publisher' => $book->publisher,
            'format' => $book->format,
        ];
    }
}
