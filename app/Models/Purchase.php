<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pembelian';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'distributor_id',
        'buku_id',
        'jumlah',
        'harga_beli',
        'total',
        'tanggal_pembelian',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'harga_beli' => 'decimal:2',
        'total' => 'decimal:2',
        'jumlah' => 'integer',
        'tanggal_pembelian' => 'date',
    ];

    /**
     * Get the distributor that owns the purchase.
     */
    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'distributor_id');
    }

    /**
     * Get the book that owns the purchase.
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'buku_id');
    }
}