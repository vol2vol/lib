<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with([
            'genres:genre_id,genre_name',
            'authors:author_id,last_name,first_name,middle_name',
            'publisher:publisher_id,publisher_name',
            'format:format_id,format_name'
        ])->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'published_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'file_path' => 'nullable|string',
            'file_size_bytes' => 'nullable|integer|min:0',
            'publisher_id' => 'nullable|exists:publishers,publisher_id',
            'format_id' => 'nullable|exists:formats,format_id',
            'author_ids' => 'required|array|min:1',
            'author_ids.*' => 'exists:authors,author_id',
            'genre_ids' => 'required|array|min:1',
            'genre_ids.*' => 'exists:genres,genre_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $book = Book::create([
            'book_title' => $request->book_title,
            'description' => $request->description,
            'published_year' => $request->published_year,
            'file_path' => $request->file_path,
            'file_size_bytes' => $request->file_size_bytes,
            'publisher_id' => $request->publisher_id,
            'format_id' => $request->format_id,
        ]);

        $book->authors()->attach($request->author_ids);

        $book->genres()->attach($request->genre_ids);

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно создана',
            'data' => $book->load(['authors', 'genres', 'publisher', 'format'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::with([
            'genres',
            'authors',
            'publisher',
            'format'
        ])->find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Книга не найдена'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $book
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Книга не найдена'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'book_title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'published_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'file_path' => 'nullable|string',
            'file_size_bytes' => 'nullable|integer|min:0',
            'publisher_id' => 'nullable|exists:publishers,publisher_id',
            'format_id' => 'nullable|exists:formats,format_id',
            'author_ids' => 'sometimes|array|min:1',
            'author_ids.*' => 'exists:authors,author_id',
            'genre_ids' => 'sometimes|array|min:1',
            'genre_ids.*' => 'exists:genres,genre_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $book->update($request->only([
            'book_title', 'description', 'published_year',
            'file_path', 'file_size_bytes', 'publisher_id', 'format_id'
        ]));

        if ($request->has('author_ids')) {
            $book->authors()->sync($request->author_ids);
        }

        if ($request->has('genre_ids')) {
            $book->genres()->sync($request->genre_ids);
        }

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно обновлена',
            'data' => $book->load(['authors', 'genres', 'publisher', 'format'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Книга не найдена'
            ], 404);
        }

        $book->authors()->detach();
        $book->genres()->detach();

        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Книга успешно удалена'
        ]);
    }
}
