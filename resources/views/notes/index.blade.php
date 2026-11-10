@extends('layouts.app')

@section('title', 'Gestión de Notas (Demo API)')

@section('content')
<div class="row">
    <div class="col-lg-5 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Nueva Nota</h5>
            </div>
            <div class="card-body">
                <div id="alert-container"></div>

                <form id="note-form">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Ej: Nota de arquitectura">
                        <div class="invalid-feedback" id="error-title"></div>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenido</label>
                        <textarea class="form-control" id="content" name="content" rows="4" placeholder="Escribe el contenido aquí..."></textarea>
                        <div class="invalid-feedback" id="error-content"></div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" id="btn-submit">
                        <span class="spinner-border spinner-border-sm d-none" id="btn-spinner"></span>
                        Guardar Nota
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Listado de Notas (API)</h5>
                <button class="btn btn-sm btn-outline-light" id="btn-reload">Recargar</button>
            </div>
            <div class="card-body">
                <div id="notes-loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Cargando notas desde /api/v1/notes...</p>
                </div>
                <div id="notes-list" class="list-group list-group-flush d-none"></div>
                <div id="notes-empty" class="alert alert-info d-none mb-0">No hay notas registradas.</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const $form = $('#note-form');
    const $alert = $('#alert-container');
    const $btn = $('#btn-submit');
    const $spinner = $('#btn-spinner');
    const $list = $('#notes-list');
    const $empty = $('#notes-empty');
    const $loading = $('#notes-loading');

    function loadNotes() {
        $loading.removeClass('d-none');
        $list.addClass('d-none');
        $empty.addClass('d-none');

        $.ajax({
            url: '/api/v1/notes',
            type: 'GET',
            success: function (res) {
                $loading.addClass('d-none');
                $list.empty();

                if (!res.data || res.data.length === 0) {
                    $empty.removeClass('d-none');
                    return;
                }

                res.data.forEach(function (note) {
                    const item = `
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 fw-bold">${$('<div>').text(note.title).html()}</h6>
                                <small class="text-muted">#${note.id}</small>
                            </div>
                            <p class="mb-1 text-secondary">${$('<div>').text(note.content).html()}</p>
                            <small class="text-muted">${note.created_at ? new Date(note.created_at).toLocaleString() : ''}</small>
                        </div>
                    `;
                    $list.append(item);
                });

                $list.removeClass('d-none');
            },
            error: function () {
                $loading.addClass('d-none');
                $alert.html('<div class="alert alert-danger">Error al cargar las notas desde el servidor.</div>');
            }
        });
    }

    function clearErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $alert.empty();
    }

    $form.on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');

        const payload = {
            title: $('#title').val(),
            content: $('#content').val()
        };

        $.ajax({
            url: '/api/v1/notes',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function () {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $form[0].reset();
                $alert.html('<div class="alert alert-success alert-dismissible fade show">¡Nota creada exitosamente!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                loadNotes();
            },
            error: function (xhr) {
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.title) {
                        $('#title').addClass('is-invalid');
                        $('#error-title').text(errors.title[0]);
                    }
                    if (errors.content) {
                        $('#content').addClass('is-invalid');
                        $('#error-content').text(errors.content[0]);
                    }
                } else {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Ocurrió un error inesperado.';
                    $alert.html(`<div class="alert alert-danger">${msg}</div>`);
                }
            }
        });
    });

    $('#btn-reload').on('click', loadNotes);

    loadNotes();
});
</script>
@endpush

