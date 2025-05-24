<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Faker\Factory;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create();

        $count = 10;
        for ($i = 0; $i < $count; $i++) {
            Customer::create([
                'name' => $faker->name,
                'phone_number' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'address' => $faker->address,
            ]);
        }
    }
}
