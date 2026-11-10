<?php

namespace Tests\Feature;

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_paginated_notes(): void
    {
        Note::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/notes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'content', 'created_at', 'updated_at'],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_can_create_a_note_with_valid_data(): void
    {
        $payload = [
            'title' => 'Nota de prueba automatizada',
            'content' => 'Contenido completo para verificar persistencia.',
        ];

        $response = $this->postJson('/api/v1/notes', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'title', 'content', 'created_at', 'updated_at'],
            ])
            ->assertJson([
                'data' => [
                    'title' => 'Nota de prueba automatizada',
                    'content' => 'Contenido completo para verificar persistencia.',
                ],
            ]);

        $this->assertDatabaseHas('notes', [
            'title' => 'Nota de prueba automatizada',
        ]);
    }

    public function test_cannot_create_a_note_without_title_and_returns_422(): void
    {
        $payload = [
            'content' => 'Contenido sin titulo.',
        ];

        $response = $this->postJson('/api/v1/notes', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['title'],
            ]);
    }

    public function test_cannot_create_a_note_with_too_short_content(): void
    {
        $payload = [
            'title' => 'Título válido',
            'content' => 'ab',
        ];

        $response = $this->postJson('/api/v1/notes', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['content'],
            ]);
    }
}
