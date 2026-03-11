<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_files', function (Blueprint $table) {
            $table->id('file_id');
            $table->unsignedBigInteger('book_id');
            $table->unsignedBigInteger('format_id');
            $table->string('file_path');
            $table->integer('file_size_bytes')->nullable();
            $table->timestamps();

            $table->unique(['book_id', 'format_id']);

            $table->foreign('book_id')
                  ->references('book_id')
                  ->on('books')
                  ->onDelete('cascade');

            $table->foreign('format_id')
                  ->references('format_id')
                  ->on('formats')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_files');
    }
};
