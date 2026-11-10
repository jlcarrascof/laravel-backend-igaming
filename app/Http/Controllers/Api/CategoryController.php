<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Listar todas las categorías disponibles.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = $this->categoryService->getAllCategories();

        return CategoryResource::collection($categories);
    }

    /**
     * Crear una nueva categoría.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return (new CategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Eliminar una categoría (valida que no tenga libros asociados).
     */
    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->deleteCategory($id);

        return response()->json([
            'message' => 'Categoría eliminada exitosamente.',
        ], 200);
    }
}
