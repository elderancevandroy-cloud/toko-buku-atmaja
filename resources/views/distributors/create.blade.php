@extends('layouts.master')

@section('title', 'Tambah Distributor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Distributor Baru</h3>
                    <div class="card-tools">
                        <a href="{{ route('distributors.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
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