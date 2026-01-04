<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'penjualan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kasir_id',
        'total_harga',
        'tanggal_penjualan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_harga' => 'decimal:2',
        'tanggal_penjualan' => 'date',
    ];

    /**
     * Get the cashier that owns the sale.
     */
    public function cashier()
    {
        return $this->belongsTo(Cashier::class, 'kasir_id');
    }

    /**
     * Get the sale details for the sale.
     */
    public function details()
    {
        return $this->hasMany(SaleDetail::class, 'penjualan_id');
    }
}