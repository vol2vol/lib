<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['format_id']);

            $table->dropColumn(['format_id', 'file_path', 'file_size_bytes']);
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('books', function (Blueprint $table) {
            $table->unsignedBigInteger('format_id')->nullable()->after('cover_path');
            $table->string('file_path')->nullable()->after('format_id');
            $table->integer('file_size_bytes')->nullable()->after('file_path');

            $table->foreign('format_id')
                  ->references('format_id')
                  ->on('formats')
                  ->onDelete('set null');
        });

        Schema::enableForeignKeyConstraints();
    }
};
