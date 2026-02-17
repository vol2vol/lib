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
}
