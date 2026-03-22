<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации параметров',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $perPage = $request->per_page ?? 20;

        $books = Book::with([
            'genres:genre_id,genre_name',
            'authors:author_id,last_name,first_name,middle_name',
            'publisher:publisher_id,publisher_name',
            'files' => fn($q) => $q->with('format:format_id,format_name')
        ])->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $this->formatBooksList($books),
            'pagination' => [
                'current_page' => $books->currentPage(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'total' => $books->total(),
                'next_page_url' => $books->nextPageUrl(),
                'prev_page_url' => $books->previousPageUrl(),
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_title' => 'required|string|max:255',
            'description' => 'required|string',
            'published_year' => 'required|integer|min:1800|max:' . date('Y'),
            'publisher_id' => 'required|exists:publishers,publisher_id',
            'author_ids' => 'required|array',
            'author_ids.*' => 'exists:authors,author_id',
            'genre_ids' => 'required|array',
            'genre_ids.*' => 'exists:genres,genre_id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'files' => 'nullable|array',
            'files.*.format_id' => 'required_with:files|exists:formats,format_id',
            'files.*.file' => 'required_with:files|file|mimes:pdf,fb2,txt,xml|max:51200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации данных',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $coverFile = $request->file('cover');
            $coverFilename = time() . '_cover_' . Str::random(10) . '.' . $coverFile->getClientOriginalExtension();
            $coverPath = $coverFile->storeAs('covers', $coverFilename, 'local');
        }

        $book = Book::create([
            'book_title' => $request->book_title,
            'description' => $request->description,
            'published_year' => $request->published_year,
            'cover_path' => $coverPath,
            'publisher_id' => $request->publisher_id,
        ]);

        if ($request->has('author_ids')) {
            $book->authors()->attach($request->author_ids);
        }

        if ($request->has('genre_ids')) {
            $book->genres()->attach($request->genre_ids);
        }

        if ($request->has('files') && is_array($request->input('files'))) {
            foreach ($request->input('files') as $index => $fileData) {
                $file = $request->file("files.{$index}.file");
                $formatId = $request->input("files.{$index}.format_id");

                if ($file && $formatId) {
                    $filename = time() . '_' . Str::random(15) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('books', $filename, 'local');

                    $book->files()->create([
                        'format_id' => $formatId,
                        'file_path' => $filePath,
                        'file_size_bytes' => $file->getSize(),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно создана',
            'data' => $this->formatBookDetail($book->load(['authors', 'genres', 'publisher', 'files.format']))
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        $book = Book::with([
            'genres',
            'authors',
            'publisher',
            'files.format'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatBookDetail($book)
        ]);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        if ($request->hasFile('cover')) {
            $coverFile = $request->file('cover');

            if ($book->cover_path && Storage::disk('local')->exists($book->cover_path)) {
                Storage::disk('local')->delete($book->cover_path);
            }

            $coverFilename = time() . '_cover_' . Str::random(10) . '.' . $coverFile->getClientOriginalExtension();
            $coverPath = $coverFile->storeAs('covers', $coverFilename, 'local');
            $book->cover_path = $coverPath;
            $book->save();
        }

        $book->fill($request->only([
            'book_title', 'description', 'published_year', 'publisher_id'
        ]));
        $book->save();

        if ($request->has('author_ids')) {
            $book->authors()->sync($request->author_ids);
        }

        if ($request->has('genre_ids')) {
            $book->genres()->sync($request->genre_ids);
        }

        if ($request->has('files') && is_array($request->input('files'))) {
            foreach ($request->input('files') as $index => $fileData) {
                $file = $request->file("files.{$index}.file");
                $formatId = $request->input("files.{$index}.format_id");

                if (!$file || !$formatId) {
                    continue;
                }

                $existingFile = $book->files()->where('format_id', $formatId)->first();

                if ($existingFile) {
                    if (Storage::disk('local')->exists($existingFile->file_path)) {
                        Storage::disk('local')->delete($existingFile->file_path);
                    }

                    $filename = time() . '_' . Str::random(15) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('books', $filename, 'local');

                    $existingFile->update([
                        'file_path' => $filePath,
                        'file_size_bytes' => $file->getSize(),
                    ]);
                } else {
                    $filename = time() . '_' . Str::random(15) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('books', $filename, 'local');

                    $book->files()->create([
                        'format_id' => $formatId,
                        'file_path' => $filePath,
                        'file_size_bytes' => $file->getSize(),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Книга обновлена',
            'data' => $this->formatBookDetail($book->load(['authors', 'genres', 'publisher', 'files.format']))
        ]);
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover_path && Storage::disk('local')->exists($book->cover_path)) {
            Storage::disk('local')->delete($book->cover_path);
        }

        foreach ($book->files as $file) {
            if (Storage::disk('local')->exists($file->file_path)) {
                Storage::disk('local')->delete($file->file_path);
            }
        }

        $book->authors()->detach();
        $book->genres()->detach();
        $book->files()->delete();
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно удалена'
        ]);
    }

    private function formatBooksList($books)
    {
        return $books->map(fn($book) => [
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
            'publisher' => [
                'publisher_id' => $book->publisher->publisher_id,
                'publisher_name' => $book->publisher->publisher_name
            ],
            'files_count' => $book->files->count()
        ]);
    }

    private function formatBookDetail($book)
    {
        return [
            'book_id' => $book->book_id,
            'book_title' => $book->book_title,
            'description' => $book->description,
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
            'publisher' => [
                'publisher_id' => $book->publisher->publisher_id,
                'publisher_name' => $book->publisher->publisher_name
            ],
            'files' => $book->files->map(fn($file) => [
                'file_id' => $file->file_id,
                'format_id' => $file->format_id,
                'format_name' => $file->format->format_name,
                'file_size_bytes' => $file->file_size_bytes,
                'file_size_mb' => round($file->file_size_bytes / 1048576, 2),
                'read_url' => '/api/books/file/' . $file->file_id . '/read',
                'download_url' => '/api/books/file/' . $file->file_id . '/download'
            ]),
        ];
    }
}
