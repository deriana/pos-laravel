<?php

namespace Database\Seeders;

use App\Models\Categories;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        Categories::create([
            'name' => 'Elektronik',
            'description' => 'Produk-produk elektronik seperti TV, kulkas, dll.',
        ]);

        Categories::create([
            'name' => 'Pakaian',
            'description' => 'Pakaian pria dan wanita.',
        ]);
    }
}
