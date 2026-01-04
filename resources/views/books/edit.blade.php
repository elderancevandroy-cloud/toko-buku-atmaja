@extends('layouts.master')

@section('title', 'Edit Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Buku: {{ $book->judul }}</h4>
            </div>
            <div class="card-body">
                {!! $form->render() !!}
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('books.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('books.show', $book) }}" class="btn btn-info">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection