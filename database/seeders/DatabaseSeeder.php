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
            'code' => 'RIKAM',
            'item' => 'Biaya kamar per-hari, maksimal 90 hari',
            'product_per' => 'day',
            'maximum_used' => 90,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'code' => 'RIKDU',
            'item' => 'Kunjungan dokter umum per-hari',
            'product_per' => 'day',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'code' => 'RIKDS',
            'item' => 'Kunjungan dokter spesialis per-hari',
            'product_per' => 'day',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'code' => 'RIICU',
            'item' => 'ICU/NICCU per-hari, maksimal 90 hari',
            'product_per' => 'day',
            'maximum_used' => 90,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'code' => 'RIPOK',
            'item' => 'Pembedahan operasi kecil, per-operasi',
            'product_per' => 'used',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'code' => 'RIPOB',
            'item' => 'Pembedahan operasi besar, per-operasi',
            'product_per' => 'used',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'code' => 'RIANK',
            'item' => 'Aneka perawatan rumah sakit, per-tahun',
            'product_per' => 'year',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'code' => 'RIAMB',
            'item' => 'Ambulance, per-kejadian',
            'product_per' => 'used',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'code' => 'RILAB',
            'item' => 'Test diagnostik, laboratorium, per-tahun',
            'product_per' => 'year',
            'maximum_used' => null,
        ]);
        \App\Models\ProductDetail::factory()->create([
            'code' => 'RIOBT',
            'item' => 'Obat-obatan, per-tahun',
            'product_per' => 'year',
            'maximum_used' => null,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 1,
            'value' => 100000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 1,
            'value' => 200000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 1,
            'value' => 400000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 1,
            'value' => 800000,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 2,
            'value' => 50000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 2,
            'value' => 60000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 2,
            'value' => 70000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 2,
            'value' => 80000,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 3,
            'value' => 80000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 3,
            'value' => 100000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 3,
            'value' => 135000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 3,
            'value' => 200000,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 4,
            'value' => 150000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 4,
            'value' => 300000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 4,
            'value' => 600000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 4,
            'value' => 1200000,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 5,
            'value' => 1000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 5,
            'value' => 2000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 5,
            'value' => 4000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 5,
            'value' => 8000000,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 6,
            'value' => 3000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 6,
            'value' => 6000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 6,
            'value' => 12000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 6,
            'value' => 24000000,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 7,
            'value' => 1000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 7,
            'value' => 2000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 7,
            'value' => 4000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 7,
            'value' => 8000000,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 8,
            'value' => 50000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 8,
            'value' => 60000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 8,
            'value' => 70000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 8,
            'value' => 80000,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 9,
            'value' => 1000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 9,
            'value' => 2000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 9,
            'value' => 4000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 9,
            'value' => 8000000,
        ]);

        \App\Models\ProductValue::factory()->create([
            'product_id' => 1,
            'product_detail_id' => 10,
            'value' => 3000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 2,
            'product_detail_id' => 10,
            'value' => 6000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 3,
            'product_detail_id' => 10,
            'value' => 12000000,
        ]);
        \App\Models\ProductValue::factory()->create([
            'product_id' => 4,
            'product_detail_id' => 10,
            'value' => 24000000,
        ]);
    }
}
