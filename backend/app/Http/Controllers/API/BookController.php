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
            'genre:genre_id,genre_name',
            'authors:author_id,last_name,first_name,middle_name',
            'publisher:publisher_id,publisher_name',
            'format:format_id,format_name'
        ])->findOrFail($id);

        return response()->json(
            $this->formatBookDetail($book)
        );
    }

    private function formatBooksList(Collection $books)
    {
        return $books->map(fn($book) => [
            'book_id' => $book->book_id,
            'book_title' => $book->book_title,
            'genre' => [
                'genre_id' => $book->genre->genre_id,
                'genre_name' => $book->genre->genre_name
            ],
            'authors' => $book->authors->map(fn($author) => [
                'author_id' => $author->author_id,
                'last_name' => $author->last_name,
                'first_name' => $author->first_name,
                'middle_name' => $author->middle_name
            ]),
            'publisher' => [
                'publisher_id' => $book->publisher->publisher_id,
                'publisher_name' => $book->publisher->publisher_name
            ]
        ]);
    }

    private function formatBookDetail($book)
    {
        return [
            'book_id' => $book->book_id,
            'book_title' => $book->book_title,
            'description' => $book->description,
            'published_year' => $book->published_year,
            'file_path' => $book->file_path,
            'file_size_bytes' => $book->file_size_bytes,
            'genre' => [
                'genre_id' => $book->genre->genre_id,
                'genre_name' => $book->genre->genre_name
            ],
            'authors' => $book->authors->map(fn($author) => [
                'author_id' => $author->author_id,
                'last_name' => $author->last_name,
                'first_name' => $author->first_name,
                'middle_name' => $author->middle_name
            ]),
            'publisher' => [
                'publisher_id' => $book->publisher->publisher_id,
                'publisher_name' => $book->publisher->publisher_name
            ],
            'format' => [
                'format_id' => $book->format->format_id,
                'format_name' => $book->format->format_name
            ]
        ];
    }
}
