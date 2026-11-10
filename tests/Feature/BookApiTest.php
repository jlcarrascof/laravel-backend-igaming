<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_paginated_books(): void
    {
        $category = Category::factory()->create();
        Book::factory()->count(10)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'description',
                        'published_year',
                        'category_id',
                        'category',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(8, 'data')
            ->assertJsonPath('meta.total', 10);
    }

    public function test_can_filter_books_by_category(): void
    {
        $catSciFi = Category::factory()->create(['name' => 'Ciencia Ficción']);
        $catHistory = Category::factory()->create(['name' => 'Historia']);

        Book::factory()->count(3)->create(['category_id' => $catSciFi->id]);
        Book::factory()->count(2)->create(['category_id' => $catHistory->id]);

        $response = $this->getJson("/api/v1/books?category={$catSciFi->id}");

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 3);
    }

    public function test_can_create_a_book_with_valid_data(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'title' => 'The Pragmatic Programmer',
            'author' => 'Andrew Hunt & David Thomas',
            'description' => 'From journeyman to master.',
            'published_year' => 1999,
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'author', 'description', 'published_year', 'category_id', 'category'],
            ])
            ->assertJson([
                'data' => [
                    'title' => 'The Pragmatic Programmer',
                    'author' => 'Andrew Hunt & David Thomas',
                    'published_year' => 1999,
                    'category_id' => $category->id,
                ],
            ]);

        $this->assertDatabaseHas('books', [
            'title' => 'The Pragmatic Programmer',
            'author' => 'Andrew Hunt & David Thomas',
        ]);
    }

    public function test_cannot_create_book_with_invalid_data(): void
    {
        $response = $this->postJson('/api/v1/books', [
            'title' => 'ab',
            'author' => '',
            'published_year' => 3000,
            'category_id' => 9999,
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['title', 'author', 'published_year', 'category_id'],
            ]);
    }

    public function test_can_show_single_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $book->id,
                    'title' => $book->title,
                ],
            ]);
    }

    public function test_can_update_a_book(): void
    {
        $book = Book::factory()->create([
            'title' => 'Título Original',
            'published_year' => 2000,
        ]);

        $payload = [
            'title' => 'Título Actualizado',
            'published_year' => 2010,
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Título Actualizado',
                    'published_year' => 2010,
                ],
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Título Actualizado',
            'published_year' => 2010,
        ]);
    }

    public function test_can_delete_an_existing_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Libro eliminado exitosamente.',
            ]);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_returns_404_when_deleting_non_existent_book(): void
    {
        $response = $this->deleteJson('/api/v1/books/999999');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }
}

