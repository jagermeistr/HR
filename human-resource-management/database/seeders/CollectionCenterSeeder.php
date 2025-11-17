<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CollectionCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $kenyanTowns = [
            'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Malindi', 'Kitale', 
            'Kakamega', 'Nyeri', 'Embu', 'Meru', 'Machakos', 'Kiambu', 'Kisii', 'Garissa',
            'Naivasha', 'Kericho', 'Bungoma', 'Busia', 'Lamu', 'Wajir', 'Mandera', 'Marsabit'
        ];

        $kenyanFirstNames = [
            'Samuel', 'Fatima', 'Benjamin', 'Grace', 'Paul', 'Esther', 'David', 'Rebecca',
            'Peter', 'Sarah', 'John', 'Lucy', 'Ahmed', 'Abdi', 'Dorcas', 'James', 'Mary',
            'Michael', 'Anne', 'Joseph', 'Jane', 'Robert', 'Joyce', 'Daniel'
        ];

        $kenyanLastNames = [
            'Gitau', 'Ali', 'Omondi', 'Wanjiru', 'Kiprop', 'Muthoni', 'Muriuki', 'Nasimiyu',
            'Mutiso', 'Wekesa', 'Maina', 'Karimi', 'Mohammed', 'Hassan', 'Nyaboke', 'Kamau',
            'Wanjiku', 'Mwangi', 'Achieng', 'Njeri', 'Korir', 'Chebet', 'Kiplagat', 'Jerono'
        ];

        for ($i = 0; $i < 15; $i++) {
            $town = $faker->randomElement($kenyanTowns);
            $firstName = $faker->randomElement($kenyanFirstNames);
            $lastName = $faker->randomElement($kenyanLastNames);
            
            $centerTypes = ['Collection Center', 'Collection Point', 'Hub', 'Center', 'Collection Hub'];
            $centerType = $faker->randomElement($centerTypes);

            DB::table('collection_centers')->insert([
                'name' => $town . ' ' . $centerType,
                'location' => $faker->streetAddress . ', ' . $town,
                'manager_name' => $firstName . ' ' . $lastName,
                'contact' => '+254-' . $faker->numerify('##-#######'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}