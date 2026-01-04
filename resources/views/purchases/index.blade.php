@extends('layouts.master')

@section('title', 'Manajemen Pembelian')

@section('content')
<div class="container-fluid">
    <!-- Search Form -->
    <x-search-form 
        :search-fields="$searchFields" 
        :filters="$filters" 
        :action="route('purchases.index')" />

    <!-- Results Summary -->
    @if(request()->hasAny(['search', 'date_from', 'date_to', 'amount_min', 'amount_max', 'distributor_id', 'quantity_min']))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Menampilkan {{ $purchases->total() }} hasil dari {{ $purchases->count() }} pembelian
        @if(request('search'))
            untuk pencarian "<strong>{{ request('search') }}</strong>"
        @endif
        @if(request('distributor_id'))
            dari distributor: <strong>{{ \App\Models\Distributor::find(request('distributor_id'))->nama ?? 'Unknown' }}</strong>
        @endif
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Pembelian</h3>
                    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Pembelian
                    </a>
                </div>
                <div class="card-body">
                    @if($purchases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tanggal</th>
                                        <th>Distributor</th>
                                        <th>Buku</th>
                                        <th>Jumlah</th>
                                        <th>Harga Beli</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchases as $purchase)
                                    <tr>
                                        <td>
                                            <strong>#{{ $purchase->id }}</strong>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($purchase->tanggal_pembelian)->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('distributors.show', $purchase->distributor->id) }}" class="text-primary">
                                                {{ $purchase->distributor->nama }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('books.show', $purchase->book->id) }}" class="text-primary">
                                                <strong>{{ $purchase->book->judul }}</strong>
                                            </a>
                                            <br>
                                            <small class="text-muted">oleh {{ $purchase->book->pengarang }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $purchase->jumlah }} unit</span>
                                        </td>
                                        <td>Rp {{ number_format($purchase->harga_beli, 0, ',', '.') }}</td>
                                        <td>
                                            <strong class="text-success">
                                                Rp {{ number_format($purchase->total, 0, ',', '.') }}
                                            </strong>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Yakin hapus pembelian #{{ $purchase->id }}?')">
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
                                        <td colspan="6"><strong>Total Keseluruhan:</strong></td>
                                        <td><strong>Rp {{ number_format($purchases->sum('total'), 0, ',', '.') }}</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $purchases->firstItem() }} sampai {{ $purchases->lastItem() }} 
                                dari {{ $purchases->total() }} hasil
                            </div>
                            {{ $purchases->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <h5>Tidak ada pembelian ditemukan</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'date_from', 'date_to', 'amount_min', 'amount_max', 'distributor_id', 'quantity_min']))
                                    Coba ubah kriteria pencarian Anda
                                @else
                                    Belum ada pembelian yang tercatat
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'date_from', 'date_to', 'amount_min', 'amount_max', 'distributor_id', 'quantity_min']))
                                <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Buat Pembelian Pertama
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