<?php

namespace App\Services;

use App\Models\ProductionRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ProductionPredictionService
{
    public function getHistoricalData($days = 365)
    {
        return ProductionRecord::select(
                DB::raw('DATE(production_date) as date'),
                DB::raw('SUM(total_kgs) as total_kgs')
            )
            ->where('production_date', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function prepareTrainingData($historicalData)
    {
        $dates = [];
        $values = [];
        
        foreach ($historicalData as $record) {
            $dates[] = $record->date;
            $values[] = (float) $record->total_kgs;
        }
        
        return [
            'dates' => $dates,
            'values' => $values
        ];
    }

    public function generatePredictions($historicalData, $periods = 30)
    {
        // For production environment, you'd integrate with Python ML service
        // For now, we'll implement a simple seasonal prediction
        
        $data = $this->prepareTrainingData($historicalData);
        
        if (count($data['values']) < 7) {
            return $this->generateFallbackPredictions($data, $periods);
        }
        
        return $this->calculateSeasonalPredictions($data, $periods);
    }

    private function calculateSeasonalPredictions($data, $periods)
    {
        $values = $data['values'];
        $lastDate = end($data['dates']);
        $predictions = [];
        
        // Simple seasonal average based prediction
        $seasonalPattern = $this->calculateSeasonalPattern($values);
        $trend = $this->calculateTrend($values);
        
        $lastValue = end($values);
        
        for ($i = 1; $i <= $periods; $i++) {
            $seasonalIndex = ($i - 1) % count($seasonalPattern);
            $seasonalEffect = $seasonalPattern[$seasonalIndex];
            
            $predictedValue = $lastValue + ($trend * $i) + $seasonalEffect;
            $predictedValue = max(0, $predictedValue); // Ensure non-negative
            
            $predictions[] = [
                'date' => date('Y-m-d', strtotime($lastDate . " +{$i} days")),
                'predicted_kgs' => round($predictedValue, 2),
                'confidence' => max(0.7, 1 - ($i * 0.01)) // Decreasing confidence
            ];
        }
        
        return $predictions;
    }

    private function calculateSeasonalPattern($values, $seasonLength = 7)
    {
        if (count($values) < $seasonLength) {
            return array_fill(0, $seasonLength, 0);
        }
        
        $pattern = array_fill(0, $seasonLength, 0);
        $counts = array_fill(0, $seasonLength, 0);
        
        foreach ($values as $index => $value) {
            $pattern[$index % $seasonLength] += $value;
            $counts[$index % $seasonLength]++;
        }
        
        // Calculate averages
        for ($i = 0; $i < $seasonLength; $i++) {
            if ($counts[$i] > 0) {
                $pattern[$i] = $pattern[$i] / $counts[$i];
            }
        }
        
        // Normalize pattern
        $overallAverage = array_sum($pattern) / $seasonLength;
        
        for ($i = 0; $i < $seasonLength; $i++) {
            $pattern[$i] = $pattern[$i] - $overallAverage;
        }
        
        return $pattern;
    }

    private function calculateTrend($values)
    {
        if (count($values) < 2) {
            return 0;
        }
        
        $n = count($values);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;
        
        foreach ($values as $index => $value) {
            $sumX += $index;
            $sumY += $value;
            $sumXY += $index * $value;
            $sumX2 += $index * $index;
        }
        
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        
        return $slope;
    }

    private function generateFallbackPredictions($data, $periods)
    {
        $lastValue = count($data['values']) > 0 ? end($data['values']) : 0;
        $lastDate = count($data['dates']) > 0 ? end($data['dates']) : date('Y-m-d');
        
        $predictions = [];
        
        for ($i = 1; $i <= $periods; $i++) {
            $predictions[] = [
                'date' => date('Y-m-d', strtotime($lastDate . " +{$i} days")),
                'predicted_kgs' => $lastValue,
                'confidence' => 0.5
            ];
        }
        
        return $predictions;
    }
}