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
        
        // Use Faker's unique feature to ensure no duplicate emails
        $faker->unique(true); // true = reset when 1000 values are generated

        $kenyanFirstNames = [
            'John', 'James', 'Peter', 'David', 'Michael', 'Joseph', 'Samuel', 'Daniel', 'Robert', 'Paul',
            'Mary', 'Grace', 'Sarah', 'Esther', 'Rebecca', 'Lydia', 'Faith', 'Joyce', 'Anne', 'Jane',
            'Elizabeth', 'Susan', 'Margaret', 'Dorothy', 'Nancy', 'Karen', 'Betty', 'Helen', 'Sandra', 'Donna',
            'Carol', 'Ruth', 'Sharon', 'Michelle', 'Laura', 'Sarah', 'Kimberly', 'Deborah', 'Jessica', 'Shirley',
            'Cynthia', 'Angela', 'Melissa', 'Brenda', 'Amy', 'Anna', 'Rebecca', 'Virginia', 'Kathleen', 'Pamela'
        ];

        $kenyanLastNames = [
            'Kamau', 'Mwangi', 'Kipchoge', 'Ochieng', 'Njoroge', 'Mutua', 'Gitonga', 'Omollo', 'Korir', 'Kiplagat',
            'Wanjiku', 'Achieng', 'Njeri', 'Wambui', 'Nyambura', 'Atieno', 'Chepkoech', 'Jepchumba', 'Chebet', 'Jerono',
            'Omondi', 'Odhiambo', 'Okoth', 'Otieno', 'Owino', 'Abongo', 'Adongo', 'Akinyi', 'Anyango', 'Apiyo',
            'Barasa', 'Chege', 'Gachiri', 'Githinji', 'Kariuki', 'Kinyua', 'Maina', 'Mbugua', 'Muriuki', 'Muthoni',
            'Ndegwa', 'Ngugi', 'Nyong\'o', 'Oloo', 'Opiyo', 'Wairimu', 'Waweru', 'Wafula', 'Were', 'Yego'
        ];

        $kenyanTowns = [
            'Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika', 'Malindi', 'Kitale', 'Kakamega', 'Nyeri',
            'Embu', 'Meru', 'Machakos', 'Kiambu', 'Bungoma', 'Busia', 'Garissa', 'Wajir', 'Lamu', 'Naivasha',
            'Narok', 'Kajiado', 'Kericho', 'Bomet', 'Siaya', 'Homa Bay', 'Migori', 'Kisii', 'Nyamira', 'Muranga',
            'Kirinyaga', 'Nandi', 'Uasin Gishu', 'Trans Nzoia', 'West Pokot', 'Samburu', 'Turkana', 'Marsabit', 'Isiolo', 'Mandera'
        ];

        for ($i = 0; $i < 100; $i++) {
            $firstName = $faker->randomElement($kenyanFirstNames);
            $lastName = $faker->randomElement($kenyanLastNames);
            $town = $faker->randomElement($kenyanTowns);

            DB::table('farmers')->insert([
                'name' => $firstName . ' ' . $lastName,
                'email' => $faker->unique()->safeEmail(), // Use Faker's unique email generator
                'phone' => '+2547' . $faker->numerify('#######'),
                'address' => $faker->buildingNumber . ' ' . $faker->streetName . ', ' . $town,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}