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
    public function index(Request $request): JsonResponse
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        $books = Book::with('authors')
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
        
        $books = $books->orderByDesc('purchases_sum_copies')->get();

        return response()->json(BookResource::collection($books));
    }
}
