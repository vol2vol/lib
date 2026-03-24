<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Format;
use Illuminate\Http\Request;

class FormatController extends Controller
{
    public function index()
    {
        return response()->json(Format::all());
    }

    public function show($id)
    {
        $format = Format::with('books')->findOrFail($id);
        return response()->json($format);
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
