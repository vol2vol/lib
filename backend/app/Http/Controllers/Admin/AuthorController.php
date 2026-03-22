<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::withCount('books')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $authors
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $author = Author::create([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'birth_date' => $request->birth_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Автор успешно создан',
            'data' => $author
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        $author = Author::with('books')->find($id);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Автор не найден'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'success' => true,
            'data' => $author
        ]);
    }

    public function update(Request $request, $id)
    {
        $author = Author::find($id);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Автор не найден'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $validator = Validator::make($request->all(), [
            'last_name' => 'sometimes|string|max:255',
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $author->update($request->only([
            'last_name', 'first_name', 'middle_name', 'birth_date'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Автор успешно обновлен',
            'data' => $author
        ]);
    }

    public function destroy($id)
    {
        $author = Author::find($id);

        if (!$author) {
            return response()->json([
                'success' => false,
                'message' => 'Автор не найден'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        if ($author->books()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить автора, у которого есть книги'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $author->delete();

        return response()->json([
            'success' => true,
            'message' => 'Автор успешно удален'
        ]);
    }
}
