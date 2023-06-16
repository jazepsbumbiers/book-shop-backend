<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Book::factory(15)->create();
        Author::factory(15)->create();

        $this->call([
            AuthorBookSeeder::class,
            BookPurchaseSeeder::class,
        ]);
    }
}
