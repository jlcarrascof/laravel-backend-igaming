<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    /**
     * Obtener todas las categorías con conteo de libros.
     */
    public function getAllCategories(): Collection
    {
        return Category::withCount('books')->orderBy('name')->get();
    }

    /**
     * Crear una categoría.
     */
    public function createCategory(array $data): Category
    {
        return Category::create([
            'name' => trim($data['name']),
        ]);
    }

    /**
     * Eliminar categoría verificando que no tenga libros asociados.
     * Regla de negocio: Verificar que una categoría no tenga libros asociados antes de permitir su eliminación.
     */
    public function deleteCategory(int $id): bool
    {
        $category = Category::withCount('books')->findOrFail($id);

        if ($category->books_count > 0) {
            throw ValidationException::withMessages([
                'category' => ["No se puede eliminar la categoría '{$category->name}' porque tiene {$category->books_count} libro(s) asociado(s)."],
            ]);
        }

        return (bool) $category->delete();
    }
}
