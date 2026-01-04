@extends('layouts.master')

@section('title', 'Detail Kasir')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Kasir</h3>
                    <div class="card-tools">
                        <a href="{{ route('cashiers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('cashiers.edit', $cashier->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('cashiers.destroy', $cashier->id) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kasir ini?')">
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
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nama:</th>
                                    <td>{{ $cashier->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $cashier->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>No. Karyawan:</th>
                                    <td>{{ $cashier->no_karyawan }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Dibuat:</th>
                                    <td>{{ $cashier->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diperbarui:</th>
                                    <td>{{ $cashier->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales History -->
    @if($cashier->sales->count() > 0)
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Riwayat Penjualan</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Total Harga</th>
                                    <th>Jumlah Item</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cashier->sales as $sale)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($sale->tanggal_penjualan)->format('d/m/Y') }}</td>
                                    <td>Rp {{ number_format($sale->total_harga, 0, ',', '.') }}</td>
                                    <td>{{ $sale->details->sum('jumlah') }} unit</td>
                                    <td>
                                        <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
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
</div>
@endsection