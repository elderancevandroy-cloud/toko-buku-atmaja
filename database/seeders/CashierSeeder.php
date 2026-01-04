<?php

namespace Database\Seeders;

use App\Models\Cashier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashierSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cashiers = [
            [
                'nama' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@bookstore.com',
                'no_karyawan' => 'KSR001',
            ],
            [
                'nama' => 'Ahmad Rizki',
                'email' => 'ahmad.rizki@bookstore.com',
                'no_karyawan' => 'KSR002',
            ],
            [
                'nama' => 'Dewi Sartika',
                'email' => 'dewi.sartika@bookstore.com',
                'no_karyawan' => 'KSR003',
            ],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@bookstore.com',
                'no_karyawan' => 'KSR004',
            ],
            [
                'nama' => 'Maya Indira',
                'email' => 'maya.indira@bookstore.com',
                'no_karyawan' => 'KSR005',
            ],
            [
                'nama' => 'Rudi Hermawan',
                'email' => 'rudi.hermawan@bookstore.com',
                'no_karyawan' => 'KSR006',
            ],
            [
                'nama' => 'Lina Marlina',
                'email' => 'lina.marlina@bookstore.com',
                'no_karyawan' => 'KSR007',
            ],
            [
                'nama' => 'Eko Prasetyo',
                'email' => 'eko.prasetyo@bookstore.com',
                'no_karyawan' => 'KSR008',
            ],
        ];

        foreach ($cashiers as $cashier) {
            Cashier::create($cashier);
        }
    }
}