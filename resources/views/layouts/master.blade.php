<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Aplikasi Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alert = document.getElementById('alert-success');
            if (alert) {
                // Setelah 3 detik, fade out dan remove
                setTimeout(() => {
                    alert.classList.remove('animate__fadeInDown');
                    alert.classList.add('animate__fadeOutUp');
                    // Remove from DOM after animation ends
                    alert.addEventListener('animationend', () => {
                        alert.remove();
                    });
                }, 1400);
            }
        });
    </script>
    
    <style>
        body {
            background: linear-gradient(to bottom right, #e0f2ff, #f3e8ff);
            min-height: 100vh;
        }
        .hero-gradient {
            background: linear-gradient(to right, #2563eb, #7e22ce);
            color: white;
        }
        .icon-circle {
            width: 60px;
            height: 60px;
            background: linear-gradient(to right, #bfdbfe, #ddd6fe);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        .alert .btn-close {
            width: 0.75rem;
            height: 0.75rem;
            padding: 0.25rem;
            background: transparent;
            opacity: 0.6;
        }
        .alert .btn-close:hover {
            opacity: 1;
        }
        .dashboard-card {
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .stats-card {
            background-color: #020949;
            color: white;
        }
    </style>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    {{-- Header dengan Dropdown MODUL --}}
    <section class="hero-gradient py-4 text-center">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="icon-circle bg-white me-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#2563eb" class="bi bi-book" viewBox="0 0 16 16">
                            <path d="M1 2.828c.885-.37 2.154-.828 4-.828s3.115.457 4 .828V13.17c-.885-.37-2.154-.828-4-.828s-3.115.457-4 .828V2.828z"/>
                            <path d="M0 1.5a.5.5 0 0 1 .5-.5c1.234 0 2.408.257 3.5.682C5.592 1.257 6.766 1 8 1s2.408.257 3.5.682c1.092-.425 2.266-.682 3.5-.682a.5.5 0 0 1 .5.5v12a.5.5 0 0 1-.5.5c-1.234 0-2.408-.257-3.5-.682-1.092.425-2.266.682-3.5.682s-2.408-.257-3.5-.682C2.408 13.743 1.234 14 0 14a.5.5 0 0 1-.5-.5v-12z"/>
                        </svg>
                    </div>
                    <div class="text-start">
                        <h3 class="mb-0">Toko Buku Online <span class="text-warning">Atmaja</span></h3>
                        <p class="mb-0 text-light">Kelola toko buku secara efisien</p>
                    </div>
                </div>
                
                {{-- Dropdown MODUL --}}
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-grid-fill me-1"></i> MODUL
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="{{ route('books.index') }}">
                            <i class="bi bi-book me-2"></i> Buku
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('distributors.index') }}">
                            <i class="bi bi-truck me-2"></i> Distributor
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('cashiers.index') }}">
                            <i class="bi bi-person-badge me-2"></i> Kasir
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('purchases.index') }}">
                            <i class="bi bi-cart-plus me-2"></i> Pembelian
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('sales.index') }}">
                            <i class="bi bi-cart-check me-2"></i> Penjualan
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                            <i class="bi bi-house me-2"></i> Dashboard
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <main class="container py-5">
        <h2 class="text-center mb-5 text-primary">@yield('title')</h2>
        
        @if(session('success'))
            <div id="alert-success" class="alert alert-success alert-dismissible fade show animate__animated animate__fadeInDown">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeInDown">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-dark text-white py-4 text-center mt-auto">
        <div class="container">
            <div class="mb-2">
                <i class="bi bi-book"></i>
                <span class="ms-2 fw-bold">Atmaja Online Book Store</span>
            </div>
            <p class="text-secondary small mb-0">© 2025 BookStore. Sistem Penjualan Buku Modern.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>