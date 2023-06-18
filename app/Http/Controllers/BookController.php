<?php

namespace App\Http\Controllers;

use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private BookService $bookService;

    public function __construct()
    {
        $this->bookService = new BookService();
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $this->bookService->setRelations(['authors', 'purchases']);
        $this->bookService->setBooks();

        $books = $this->bookService->getBooksPurchasedInCurrentMonth();

        $searchQuery = $request->query('query');

        if ($searchQuery) {
            $books = $this->bookService->applySearch($books, $searchQuery);
        } 
        
        try {
            $books = $this->bookService->getBooksSortedWithLimit($books, 'purchases_sum_copies', 'DESC')->get();
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

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

        $this->bookService->buyBook($bookId, $copies);

        return response()->json();
    }

    /**
     * @param Request $request
     * 
     * @return JsonResponse
     */
    public function top10(Request $request): JsonResponse
    {   
        $this->bookService->setRelations(['authors']);
        $this->bookService->setBooks();

        $books = $this->bookService->getPurchasesTotalSum($this->bookService->getBooks());

        try {
            $query = $this->bookService->getBooksSortedWithLimit($books, 'purchases_sum_copies', 'DESC', 10);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }

        $top10Books = $query->get();

        $searchQuery = $request->query('query');

        if ($searchQuery) {
            $top10Books = $this->bookService->applySearch($query, $searchQuery)
                ->get()
                ->filter(fn (Book $book) => $top10Books->contains($book));
        } 
        
        return response()->json(BookResource::collection($top10Books));
    }
}
