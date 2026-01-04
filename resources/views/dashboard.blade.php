@extends('layouts.master')

@section('title', 'Dashboard')

@push('styles')
<style>
/* Dashboard Hero Image Styles */
.hero-image-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.hero-image:hover {
    transform: scale(1.05);
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        135deg,
        rgba(2, 9, 73, 0.1) 0%,
        rgba(2, 9, 73, 0.3) 100%
    );
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.hero-image-container:hover .hero-overlay {
    opacity: 1;
}

.hero-text {
    text-align: center;
    color: white;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.hero-icon {
    font-size: 3rem;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

/* Floating animation for the overlay icon */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.floating-overlay-icon {
    animation: float 3s ease-in-out infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero-image-container {
        height: 200px !important;
        margin-top: 2rem;
    }
    
    .hero-icon {
        font-size: 2rem;
    }
}
</style>
@endpush

@section('content')
{{-- Welcome Section with Animation --}}
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-lg" style="background-color: #020949;">
            <div class="card-body text-white text-center py-5">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInLeft">
                            Selamat Datang di Dashboard
                        </h1>
                        <p class="lead mb-4 animate__animated animate__fadeInLeft animate__delay-1s">
                            Kelola toko buku Anda dengan mudah dan efisien
                        </p>
                        <div class="d-flex justify-content-center gap-3 animate__animated animate__fadeInUp animate__delay-2s">
                            <a href="{{ route('books.index') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-book me-2"></i>Kelola Buku
                            </a>
                            <a href="{{ route('sales.create') }}" class="btn btn-outline-light btn-lg">
                                <i class="bi bi-cart-plus me-2"></i>Penjualan Baru
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        {{-- Library Hero Image --}}
                        <div class="hero-image-container position-relative" style="height: 300px;">
                            <img src="{{ asset('storage/images/library-hero.jpg') }}" 
                                 alt="Modern Library Interior - Toko Buku Atmaja" 
                                 style="height: 280px;"
                                 class="hero-image img-fluid rounded animate__animated animate__fadeInRight"
                                 loading="lazy"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjhmOWZhIi8+CiAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzZjNzU3ZCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkxpYnJhcnkgSW1hZ2U8L3RleHQ+Cjwvc3ZnPg=='">
                            
                            {{-- Hover Overlay --}}
                            <div class="hero-overlay">
                                <div class="hero-text floating-overlay-icon">
                                    <i class="bi bi-book-half hero-icon"></i>
                                    <small>Toko Buku Modern</small>
                                </div>
                            </div>
                            
                            {{-- Corner Badge --}}
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                    <i class="bi bi-star-fill me-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Statistics Cards --}}
<div class="row mb-4">
    <div class="col-md-2 mb-3">
        <div class="card stats-card dashboard-card h-100 animate__animated animate__fadeInUp" style="background-color: #020949; animation-delay: 0.1s;">
            <div class="card-body text-center text-white">
                <i class="bi bi-book display-4 mb-3"></i>
                <h3 class="fw-bold counter" data-target="{{ $totalBooks }}">0</h3>
                <p class="mb-0">Total Buku</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-2 mb-3">
        <div class="card stats-card dashboard-card h-100 animate__animated animate__fadeInUp" style="background-color: #020949; animation-delay: 0.2s;">
            <div class="card-body text-center text-white">
                <i class="bi bi-person-badge display-4 mb-3"></i>
                <h3 class="fw-bold counter" data-target="{{ $totalCashiers }}">0</h3>
                <p class="mb-0">Total Kasir</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-2 mb-3">
        <div class="card stats-card dashboard-card h-100 animate__animated animate__fadeInUp" style="background-color: #020949; animation-delay: 0.3s;">
            <div class="card-body text-center text-white">
                <i class="bi bi-truck display-4 mb-3"></i>
                <h3 class="fw-bold counter" data-target="{{ $totalDistributors }}">0</h3>
                <p class="mb-0">Distributor</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-2 mb-3">
        <div class="card stats-card dashboard-card h-100 animate__animated animate__fadeInUp" style="background-color: #020949; animation-delay: 0.4s;">
            <div class="card-body text-center text-white">
                <i class="bi bi-cart-check display-4 mb-3"></i>
                <h3 class="fw-bold counter" data-target="{{ $totalSales }}">0</h3>
                <p class="mb-0">Penjualan</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-2 mb-3">
        <div class="card stats-card dashboard-card h-100 animate__animated animate__fadeInUp" style="background-color: #020949; animation-delay: 0.5s;">
            <div class="card-body text-center text-white">
                <i class="bi bi-cart-plus display-4 mb-3"></i>
                <h3 class="fw-bold counter" data-target="{{ $totalPurchases }}">0</h3>
                <p class="mb-0">Pembelian</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-2 mb-3">
        <div class="card stats-card dashboard-card h-100 animate__animated animate__fadeInUp" style="background-color: #020949; animation-delay: 0.6s;">
            <div class="card-body text-center text-white">
                <i class="bi bi-currency-dollar display-4 mb-3"></i>
                <h3 class="fw-bold">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</h3>
                <p class="mb-0">Nilai Stok</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Quick Actions --}}
    <div class="col-md-4 mb-4">
        <div class="card dashboard-card h-100 animate__animated animate__fadeInLeft">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('books.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Buku
                    </a>
                    <a href="{{ route('sales.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-cart-plus me-2"></i>Penjualan Baru
                    </a>
                    <a href="{{ route('purchases.create') }}" class="btn btn-outline-info">
                        <i class="bi bi-bag-plus me-2"></i>Pembelian Baru
                    </a>
                    <a href="{{ route('cashiers.create') }}" class="btn btn-outline-warning">
                        <i class="bi bi-person-plus me-2"></i>Tambah Kasir
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Recent Books --}}
    <div class="col-md-4 mb-4">
        <div class="card dashboard-card h-100 animate__animated animate__fadeInUp">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Buku Terbaru</h5>
            </div>
            <div class="card-body">
                @if($recentBooks->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentBooks as $book)
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ Str::limit($book->judul, 25) }}</h6>
                                        <p class="mb-1 text-muted small">{{ $book->pengarang }}</p>
                                        <small class="text-success">Stok: {{ $book->stok }}</small>
                                    </div>
                                    <small class="text-muted">{{ $book->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">Belum ada buku terbaru</p>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Low Stock Alert --}}
    <div class="col-md-4 mb-4">
        <div class="card dashboard-card h-100 animate__animated animate__fadeInRight">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Stok Rendah</h5>
            </div>
            <div class="card-body">
                @if($lowStockBooks->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($lowStockBooks as $book)
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ Str::limit($book->judul, 20) }}</h6>
                                        <small class="text-muted">{{ $book->pengarang }}</small>
                                    </div>
                                    <span class="badge bg-danger">{{ $book->stok }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('books.index') }}" class="btn btn-sm btn-outline-warning w-100">
                            Lihat Semua Buku
                        </a>
                    </div>
                @else
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle me-2"></i>Semua buku stok aman!
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Sales Chart --}}
<div class="row mt-4">
    <div class="col-12">
        <div class="card dashboard-card animate__animated animate__fadeInUp">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Grafik Penjualan 6 Bulan Terakhir</h5>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/image-handler.js') }}"></script>
@vite(['resources/js/dashboard-animations.js'])
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sales Chart
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($monthlySales);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(item => item.month),
            datasets: [{
                label: 'Jumlah Penjualan',
                data: salesData.map(item => item.count),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Trend Penjualan Bulanan'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush