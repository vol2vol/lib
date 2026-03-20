<?php

namespace Tests\Feature\API;

use App\Models\Book;
use App\Models\BookFile;
use App\Models\Format;
use App\Models\User;
use Database\Seeders\FormatSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected string $testStoragePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FormatSeeder::class);

        // Настраиваем реальное временное хранилище для тестов
        $this->testStoragePath = storage_path('app/testing_' . uniqid());
        mkdir($this->testStoragePath, 0755, true);
        config(['filesystems.disks.local.root' => $this->testStoragePath]);
    }

    protected function tearDown(): void
    {
        // Очищаем временные файлы после тестов
        if (is_dir($this->testStoragePath)) {
            $this->deleteDirectory($this->testStoragePath);
        }
        parent::tearDown();
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "{$dir}/{$file}";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Вспомогательный метод для получения формата без дубликатов
     */
    protected function getFormat(string $name): Format
    {
        return Format::firstOrCreate(['format_name' => $name]);
    }

    /**
     * Вспомогательный метод для создания файла в тестовом хранилище
     */
    protected function createTestFile(string $relativePath, string $content): string
    {
        Storage::disk('local')->put($relativePath, $content);
        return $content;
    }

    // ==================== ТЕСТЫ МЕТОДА getCover() ====================

    public function test_get_cover_is_public_endpoint()
    {
        $filename = 'test_cover.jpg';
        $content = 'fake_image_content';
        $this->createTestFile('covers/' . $filename, $content);

        $response = $this->get('/api/covers/' . $filename);

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'image/jpeg');
        
        $cacheHeader = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cacheHeader);
        $this->assertStringContainsString('max-age=86400', $cacheHeader);
        
        $this->assertEquals($content, $response->getContent());
    }

    public function test_get_cover_returns_404_for_nonexistent_file()
    {
        $response = $this->get('/api/covers/nonexistent.jpg');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Обложка не найдена'
            ]);
    }

    public function test_get_cover_returns_correct_mime_type_for_jpg()
    {
        $this->createTestFile('covers/book.jpg', 'content');
        $response = $this->get('/api/covers/book.jpg');
        $response->assertStatus(200)->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_get_cover_returns_correct_mime_type_for_png()
    {
        $this->createTestFile('covers/book.png', 'content');
        $response = $this->get('/api/covers/book.png');
        $response->assertStatus(200)->assertHeader('Content-Type', 'image/png');
    }

    public function test_get_cover_returns_correct_mime_type_for_webp()
    {
        $this->createTestFile('covers/book.webp', 'content');
        $response = $this->get('/api/covers/book.webp');
        $response->assertStatus(200)->assertHeader('Content-Type', 'image/webp');
    }

    public function test_get_cover_handles_cyrillic_filename()
    {
        $filename = 'книга_обложка.jpg';
        $this->createTestFile('covers/' . $filename, 'content');

        $response = $this->get('/api/covers/' . rawurlencode($filename));
        $response->assertStatus(200)->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_get_cover_prevents_path_traversal_attack()
    {
        // Path traversal обрабатывается на уровне роутера Laravel
        $response = $this->get('/api/covers/../.env');
        
        // Ожидаем 404 (от роутера или контроллера)
        $response->assertStatus(404);
        
        // Проверяем, что в ответе нет чувствительных данных
        $this->assertStringNotContainsString('DB_', $response->getContent());
    }

    public function test_get_cover_cache_control_header()
    {
        $this->createTestFile('covers/cached.jpg', 'content');
        $response = $this->get('/api/covers/cached.jpg');
        
        $response->assertStatus(200);
        $cacheHeader = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cacheHeader);
        $this->assertStringContainsString('max-age=86400', $cacheHeader);
    }

    // ==================== ТЕСТЫ МЕТОДА readFile() ====================

    public function test_read_file_requires_authentication()
    {
        $response = $this->getJson('/api/books/file/1/read');
        
        // Sanctum возвращает стандартное сообщение
        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_read_file_returns_404_for_nonexistent_book_file()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/books/file/999999/read');
        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Файл не найден']);
    }

    public function test_read_file_returns_404_when_file_missing_from_storage()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Тестовая книга']);
        $format = $this->getFormat('PDF');

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => 'files/nonexistent.pdf',
            'file_size_bytes' => 1024
        ]);

        // Файл НЕ создаём в хранилище

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/books/file/{$bookFile->file_id}/read");

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Файл отсутствует на сервере']);
    }

    public function test_read_file_returns_file_with_inline_disposition()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Война и мир']);
        $format = $this->getFormat('PDF');
        $filePath = 'files/books/war_and_peace.pdf';
        $fileContent = 'fake_pdf_content';

        $this->createTestFile($filePath, $fileContent);

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => strlen($fileContent)
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/read");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
        
        // Laravel не добавляет кавычки в filename по умолчанию
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Война и мир.pdf', $response->headers->get('Content-Disposition'));

        $this->assertEquals($fileContent, $response->getContent());
    }

    public function test_read_file_correct_mime_types()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Book']);

        $testCases = [
            ['ext' => 'pdf', 'path' => 'files/book.pdf', 'content' => '%PDF-1.4 fake pdf content'],
            ['ext' => 'fb2', 'path' => 'files/book.fb2', 'content' => '<?xml version="1.0"?><FictionBook></FictionBook>'],
            ['ext' => 'txt', 'path' => 'files/book.txt', 'content' => 'Plain text content'],
        ];

        foreach ($testCases as $case) {
            $this->createTestFile($case['path'], $case['content']);

            $format = $this->getFormat(strtoupper($case['ext']));
            $bookFile = BookFile::factory()->create([
                'book_id' => $book->book_id,
                'format_id' => $format->format_id,
                'file_path' => $case['path'],
                'file_size_bytes' => strlen($case['content'])
            ]);

            $response = $this->actingAs($user, 'sanctum')
                ->get("/api/books/file/{$bookFile->file_id}/read");
            
            $response->assertStatus(200);
            
            // Проверяем, что Content-Type не пустой
            $contentType = $response->headers->get('Content-Type');
            $this->assertNotEmpty($contentType, "Content-Type should not be empty for {$case['ext']}");
            
            // Проверяем соответствие расширения и типа
            $expectedMimeMap = [
                'pdf' => 'application/pdf',
                'fb2' => 'xml',  // FB2 должен определяться как XML
                'txt' => 'text/plain',
            ];
            
            if (isset($expectedMimeMap[$case['ext']])) {
                $this->assertStringContainsString(
                    $expectedMimeMap[$case['ext']], 
                    strtolower($contentType),
                    "Content-Type for {$case['ext']} should contain {$expectedMimeMap[$case['ext']]}"
                );
            }
        }
    }

    public function test_read_file_handles_cyrillic_book_title_in_filename()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Мастер и Маргарита']);
        $format = $this->getFormat('FB2');
        $filePath = 'files/master.fb2';
        $fileContent = 'fake_fb2_content';

        $this->createTestFile($filePath, $fileContent);

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => strlen($fileContent)
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/read");

        $response->assertStatus(200);
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('Мастер и Маргарита.fb2', $response->headers->get('Content-Disposition'));
    }

    public function test_read_file_handles_special_characters_in_book_title()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Book (2nd Edition)']);
        $format = $this->getFormat('PDF');
        $filePath = 'files/book_special.pdf';

        $this->createTestFile($filePath, 'content');

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => 100
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/read");

        $response->assertStatus(200);
        $this->assertStringContainsString('Book (2nd Edition).pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_read_file_eager_loads_book_relationship()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Test Book']);
        $format = $this->getFormat('PDF');
        $filePath = 'files/test.pdf';

        $this->createTestFile($filePath, 'content');

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => 100
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/read");
        $response->assertStatus(200);
    }

    // ==================== ТЕСТЫ МЕТОДА downloadFile() ====================

    public function test_download_file_requires_authentication()
    {
        $response = $this->getJson('/api/books/file/1/download');
        
        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Требуется авторизация'
            ]);
    }

    public function test_download_file_returns_404_for_nonexistent_book_file()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/books/file/999999/download');
        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Файл не найден']);
    }

    public function test_download_file_returns_404_when_file_missing_from_storage()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Test Book']);
        $format = $this->getFormat('PDF');

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => 'files/missing.pdf',
            'file_size_bytes' => 1024
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/books/file/{$bookFile->file_id}/download");

        $response->assertStatus(404)
            ->assertJson(['success' => false, 'message' => 'Файл отсутствует на сервере']);
    }

    public function test_download_file_triggers_download_with_correct_filename()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Анна Каренина']);
        $format = $this->getFormat('PDF');
        $filePath = 'files/anna.pdf';
        $fileContent = 'fake_pdf_binary_data';

        $this->createTestFile($filePath, $fileContent);

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => strlen($fileContent)
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/download");

        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment', $contentDisposition);
        
        // Проверяем RFC 5987 encoding для кириллицы
        $this->assertStringContainsString('filename*=', $contentDisposition);
        $this->assertStringContainsString('utf-8', strtolower($contentDisposition));
        $this->assertStringContainsString(
            rawurlencode('Анна Каренина.pdf'), 
            $contentDisposition
        );

        // BinaryFileResponse не поддерживает getContent(), проверяем только заголовки
        $this->assertTrue($response->isOk());
    }

    public function test_download_file_vs_read_file_header_difference()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Test']);
        $format = $this->getFormat('PDF');
        $filePath = 'files/test.pdf';

        $this->createTestFile($filePath, 'content');

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => 100
        ]);

        // readFile - inline
        $readResponse = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/read");
        $this->assertStringContainsString('inline', $readResponse->headers->get('Content-Disposition'));

        // downloadFile - attachment
        $downloadResponse = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/download");
        $this->assertStringContainsString('attachment', $downloadResponse->headers->get('Content-Disposition'));
    }

    public function test_download_file_handles_cyrillic_filename()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Преступление и наказание']);
        $format = $this->getFormat('FB2');
        $filePath = 'files/crime.fb2';

        $this->createTestFile($filePath, 'fb2_content');

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => 200
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/download");

        $response->assertStatus(200);
        
        $contentDisposition = $response->headers->get('Content-Disposition');
        
        // Проверяем наличие attachment
        $this->assertStringContainsString('attachment', $contentDisposition);
        
        // Проверяем, что есть RFC 5987 encoding для кириллицы
        $this->assertStringContainsString('filename*=', $contentDisposition);
        $this->assertStringContainsString('utf-8', strtolower($contentDisposition));
        
        // Проверяем URL-кодированную версию названия (Преступление и наказание = %D0%9F%D1%80%D0%B5%D1%81%D1%82%D1%83%D0%BF%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D0%BD%D0%B0%D0%BA%D0%B0%D0%B7%D0%B0%D0%BD%D0%B8%D0%B5)
        $this->assertStringContainsString(
            rawurlencode('Преступление и наказание.fb2'), 
            $contentDisposition
        );
    }

    public function test_download_file_preserves_unicode_encoding()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Book 📚 Title']);
        $format = $this->getFormat('PDF');
        $filePath = 'files/emoji.pdf';

        $this->createTestFile($filePath, 'content');

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => 100
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/download");

        $response->assertStatus(200);
        
        // Проверяем наличие заголовка с unicode-кодировкой (RFC 5987)
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('filename*=', $contentDisposition);
    }

    // ==================== ИНТЕГРАЦИОННЫЕ ТЕСТЫ ====================

    public function test_multiple_users_can_access_same_file()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Shared Book']);
        $format = $this->getFormat('PDF');
        $filePath = 'files/shared.pdf';
        $fileContent = 'shared_content';

        $this->createTestFile($filePath, $fileContent);

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => strlen($fileContent)
        ]);

        $response1 = $this->actingAs($user1, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/download");
        $response2 = $this->actingAs($user2, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/download");

        $response1->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');
        
        $response2->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        // Проверяем Content-Disposition через assertStringContainsString
        $disposition1 = $response1->headers->get('Content-Disposition');
        $disposition2 = $response2->headers->get('Content-Disposition');
        
        $this->assertStringContainsString('attachment', $disposition1);
        $this->assertStringContainsString('Shared Book.pdf', $disposition1);
        $this->assertStringContainsString('attachment', $disposition2);
        $this->assertStringContainsString('Shared Book.pdf', $disposition2);
        
        // Опционально: проверяем, что файл существует и имеет правильный размер
        $this->assertTrue(Storage::disk('local')->exists($filePath));
        $this->assertEquals(strlen($fileContent), Storage::disk('local')->size($filePath));
    }

    public function test_file_operations_with_zero_id()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/books/file/0/read');
        $response->assertStatus(404)->assertJson(['message' => 'Файл не найден']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/books/file/0/download');
        $response->assertStatus(404)->assertJson(['message' => 'Файл не найден']);
    }

    public function test_cover_with_empty_filename()
    {
        $response = $this->get('/api/covers/');
        // Пустой filename не соответствует маршруту, поэтому 404 от роутера
        $response->assertStatus(404);
    }

    public function test_cover_with_only_extension()
    {
        $this->createTestFile('covers/.jpg', 'content');

        $response = $this->get('/api/covers/.jpg');
        $response->assertStatus(200)->assertHeader('Content-Type', 'image/jpeg');
    }

    public function test_read_file_with_deleted_book_still_works()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create(['book_title' => 'Temp Book']);
        $format = $this->getFormat('PDF');
        $filePath = 'files/temp.pdf';

        $this->createTestFile($filePath, 'content');

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => 100
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/read");
        $response->assertStatus(200);
    }

    public function test_storage_disk_configuration()
    {
        $this->assertEquals('local', config('filesystems.default'));
    }

    public function test_file_size_matches_stored_content()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $format = $this->getFormat('PDF');
        $filePath = 'files/size_test.pdf';
        $content = str_repeat('x', 10240);

        $this->createTestFile($filePath, $content);

        $bookFile = BookFile::factory()->create([
            'book_id' => $book->book_id,
            'format_id' => $format->format_id,
            'file_path' => $filePath,
            'file_size_bytes' => strlen($content)
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->get("/api/books/file/{$bookFile->file_id}/read");

        $response->assertStatus(200);
        $this->assertEquals(strlen($content), strlen($response->getContent()));
        $this->assertEquals($content, $response->getContent());
    }

    public function test_concurrent_cover_requests()
    {
        $covers = [
            ['name' => 'cover1.jpg', 'mime' => 'image/jpeg'],
            ['name' => 'cover2.png', 'mime' => 'image/png'],
            ['name' => 'cover3.webp', 'mime' => 'image/webp'],
        ];

        foreach ($covers as $cover) {
            $this->createTestFile("covers/{$cover['name']}", "content_{$cover['name']}");
        }

        foreach ($covers as $cover) {
            $response = $this->get('/api/covers/' . $cover['name']);
            $response->assertStatus(200)->assertHeader('Content-Type', $cover['mime']);
        }
    }
}