<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FarmerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $kenyanFirstNames = [
            'John', 'James', 'Peter', 'David', 'Michael', 'Joseph', 'Samuel', 'Daniel', 'Robert', 'Paul',
            'Mary', 'Grace', 'Sarah', 'Esther', 'Rebecca', 'Lydia', 'Faith', 'Joyce', 'Anne', 'Jane'
        ];

        $kenyanLastNames = [
            'Kamau', 'Mwangi', 'Kipchoge', 'Ochieng', 'Njoroge', 'Mutua', 'Gitonga', 'Omollo', 'Korir', 'Kiplagat',
            'Wanjiku', 'Achieng', 'Njeri', 'Wambui', 'Nyambura', 'Atieno', 'Chepkoech', 'Jepchumba', 'Chebet', 'Jerono'
        ];

        $kenyanTowns = [
            'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Malindi', 'Kitale', 'Kakamega', 'Nyeri',
            'Embu', 'Meru', 'Machakos', 'Kiambu', 'Bungoma', 'Busia', 'Garissa', 'Wajir', 'Lamu', 'Naivasha'
        ];

        for ($i = 0; $i < 100; $i++) {
            $firstName = $faker->randomElement($kenyanFirstNames);
            $lastName = $faker->randomElement($kenyanLastNames);
            $town = $faker->randomElement($kenyanTowns);

            DB::table('farmers')->insert([
                'name' => $firstName . ' ' . $lastName,
                'email' => strtolower($firstName . '.' . $lastName) . '@example.com',
                'phone' => '+2547' . $faker->numerify('#######'),
                'address' => $faker->buildingNumber . ' ' . $faker->streetName . ', ' . $town,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}