<?php

namespace Database\Seeders;

use App\Models\Distributor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistributorSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $distributors = [
            [
                'nama' => 'PT Gramedia Pustaka Utama',
                'alamat' => 'Jl. Palmerah Selatan No. 22-28, Jakarta Pusat 10270',
                'telepon' => '021-5365555',
                'email' => 'info@gramedia.com',
            ],
            [
                'nama' => 'PT Bentang Pustaka',
                'alamat' => 'Jl. Gegerkalong Hilir No. 84, Bandung 40153',
                'telepon' => '022-2013163',
                'email' => 'bentang@bentangpustaka.com',
            ],
            [
                'nama' => 'PT Mizan Pustaka',
                'alamat' => 'Jl. Cinambo No. 135, Bandung 40294',
                'telepon' => '022-7834310',
                'email' => 'mizan@mizan.com',
            ],
            [
                'nama' => 'PT Erlangga',
                'alamat' => 'Jl. H. Baping Raya No. 100, Jakarta Timur 13560',
                'telepon' => '021-8690588',
                'email' => 'info@erlangga.co.id',
            ],
            [
                'nama' => 'PT Republika Penerbit',
                'alamat' => 'Jl. Warung Buncit Raya No. 37, Jakarta Selatan 12510',
                'telepon' => '021-7918001',
                'email' => 'penerbit@republika.co.id',
            ],
            [
                'nama' => 'PT Hasta Mitra',
                'alamat' => 'Jl. Gelora I No. 4, Jakarta Pusat 10270',
                'telepon' => '021-5711144',
                'email' => 'hastamitra@hastamitra.com',
            ],
            [
                'nama' => 'CV Andi Offset',
                'alamat' => 'Jl. Beo No. 38-40, Yogyakarta 55281',
                'telepon' => '0274-561881',
                'email' => 'andi@andipublisher.com',
            ],
            [
                'nama' => 'PT Tiga Serangkai Pustaka Mandiri',
                'alamat' => 'Jl. Dr. Supomo No. 23, Solo 57141',
                'telepon' => '0271-714344',
                'email' => 'tigaserangkai@tigaserangkai.com',
            ],
            [
                'nama' => 'PT Kanisius',
                'alamat' => 'Jl. Cempaka No. 9, Yogyakarta 55166',
                'telepon' => '0274-588783',
                'email' => 'kanisius@kanisius.or.id',
            ],
            [
                'nama' => 'PT Grasindo',
                'alamat' => 'Jl. Palmerah Barat No. 33-37, Jakarta Barat 11480',
                'telepon' => '021-5483008',
                'email' => 'grasindo@grasindo.co.id',
            ],
        ];

        foreach ($distributors as $distributor) {
            Distributor::create($distributor);
        }
    }
}