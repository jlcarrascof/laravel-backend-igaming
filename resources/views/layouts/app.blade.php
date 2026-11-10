<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Biblioteca de Libros - iGaming')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .table-hover tbody tr:hover { background-color: #f1f3f5; }
        .badge-category { font-size: 0.85rem; padding: 0.4em 0.7em; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <i class="bi bi-book-half text-warning me-2"></i>Biblioteca Digital
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link {{ request()->is('/') ? 'active fw-semibold' : '' }}" href="{{ url('/') }}">
                    <i class="bi bi-collection me-1"></i>Libros
                </a>
                <a class="nav-link {{ request()->is('notes*') ? 'active fw-semibold' : '' }}" href="{{ url('/notes') }}">
                    <i class="bi bi-journal-text me-1"></i>Notas (Demo Base)
                </a>
                <a class="nav-link" href="{{ url('/api/v1/health') }}" target="_blank">
                    <i class="bi bi-heart-pulse me-1"></i>Health API
                </a>
            </div>
        </div>
    </nav>

    <main class="container mb-5">
        @yield('content')
    </main>

    <footer class="text-center py-3 text-muted border-top mt-auto">
        <div class="container">
            <small>&copy; {{ date('Y') }} - Evaluación Técnica Backend / Full Stack (Laravel)</small>
        </div>
    </footer>

    <!-- jQuery 3.7.1 & Bootstrap 5 Bundle -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
