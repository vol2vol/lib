<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администраторы могут просматривать жанры'
                ], 403);
            }

            $genres = Genre::withCount('books')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $genres
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения списка жанров',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администраторы могут создавать жанры'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'genre_name' => 'required|string|max:255|unique:genres',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            $genre = Genre::create([
                'genre_name' => $request->genre_name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Жанр успешно создан',
                'data' => $genre
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании жанра',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администраторы могут просматривать жанры'
                ], 403);
            }

            $genre = Genre::with('books')->find($id);

            if (!$genre) {
                return response()->json([
                    'success' => false,
                    'message' => 'Жанр не найден'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $genre
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения жанра',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администраторы могут обновлять жанры'
                ], 403);
            }

            $genre = Genre::find($id);

            if (!$genre) {
                return response()->json([
                    'success' => false,
                    'message' => 'Жанр не найден'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'genre_name' => 'sometimes|string|max:255|unique:genres,genre_name,' . $id . ',genre_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            $genre->update($request->only('genre_name'));

            return response()->json([
                'success' => true,
                'message' => 'Жанр успешно обновлен',
                'data' => $genre
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении жанра',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо авторизоваться'
                ], 401);
            }

            if (auth()->user()->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администраторы могут удалять жанры'
                ], 403);
            }

            $genre = Genre::find($id);

            if (!$genre) {
                return response()->json([
                    'success' => false,
                    'message' => 'Жанр не найден'
                ], 404);
            }

            // Проверяем, есть ли книги в этом жанре
            if ($genre->books()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить жанр, в котором есть книги'
                ], 422);
            }

            $genre->delete();

            return response()->json([
                'success' => true,
                'message' => 'Жанр успешно удален'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении жанра',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
