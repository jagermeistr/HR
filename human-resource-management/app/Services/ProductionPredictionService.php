<?php

namespace App\Services;

use App\Models\ProductionRecord;
use Carbon\Carbon;
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

    public function generatePredictions($historicalData, $predictionPeriod)
    {
        // Use the existing method that actually works
        $trainingData = $this->prepareTrainingData($historicalData);
        
        if (count($trainingData['values']) >= 7) {
            return $this->calculateSeasonalPredictions($trainingData, $predictionPeriod);
        } else {
            return $this->generateFallbackPredictions($trainingData, $predictionPeriod);
        }
    }

    // Add the missing methods that are being called
    private function calculatePrediction($historicalData, $daysAhead): float
    {
        $trainingData = $this->prepareTrainingData($historicalData);
        
        if (count($trainingData['values']) < 7) {
            return count($trainingData['values']) > 0 ? end($trainingData['values']) : 0;
        }

        // Use seasonal prediction logic
        $seasonalPattern = $this->calculateSeasonalPattern($trainingData['values']);
        $trend = $this->calculateTrend($trainingData['values']);
        $lastValue = end($trainingData['values']);

        $seasonalIndex = ($daysAhead - 1) % count($seasonalPattern);
        $seasonalEffect = $seasonalPattern[$seasonalIndex];

        $predictedValue = $lastValue + ($trend * $daysAhead) + $seasonalEffect;
        return max(0, round($predictedValue, 2));
    }

    private function calculateConfidence(int $daysAhead, int $totalPredictionPeriod): float
    {
        // Confidence decreases as we predict further into the future
        $baseConfidence = 0.85; // 85% confidence for first day
        
        // Linear decrease in confidence
        $confidenceDecrease = (1 - $baseConfidence) / $totalPredictionPeriod;
        
        $confidence = $baseConfidence - ($confidenceDecrease * ($daysAhead - 1));
        
        // Ensure confidence doesn't go below a minimum
        return max(0.5, $confidence);
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
                'confidence' => $this->calculateConfidence($i, $periods)
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