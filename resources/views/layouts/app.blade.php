<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Bookstore') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Custom Flash Message Styles -->
    <style>
        .alert {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border-left: 4px solid #dc3545;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-left: 4px solid #ffc107;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            border-left: 4px solid #17a2b8;
        }
        
        .alert i {
            font-size: 1.1em;
        }
        
        .alert strong {
            font-weight: 600;
        }
        
        .alert ul {
            padding-left: 1.2rem;
        }
        
        .alert .btn-close {
            padding: 0.5rem;
        }
        
        /* Auto-hide flash messages after 5 seconds */
        .alert.auto-dismiss {
            animation: slideIn 0.3s ease-out, slideOut 0.3s ease-in 4.7s forwards;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }
            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-book"></i> {{ config('app.name', 'Laravel Bookstore') }}
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('books.index') }}">
                                <i class="bi bi-book"></i> Buku
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('cashiers.index') }}">
                                <i class="bi bi-person-badge"></i> Kasir
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('distributors.index') }}">
                                <i class="bi bi-truck"></i> Distributor
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('purchases.index') }}">
                                <i class="bi bi-cart-plus"></i> Pembelian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('sales.index') }}">
                                <i class="bi bi-receipt"></i> Penjualan
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        <div class="container mt-3">
            <x-flash-messages />
        </div>

        <!-- Main Content -->
        <main class="container mt-4">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-dark text-light mt-5 py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Laravel Bookstore</h5>
                        <p class="mb-0">Sistem Manajemen Toko Buku</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-0">&copy; {{ date('Y') }} Laravel Bookstore. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Flash Message Auto-dismiss Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-dismiss flash messages after 5 seconds (except error messages)
            const alerts = document.querySelectorAll('.alert:not(.alert-danger)');
            alerts.forEach(function(alert) {
                alert.classList.add('auto-dismiss');
                
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Add click-to-dismiss functionality for all alerts
            const allAlerts = document.querySelectorAll('.alert');
            allAlerts.forEach(function(alert) {
                alert.style.cursor = 'pointer';
                alert.addEventListener('click', function(e) {
                    if (!e.target.classList.contains('btn-close')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>