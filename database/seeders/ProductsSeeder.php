<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        Products::create([
            'category_id' => 1,
            'name' => 'TV LED 32 Inch',
            'sku' => 'TV32LED001',
            'product_image' => 'tv32.jpg',
            'purchase_price' => 2000000,
            'selling_price' => 2500000,
            'stock' => 10,
            'unit' => 'pcs',
        ]);

        Products::create([
            'category_id' => 2,
            'name' => 'Kaos Polos Hitam',
            'sku' => 'KPH001',
            'product_image' => 'kaos.jpg',
            'purchase_price' => 30000,
            'selling_price' => 50000,
            'stock' => 100,
            'unit' => 'pcs',
        ]);
    }
}
