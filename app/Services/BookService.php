<?php

namespace App\Services;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;

class BookService
{
    /**
     * @param array<int,string> $relations
     * @param Builder|null $books
     */
    public function __construct(private array $relations = [], private ?Builder $books = null) {}

    /**
     * @param array<int,string> $relations
     * 
     * @return void
     */
    public function setRelations(array $relations): void
    {
        $this->relations = $relations;
    }

    /**
     * @return array<int,string>
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @return void
     */
    public function setBooks(): void
    {
        $this->books = Book::with($this->relations);
    }

    /**
     * @return Builder|null
     */
    public function getBooks(): ?Builder
    {
        return $this->books;
    }

    /**
     * @return Builder
     */
    public function getBooksPurchasedInCurrentMonth(): Builder
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        return $this->getBooksPurchasedInPeriod($currentMonthStart, $currentMonthEnd);
    }

    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param bool $getSum
     * 
     * @return Builder
     */
    public function getBooksPurchasedInPeriod(Carbon $start, Carbon $end, bool $getSum = true): Builder
    {
        $books = $this->books
            ->whereHas('purchases', fn (Builder $query) => $query->purchasedInPeriod($start, $end));

        if ($getSum) {
            $books = $this->getPurchasesSumInPeriod($books, $start, $end);
        }

        return $books;
    }

    /**
     * @param Builder $books
     * @param Carbon $start
     * @param Carbon $end
     * 
     * @return Builder
     */
    private function getPurchasesSumInPeriod(Builder $books, Carbon $start, Carbon $end): Builder
    {
        return $books
            ->withSum(['purchases' => fn (Builder $query) => $query->purchasedInPeriod($start, $end)], 'copies');
    }

    /**
     * @param Builder $books
     * 
     * @return Builder
     */
    public function getPurchasesTotalSum(Builder $books): Builder
    {
        return $books->withSum('purchases', 'copies');
    }

    /**
     * @param Builder $books
     * @param string $searchQuery
     * 
     * @return Builder
     */
    public function applySearch(Builder $books, string $searchQuery): Builder
    {
        return $books
            ->where('name', 'LIKE', "%{$searchQuery}%")
            ->orWhereHas('authors', fn (Builder $query) => $query->whereRaw("CONCAT(name, ' ', surname) LIKE ?", ["%{$searchQuery}%"]));
    }

    /**
     * @param Builder $books
     * @param string $sortBy
     * @param string $sortOrder
     * @param int|null $limit
     * 
     * @throws \InvalidArgumentException
     * 
     * @return Builder
     */
    public function getBooksSortedWithLimit(Builder $books, string $sortBy, string $sortOrder = 'ASC', ?int $limit = null): Builder
    {
        $books = match ($sortOrder) {
            'ASC'   => $books->orderBy($sortBy),
            'DESC'  => $books->orderByDesc($sortBy),
            default => throw new \InvalidArgumentException('Invalid sort order!'),
        };
        
        if ($limit) {
            $books = $books->limit($limit);
        }

        return $books;
    }

    /**
     * @param int $bookId
     * @param int $copies
     * 
     * @return Book
     */
    public function buyBook(int $bookId, int $copies): Book
    {
        $book = Book::findOrFail($bookId);
        $book->purchases()->create([
            'copies' => $copies,
        ]);

        return $book;
    }
}
