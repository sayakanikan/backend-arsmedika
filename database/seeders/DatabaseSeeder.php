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

        DB::table('products')->insert([
            'product_name' => 'PM-100',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('products')->insert([
            'product_name' => 'PM-200',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('products')->insert([
            'product_name' => 'PM-400',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('products')->insert([
            'product_name' => 'PM-800',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
