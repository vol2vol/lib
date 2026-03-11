<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Book;

class FavoriteBookSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        foreach ($users as $user) {
            $randomBooks = $books->random(rand(3, 8));

            foreach ($randomBooks as $book) {
                DB::table('favorite_books')->insert([
                    'user_id' => $user->user_id,
                    'book_id' => $book->book_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
