<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Resources\NoteResource;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NoteController extends Controller
{
    public function __construct(
        protected NoteService $noteService
    ) {}

    /**
     * Listar notas paginadas.
     */
    public function index(): AnonymousResourceCollection
    {
        $notes = $this->noteService->getPaginatedNotes();

        return NoteResource::collection($notes);
    }

    /**
     * Crear una nueva nota.
     */
    public function store(StoreNoteRequest $request): JsonResponse
    {
        $note = $this->noteService->createNote($request->validated());

        return (new NoteResource($note))
            ->response()
            ->setStatusCode(201);
    }
}
