<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_categories(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'books_count', 'created_at', 'updated_at'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_a_category_with_unique_name(): void
    {
        $payload = ['name' => 'Inteligencia Artificial'];

        $response = $this->postJson('/api/v1/categories', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'Inteligencia Artificial',
                ],
            ]);

        $this->assertDatabaseHas('categories', ['name' => 'Inteligencia Artificial']);
    }

    public function test_cannot_create_duplicate_category_name(): void
    {
        Category::factory()->create(['name' => 'Matemáticas']);

        $response = $this->postJson('/api/v1/categories', ['name' => 'Matemáticas']);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['name'],
            ]);
    }

    public function test_can_delete_category_without_books(): void
    {
        $category = Category::factory()->create(['name' => 'Categoría Libre']);

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Categoría eliminada exitosamente.',
            ]);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_cannot_delete_category_with_associated_books(): void
    {
        $category = Category::factory()->hasBooks(2)->create(['name' => 'Con Libros']);

        $response = $this->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['category'],
            ]);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}

