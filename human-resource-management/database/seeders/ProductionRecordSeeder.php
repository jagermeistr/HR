<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductionRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all collection center IDs
        $centerIds = DB::table('collection_centers')->pluck('id');
        
        if ($centerIds->isEmpty()) {
            $this->command->info('No collection centers found. Please run CollectionCenterSeeder first.');
            return;
        }

        $productionRecords = [];
        
        // Define the date range (last 30 days including today)
        $startDate = Carbon::now()->subDays(29); // 30 days total
        $endDate = Carbon::now();
        
        foreach ($centerIds as $centerId) {
            $currentDate = $startDate->copy();
            
            while ($currentDate->lte($endDate)) {
                // Get base capacity for this center
                $baseCapacity = $this->getBaseCapacity($centerId);
                
                // Add daily variation (-15% to +15%)
                $dailyVariation = rand(-15, 15);
                
                // Get seasonal factor based on date
                $seasonalFactor = $this->getSeasonalFactor($currentDate);
                
                // Calculate final production with variation and seasonal factors
                $totalKgs = $baseCapacity * (1 + $dailyVariation/100) * $seasonalFactor;
                
                // Ensure minimum production of 50 kgs
                $totalKgs = max(50, $totalKgs);
                
                $productionRecords[] = [
                    'collection_center_id' => $centerId,
                    'total_kgs' => round($totalKgs, 2),
                    'production_date' => $currentDate->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                $currentDate->addDay();
            }
        }

        // Insert all records
        DB::table('production_records')->insert($productionRecords);
        
        $this->command->info('Created ' . count($productionRecords) . ' production records (' . count($centerIds) . ' centers × 30 days).');
    }

    /**
     * Get base production capacity based on center ID
     */
    private function getBaseCapacity($centerId): float
    {
        // Different centers have different base capacities
        $baseCapacities = [
            500,  // Small center - 500 kgs/day
            800,  // Medium center - 800 kgs/day
            1200, // Large center - 1200 kgs/day
            650,  // Medium-small - 650 kgs/day
            950,  // Medium-large - 950 kgs/day
            1100, // Large - 1100 kgs/day
            700,  // Medium - 700 kgs/day
            850,  // Medium - 850 kgs/day
            1300, // Very large - 1300 kgs/day
            600,  // Small-medium - 600 kgs/day
            750,  // Medium - 750 kgs/day
            1000, // Large - 1000 kgs/day
            550,  // Small - 550 kgs/day
            900,  // Medium-large - 900 kgs/day
            1150, // Large - 1150 kgs/day
        ];

        return $baseCapacities[($centerId - 1) % count($baseCapacities)] ?? 750;
    }

    /**
     * Get seasonal factor based on month (Kenyan agricultural seasons)
     */
    private function getSeasonalFactor($date): float
    {
        $month = (int)$date->format('n');
        
        // Kenyan agricultural seasons:
        return match($month) {
            3, 4, 5, 6   => 1.3,    // High season - long rains harvest (March-June)
            10, 11, 12   => 1.1,    // Medium season - short rains harvest (Oct-Dec)
            1, 2, 7, 8, 9 => 0.8,   // Low season - planting/dry seasons
            default => 1.0
        };
    }
}