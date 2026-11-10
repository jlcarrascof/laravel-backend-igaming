<?php

namespace App\Services;

use App\Models\Note;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NoteService
{
    /**
     * Obtener listado de notas paginado.
     */
    public function getPaginatedNotes(int $perPage = 10): LengthAwarePaginator
    {
        return Note::query()
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Crear una nueva nota.
     */
    public function createNote(array $data): Note
    {
        return Note::create([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
    }
}
