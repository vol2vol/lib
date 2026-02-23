<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id('book_id');
            $table->string('book_title');
            $table->text('description');

            $table->unsignedBigInteger('publisher_id');
            $table->unsignedBigInteger('format_id');

            $table->string('file_path');
            $table->bigInteger('file_size_bytes');
            $table->timestamps();

            $table->foreign('publisher_id')
                  ->references('publisher_id')
                  ->on('publishers')
                  ->onDelete('restrict');

            $table->foreign('format_id')
                  ->references('format_id')
                  ->on('formats')
                  ->onDelete('restrict');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
