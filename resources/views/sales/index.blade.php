@extends('layouts.master')

@section('title', 'Manajemen Penjualan')

@section('content')
<div class="container-fluid">
    <!-- Search Form -->
    <x-search-form 
        :search-fields="$searchFields" 
        :filters="$filters" 
        :action="route('sales.index')" />

    <!-- Results Summary -->
    @if(request()->hasAny(['search', 'date_from', 'date_to', 'amount_min', 'amount_max', 'cashier_id']))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Menampilkan {{ $sales->total() }} hasil dari {{ $sales->count() }} penjualan
        @if(request('search'))
            untuk pencarian "<strong>{{ request('search') }}</strong>"
        @endif
        @if(request('cashier_id'))
            oleh kasir: <strong>{{ \App\Models\Cashier::find(request('cashier_id'))->nama ?? 'Unknown' }}</strong>
        @endif
        @if(request('amount_min') || request('amount_max'))
            dengan total 
            @if(request('amount_min'))
                dari Rp {{ number_format(request('amount_min'), 0, ',', '.') }}
            @endif
            @if(request('amount_max'))
                sampai Rp {{ number_format(request('amount_max'), 0, ',', '.') }}
            @endif
        @endif
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Penjualan</h3>
                    <a href="{{ route('sales.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Penjualan
                    </a>
                </div>
                <div class="card-body">
                    @if($sales->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Kasir</th>
                                        <th>Items</th>
                                        <th>Total Harga</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sales as $sale)
                                    <tr>
                                        <td>
                                            <strong>#{{ $sale->id }}</strong>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($sale->tanggal_penjualan)->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('cashiers.show', $sale->cashier->id) }}" class="text-primary">
                                                {{ $sale->cashier->nama }}
                                            </a>
                                            <br>
                                            <small class="text-muted">{{ $sale->cashier->no_karyawan }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $sale->details->count() }} jenis</span>
                                            <br>
                                            <small class="text-muted">{{ $sale->details->sum('jumlah') }} unit</small>
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                Rp {{ number_format($sale->total_harga, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                        <td>
                                            @if($sale->created_at->isToday())
                                                <span class="badge bg-success">Hari Ini</span>
                                            @elseif($sale->created_at->isYesterday())
                                                <span class="badge bg-warning">Kemarin</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $sale->created_at->diffForHumans() }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Yakin hapus penjualan #{{ $sale->id }}?')">
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
                                <tfoot>
                                    <tr class="table-active">
                                        <td colspan="4"><strong>Total Keseluruhan:</strong></td>
                                        <td><strong>Rp {{ number_format($sales->sum('total_harga'), 0, ',', '.') }}</strong></td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $sales->firstItem() }} sampai {{ $sales->lastItem() }} 
                                dari {{ $sales->total() }} hasil
                            </div>
                            {{ $sales->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h5>Tidak ada penjualan ditemukan</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'date_from', 'date_to', 'amount_min', 'amount_max', 'cashier_id']))
                                    Coba ubah kriteria pencarian Anda
                                @else
                                    Belum ada penjualan yang tercatat
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'date_from', 'date_to', 'amount_min', 'amount_max', 'cashier_id']))
                                <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Buat Penjualan Pertama
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