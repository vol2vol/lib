<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Format;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormatController extends Controller
{
    public function index()
    {
        $formats = Format::withCount('books')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $formats
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'format_name' => 'required|string|max:255|unique:formats',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $format = Format::create([
            'format_name' => $request->format_name
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Формат успешно создан',
            'data' => $format
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        $format = Format::with('books')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $format
        ]);
    }

    public function update(Request $request, $id)
    {
        $format = Format::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'format_name' => 'sometimes|string|max:255|unique:formats,format_name,' . $id . ',format_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $validator->errors()
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $format->update($request->only('format_name'));

        return response()->json([
            'success' => true,
            'message' => 'Формат успешно обновлен',
            'data' => $format
        ]);
    }

    public function destroy($id)
    {
        $format = Format::findOrFail($id);

        if ($format->books()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя удалить формат, в котором есть книги'
            ], 422, [], JSON_UNESCAPED_UNICODE);
        }

        $format->delete();

        return response()->json([
            'success' => true,
            'message' => 'Формат успешно удален'
        ]);
    }

    public function books($id, Request $request)
    {
        $format = Format::findOrFail($id);

        $books = $format->books()
            ->with(['authors', 'publisher', 'genres'])
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'format' => $format->format_name,
            'books' => $books
        ]);
    }
}
