<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Services\BookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function __construct(
        protected BookService $bookService
    ) {}

    /**
     * Listar todos los libros con soporte de filtro por categoría (?category={category_id}).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $categoryId = $request->query('category') ? (int) $request->query('category') : null;
        $perPage = (int) $request->query('per_page', 8);

        $books = $this->bookService->getBooks($categoryId, $perPage);

        return BookResource::collection($books);
    }

    /**
     * Obtener el detalle de un libro por ID.
     */
    public function show(int $id): BookResource
    {
        $book = $this->bookService->findBookOrFail($id);

        return new BookResource($book);
    }

    /**
     * Crear un nuevo libro en el sistema.
     */
    public function store(StoreBookRequest $request): JsonResponse
    {
        $book = $this->bookService->createBook($request->validated());

        return (new BookResource($book))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Actualizar la información de un libro.
     */
    public function update(UpdateBookRequest $request, int $id): BookResource
    {
        $book = $this->bookService->updateBook($id, $request->validated());

        return new BookResource($book);
    }

    /**
     * Eliminar un libro específico (verificando existencia previa).
     */
    public function destroy(int $id): JsonResponse
    {
        $this->bookService->deleteBook($id);

        return response()->json([
            'message' => 'Libro eliminado exitosamente.',
        ], 200);
    }
}
