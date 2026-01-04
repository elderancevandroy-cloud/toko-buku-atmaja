<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Cashier;
use App\Models\Distributor;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        // Get statistics
        $totalBooks = Book::count();
        $totalCashiers = Cashier::count();
        $totalDistributors = Distributor::count();
        $totalSales = Sale::count();
        $totalPurchases = Purchase::count();
        
        // Get total stock value
        $totalStockValue = Book::sum(DB::raw('stok * harga'));
        
        // Get recent books (last 5)
        $recentBooks = Book::latest()->limit(5)->get();
        
        // Get books with low stock (less than 10)
        $lowStockBooks = Book::where('stok', '<', 10)->orderBy('stok', 'asc')->limit(5)->get();
        
        // Get top selling books (if sales data exists)
        $topBooks = Book::withCount('saleDetails')
            ->orderBy('sale_details_count', 'desc')
            ->limit(5)
            ->get();
        
        // Monthly sales data for chart (last 6 months)
        $monthlySales = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Sale::whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();
            $monthlySales[] = [
                'month' => $date->format('M Y'),
                'count' => $count
            ];
        }
        
        return view('dashboard', compact(
            'totalBooks',
            'totalCashiers', 
            'totalDistributors',
            'totalSales',
            'totalPurchases',
            'totalStockValue',
            'recentBooks',
            'lowStockBooks',
            'topBooks',
            'monthlySales'
        ));
    }
}