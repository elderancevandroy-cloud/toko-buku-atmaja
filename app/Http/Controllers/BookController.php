<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Book;
use App\Services\GridBuilder;
use App\Services\FormBuilder;
use App\Traits\Searchable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookController extends Controller
{
    use Searchable;
    
    protected $gridBuilder;
    protected $formBuilder;

    public function __construct(GridBuilder $gridBuilder, FormBuilder $formBuilder)
    {
        $this->gridBuilder = $gridBuilder;
        $this->formBuilder = $formBuilder;
    }

    /**
     * Display a listing of books
     */
    public function index(Request $request)
    {
        $query = Book::query();

        // Apply search filters
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('judul', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('pengarang', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('penerbit', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Stock level filter
        if ($request->filled('stock_level')) {
            $stockLevel = $request->get('stock_level');
            switch ($stockLevel) {
                case 'low':
                    $query->where('stok', '<', 10);
                    break;
                case 'medium':
                    $query->whereBetween('stok', [10, 50]);
                    break;
                case 'high':
                    $query->where('stok', '>', 50);
                    break;
                case 'out':
                    $query->where('stok', 0);
                    break;
            }
        }

        // Price range filter
        if ($request->filled('price_min')) {
            $query->where('harga', '>=', $request->get('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('harga', '<=', $request->get('price_max'));
        }

        // Publisher filter
        if ($request->filled('penerbit_filter')) {
            $query->where('penerbit', 'LIKE', "%{$request->get('penerbit_filter')}%");
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(10);

        // Search configuration
        $searchFields = ['judul', 'pengarang', 'penerbit'];
        $filters = [
            [
                'name' => 'stock_level',
                'label' => 'Level Stok',
                'type' => 'select',
                'options' => [
                    'out' => 'Habis (0)',
                    'low' => 'Rendah (< 10)',
                    'medium' => 'Sedang (10-50)',
                    'high' => 'Tinggi (> 50)'
                ]
            ],
            [
                'name' => 'price_min',
                'label' => 'Harga Minimum',
                'type' => 'number',
                'placeholder' => 'Rp 0'
            ],
            [
                'name' => 'price_max',
                'label' => 'Harga Maksimum',
                'type' => 'number',
                'placeholder' => 'Rp 999999'
            ],
            [
                'name' => 'penerbit_filter',
                'label' => 'Penerbit',
                'type' => 'text',
                'placeholder' => 'Nama penerbit'
            ]
        ];

        $grid = $this->gridBuilder
            ->setModel(Book::class)
            ->addColumn('judul', 'Judul')
            ->addColumn('pengarang', 'Pengarang')
            ->addColumn('penerbit', 'Penerbit')
            ->addColumn('harga', 'Harga')
            ->addColumn('stok', 'Stok')
            ->addAction('Lihat', 'books.show', 'btn-info')
            ->addAction('Edit', 'books.edit', 'btn-warning')
            ->addAction('Hapus', 'books.destroy', 'btn-danger')
            ->setSearchable(['judul', 'pengarang', 'penerbit']);

        return view('books.index', compact('grid', 'books', 'searchFields', 'filters'));
    }

    /**
     * Show the form for creating a new book
     */
    public function create()
    {
        $form = $this->formBuilder
            ->setAction(route('books.store'))
            ->addInput('judul', 'Judul', 'text', ['required' => true])
            ->addInput('pengarang', 'Pengarang', 'text', ['required' => true])
            ->addInput('penerbit', 'Penerbit', 'text')
            ->addInput('harga', 'Harga', 'number', ['required' => true, 'step' => '0.01'])
            ->addInput('stok', 'Stok', 'number', ['required' => true]);

        return view('books.create', compact('form'));
    }

    /**
     * Store a newly created book
     */
    public function store(BookRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Check if stock is low and add warning
            $message = 'Buku berhasil ditambahkan';
            $messageType = 'success';
            
            if ($data['stok'] < 10) {
                $message = 'Buku berhasil ditambahkan. Perhatian: Stok buku rendah (kurang dari 10)';
                $messageType = 'warning';
            }
            
            $book = Book::create($data);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $book
                ], 201);
            }

            return redirect()->route('books.index')
                ->with($messageType, $message);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan buku: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menambahkan buku: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified book
     */
    public function show(Book $book)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $book
            ]);
        }

        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book
     */
    public function edit(Book $book)
    {
        $form = $this->formBuilder
            ->setModel($book)
            ->setAction(route('books.update', $book), 'PUT')
            ->addInput('judul', 'Judul', 'text', ['required' => true])
            ->addInput('pengarang', 'Pengarang', 'text', ['required' => true])
            ->addInput('penerbit', 'Penerbit', 'text')
            ->addInput('harga', 'Harga', 'number', ['required' => true, 'step' => '0.01'])
            ->addInput('stok', 'Stok', 'number', ['required' => true]);

        return view('books.edit', compact('form', 'book'));
    }

    /**
     * Update the specified book
     */
    public function update(BookRequest $request, Book $book)
    {
        try {
            $data = $request->validated();
            $oldStock = $book->stok;
            
            $book->update($data);
            
            // Determine message type based on stock changes
            $message = 'Buku berhasil diperbarui';
            $messageType = 'success';
            
            if ($data['stok'] < 10) {
                $message = 'Buku berhasil diperbarui. Perhatian: Stok buku rendah (kurang dari 10)';
                $messageType = 'warning';
            } elseif ($data['stok'] > $oldStock) {
                $message = 'Buku berhasil diperbarui. Info: Stok bertambah dari ' . $oldStock . ' menjadi ' . $data['stok'];
                $messageType = 'info';
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $book->fresh()
                ]);
            }

            return redirect()->route('books.index')
                ->with($messageType, $message);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui buku: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui buku: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified book
     */
    public function destroy(Book $book)
    {
        try {
            $bookTitle = $book->judul;
            $hasStock = $book->stok > 0;
            
            $book->delete();
            
            $message = "Buku '{$bookTitle}' berhasil dihapus";
            $messageType = 'success';
            
            if ($hasStock) {
                $message = "Buku '{$bookTitle}' berhasil dihapus. Perhatian: Buku masih memiliki stok saat dihapus";
                $messageType = 'warning';
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('books.index')
                ->with($messageType, $message);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus buku: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus buku: ' . $e->getMessage());
        }
    }

    /**
     * Get JSON data for GridBuilder
     */
    public function getData(Request $request): JsonResponse
    {
        return response()->json(
            $this->gridBuilder
                ->setModel(Book::class)
                ->setSearchable(['judul', 'pengarang', 'penerbit'])
                ->getJsonData($request)
        );
    }
}