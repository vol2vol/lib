<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::withCount('books')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $genres
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'genre_name' => 'required|string|max:255|unique:genres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $genre = Genre::create([
            'genre_name' => $request->genre_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Жанр успешно создан',
            'data' => $genre
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        $genre = Genre::with('books')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $genre
        ]);
    }

    public function update(Request $request, $id)
    {
        $genre = Genre::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'genre_name' => 'sometimes|string|max:255|unique:genres,genre_name,' . $id . ',genre_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $genre->update($request->only('genre_name'));

        return response()->json([
            'success' => true,
            'message' => 'Жанр успешно обновлен',
            'data' => $genre
        ]);
    }

    public function destroy($id)
    {
        $genre = Genre::findOrFail($id);

        if ($genre->books()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить жанр, в котором есть книги'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $genre->delete();

        return response()->json([
            'success' => true,
            'message' => 'Жанр успешно удален'
        ]);
    }
}
