<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index()
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Для доступа к админ-панели необходимо авторизоваться'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет прав администратора для доступа к этому разделу'
                ], 403);
            }

            $books = Book::with([
                'genres:genre_id,genre_name',
                'authors:author_id,last_name,first_name,middle_name',
                'publisher:publisher_id,publisher_name',
                'files' => function($q) {
                    $q->with('format:format_id,format_name');
                }
            ])->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $books
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin books index error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера. Пожалуйста, попробуйте позже.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться для создания книги'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администраторы могут создавать книги'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'book_title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'published_year' => 'nullable|integer|min:1800|max:' . date('Y'),
                'cover_path' => 'nullable|string',
                'publisher_id' => 'nullable|exists:publishers,publisher_id',
                'author_ids' => 'required|array|min:1',
                'author_ids.*' => 'exists:authors,author_id',
                'genre_ids' => 'required|array|min:1',
                'genre_ids.*' => 'exists:genres,genre_id',
                'files' => 'nullable|array',
                'files.*.format_id' => 'required_with:files|exists:formats,format_id',
                'files.*.file_path' => 'required_with:files|string',
                'files.*.file_size_bytes' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации данных',
                    'errors' => $validator->errors()
                ], 422);
            }

            $book = Book::create([
                'book_title' => $request->book_title,
                'description' => $request->description,
                'published_year' => $request->published_year,
                'cover_path' => $request->cover_path,
                'publisher_id' => $request->publisher_id,
            ]);

            if ($request->has('author_ids')) {
                $book->authors()->attach($request->author_ids);
            }

            if ($request->has('genre_ids')) {
                $book->genres()->attach($request->genre_ids);
            }

            if ($request->has('files')) {
                foreach ($request->files as $fileData) {
                    $book->files()->create([
                        'format_id' => $fileData['format_id'],
                        'file_path' => $fileData['file_path'],
                        'file_size_bytes' => $fileData['file_size_bytes'] ?? null,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Книга успешно создана',
                'data' => $book->load(['authors', 'genres', 'publisher', 'files.format'])
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error creating book: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка базы данных при создании книги',
                'detail' => 'Проверьте правильность заполнения всех обязательных полей'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Error creating book: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Не удалось создать книгу. Пожалуйста, попробуйте позже.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function show(string $id)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться для просмотра книги'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администраторы могут просматривать детали книги'
                ], 403);
            }

            $book = Book::with([
                'genres',
                'authors',
                'publisher',
                'files.format'
            ])->find($id);

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Книга с указанным ID не найдена'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $book
            ]);

        } catch (\Exception $e) {
            \Log::error('Error showing book: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении данных книги',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться для обновления книги'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администраторы могут обновлять книги'
                ], 403);
            }

            $book = Book::find($id);

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Книга с указанным ID не найдена'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'book_title' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'published_year' => 'nullable|integer|min:1800|max:' . date('Y'),
                'cover_path' => 'nullable|string',
                'publisher_id' => 'nullable|exists:publishers,publisher_id',
                'author_ids' => 'sometimes|array|min:1',
                'author_ids.*' => 'exists:authors,author_id',
                'genre_ids' => 'sometimes|array|min:1',
                'genre_ids.*' => 'exists:genres,genre_id',
                'files' => 'nullable|array',
                'files.*.file_id' => 'sometimes|integer|exists:book_files,file_id',
                'files.*.format_id' => 'required_with:files|exists:formats,format_id',
                'files.*.file_path' => 'required_with:files|string',
                'files.*.file_size_bytes' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации данных',
                    'errors' => $validator->errors()
                ], 422);
            }

            $book->update($request->only([
                'book_title', 'description', 'published_year',
                'cover_path', 'publisher_id'
            ]));

            if ($request->has('author_ids')) {
                $book->authors()->sync($request->author_ids);
            }

            if ($request->has('genre_ids')) {
                $book->genres()->sync($request->genre_ids);
            }

            if ($request->has('files')) {
                $existingFileIds = $book->files()->pluck('file_id')->toArray();
                $newFileIds = [];

                foreach ($request->files as $fileData) {
                    if (isset($fileData['file_id'])) {
                        $file = BookFile::find($fileData['file_id']);
                        if ($file) {
                            $file->update([
                                'format_id' => $fileData['format_id'],
                                'file_path' => $fileData['file_path'],
                                'file_size_bytes' => $fileData['file_size_bytes'] ?? $file->file_size_bytes,
                            ]);
                            $newFileIds[] = $fileData['file_id'];
                        }
                    } else {
                        $newFile = $book->files()->create([
                            'format_id' => $fileData['format_id'],
                            'file_path' => $fileData['file_path'],
                            'file_size_bytes' => $fileData['file_size_bytes'] ?? null,
                        ]);
                        $newFileIds[] = $newFile->file_id;
                    }
                }

                $filesToDelete = array_diff($existingFileIds, $newFileIds);
                if (!empty($filesToDelete)) {
                    BookFile::whereIn('file_id', $filesToDelete)->delete();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Книга успешно обновлена',
                'data' => $book->load(['authors', 'genres', 'publisher', 'files.format'])
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error updating book: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка базы данных при обновлении книги'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Error updating book: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Не удалось обновить книгу. Пожалуйста, попробуйте позже.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться для удаления книги'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администраторы могут удалять книги'
                ], 403);
            }

            $book = Book::find($id);

            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'Книга с указанным ID не найдена'
                ], 404);
            }

            $book->authors()->detach();
            $book->genres()->detach();
            $book->files()->delete();
            $book->delete();

            return response()->json([
                'success' => true,
                'message' => 'Книга успешно удалена'
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error deleting book: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка базы данных при удалении книги'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Error deleting book: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Не удалось удалить книгу. Пожалуйста, попробуйте позже.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
