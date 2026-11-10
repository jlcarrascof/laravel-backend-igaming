<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BookService
{
    /**
     * Listar libros con opción de filtrado por categoría y paginación.
     */
    public function getBooks(?int $categoryId = null, int $perPage = 8): LengthAwarePaginator
    {
        return Book::query()
            ->with('category')
            ->when($categoryId, function (Builder $query, int $catId) {
                $query->where('category_id', $catId);
            })
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Obtener un libro por ID verificando su existencia.
     */
    public function findBookOrFail(int $id): Book
    {
        return Book::with('category')->findOrFail($id);
    }

    /**
     * Crear un nuevo libro.
     */
    public function createBook(array $data): Book
    {
        return DB::transaction(function () use ($data) {
            $book = Book::create($data);

            return $book->load('category');
        });
    }

    /**
     * Actualizar la información de un libro existente.
     */
    public function updateBook(int $id, array $data): Book
    {
        return DB::transaction(function () use ($id, $data) {
            $book = Book::findOrFail($id);
            $book->update($data);

            return $book->fresh('category');
        });
    }

    /**
     * Eliminar un libro verificando previamente su existencia.
     * Regla de negocio: Verificar la existencia del libro antes de proceder con su eliminación.
     */
    public function deleteBook(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $book = Book::findOrFail($id);

            return (bool) $book->delete();
        });
    }
}
