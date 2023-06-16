<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Seeder;

class AuthorBookSeeder extends Seeder
{
    public function run(): void
    {
        $allAuthors = Author::all();
        $allBooks = Book::all();

        foreach ($allBooks as $idx => $book) {
            if ($idx === $allBooks->count() - 1) {
                break;
            }
            
            $authors = $allAuthors->random(2)->pluck('id')->toArray();

            if ($idx % 2 === 0) {
                $firstAuthorId = $authors[0] ?? null;
                $secondAuthorId = $authors[1] ?? null;

                if ($firstAuthorId === null || $secondAuthorId === null) {
                    throw new ModelNotFoundException("There is not enough authors or there are no authors to attach to book with id: {$book->id}.");
                }

                $book->authors()->attach([$firstAuthorId, $secondAuthorId]);
            
                continue;
            }

            $authorId = $authors[0] ?? null;
            
            if ($authorId === null) {
                throw new ModelNotFoundException("There are no authors to attach to book with id: {$book->id}.");
            }

            $book->authors()->attach($authorId);
        }
    }
}
