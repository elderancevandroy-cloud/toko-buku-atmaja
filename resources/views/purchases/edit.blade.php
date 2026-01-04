@extends('layouts.master')

@section('title', 'Edit Pembelian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Pembelian</h3>
                    <div class="card-tools">
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('purchases.show', $purchase->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {!! $form->render() !!}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate total when quantity or price changes
    function calculateTotal() {
        const jumlah = parseFloat($('#jumlah').val()) || 0;
        const hargaBeli = parseFloat($('#harga_beli').val()) || 0;
        const total = jumlah * hargaBeli;
        $('#total').val(total.toFixed(2));
    }

    $('#jumlah, #harga_beli').on('input', calculateTotal);
});
</script>
@endpush
@endsection