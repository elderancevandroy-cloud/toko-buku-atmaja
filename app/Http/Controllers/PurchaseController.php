<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\Distributor;
use App\Models\Book;
use App\Services\GridBuilder;
use App\Services\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{
    protected $gridBuilder;
    protected $formBuilder;

    public function __construct(GridBuilder $gridBuilder, FormBuilder $formBuilder)
    {
        $this->gridBuilder = $gridBuilder;
        $this->formBuilder = $formBuilder;
    }

    /**
     * Display a listing of purchases
     */
    public function index(Request $request)
    {
        $query = Purchase::query()->with(['distributor', 'book']);

        // Apply search filters
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('distributor', function ($distributorQuery) use ($searchTerm) {
                    $distributorQuery->where('nama', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhereHas('book', function ($bookQuery) use ($searchTerm) {
                    $bookQuery->where('judul', 'LIKE', "%{$searchTerm}%")
                             ->orWhere('pengarang', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhere('id', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_pembelian', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_pembelian', '<=', $request->get('date_to'));
        }

        // Amount range filter
        if ($request->filled('amount_min')) {
            $query->where('total', '>=', $request->get('amount_min'));
        }

        if ($request->filled('amount_max')) {
            $query->where('total', '<=', $request->get('amount_max'));
        }

        // Distributor filter
        if ($request->filled('distributor_id')) {
            $query->where('distributor_id', $request->get('distributor_id'));
        }

        // Quantity filter
        if ($request->filled('quantity_min')) {
            $query->where('jumlah', '>=', $request->get('quantity_min'));
        }

        $purchases = $query->orderBy('tanggal_pembelian', 'desc')->paginate(10);

        // Get distributors for filter
        $distributorOptions = Distributor::pluck('nama', 'id')->toArray();

        // Search configuration
        $searchFields = ['distributor', 'buku', 'pengarang', 'ID transaksi'];
        $filters = [
            [
                'name' => 'distributor_id',
                'label' => 'Distributor',
                'type' => 'select',
                'options' => $distributorOptions
            ],
            [
                'name' => 'amount_min',
                'label' => 'Total Minimum',
                'type' => 'number',
                'placeholder' => 'Rp 0'
            ],
            [
                'name' => 'amount_max',
                'label' => 'Total Maksimum',
                'type' => 'number',
                'placeholder' => 'Rp 999999999'
            ],
            [
                'name' => 'quantity_min',
                'label' => 'Jumlah Minimum',
                'type' => 'number',
                'placeholder' => 'Unit minimum'
            ]
        ];

        $grid = $this->gridBuilder
            ->setModel(Purchase::class)
            ->addColumn('distributor.nama', 'Distributor')
            ->addColumn('book.judul', 'Buku')
            ->addColumn('jumlah', 'Jumlah')
            ->addColumn('harga_beli', 'Harga Beli')
            ->addColumn('total', 'Total')
            ->addColumn('tanggal_pembelian', 'Tanggal')
            ->addAction('Lihat', 'purchases.show', 'btn-info')
            ->addAction('Edit', 'purchases.edit', 'btn-warning')
            ->addAction('Hapus', 'purchases.destroy', 'btn-danger')
            ->setSearchable(['distributor.nama', 'book.judul']);

        return view('purchases.index', compact('grid', 'purchases', 'searchFields', 'filters'));
    }

    /**
     * Show the form for creating a new purchase
     */
    public function create()
    {
        $distributors = Distributor::pluck('nama', 'id')->toArray();
        $books = Book::pluck('judul', 'id')->toArray();

        $form = $this->formBuilder
            ->setAction(route('purchases.store'))
            ->addSelect('distributor_id', 'Distributor', $distributors, ['required' => true])
            ->addSelect('buku_id', 'Buku', $books, ['required' => true])
            ->addInput('jumlah', 'Jumlah', 'number', ['required' => true, 'min' => 1])
            ->addInput('harga_beli', 'Harga Beli', 'number', ['required' => true, 'step' => '0.01'])
            ->addInput('tanggal_pembelian', 'Tanggal Pembelian', 'date', ['required' => true]);

        return view('purchases.create', compact('form'));
    }

    /**
     * Store a newly created purchase
     */
    public function store(PurchaseRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Calculate total
            $data['total'] = $data['jumlah'] * $data['harga_beli'];
            
            $purchase = Purchase::create($data);
            
            // Load relationships for response
            $purchase->load(['distributor', 'book']);
            
            // Check if this is a large purchase
            $message = 'Pembelian berhasil ditambahkan dan stok telah diperbarui';
            $messageType = 'success';
            
            if ($data['jumlah'] >= 100) {
                $message = 'Pembelian berhasil ditambahkan dan stok telah diperbarui. Info: Pembelian dalam jumlah besar (' . $data['jumlah'] . ' unit)';
                $messageType = 'info';
            } elseif ($data['total'] >= 1000000) {
                $message = 'Pembelian berhasil ditambahkan dan stok telah diperbarui. Info: Pembelian dengan nilai tinggi (Rp ' . number_format($data['total'], 0, ',', '.') . ')';
                $messageType = 'info';
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $purchase
                ], 201);
            }

            return redirect()->route('purchases.index')
                ->with($messageType, $message);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan pembelian: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menambahkan pembelian: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified purchase
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['distributor', 'book']);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $purchase
            ]);
        }

        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified purchase
     */
    public function edit(Purchase $purchase)
    {
        $distributors = Distributor::pluck('nama', 'id')->toArray();
        $books = Book::pluck('judul', 'id')->toArray();

        $form = $this->formBuilder
            ->setModel($purchase)
            ->setAction(route('purchases.update', $purchase), 'PUT')
            ->addSelect('distributor_id', 'Distributor', $distributors, ['required' => true])
            ->addSelect('buku_id', 'Buku', $books, ['required' => true])
            ->addInput('jumlah', 'Jumlah', 'number', ['required' => true, 'min' => 1])
            ->addInput('harga_beli', 'Harga Beli', 'number', ['required' => true, 'step' => '0.01'])
            ->addInput('tanggal_pembelian', 'Tanggal Pembelian', 'date', ['required' => true]);

        return view('purchases.edit', compact('form', 'purchase'));
    }

    /**
     * Update the specified purchase
     */
    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        try {
            $data = $request->validated();
            
            // Calculate total
            $data['total'] = $data['jumlah'] * $data['harga_beli'];
            
            // Store old values for stock adjustment
            $oldQuantity = $purchase->jumlah;
            $oldBookId = $purchase->buku_id;
            
            $purchase->update($data);
            
            // Manual stock adjustment since triggers only work on INSERT
            if ($oldBookId != $data['buku_id']) {
                // Different book - decrease old book stock and increase new book stock
                Book::where('id', $oldBookId)->decrement('stok', $oldQuantity);
                Book::where('id', $data['buku_id'])->increment('stok', $data['jumlah']);
            } else {
                // Same book - adjust stock by difference
                $difference = $data['jumlah'] - $oldQuantity;
                if ($difference != 0) {
                    Book::where('id', $data['buku_id'])->increment('stok', $difference);
                }
            }
            
            $purchase->load(['distributor', 'book']);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembelian berhasil diperbarui dan stok telah disesuaikan',
                    'data' => $purchase
                ]);
            }

            return redirect()->route('purchases.index')
                ->with('success', 'Pembelian berhasil diperbarui dan stok telah disesuaikan');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui pembelian: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui pembelian: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified purchase
     */
    public function destroy(Purchase $purchase)
    {
        try {
            // Decrease stock before deleting (reverse the stock increase)
            Book::where('id', $purchase->buku_id)->decrement('stok', $purchase->jumlah);
            
            $purchase->delete();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pembelian berhasil dihapus dan stok telah disesuaikan'
                ]);
            }

            return redirect()->route('purchases.index')
                ->with('success', 'Pembelian berhasil dihapus dan stok telah disesuaikan');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus pembelian: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus pembelian: ' . $e->getMessage());
        }
    }

    /**
     * Get JSON data for GridBuilder
     */
    public function getData(Request $request): JsonResponse
    {
        return response()->json(
            $this->gridBuilder
                ->setModel(Purchase::class)
                ->setSearchable(['distributor.nama', 'book.judul'])
                ->getJsonData($request)
        );
    }
}