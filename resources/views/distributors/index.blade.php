@extends('layouts.master')

@section('title', 'Manajemen Distributor')

@section('content')
<div class="container-fluid">
    <!-- Search Form -->
    <x-search-form 
        :search-fields="$searchFields" 
        :filters="$filters" 
        :action="route('distributors.index')" />

    <!-- Results Summary -->
    @if(request()->hasAny(['search', 'date_from', 'date_to', 'purchase_activity', 'location_filter']))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Menampilkan {{ $distributors->total() }} hasil dari {{ $distributors->count() }} distributor
        @if(request('search'))
            untuk pencarian "<strong>{{ request('search') }}</strong>"
        @endif
        @if(request('purchase_activity'))
            dengan aktivitas: <strong>{{ collect([
                'active' => 'Pernah Melakukan Transaksi',
                'inactive' => 'Belum Pernah Transaksi',
                'recent' => 'Aktif 30 Hari Terakhir'
            ])[request('purchase_activity')] }}</strong>
        @endif
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Distributor</h3>
                    <a href="{{ route('distributors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Distributor
                    </a>
                </div>
                <div class="card-body">
                    @if($distributors->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Kontak</th>
                                        <th>Alamat</th>
                                        <th>Total Pembelian</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($distributors as $distributor)
                                    <tr>
                                        <td>
                                            <strong>{{ $distributor->nama }}</strong>
                                        </td>
                                        <td>
                                            @if($distributor->telepon)
                                                <i class="fas fa-phone text-muted"></i> {{ $distributor->telepon }}
                                                <br>
                                            @endif
                                            @if($distributor->email)
                                                <i class="fas fa-envelope text-muted"></i> {{ $distributor->email }}
                                            @endif
                                            @if(!$distributor->telepon && !$distributor->email)
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $distributor->alamat ?? '-' }}
                                        </td>
                                        <td>
                                            @if($distributor->purchases->count() > 0)
                                                <span class="badge bg-success">{{ $distributor->purchases->count() }} transaksi</span>
                                                <br>
                                                <small class="text-muted">
                                                    Rp {{ number_format($distributor->purchases->sum('total'), 0, ',', '.') }}
                                                </small>
                                            @else
                                                <span class="badge bg-warning">Belum ada transaksi</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($distributor->purchases()->where('created_at', '>=', now()->subDays(30))->exists())
                                                <span class="badge bg-success">Aktif</span>
                                            @elseif($distributor->purchases->count() > 0)
                                                <span class="badge bg-warning">Tidak Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Baru</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('distributors.show', $distributor) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('distributors.edit', $distributor) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('distributors.destroy', $distributor) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Yakin hapus distributor {{ $distributor->nama }}?')">
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
                                Menampilkan {{ $distributors->firstItem() }} sampai {{ $distributors->lastItem() }} 
                                dari {{ $distributors->total() }} hasil
                            </div>
                            {{ $distributors->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-building fa-3x text-muted mb-3"></i>
                            <h5>Tidak ada distributor ditemukan</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'date_from', 'date_to', 'purchase_activity', 'location_filter']))
                                    Coba ubah kriteria pencarian Anda
                                @else
                                    Belum ada distributor yang ditambahkan
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'date_from', 'date_to', 'purchase_activity', 'location_filter']))
                                <a href="{{ route('distributors.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Distributor Pertama
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