@extends('layouts.master')

@section('title', 'Manajemen Buku')

@section('content')
<div class="container-fluid">
    <!-- Search Form -->
    <x-search-form 
        :search-fields="$searchFields" 
        :filters="$filters" 
        :action="route('books.index')" />

    <!-- Results Summary -->
    @if(request()->hasAny(['search', 'date_from', 'date_to', 'stock_level', 'price_min', 'price_max', 'penerbit_filter']))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Menampilkan {{ $books->total() }} hasil dari {{ $books->count() }} buku
        @if(request('search'))
            untuk pencarian "<strong>{{ request('search') }}</strong>"
        @endif
        @if(request('stock_level'))
            dengan level stok: <strong>{{ collect([
                'out' => 'Habis',
                'low' => 'Rendah',
                'medium' => 'Sedang',
                'high' => 'Tinggi'
            ])[request('stock_level')] }}</strong>
        @endif
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Buku</h3>
                    <a href="{{ route('books.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Buku
                    </a>
                </div>
                <div class="card-body">
                    @if($books->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Pengarang</th>
                                        <th>Penerbit</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($books as $book)
                                    <tr>
                                        <td>
                                            <strong>{{ $book->judul }}</strong>
                                            @if($book->stok < 10)
                                                <span class="badge bg-warning ms-1">Stok Rendah</span>
                                            @endif
                                            @if($book->stok == 0)
                                                <span class="badge bg-danger ms-1">Habis</span>
                                            @endif
                                        </td>
                                        <td>{{ $book->pengarang }}</td>
                                        <td>{{ $book->penerbit ?? '-' }}</td>
                                        <td>Rp {{ number_format($book->harga, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $book->stok > 10 ? 'success' : ($book->stok > 0 ? 'warning' : 'danger') }}">
                                                {{ $book->stok }} unit
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('books.show', $book) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('books.edit', $book) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Yakin hapus buku {{ $book->judul }}?')">
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
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $books->firstItem() }} sampai {{ $books->lastItem() }} 
                                dari {{ $books->total() }} hasil
                            </div>
                            {{ $books->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5>Tidak ada buku ditemukan</h5>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'date_from', 'date_to', 'stock_level', 'price_min', 'price_max', 'penerbit_filter']))
                                    Coba ubah kriteria pencarian Anda
                                @else
                                    Belum ada buku yang ditambahkan
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'date_from', 'date_to', 'stock_level', 'price_min', 'price_max', 'penerbit_filter']))
                                <a href="{{ route('books.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Buku Pertama
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