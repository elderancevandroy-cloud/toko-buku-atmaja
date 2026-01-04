@extends('layouts.master')

@section('title', 'Detail Penjualan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Penjualan #{{ $sale->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus penjualan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Informasi Penjualan</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Tanggal Penjualan:</th>
                                    <td>{{ \Carbon\Carbon::parse($sale->tanggal_penjualan)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Kasir:</th>
                                    <td>
                                        <a href="{{ route('cashiers.show', $sale->cashier->id) }}" class="text-primary">
                                            {{ $sale->cashier->nama }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $sale->cashier->no_karyawan }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Harga:</th>
                                    <td>
                                        <strong class="text-success h5">
                                            Rp {{ number_format($sale->total_harga, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informasi Tambahan</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Total Item:</th>
                                    <td>{{ $sale->details->sum('jumlah') }} unit</td>
                                </tr>
                                <tr>
                                    <th>Jenis Buku:</th>
                                    <td>{{ $sale->details->count() }} jenis</td>
                                </tr>
                                <tr>
                                    <th>Dibuat:</th>
                                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diperbarui:</th>
                                    <td>{{ $sale->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Details -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Detail Item Penjualan</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="35%">Buku</th>
                                    <th width="15%">Harga Satuan</th>
                                    <th width="10%">Jumlah</th>
                                    <th width="15%">Subtotal</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sale->details as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('books.show', $detail->book->id) }}" class="text-primary">
                                            <strong>{{ $detail->book->judul }}</strong>
                                        </a>
                                        <br>
                                        <small class="text-muted">oleh {{ $detail->book->pengarang }}</small>
                                        @if($detail->book->penerbit)
                                        <br>
                                        <small class="text-muted">{{ $detail->book->penerbit }}</small>
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                    <td>{{ $detail->jumlah }} unit</td>
                                    <td>
                                        <strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('books.show', $detail->book->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Lihat Buku
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="4" class="text-right"><strong>Total Keseluruhan:</strong></td>
                                    <td><strong>Rp {{ number_format($sale->total_harga, 0, ',', '.') }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Impact Information -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Dampak Stok</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Penjualan ini mengurangi stok buku sebagai berikut:</strong>
                        <ul class="mt-2 mb-0">
                            @foreach($sale->details as $detail)
                            <li>
                                {{ $detail->book->judul }}: <strong>-{{ $detail->jumlah }} unit</strong>
                                (Stok saat ini: {{ $detail->book->stok }} unit)
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection