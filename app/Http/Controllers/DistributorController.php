<?php

namespace App\Http\Controllers;

use App\Http\Requests\DistributorRequest;
use App\Models\Distributor;
use App\Services\GridBuilder;
use App\Services\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DistributorController extends Controller
{
    protected $gridBuilder;
    protected $formBuilder;

    public function __construct(GridBuilder $gridBuilder, FormBuilder $formBuilder)
    {
        $this->gridBuilder = $gridBuilder;
        $this->formBuilder = $formBuilder;
    }

    /**
     * Display a listing of distributors
     */
    public function index(Request $request)
    {
        $query = Distributor::query()->with('purchases');

        // Apply search filters
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('alamat', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('telepon', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Purchase activity filter
        if ($request->filled('purchase_activity')) {
            $activity = $request->get('purchase_activity');
            switch ($activity) {
                case 'active':
                    $query->whereHas('purchases');
                    break;
                case 'inactive':
                    $query->whereDoesntHave('purchases');
                    break;
                case 'recent':
                    $query->whereHas('purchases', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    });
                    break;
            }
        }

        // Location filter
        if ($request->filled('location_filter')) {
            $query->where('alamat', 'LIKE', "%{$request->get('location_filter')}%");
        }

        $distributors = $query->orderBy('created_at', 'desc')->paginate(10);

        // Search configuration
        $searchFields = ['nama', 'alamat', 'telepon', 'email'];
        $filters = [
            [
                'name' => 'purchase_activity',
                'label' => 'Aktivitas Pembelian',
                'type' => 'select',
                'options' => [
                    'active' => 'Pernah Melakukan Transaksi',
                    'inactive' => 'Belum Pernah Transaksi',
                    'recent' => 'Aktif 30 Hari Terakhir'
                ]
            ],
            [
                'name' => 'location_filter',
                'label' => 'Lokasi',
                'type' => 'text',
                'placeholder' => 'Cari berdasarkan alamat'
            ]
        ];

        $grid = $this->gridBuilder
            ->setModel(Distributor::class)
            ->addColumn('nama', 'Nama')
            ->addColumn('alamat', 'Alamat')
            ->addColumn('telepon', 'Telepon')
            ->addColumn('email', 'Email')
            ->addAction('Lihat', 'distributors.show', 'btn-info')
            ->addAction('Edit', 'distributors.edit', 'btn-warning')
            ->addAction('Hapus', 'distributors.destroy', 'btn-danger')
            ->setSearchable(['nama', 'alamat', 'telepon', 'email']);

        return view('distributors.index', compact('grid', 'distributors', 'searchFields', 'filters'));
    }

    /**
     * Show the form for creating a new distributor
     */
    public function create()
    {
        $form = $this->formBuilder
            ->setAction(route('distributors.store'))
            ->addInput('nama', 'Nama', 'text', ['required' => true])
            ->addInput('alamat', 'Alamat', 'textarea')
            ->addInput('telepon', 'Telepon', 'tel')
            ->addInput('email', 'Email', 'email');

        return view('distributors.create', compact('form'));
    }

    /**
     * Store a newly created distributor
     */
    public function store(DistributorRequest $request)
    {
        try {
            $distributor = Distributor::create($request->validated());
            
            // Add info message for new distributor registration
            $message = 'Distributor berhasil ditambahkan. Info: Distributor baru "' . $distributor->nama . '" telah terdaftar dalam sistem';
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $distributor
                ], 201);
            }

            return redirect()->route('distributors.index')
                ->with('info', $message);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan distributor: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menambahkan distributor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified distributor
     */
    public function show(Distributor $distributor)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $distributor
            ]);
        }

        return view('distributors.show', compact('distributor'));
    }

    /**
     * Show the form for editing the specified distributor
     */
    public function edit(Distributor $distributor)
    {
        $form = $this->formBuilder
            ->setModel($distributor)
            ->setAction(route('distributors.update', $distributor), 'PUT')
            ->addInput('nama', 'Nama', 'text', ['required' => true])
            ->addInput('alamat', 'Alamat', 'textarea')
            ->addInput('telepon', 'Telepon', 'tel')
            ->addInput('email', 'Email', 'email');

        return view('distributors.edit', compact('form', 'distributor'));
    }

    /**
     * Update the specified distributor
     */
    public function update(DistributorRequest $request, Distributor $distributor)
    {
        try {
            $distributor->update($request->validated());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Distributor berhasil diperbarui',
                    'data' => $distributor->fresh()
                ]);
            }

            return redirect()->route('distributors.index')
                ->with('success', 'Distributor berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui distributor: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui distributor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified distributor
     */
    public function destroy(Distributor $distributor)
    {
        try {
            // Check if distributor has purchase records
            $hasPurchases = $distributor->purchases()->exists();
            $distributorName = $distributor->nama;
            
            $distributor->delete();
            
            $message = "Distributor '{$distributorName}' berhasil dihapus";
            $messageType = 'success';
            
            if ($hasPurchases) {
                $message = "Distributor '{$distributorName}' berhasil dihapus. Perhatian: Distributor memiliki riwayat pembelian yang masih tersimpan";
                $messageType = 'warning';
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('distributors.index')
                ->with($messageType, $message);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus distributor: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus distributor: ' . $e->getMessage());
        }
    }

    /**
     * Get JSON data for GridBuilder
     */
    public function getData(Request $request): JsonResponse
    {
        return response()->json(
            $this->gridBuilder
                ->setModel(Distributor::class)
                ->setSearchable(['nama', 'alamat', 'telepon', 'email'])
                ->getJsonData($request)
        );
    }
}