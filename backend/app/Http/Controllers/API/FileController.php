<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BookFile;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Получить обложку книги (доступно всем)
     */
    public function getCover($filename)
    {
        $path = 'covers/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Обложка не найдена'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $file = Storage::disk('local')->get($path);
        $mime = Storage::disk('local')->mimeType($path);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Cache-Control', 'public, max-age=86400');
    }

    /**
     * Читать файл книги в браузере (только авторизованные)
     */
    public function readFile($fileId)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для чтения книги необходимо авторизоваться'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $bookFile = BookFile::with('book')->find($fileId);

        if (!$bookFile) {
            return response()->json([
                'success' => false,
                'message' => 'Файл не найден'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $path = $bookFile->file_path;

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Файл отсутствует на сервере'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $file = Storage::disk('local')->get($path);
        $mime = Storage::disk('local')->mimeType($path);
        $filename = $bookFile->book->book_title . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Скачать файл книги (только авторизованные)
     */
    public function downloadFile($fileId)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Для скачивания книги необходимо авторизоваться'
            ], 401, [], JSON_UNESCAPED_UNICODE);
        }

        $bookFile = BookFile::with('book')->find($fileId);

        if (!$bookFile) {
            return response()->json([
                'success' => false,
                'message' => 'Файл не найден'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $path = $bookFile->file_path;

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Файл отсутствует на сервере'
            ], 404, [], JSON_UNESCAPED_UNICODE);
        }

        $filename = $bookFile->book->book_title . '.' . pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($path, $filename);
    }
}
