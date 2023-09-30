<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductDetail>
 */
class ProductDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => 'RIKAM',
            'item' => 'Biaya kamar per-hari, maksimal 90 hari',
            'product_per' => 'day',
            'maximum_used' => 90,
        ];
    }
}
