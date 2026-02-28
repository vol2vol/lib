<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
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
                    'message' => 'Только администраторы могут просматривать издательства'
                ], 403);
            }

            $publishers = Publisher::withCount('books')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $publishers
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения списка издательств',
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
                    'message' => 'Только администраторы могут создавать издательства'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'publisher_name' => 'required|string|max:255|unique:publishers',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            $publisher = Publisher::create([
                'publisher_name' => $request->publisher_name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Издательство успешно создано',
                'data' => $publisher
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании издательства',
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
                    'message' => 'Только администраторы могут просматривать издательства'
                ], 403);
            }

            $publisher = Publisher::with('books')->find($id);

            if (!$publisher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Издательство не найдено'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $publisher
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка получения издательства',
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
                    'message' => 'Только администраторы могут обновлять издательства'
                ], 403);
            }

            $publisher = Publisher::find($id);

            if (!$publisher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Издательство не найдено'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'publisher_name' => 'sometimes|string|max:255|unique:publishers,publisher_name,' . $id . ',publisher_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации',
                    'errors' => $validator->errors()
                ], 422);
            }

            $publisher->update($request->only('publisher_name'));

            return response()->json([
                'success' => true,
                'message' => 'Издательство успешно обновлено',
                'data' => $publisher
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении издательства',
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
                    'message' => 'Только администраторы могут удалять издательства'
                ], 403);
            }

            $publisher = Publisher::find($id);

            if (!$publisher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Издательство не найдено'
                ], 404);
            }

            // Проверяем, есть ли книги у этого издательства
            if ($publisher->books()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя удалить издательство, у которого есть книги'
                ], 422);
            }

            $publisher->delete();

            return response()->json([
                'success' => true,
                'message' => 'Издательство успешно удалено'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении издательства',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
