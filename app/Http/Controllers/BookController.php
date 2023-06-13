<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $books = Book::with('authors')
            ->withSum('purchases', 'copies')
            ->orderByDesc('purchases_sum_copies')
            ->get();

        return response()->json(BookResource::collection($books));
    }
}
