@extends('layouts.master')

@section('title', 'Edit Penjualan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Penjualan</h3>
                    <div class="card-tools">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('sales.show', $sale->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales.update', $sale->id) }}" method="POST" id="saleForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Sale Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <x-select 
                                    name="kasir_id" 
                                    label="Kasir" 
                                    :options="$cashiers" 
                                    :selected="$sale->kasir_id"
                                    required="true"
                                    placeholder="Pilih Kasir" />
                            </div>
                            <div class="col-md-6">
                                <x-input 
                                    name="tanggal_penjualan" 
                                    label="Tanggal Penjualan" 
                                    type="date" 
                                    :value="$sale->tanggal_penjualan"
                                    required="true" />
                            </div>
                        </div>

                        <!-- Sale Details -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5>Detail Penjualan</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="saleDetailsTable">
                                        <thead>
                                            <tr>
                                                <th width="40%">Buku</th>
                                                <th width="15%">Harga Satuan</th>
                                                <th width="15%">Jumlah</th>
                                                <th width="20%">Subtotal</th>
                                                <th width="10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="saleDetailsBody">
                                            @foreach($sale->details as $index => $detail)
                                            <tr data-index="{{ $index }}">
                                                <td>
                                                    <select name="details[{{ $index }}][buku_id]" class="form-control book-select" required>
                                                        <option value="">Pilih Buku</option>
                                                        @foreach($books as $book)
                                                        <option value="{{ $book['id'] }}" 
                                                                data-price="{{ $book['harga'] }}" 
                                                                data-stock="{{ $book['stok'] }}"
                                                                {{ $detail->buku_id == $book['id'] ? 'selected' : '' }}>
                                                            {{ $book['judul'] }} (Stok: {{ $book['stok'] }})
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="details[{{ $index }}][harga_satuan]" 
                                                           class="form-control price-input" step="0.01" 
                                                           value="{{ $detail->harga_satuan }}" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="details[{{ $index }}][jumlah]" 
                                                           class="form-control quantity-input" min="1" 
                                                           value="{{ $detail->jumlah }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="details[{{ $index }}][subtotal]" 
                                                           class="form-control subtotal-input" step="0.01" 
                                                           value="{{ $detail->subtotal }}" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm remove-detail">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3" class="text-right"><strong>Total Harga:</strong></td>
                                                <td><strong id="totalHarga">Rp {{ number_format($sale->total_harga, 0, ',', '.') }}</strong></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                
                                <button type="button" class="btn btn-success" id="addDetailBtn">
                                    <i class="fas fa-plus"></i> Tambah Item
                                </button>
                            </div>
                        </div>

                        <!-- Hidden total input -->
                        <input type="hidden" name="total_harga" id="totalHargaInput" value="{{ $sale->total_harga }}">

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Penjualan
                                </button>
                                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@vite(['resources/js/form-enhancements.js'])
<script>
document.addEventListener('DOMContentLoaded', function() {
    let detailIndex = {{ $sale->details->count() }};
    const books = @json($books);

    // Add new detail row
    document.getElementById('addDetailBtn').addEventListener('click', function() {
        addDetailRow();
    });

    function addDetailRow() {
        const bookOptions = books.map(book => 
            `<option value="${book.id}" data-price="${book.harga}" data-stock="${book.stok}">
                ${book.judul} (Stok: ${book.stok})
            </option>`
        ).join('');

        const row = document.createElement('tr');
        row.dataset.index = detailIndex;
        row.innerHTML = `
            <td>
                <select name="details[${detailIndex}][buku_id]" class="form-control book-select" required>
                    <option value="">Pilih Buku</option>
                    ${bookOptions}
                </select>
            </td>
            <td>
                <input type="number" name="details[${detailIndex}][harga_satuan]" 
                       class="form-control price-input" step="0.01" readonly>
            </td>
            <td>
                <input type="number" name="details[${detailIndex}][jumlah]" 
                       class="form-control quantity-input" min="1" required>
            </td>
            <td>
                <input type="number" name="details[${detailIndex}][subtotal]" 
                       class="form-control subtotal-input" step="0.01" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-detail">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        document.getElementById('saleDetailsBody').appendChild(row);
        detailIndex++;
    }

    // Remove detail row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-detail') || e.target.closest('.remove-detail')) {
            const row = e.target.closest('tr');
            row.remove();
            calculateTotal();
        }
    });

    // Handle book selection
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('book-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const price = selectedOption.dataset.price;
            const stock = selectedOption.dataset.stock;
            const row = e.target.closest('tr');
            
            const priceInput = row.querySelector('.price-input');
            const quantityInput = row.querySelector('.quantity-input');
            
            priceInput.value = price || '';
            quantityInput.setAttribute('max', stock || '');
            quantityInput.setAttribute('data-stock', stock || '');
            
            calculateRowSubtotal(row);
        }
    });

    // Handle quantity change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity-input')) {
            const row = e.target.closest('tr');
            const stock = parseInt(e.target.dataset.stock);
            const quantity = parseInt(e.target.value);
            
            if (stock && quantity > stock) {
                showNotification(`Stok tidak mencukupi! Stok tersedia: ${stock}`, 'warning');
                e.target.value = stock;
            }
            
            calculateRowSubtotal(row);
        }
    });

    function calculateRowSubtotal(row) {
        const priceInput = row.querySelector('.price-input');
        const quantityInput = row.querySelector('.quantity-input');
        const subtotalInput = row.querySelector('.subtotal-input');
        
        const price = parseFloat(priceInput.value) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const subtotal = price * quantity;
        
        subtotalInput.value = subtotal.toFixed(2);
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(function(input) {
            total += parseFloat(input.value) || 0;
        });
        
        const totalDisplay = document.getElementById('totalHarga');
        const totalInput = document.getElementById('totalHargaInput');
        
        if (totalDisplay) {
            totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
        if (totalInput) {
            totalInput.value = total.toFixed(2);
        }
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="bi bi-exclamation-triangle"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Initial calculation
    calculateTotal();

    // Form validation
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        const detailRows = document.querySelectorAll('#saleDetailsBody tr').length;
        if (detailRows === 0) {
            e.preventDefault();
            showNotification('Minimal harus ada satu item penjualan!', 'danger');
            return false;
        }

        let hasValidDetails = false;
        document.querySelectorAll('.book-select').forEach(function(select) {
            if (select.value !== '') {
                hasValidDetails = true;
            }
        });

        if (!hasValidDetails) {
            e.preventDefault();
            showNotification('Pilih minimal satu buku untuk dijual!', 'danger');
            return false;
        }
    });
});
</script>
@endpush
@endsection