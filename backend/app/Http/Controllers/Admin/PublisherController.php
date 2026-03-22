<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
{
    public function index()
    {
        $publishers = Publisher::withCount('books')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $publishers
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publisher_name' => 'required|string|max:255|unique:publishers',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $publisher = Publisher::create([
            'publisher_name' => $request->publisher_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Издательство успешно создано',
            'data' => $publisher
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        $publisher = Publisher::with('books')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $publisher
        ]);
    }

    public function update(Request $request, $id)
    {
        $publisher = Publisher::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'publisher_name' => 'sometimes|string|max:255|unique:publishers,publisher_name,' . $id . ',publisher_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $publisher->update($request->only('publisher_name'));

        return response()->json([
            'success' => true,
            'message' => 'Издательство успешно обновлено',
            'data' => $publisher
        ]);
    }

    public function destroy($id)
    {
        $publisher = Publisher::findOrFail($id);

        if ($publisher->books()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить издательство, у которого есть книги'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $publisher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Издательство успешно удалено'
        ]);
    }
}
