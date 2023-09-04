<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // \App\Models\Book::truncate();
        // $booksJson = file_get_contents(database_path('seeders/books.json'));
        // $books = json_decode($booksJson, true);

        // foreach ($books as $book) {
        //     \App\Models\Book::factory()->create([
        //         'title' => $book['name'],
        //         'author' => $book['author'] ?? null,
        //     ]);
        // }

        Book::factory(10)->create();
    }
}
