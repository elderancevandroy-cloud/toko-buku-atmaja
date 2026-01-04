<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'detail_penjualan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'penjualan_id',
        'buku_id',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'jumlah' => 'integer',
    ];

    /**
     * Get the sale that owns the sale detail.
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class, 'penjualan_id');
    }

    /**
     * Get the book that owns the sale detail.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'buku_id');
    }
}