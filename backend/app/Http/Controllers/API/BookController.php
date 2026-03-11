<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|min:2|max:100',
            'genre_id' => 'sometimes|integer|exists:genres,genre_id',
            'author_id' => 'sometimes|integer|exists:authors,author_id',
            'publisher_id' => 'sometimes|integer|exists:publishers,publisher_id',
            'year_from' => 'sometimes|integer|min:1000|max:' . date('Y'),
            'year_to' => 'sometimes|integer|min:1000|max:' . date('Y'),
            'sort' => 'sometimes|in:book_title,published_year,created_at',
            'order' => 'sometimes|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации параметров',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Book::with([
            'genres:genre_id,genre_name',
            'authors:author_id,last_name,first_name,middle_name',
            'publisher:publisher_id,publisher_name',
            'files' => fn($q) => $q->with('format:format_id,format_name')
        ]);

        $activeFilters = [];

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('book_title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
            $activeFilters['поиск'] = $search;
        }

        if ($request->has('genre_id')) {
            $query->whereHas('genres', fn($q) => $q->where('book_genres.genre_id', $request->genre_id));
            $activeFilters['жанр'] = $request->genre_id;
        }

        if ($request->has('author_id')) {
            $query->whereHas('authors', fn($q) => $q->where('book_authors.author_id', $request->author_id));
            $activeFilters['автор'] = $request->author_id;
        }

        if ($request->has('publisher_id')) {
            $query->where('publisher_id', $request->publisher_id);
            $activeFilters['издательство'] = $request->publisher_id;
        }

        if ($request->has('year_from')) {
            $query->where('published_year', '>=', $request->year_from);
            $activeFilters['год от'] = $request->year_from;
        }
        if ($request->has('year_to')) {
            $query->where('published_year', '<=', $request->year_to);
            $activeFilters['год до'] = $request->year_to;
        }

        $sortField = $request->sort ?? 'book_title';
        $sortOrder = $request->order ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        $perPage = $request->per_page ?? 15;
        $books = $query->paginate($perPage);

        if ($request->has('page') && $request->page > $books->lastPage()) {
            return response()->json([
                'success' => false,
                'message' => 'Запрошенная страница не существует',
                'errors' => [
                    'page' => ["Страница {$request->page} не существует. Всего страниц: {$books->lastPage()}"]
                ]
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($books->total() === 0) {
            $filterMessage = $this->getFilterMessage($activeFilters);

            return response()->json([
                'success' => true,
                'message' => $filterMessage ? "По запросу {$filterMessage} ничего не найдено" : "В библиотеке пока нет книг",
                'data' => [],
                'total' => 0
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json(
            $this->formatBooksList($books),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function show($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Некорректный ID книги'
            ], 400, [], JSON_UNESCAPED_UNICODE);
        }

        $book = Book::with([
            'genres:genre_id,genre_name',
            'authors:author_id,last_name,first_name,middle_name',
            'publisher:publisher_id,publisher_name',
            'files' => fn($q) => $q->with('format:format_id,format_name')
        ])->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => "Книга с ID {$id} не найдена"
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json(
            $this->formatBookDetail($book),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    private function getFilterMessage(array $filters): string
    {
        if (empty($filters)) {
            return '';
        }

        $parts = [];
        foreach ($filters as $key => $value) {
            $parts[] = "{$key}: {$value}";
        }

        return 'с параметрами ' . implode(', ', $parts);
    }

    private function formatBooksList($books)
    {
        return [
            'success' => true,
            'current_page' => $books->currentPage(),
            'data' => $books->map(fn($book) => [
                'book_id' => $book->book_id,
                'book_title' => $book->book_title,
                'published_year' => $book->published_year,
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
                'publisher' => [
                    'publisher_id' => $book->publisher->publisher_id,
                    'publisher_name' => $book->publisher->publisher_name
                ],
                'files_count' => $book->files->count()
            ]),
            'first_page_url' => $books->url(1),
            'from' => $books->firstItem(),
            'last_page' => $books->lastPage(),
            'last_page_url' => $books->url($books->lastPage()),
            'next_page_url' => $books->nextPageUrl(),
            'path' => $books->path(),
            'per_page' => $books->perPage(),
            'prev_page_url' => $books->previousPageUrl(),
            'to' => $books->lastItem(),
            'total' => $books->total()
        ];
    }

    private function formatBookDetail($book)
    {
        return [
            'success' => true,
            'data' => [
                'book_id' => $book->book_id,
                'book_title' => $book->book_title,
                'description' => $book->description,
                'published_year' => $book->published_year,
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
                'publisher' => [
                    'publisher_id' => $book->publisher->publisher_id,
                    'publisher_name' => $book->publisher->publisher_name
                ],
                'files' => $book->files->map(fn($file) => [
                    'file_id' => $file->file_id,
                    'format_id' => $file->format_id,
                    'format_name' => $file->format->format_name,
                    'file_path' => $file->file_path,
                    'file_size_bytes' => $file->file_size_bytes,
                    'file_size_mb' => round($file->file_size_bytes / 1048576, 2)
                ]),
            ]
        ];
    }
}
