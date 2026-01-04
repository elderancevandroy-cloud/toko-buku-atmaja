@extends('layouts.master')

@section('title', 'Detail Pembelian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Pembelian</h3>
                    <div class="card-tools">
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pembelian ini?')">
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
                            <h5>Informasi Pembelian</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Tanggal Pembelian:</th>
                                    <td>{{ \Carbon\Carbon::parse($purchase->tanggal_pembelian)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Distributor:</th>
                                    <td>
                                        <a href="{{ route('distributors.show', $purchase->distributor->id) }}" class="text-primary">
                                            {{ $purchase->distributor->nama }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Buku:</th>
                                    <td>
                                        <a href="{{ route('books.show', $purchase->book->id) }}" class="text-primary">
                                            {{ $purchase->book->judul }}
                                        </a>
                                        <br>
                                        <small class="text-muted">oleh {{ $purchase->book->pengarang }}</small>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Detail Transaksi</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Jumlah:</th>
                                    <td>{{ $purchase->jumlah }} unit</td>
                                </tr>
                                <tr>
                                    <th>Harga Beli:</th>
                                    <td>Rp {{ number_format($purchase->harga_beli, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td>
                                        <strong class="text-success">
                                            Rp {{ number_format($purchase->total, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Informasi Tambahan</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th width="20%">Dibuat:</th>
                                    <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diperbarui:</th>
                                    <td>{{ $purchase->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
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
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Pembelian ini menambahkan <strong>{{ $purchase->jumlah }} unit</strong> 
                        ke stok buku "{{ $purchase->book->judul }}".
                        <br>
                        Stok saat ini: <strong>{{ $purchase->book->stok }} unit</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection