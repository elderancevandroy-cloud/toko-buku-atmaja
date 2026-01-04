<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashierRequest;
use App\Models\Cashier;
use App\Services\GridBuilder;
use App\Services\FormBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CashierController extends Controller
{
    protected $gridBuilder;
    protected $formBuilder;

    public function __construct(GridBuilder $gridBuilder, FormBuilder $formBuilder)
    {
        $this->gridBuilder = $gridBuilder;
        $this->formBuilder = $formBuilder;
    }

    /**
     * Display a listing of cashiers
     */
    public function index(Request $request)
    {
        $query = Cashier::query()->with('sales');

        // Apply search filters
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('no_karyawan', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        // Sales activity filter
        if ($request->filled('sales_activity')) {
            $activity = $request->get('sales_activity');
            switch ($activity) {
                case 'active':
                    $query->whereHas('sales');
                    break;
                case 'inactive':
                    $query->whereDoesntHave('sales');
                    break;
                case 'recent':
                    $query->whereHas('sales', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(30));
                    });
                    break;
            }
        }

        // Employee number filter
        if ($request->filled('no_karyawan_filter')) {
            $query->where('no_karyawan', 'LIKE', "%{$request->get('no_karyawan_filter')}%");
        }

        $cashiers = $query->orderBy('created_at', 'desc')->paginate(10);

        // Search configuration
        $searchFields = ['nama', 'email', 'no_karyawan'];
        $filters = [
            [
                'name' => 'sales_activity',
                'label' => 'Aktivitas Penjualan',
                'type' => 'select',
                'options' => [
                    'active' => 'Pernah Melakukan Penjualan',
                    'inactive' => 'Belum Pernah Penjualan',
                    'recent' => 'Aktif 30 Hari Terakhir'
                ]
            ],
            [
                'name' => 'no_karyawan_filter',
                'label' => 'No. Karyawan',
                'type' => 'text',
                'placeholder' => 'Cari berdasarkan nomor karyawan'
            ]
        ];

        $grid = $this->gridBuilder
            ->setModel(Cashier::class)
            ->addColumn('nama', 'Nama')
            ->addColumn('email', 'Email')
            ->addColumn('no_karyawan', 'No. Karyawan')
            ->addAction('Lihat', 'cashiers.show', 'btn-info')
            ->addAction('Edit', 'cashiers.edit', 'btn-warning')
            ->addAction('Hapus', 'cashiers.destroy', 'btn-danger')
            ->setSearchable(['nama', 'email', 'no_karyawan']);

        return view('cashiers.index', compact('grid', 'cashiers', 'searchFields', 'filters'));
    }

    /**
     * Show the form for creating a new cashier
     */
    public function create()
    {
        $form = $this->formBuilder
            ->setAction(route('cashiers.store'))
            ->addInput('nama', 'Nama', 'text', ['required' => true])
            ->addInput('email', 'Email', 'email', ['required' => true])
            ->addInput('no_karyawan', 'No. Karyawan', 'text', ['required' => true]);

        return view('cashiers.create', compact('form'));
    }

    /**
     * Store a newly created cashier
     */
    public function store(CashierRequest $request)
    {
        try {
            $cashier = Cashier::create($request->validated());
            
            // Add info message for new cashier registration
            $message = 'Kasir berhasil ditambahkan. Info: Kasir baru terdaftar dengan nomor karyawan ' . $cashier->no_karyawan;
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $cashier
                ], 201);
            }

            return redirect()->route('cashiers.index')
                ->with('info', $message);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan kasir: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menambahkan kasir: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified cashier
     */
    public function show(Cashier $cashier)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $cashier
            ]);
        }

        return view('cashiers.show', compact('cashier'));
    }

    /**
     * Show the form for editing the specified cashier
     */
    public function edit(Cashier $cashier)
    {
        $form = $this->formBuilder
            ->setModel($cashier)
            ->setAction(route('cashiers.update', $cashier), 'PUT')
            ->addInput('nama', 'Nama', 'text', ['required' => true])
            ->addInput('email', 'Email', 'email', ['required' => true])
            ->addInput('no_karyawan', 'No. Karyawan', 'text', ['required' => true]);

        return view('cashiers.edit', compact('form', 'cashier'));
    }

    /**
     * Update the specified cashier
     */
    public function update(CashierRequest $request, Cashier $cashier)
    {
        try {
            $cashier->update($request->validated());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kasir berhasil diperbarui',
                    'data' => $cashier->fresh()
                ]);
            }

            return redirect()->route('cashiers.index')
                ->with('success', 'Kasir berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui kasir: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal memperbarui kasir: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified cashier
     */
    public function destroy(Cashier $cashier)
    {
        try {
            // Check if cashier has sales records
            $hasSales = $cashier->sales()->exists();
            $cashierName = $cashier->nama;
            
            $cashier->delete();
            
            $message = "Kasir '{$cashierName}' berhasil dihapus";
            $messageType = 'success';
            
            if ($hasSales) {
                $message = "Kasir '{$cashierName}' berhasil dihapus. Perhatian: Kasir memiliki riwayat penjualan yang masih tersimpan";
                $messageType = 'warning';
            }
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('cashiers.index')
                ->with($messageType, $message);
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus kasir: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal menghapus kasir: ' . $e->getMessage());
        }
    }

    /**
     * Get JSON data for GridBuilder
     */
    public function getData(Request $request): JsonResponse
    {
        return response()->json(
            $this->gridBuilder
                ->setModel(Cashier::class)
                ->setSearchable(['nama', 'email', 'no_karyawan'])
                ->getJsonData($request)
        );
    }
}