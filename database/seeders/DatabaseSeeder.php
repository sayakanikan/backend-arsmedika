<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@gmail.com',
        ]);

        \App\Models\Product::factory()->create([
            'product_name' => 'PM-100',
        ]);
        \App\Models\Product::factory()->create([
            'product_name' => 'PM-200',
        ]);
        \App\Models\Product::factory()->create([
            'product_name' => 'PM-400',
        ]);
        \App\Models\Product::factory()->create([
            'product_name' => 'PM-800',
        ]);
        \App\Models\ProductDetail::factory()->create([
            'product_id' => '1',
            'code' => 'RIKAM',
            'item' => 'Biaya kamar per-hari, maksimal 90 hari',
            'value' => 100000,
            'product_per' => 'day',
            'maximum_used' => 90,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'product_id' => '1',
            'code' => 'RIKDU',
            'item' => 'Kunjungan dokter umum per-hari',
            'value' => 50000,
            'product_per' => 'day',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'product_id' => '1',
            'code' => 'RIKDS',
            'item' => 'Kunjungan dokter spesialis per-hari',
            'value' => 80000,
            'product_per' => 'day',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'product_id' => '2',
            'code' => 'RIKAM',
            'item' => 'Biaya kamar per-hari, maksimal 90 hari',
            'value' => 200000,
            'product_per' => 'day',
            'maximum_used' => 90,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'product_id' => '2',
            'code' => 'RIKDU',
            'item' => 'Kunjungan dokter umum per-hari',
            'value' => 60000,
            'product_per' => 'day',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'product_id' => '2',
            'code' => 'RIKDS',
            'item' => 'Kunjungan dokter spesialis per-hari',
            'value' => 100000,
            'product_per' => 'day',
            'maximum_used' => null,
        ]);
    }
}
