@extends('layouts.app')

@section('title', 'Gestión de Libros - Biblioteca')

@section('content')
<!-- Encabezado y Barra de Acciones -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h2 class="h3 fw-bold mb-1 text-dark">
            <i class="bi bi-journal-bookmark text-primary me-2"></i>Gestión de Libros
        </h2>
        <p class="text-muted mb-0">Listado, filtrado, creación, edición y eliminación de libros.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" id="btn-refresh">
            <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
        </button>
        <button class="btn btn-primary" id="btn-open-create-modal">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Libro
        </button>
    </div>
</div>

<!-- Alertas Globales -->
<div id="global-alert"></div>

<!-- Card de Filtros -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body bg-white rounded">
        <div class="row align-items-center g-3">
            <div class="col-md-5">
                <label for="filter-category" class="form-label fw-semibold small text-uppercase text-muted">
                    <i class="bi bi-funnel me-1"></i>Filtrar por Categoría:
                </label>
                <select class="form-select" id="filter-category">
                    <option value="">Todas las categorías</option>
                </select>
            </div>
            <div class="col-md-7 text-md-end pt-md-4">
                <span class="badge bg-light text-dark border px-3 py-2" id="books-count-badge">
                    Cargando libros...
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Libros -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div id="books-loading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted mb-0">Cargando biblioteca desde la API...</p>
        </div>

        <div id="books-empty" class="alert alert-info m-4 d-none text-center">
            <i class="bi bi-info-circle me-2"></i>No se encontraron libros para la categoría seleccionada.
        </div>

        <div class="table-responsive d-none" id="books-table-container">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Categoría</th>
                        <th style="width: 100px;">Año</th>
                        <th>Descripción</th>
                        <th style="width: 140px;" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="books-table-body"></tbody>
            </table>
        </div>

        <!-- Paginación Asíncrona (10 en 10) -->
        <div class="card-footer bg-white border-top-0 py-3 d-none" id="books-pagination-container">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="small text-muted" id="pagination-info">Mostrando registros</div>
                <nav aria-label="Navegación de libros">
                    <ul class="pagination pagination-sm mb-0" id="pagination-links"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Crear / Editar Libro -->
<div class="modal fade" id="bookModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookModalLabel">Nuevo Libro</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="book-form">
                <div class="modal-body">
                    <input type="hidden" id="book-id" name="id">

                    <div id="modal-alert"></div>

                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="book-title" class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="book-title" name="title" placeholder="Ej: Clean Code" required>
                            <div class="invalid-feedback" id="err-title"></div>
                        </div>

                        <div class="col-md-4">
                            <label for="book-year" class="form-label fw-semibold">Año de Publicación <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="book-year" name="published_year" placeholder="Ej: 2008" min="1000" max="{{ date('Y') }}" required>
                            <div class="invalid-feedback" id="err-published_year"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="book-author" class="form-label fw-semibold">Autor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="book-author" name="author" placeholder="Ej: Robert C. Martin" required>
                            <div class="invalid-feedback" id="err-author"></div>
                        </div>

                        <div class="col-md-6">
                            <label for="book-category" class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select" id="book-category" name="category_id" required>
                                <option value="">Selecciona una categoría...</option>
                            </select>
                            <div class="invalid-feedback" id="err-category_id"></div>
                        </div>

                        <div class="col-12">
                            <label for="book-description" class="form-label fw-semibold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="book-description" name="description" rows="3" placeholder="Sinopsis o detalles del libro..."></textarea>
                            <div class="invalid-feedback" id="err-description"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn-save-book">
                        <span class="spinner-border spinner-border-sm d-none me-1" id="save-spinner"></span>
                        <span id="save-text">Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const bookModal = new bootstrap.Modal(document.getElementById('bookModal'));
    const $filterCategory = $('#filter-category');
    const $bookCategory = $('#book-category');
    const $tableContainer = $('#books-table-container');
    const $tableBody = $('#books-table-body');
    const $loading = $('#books-loading');
    const $empty = $('#books-empty');
    const $countBadge = $('#books-count-badge');
    const $globalAlert = $('#global-alert');
    const $modalAlert = $('#modal-alert');
    const $form = $('#book-form');
    const $saveBtn = $('#btn-save-book');
    const $saveSpinner = $('#save-spinner');
    const $saveText = $('#save-text');

    let categoriesCache = [];
    let currentPage = 1;

    // Mostrar alerta descartable
    function showAlert(container, type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        container.html(alertHtml);
    }

    // Renderizar Paginación
    function renderPagination(meta) {
        const $pagContainer = $('#books-pagination-container');
        const $pagInfo = $('#pagination-info');
        const $pagLinks = $('#pagination-links');

        if (!meta || meta.total === 0) {
            $pagContainer.addClass('d-none');
            return;
        }

        $pagInfo.text(`Mostrando ${meta.from || 0} a ${meta.to || 0} de ${meta.total} libro(s) (Pág. ${meta.current_page} de ${meta.last_page})`);
        $pagLinks.empty();

        if (meta.last_page <= 1) {
            $pagContainer.removeClass('d-none');
            return;
        }

        // Botón Anterior
        const prevDisabled = meta.current_page === 1 ? 'disabled' : '';
        $pagLinks.append(`
            <li class="page-item ${prevDisabled}">
                <a class="page-link page-nav-link" href="#" data-page="${meta.current_page - 1}">&laquo;</a>
            </li>
        `);

        // Botones de Páginas Numéricas
        for (let i = 1; i <= meta.last_page; i++) {
            const active = i === meta.current_page ? 'active' : '';
            $pagLinks.append(`
                <li class="page-item ${active}">
                    <a class="page-link page-nav-link" href="#" data-page="${i}">${i}</a>
                </li>
            `);
        }

        // Botón Siguiente
        const nextDisabled = meta.current_page === meta.last_page ? 'disabled' : '';
        $pagLinks.append(`
            <li class="page-item ${nextDisabled}">
                <a class="page-link page-nav-link" href="#" data-page="${meta.current_page + 1}">&raquo;</a>
            </li>
        `);

        $pagContainer.removeClass('d-none');
    }

    // Cargar Categorías en Selects
    function loadCategories() {
        $.ajax({
            url: '/api/v1/categories',
            type: 'GET',
            success: function (res) {
                categoriesCache = res.data || [];
                $filterCategory.find('option:not(:first)').remove();
                $bookCategory.find('option:not(:first)').remove();

                categoriesCache.forEach(function (cat) {
                    $filterCategory.append(`<option value="${cat.id}">${cat.name}</option>`);
                    $bookCategory.append(`<option value="${cat.id}">${cat.name}</option>`);
                });
            },
            error: function () {
                showAlert($globalAlert, 'danger', 'Error al cargar las categorías desde la API.');
            }
        });
    }

    // Cargar Libros con soporte de filtro y paginación
    function loadBooks(categoryId = null, page = 1) {
        currentPage = page;
        $loading.removeClass('d-none');
        $tableContainer.addClass('d-none');
        $empty.addClass('d-none');
        $('#books-pagination-container').addClass('d-none');

        let url = `/api/v1/books?per_page=10&page=${page}`;
        if (categoryId) {
            url += `&category=${encodeURIComponent(categoryId)}`;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function (res) {
                $loading.addClass('d-none');
                $tableBody.empty();

                const books = res.data || [];
                const total = (res.meta && res.meta.total) ? res.meta.total : books.length;
                $countBadge.text(`Total: ${total} libro(s)`);

                if (books.length === 0) {
                    $empty.removeClass('d-none');
                    return;
                }

                books.forEach(function (book) {
                    const categoryName = book.category ? book.category.name : 'Sin categoría';
                    const desc = book.description ? $('<div>').text(book.description).html() : '<span class="text-muted fst-italic">Sin descripción</span>';

                    const row = `
                        <tr id="book-row-${book.id}">
                            <td class="fw-bold text-muted">#${book.id}</td>
                            <td class="fw-semibold text-dark">${$('<div>').text(book.title).html()}</td>
                            <td>${$('<div>').text(book.author).html()}</td>
                            <td><span class="badge bg-secondary badge-category">${$('<div>').text(categoryName).html()}</span></td>
                            <td><span class="badge bg-light text-dark border">${book.published_year}</span></td>
                            <td class="small text-secondary text-truncate" style="max-width: 280px;" title="${$('<div>').text(book.description || '').html()}">
                                ${desc}
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary btn-edit" data-id="${book.id}" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-delete" data-id="${book.id}" data-title="${$('<div>').text(book.title).html()}" title="Eliminar">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                    $tableBody.append(row);
                });

                $tableContainer.removeClass('d-none');
                renderPagination(res.meta);
            },
            error: function () {
                $loading.addClass('d-none');
                showAlert($globalAlert, 'danger', 'Error al obtener los libros desde el servidor.');
            }
        });
    }

    // Navegar por la paginación
    $(document).on('click', '.page-nav-link', function (e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'), 10);
        if (page && page !== currentPage) {
            loadBooks($filterCategory.val(), page);
        }
    });

    // Limpiar Errores del Modal
    function clearModalErrors() {
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $modalAlert.empty();
    }

    // Abrir Modal para Crear
    $('#btn-open-create-modal').on('click', function () {
        clearModalErrors();
        $form[0].reset();
        $('#book-id').val('');
        $('#bookModalLabel').text('Nuevo Libro');
        $saveText.text('Guardar Libro');
        bookModal.show();
    });

    // Abrir Modal para Editar
    $(document).on('click', '.btn-edit', function () {
        const bookId = $(this).data('id');
        clearModalErrors();
        $form[0].reset();

        $.ajax({
            url: `/api/v1/books/${bookId}`,
            type: 'GET',
            success: function (res) {
                const book = res.data;
                $('#book-id').val(book.id);
                $('#book-title').val(book.title);
                $('#book-author').val(book.author);
                $('#book-year').val(book.published_year);
                $('#book-category').val(book.category_id);
                $('#book-description').val(book.description || '');

                $('#bookModalLabel').text(`Editar Libro #${book.id}`);
                $saveText.text('Actualizar Cambios');
                bookModal.show();
            },
            error: function () {
                showAlert($globalAlert, 'danger', 'No se pudo cargar la información del libro.');
            }
        });
    });

    // Enviar Formulario (Crear o Editar)
    $form.on('submit', function (e) {
        e.preventDefault();
        clearModalErrors();

        const bookId = $('#book-id').val();
        const isEditing = Boolean(bookId);

        const payload = {
            title: $('#book-title').val(),
            author: $('#book-author').val(),
            published_year: parseInt($('#book-year').val(), 10),
            category_id: parseInt($('#book-category').val(), 10),
            description: $('#book-description').val() || null
        };

        const url = isEditing ? `/api/v1/books/${bookId}` : '/api/v1/books';
        const method = isEditing ? 'PUT' : 'POST';

        $saveBtn.prop('disabled', true);
        $saveSpinner.removeClass('d-none');

        $.ajax({
            url: url,
            type: method,
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function () {
                $saveBtn.prop('disabled', false);
                $saveSpinner.addClass('d-none');
                bookModal.hide();
                showAlert($globalAlert, 'success', `Libro ${isEditing ? 'actualizado' : 'creado'} exitosamente.`);
                loadBooks($filterCategory.val());
            },
            error: function (xhr) {
                $saveBtn.prop('disabled', false);
                $saveSpinner.addClass('d-none');

                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    if (errors.title) {
                        $('#book-title').addClass('is-invalid');
                        $('#err-title').text(errors.title[0]);
                    }
                    if (errors.author) {
                        $('#book-author').addClass('is-invalid');
                        $('#err-author').text(errors.author[0]);
                    }
                    if (errors.published_year) {
                        $('#book-year').addClass('is-invalid');
                        $('#err-published_year').text(errors.published_year[0]);
                    }
                    if (errors.category_id) {
                        $('#book-category').addClass('is-invalid');
                        $('#err-category_id').text(errors.category_id[0]);
                    }
                    if (errors.description) {
                        $('#book-description').addClass('is-invalid');
                        $('#err-description').text(errors.description[0]);
                    }
                } else {
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error al procesar la solicitud.';
                    showAlert($modalAlert, 'danger', msg);
                }
            }
        });
    });

    // Eliminar Libro con Confirmación
    $(document).on('click', '.btn-delete', function () {
        const bookId = $(this).data('id');
        const title = $(this).data('title');

        if (!confirm(`¿Estás seguro de que deseas eliminar el libro "${title}" (ID: ${bookId})?`)) {
            return;
        }

        $.ajax({
            url: `/api/v1/books/${bookId}`,
            type: 'DELETE',
            success: function () {
                showAlert($globalAlert, 'success', `Libro "${title}" eliminado exitosamente.`);
                loadBooks($filterCategory.val());
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'No se pudo eliminar el libro.';
                showAlert($globalAlert, 'danger', msg);
            }
        });
    });

    // Evento de Cambio en el Filtro de Categoría
    $filterCategory.on('change', function () {
        loadBooks($(this).val());
    });

    // Botón de Actualizar
    $('#btn-refresh').on('click', function () {
        loadCategories();
        loadBooks($filterCategory.val());
    });

    // Inicialización
    loadCategories();
    loadBooks();
});
</script>
@endpush

