<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'buku';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'judul',
        'pengarang',
        'penerbit',
        'harga',
        'stok',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
    ];

    /**
     * Get the purchases for the book.
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'buku_id');
    }

    /**
     * Get the sale details for the book.
     */
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'buku_id');
    }

    /**
     * Get formatted title with stock for select options.
     */
    public function getJudulWithStockAttribute()
    {
        return "{$this->judul} (Stok: {$this->stok}, Harga: Rp " . number_format($this->harga, 0, ',', '.') . ")";
    }
}