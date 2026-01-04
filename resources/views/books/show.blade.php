@extends('layouts.master')

@section('title', 'Detail Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-book me-2"></i>Detail Buku</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary">{{ $book->judul }}</h5>
                        <hr>
                        <p><strong>Pengarang:</strong> {{ $book->pengarang }}</p>
                        <p><strong>Penerbit:</strong> {{ $book->penerbit ?? '-' }}</p>
                        <p><strong>Stok:</strong> 
                            <span class="badge bg-{{ $book->stok > 10 ? 'success' : ($book->stok > 0 ? 'warning' : 'danger') }}">
                                {{ $book->stok }} unit
                            </span>
                        </p>
                        <p><strong>Dibuat:</strong> {{ $book->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Diperbarui:</strong> {{ $book->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <div class="text-end">
                            <h3 class="text-success">Rp {{ number_format($book->harga, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('books.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <div>
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Yakin hapus buku {{ $book->judul }}?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purchase History -->
@if($book->purchases->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Riwayat Pembelian</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Distributor</th>
                                <th>Jumlah</th>
                                <th>Harga Beli</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($book->purchases as $purchase)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($purchase->tanggal_pembelian)->format('d/m/Y') }}</td>
                                <td>{{ $purchase->distributor->nama }}</td>
                                <td>{{ $purchase->jumlah }} unit</td>
                                <td>Rp {{ number_format($purchase->harga_beli, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($purchase->total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Sales History -->
@if($book->saleDetails->count() > 0)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-cart-check me-2"></i>Riwayat Penjualan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($book->saleDetails as $detail)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($detail->sale->tanggal_penjualan)->format('d/m/Y') }}</td>
                                <td>{{ $detail->sale->cashier->nama }}</td>
                                <td>{{ $detail->jumlah }} unit</td>
                                <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection