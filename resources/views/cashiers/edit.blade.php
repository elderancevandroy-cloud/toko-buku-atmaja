@extends('layouts.master')

@section('title', 'Edit Kasir')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Kasir: {{ $cashier->nama }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('cashiers.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('cashiers.show', $cashier->id) }}" class="btn btn-info btn-sm">
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
@endsection