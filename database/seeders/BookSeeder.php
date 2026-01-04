<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'judul' => 'Laskar Pelangi',
                'pengarang' => 'Andrea Hirata',
                'penerbit' => 'Bentang Pustaka',
                'harga' => 75000,
                'stok' => 25,
            ],
            [
                'judul' => 'Bumi Manusia',
                'pengarang' => 'Pramoedya Ananta Toer',
                'penerbit' => 'Hasta Mitra',
                'harga' => 85000,
                'stok' => 15,
            ],
            [
                'judul' => 'Ayat-Ayat Cinta',
                'pengarang' => 'Habiburrahman El Shirazy',
                'penerbit' => 'Republika',
                'harga' => 65000,
                'stok' => 30,
            ],
            [
                'judul' => 'Negeri 5 Menara',
                'pengarang' => 'Ahmad Fuadi',
                'penerbit' => 'Gramedia',
                'harga' => 70000,
                'stok' => 20,
            ],
            [
                'judul' => 'Perahu Kertas',
                'pengarang' => 'Dee Lestari',
                'penerbit' => 'Bentang Pustaka',
                'harga' => 68000,
                'stok' => 18,
            ],
            [
                'judul' => 'Sang Pemimpi',
                'pengarang' => 'Andrea Hirata',
                'penerbit' => 'Bentang Pustaka',
                'harga' => 72000,
                'stok' => 22,
            ],
            [
                'judul' => 'Ronggeng Dukuh Paruk',
                'pengarang' => 'Ahmad Tohari',
                'penerbit' => 'Gramedia',
                'harga' => 78000,
                'stok' => 12,
            ],
            [
                'judul' => 'Cantik Itu Luka',
                'pengarang' => 'Eka Kurniawan',
                'penerbit' => 'Gramedia',
                'harga' => 82000,
                'stok' => 16,
            ],
            [
                'judul' => 'Pulang',
                'pengarang' => 'Leila S. Chudori',
                'penerbit' => 'Kepustakaan Populer Gramedia',
                'harga' => 89000,
                'stok' => 14,
            ],
            [
                'judul' => 'Hujan',
                'pengarang' => 'Tere Liye',
                'penerbit' => 'Gramedia Pustaka Utama',
                'harga' => 58000,
                'stok' => 35,
            ],
            [
                'judul' => 'Bintang',
                'pengarang' => 'Tere Liye',
                'penerbit' => 'Gramedia Pustaka Utama',
                'harga' => 60000,
                'stok' => 28,
            ],
            [
                'judul' => 'Matahari',
                'pengarang' => 'Tere Liye',
                'penerbit' => 'Gramedia Pustaka Utama',
                'harga' => 62000,
                'stok' => 24,
            ],
            [
                'judul' => 'Filosofi Teras',
                'pengarang' => 'Henry Manampiring',
                'penerbit' => 'Kompas Gramedia',
                'harga' => 95000,
                'stok' => 40,
            ],
            [
                'judul' => 'Atomic Habits',
                'pengarang' => 'James Clear',
                'penerbit' => 'Gramedia Pustaka Utama',
                'harga' => 120000,
                'stok' => 32,
            ],
            [
                'judul' => 'Sebuah Seni untuk Bersikap Bodo Amat',
                'pengarang' => 'Mark Manson',
                'penerbit' => 'Gramedia Pustaka Utama',
                'harga' => 98000,
                'stok' => 26,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}