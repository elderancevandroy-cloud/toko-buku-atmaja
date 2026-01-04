@extends('layouts.master')

@section('title', 'Manajemen Kasir')

@section('content')
<div class="container-fluid">
    <!-- Search Form -->
    <x-search-form 
        :search-fields="$searchFields" 
        :filters="$filters" 
        :action="route('cashiers.index')" />

    <!-- Results Summary -->
    @if(request()->hasAny(['search', 'date_from', 'date_to', 'sales_activity', 'no_karyawan_filter']))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Menampilkan {{ $cashiers->total() }} hasil dari {{ $cashiers->count() }} kasir
        @if(request('search'))
            untuk pencarian "<strong>{{ request('search') }}</strong>"
        @endif
        @if(request('sales_activity'))
            dengan aktivitas: <strong>{{ collect([
                'active' => 'Pernah Melakukan Penjualan',
                'inactive' => 'Belum Pernah Penjualan',
                'recent' => 'Aktif 30 Hari Terakhir'
            ])[request('sales_activity')] }}</strong>
        @endif
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Kasir</h3>
                    <a href="{{ route('cashiers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Kasir
                    </a>
                </div>
                <div class="card-body">
                    @if($cashiers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>No. Karyawan</th>
                                        <th>Total Penjualan</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cashiers as $cashier)
                                    <tr>
                                        <td>
                                            <strong>{{ $cashier->nama }}</strong>
                                        </td>
                                        <td>{{ $cashier->email ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $cashier->no_karyawan }}</span>
                                        </td>
                                        <td>
                                            @if($cashier->sales->count() > 0)
                                                <span class="badge bg-success">{{ $cashier->sales->count() }} transaksi</span>
                                                <br>
                                                <small class="text-muted">
                                                    Rp {{ number_format($cashier->sales->sum('total_harga'), 0, ',', '.') }}
                                                </small>
                                            @else
                                                <span class="badge bg-warning">Belum ada penjualan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cashier->sales()->where('created_at', '>=', now()->subDays(30))->exists())
                                                <span class="badge bg-success">Aktif</span>
                                            @elseif($cashier->sales->count() > 0)
                                                <span class="badge bg-warning">Tidak Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Baru</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('cashiers.show', $cashier) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('cashiers.edit', $cashier) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('cashiers.destroy', $cashier) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Yakin hapus kasir {{ $cashier->nama }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $cashiers->firstItem() }} sampai {{ $cashiers->lastItem() }} 
                                dari {{ $cashiers->total() }} hasil
                            </div>
                            {{ $cashiers->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                            <h5>Tidak ada kasir ditemukan</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'date_from', 'date_to', 'sales_activity', 'no_karyawan_filter']))
                                    Coba ubah kriteria pencarian Anda
                                @else
                                    Belum ada kasir yang ditambahkan
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'date_from', 'date_to', 'sales_activity', 'no_karyawan_filter']))
                                <a href="{{ route('cashiers.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Kasir Pertama
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection