<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookPurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $allBooks = Book::all();

        foreach ($allBooks as $book) {
            $timesPurchases = rand(1, 25);

            for ($i = 0; $i < $timesPurchases; $i++) {
                $book->purchases()->create([
                    'copies' => rand(1, 5),
                ]);
            }
        }
    }
}
