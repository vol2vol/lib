<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Publisher;
use Illuminate\Http\Request;

class PublisherController extends Controller
{
    public function index()
    {
        return response()->json(Publisher::all());
    }

    public function show($id)
    {
        $publisher = Publisher::with('books')->findOrFail($id);
        return response()->json($publisher);
    }
}
