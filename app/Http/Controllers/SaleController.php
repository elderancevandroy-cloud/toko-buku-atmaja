<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleRequest;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Cashier;
use App\Models\Book;
use App\Services\GridBuilder;
use App\Services\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    protected $gridBuilder;
    protected $formBuilder;

    public function __construct(GridBuilder $gridBuilder, FormBuilder $formBuilder)
    {
        $this->gridBuilder = $gridBuilder;
        $this->formBuilder = $formBuilder;
    }

    /**
     * Display a listing of sales
     */
    public function index(Request $request)
    {
        $query = Sale::query()->with(['cashier', 'details.book']);

        // Apply search filters
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('cashier', function ($cashierQuery) use ($searchTerm) {
                    $cashierQuery->where('nama', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhereHas('details.book', function ($bookQuery) use ($searchTerm) {
                    $bookQuery->where('judul', 'LIKE', "%{$searchTerm}%");
                })
                ->orWhere('id', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('tanggal_penjualan', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('tanggal_penjualan', '<=', $request->get('date_to'));
        }

        // Amount range filter
        if ($request->filled('amount_min')) {
            $query->where('total_harga', '>=', $request->get('amount_min'));
        }

        if ($request->filled('amount_max')) {
            $query->where('total_harga', '<=', $request->get('amount_max'));
        }

        // Cashier filter
        if ($request->filled('cashier_id')) {
            $query->where('kasir_id', $request->get('cashier_id'));
        }

        $sales = $query->orderBy('tanggal_penjualan', 'desc')->paginate(10);

        // Get cashiers for filter
        $cashierOptions = Cashier::pluck('nama', 'id')->toArray();

        // Search configuration
        $searchFields = ['kasir', 'buku', 'ID transaksi'];
        $filters = [
            [
                'name' => 'cashier_id',
                'label' => 'Kasir',
                'type' => 'select',
                'options' => $cashierOptions
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
            ]
        ];

        $grid = $this->gridBuilder
            ->setModel(Sale::class)
            ->addColumn('cashier.nama', 'Kasir')
            ->addColumn('total_harga', 'Total Harga')
            ->addColumn('tanggal_penjualan', 'Tanggal')
            ->addAction('Lihat', 'sales.show', 'btn-info')
            ->addAction('Edit', 'sales.edit', 'btn-warning')
            ->addAction('Hapus', 'sales.destroy', 'btn-danger')
            ->setSearchable(['cashier.nama']);

        return view('sales.index', compact('grid', 'sales', 'searchFields', 'filters'));
    }

    /**
     * Show the form for creating a new sale
     */
    public function create()
    {
        $cashiers = Cashier::pluck('nama', 'id')->toArray();
        $books = Book::where('stok', '>', 0)->get()->pluck('judul_with_stock', 'id')->toArray();

        $form = $this->formBuilder
            ->setAction(route('sales.store'))
            ->addSelect('kasir_id', 'Kasir', $cashiers, ['required' => true])
            ->addInput('tanggal_penjualan', 'Tanggal Penjualan', 'date', ['required' => true]);

        return view('sales.create', compact('form', 'books'));
    }

    /**
     * Store a newly created sale
     */
    public function store(SaleRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $saleDetails = $data['sale_details'] ?? [];
            
            // Calculate total and check for low stock warnings
            $totalHarga = 0;
            $lowStockWarnings = [];
            
            foreach ($saleDetails as $detail) {
                $book = Book::find($detail['buku_id']);
                if (!$book) {
                    throw new \Exception("Buku dengan ID {$detail['buku_id']} tidak ditemukan");
                }
                
                if ($book->stok < $detail['jumlah']) {
                    throw new \Exception("Stok buku '{$book->judul}' tidak mencukupi. Stok tersedia: {$book->stok}");
                }
                
                // Check if this sale will result in low stock
                $remainingStock = $book->stok - $detail['jumlah'];
                if ($remainingStock < 5 && $remainingStock >= 0) {
                    $lowStockWarnings[] = "'{$book->judul}' (sisa stok: {$remainingStock})";
                }
                
                $subtotal = $detail['jumlah'] * $book->harga;
                $totalHarga += $subtotal;
            }
            
            // Create sale
            $sale = Sale::create([
                'kasir_id' => $data['kasir_id'],
                'total_harga' => $totalHarga,
                'tanggal_penjualan' => $data['tanggal_penjualan']
            ]);
            
            // Create sale details
            foreach ($saleDetails as $detail) {
                $book = Book::find($detail['buku_id']);
                $subtotal = $detail['jumlah'] * $book->harga;
                
                SaleDetail::create([
                    'penjualan_id' => $sale->id,
                    'buku_id' => $detail['buku_id'],
                    'jumlah' => $detail['jumlah'],
                    'harga_satuan' => $book->harga,
                    'subtotal' => $subtotal
                ]);
            }
            
            DB::commit();
            
            // Load relationships for response
            $sale->load(['cashier', 'details.book']);
            
            // Determine message type and content
            $message = 'Penjualan berhasil ditambahkan dan stok telah diperbarui';
            $messageType = 'success';
            
            if (!empty($lowStockWarnings)) {
                $message = 'Penjualan berhasil ditambahkan dan stok telah diperbarui. Perhatian: Stok rendah untuk buku: ' . implode(', ', $lowStockWarnings);
                $messageType = 'warning';
            } elseif ($totalHarga >= 500000) {
                $message = 'Penjualan berhasil ditambahkan dan stok telah diperbarui. Info: Penjualan dengan nilai tinggi (Rp ' . number_format($totalHarga, 0, ',', '.') . ')';
                $messageType = 'info';
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $sale
                ], 201);
            }

            return redirect()->route('sales.index')
                ->with($messageType, $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan penjualan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menambahkan penjualan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified sale
     */
    public function show(Sale $sale)
    {
        $sale->load(['cashier', 'details.book']);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $sale
            ]);
        }

        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified sale
     */
    public function edit(Sale $sale)
    {
        $sale->load(['details.book']);
        $cashiers = Cashier::pluck('nama', 'id')->toArray();
        $books = Book::where('stok', '>', 0)->get()->pluck('judul_with_stock', 'id')->toArray();

        $form = $this->formBuilder
            ->setModel($sale)
            ->setAction(route('sales.update', $sale), 'PUT')
            ->addSelect('kasir_id', 'Kasir', $cashiers, ['required' => true])
            ->addInput('tanggal_penjualan', 'Tanggal Penjualan', 'date', ['required' => true]);

        return view('sales.edit', compact('form', 'sale', 'books'));
    }

    /**
     * Update the specified sale
     */
    public function update(SaleRequest $request, Sale $sale)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            $saleDetails = $data['sale_details'] ?? [];
            
            // Restore stock from old sale details
            foreach ($sale->details as $oldDetail) {
                Book::where('id', $oldDetail->buku_id)->increment('stok', $oldDetail->jumlah);
            }
            
            // Delete old sale details
            $sale->details()->delete();
            
            // Calculate new total
            $totalHarga = 0;
            foreach ($saleDetails as $detail) {
                $book = Book::find($detail['buku_id']);
                if (!$book) {
                    throw new \Exception("Buku dengan ID {$detail['buku_id']} tidak ditemukan");
                }
                
                if ($book->stok < $detail['jumlah']) {
                    throw new \Exception("Stok buku '{$book->judul}' tidak mencukupi. Stok tersedia: {$book->stok}");
                }
                
                $subtotal = $detail['jumlah'] * $book->harga;
                $totalHarga += $subtotal;
            }
            
            // Update sale
            $sale->update([
                'kasir_id' => $data['kasir_id'],
                'total_harga' => $totalHarga,
                'tanggal_penjualan' => $data['tanggal_penjualan']
            ]);
            
            // Create new sale details
            foreach ($saleDetails as $detail) {
                $book = Book::find($detail['buku_id']);
                $subtotal = $detail['jumlah'] * $book->harga;
                
                SaleDetail::create([
                    'penjualan_id' => $sale->id,
                    'buku_id' => $detail['buku_id'],
                    'jumlah' => $detail['jumlah'],
                    'harga_satuan' => $book->harga,
                    'subtotal' => $subtotal
                ]);
            }
            
            DB::commit();
            
            $sale->load(['cashier', 'details.book']);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Penjualan berhasil diperbarui dan stok telah disesuaikan',
                    'data' => $sale
                ]);
            }

            return redirect()->route('sales.index')
                ->with('success', 'Penjualan berhasil diperbarui dan stok telah disesuaikan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui penjualan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui penjualan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified sale
     */
    public function destroy(Sale $sale)
    {
        try {
            DB::beginTransaction();
            
            // Restore stock from sale details
            foreach ($sale->details as $detail) {
                Book::where('id', $detail->buku_id)->increment('stok', $detail->jumlah);
            }
            
            // Delete sale details first
            $sale->details()->delete();
            
            // Delete sale
            $sale->delete();
            
            DB::commit();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Penjualan berhasil dihapus dan stok telah dikembalikan'
                ]);
            }

            return redirect()->route('sales.index')
                ->with('success', 'Penjualan berhasil dihapus dan stok telah dikembalikan');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus penjualan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus penjualan: ' . $e->getMessage());
        }
    }

    /**
     * Get JSON data for GridBuilder
     */
    public function getData(Request $request): JsonResponse
    {
        return response()->json(
            $this->gridBuilder
                ->setModel(Sale::class)
                ->setSearchable(['cashier.nama'])
                ->getJsonData($request)
        );
    }

    /**
     * Get available books for sale (with stock > 0)
     */
    public function getAvailableBooks(): JsonResponse
    {
        $books = Book::where('stok', '>', 0)
            ->select('id', 'judul', 'harga', 'stok')
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'judul' => $book->judul,
                    'harga' => $book->harga,
                    'stok' => $book->stok,
                    'display' => "{$book->judul} (Stok: {$book->stok}, Harga: Rp " . number_format($book->harga, 0, ',', '.') . ")"
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }
}