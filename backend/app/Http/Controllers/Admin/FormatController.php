<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Format;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormatController extends Controller
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
                    'message' => 'Только администраторы могут просматривать форматы'
                ], 403);
            }

            $formats = Format::withCount('books')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $formats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения списка форматов',
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
                    'message' => 'Только администраторы могут создавать форматы'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'format_name' => 'required|string|max:255|unique:formats',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            $format = Format::create([
                'format_name' => $request->format_name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Формат успешно создан',
                'data' => $format
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании формата',
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
                    'message' => 'Только администраторы могут просматривать форматы'
                ], 403);
            }

            $format = Format::with('books')->find($id);

            if (!$format) {
                return response()->json([
                    'success' => false,
                    'message' => 'Формат не найден'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $format
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения формата',
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
                    'message' => 'Только администраторы могут обновлять форматы'
                ], 403);
            }

            $format = Format::find($id);

            if (!$format) {
                return response()->json([
                    'success' => false,
                    'message' => 'Формат не найден'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'format_name' => 'sometimes|string|max:255|unique:formats,format_name,' . $id . ',format_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            $format->update($request->only('format_name'));

            return response()->json([
                'success' => true,
                'message' => 'Формат успешно обновлен',
                'data' => $format
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении формата',
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
                    'message' => 'Только администраторы могут удалять форматы'
                ], 403);
            }

            $format = Format::find($id);

            if (!$format) {
                return response()->json([
                    'success' => false,
                    'message' => 'Формат не найден'
                ], 404);
            }

            if ($format->books()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить формат, в котором есть книги'
                ], 422);
            }

            $format->delete();

            return response()->json([
                'success' => true,
                'message' => 'Формат успешно удален'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении формата',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function books($id, Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Необходимо авторизоваться'], 401);
        }

        if (auth()->user()->role_id != 1) {
            return response()->json(['success' => false, 'message' => 'Только администраторы'], 403);
        }

        $genre = Genre::findOrFail($id);

        $books = $genre->books()
            ->with(['authors', 'publisher', 'files.format'])
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'genre' => $genre->genre_name,
            'data' => $books
        ]);
    }
}
