<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        $books = Book::with(['authors', 'purchases'])
            ->whereHas('purchases', function (Builder $query) use ($currentMonthStart, $currentMonthEnd) {
                $query->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
            })
            ->withSum(['purchases' => function ($query) use ($currentMonthStart, $currentMonthEnd) {
                $query->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd]);
            }], 'copies');

        $searchQuery = $request->query('query');

        if ($searchQuery) {
            $books = $books
                ->where('name', 'LIKE', "%{$searchQuery}%")
                ->orWhereHas('authors', function (Builder $query) use ($searchQuery) {
                    $query->whereRaw("CONCAT(name, ' ', surname) LIKE ?", ["%{$searchQuery}%"]);
                });
        } 
        
        $books = $books
            ->orderByDesc('purchases_sum_copies')
            ->get();

        return response()->json(BookResource::collection($books));
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function buy(Request $request): JsonResponse
    {   
        $bookId = (int) $request->query('bookId');
        $copies = (int) $request->query('copies');

        $book = Book::findOrFail($bookId);
        $book->purchases()->create([
            'copies' => $copies,
        ]);

        return response()->json();
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function top10(Request $request): JsonResponse
    {   
        $query = Book::with('authors')
            ->withSum('purchases', 'copies')
            ->orderByDesc('purchases_sum_copies')
            ->limit(10);

        $books = $query->get();

        $searchQuery = $request->query('query');

        if ($searchQuery) {
            $books = $query
                ->where('name', 'LIKE', "%{$searchQuery}%")
                ->orWhereHas('authors', function (Builder $query) use ($searchQuery) {
                    $query->whereRaw("CONCAT(name, ' ', surname) LIKE ?", ["%{$searchQuery}%"]);
                })
                ->get()
                ->filter(function (Book $book) use ($books) {
                    return $books->contains($book);
                });
        } 
        
        return response()->json(BookResource::collection($books));
    }
}
