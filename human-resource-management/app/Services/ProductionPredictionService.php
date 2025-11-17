<?php

namespace App\Services;

use Carbon\Carbon;

class ProductionPredictionService
{
    /**
     * Prepares training arrays (dates as ISO strings and numeric values)
     *
     * @param \Illuminate\Support\Collection|array $historicalData
     * @return array
     */
    public function prepareTrainingData($historicalData): array
    {
        $dates = [];
        $values = [];

        foreach ($historicalData as $record) {
            // Ensure we use ISO date strings only
            $date = Carbon::parse($record->date)->toDateString();
            $dates[] = $date;
            $values[] = (float) $record->total ?? (float) $record->total_kgs ?? 0.0;
        }

        return [
            'dates' => $dates,
            'values' => $values,
        ];
    }

    /**
     * Main entry to generate predictions
     *
     * @param \Illuminate\Support\Collection $historicalData
     * @param int $predictionPeriod (days)
     * @return array
     */
    public function generatePredictions($historicalData, int $predictionPeriod = 30): array
    {
        $training = $this->prepareTrainingData($historicalData);

        if (count($training['values']) >= 7) {
            return $this->calculateSeasonalPredictions($training, $predictionPeriod);
        }

        return $this->generateFallbackPredictions($training, $predictionPeriod);
    }

    /**
     * Seasonal + trend simple predictor (not full SARIMA, but robust)
     *
     * @param array $data (dates & values)
     * @param int $periods
     * @return array
     */
    private function calculateSeasonalPredictions(array $data, int $periods): array
    {
        $values = $data['values'];
        $dates = $data['dates'];
        $predictions = [];

        $seasonalPattern = $this->calculateSeasonalPattern($values);
        $trend = $this->calculateTrend($values);
        $lastValue = end($values) ?: 0.0;
        $lastDate = end($dates) ?: Carbon::now()->toDateString();

        for ($i = 1; $i <= $periods; $i++) {
            $seasonalIndex = ($i - 1) % count($seasonalPattern);
            $seasonalEffect = $seasonalPattern[$seasonalIndex] ?? 0;
            $predictedValue = $lastValue + ($trend * $i) + $seasonalEffect;
            $predictedValue = max(0, round($predictedValue, 2));

            $predictions[] = [
                'date' => Carbon::parse($lastDate)->addDays($i)->toDateString(), // ISO Y-m-d
                'predicted_kgs' => $predictedValue,
                'confidence' => round($this->calculateConfidence($i, $periods), 3) // 0..1
            ];
        }

        return $predictions;
    }

    /**
     * Build a weekly seasonal pattern (default 7-day season)
     *
     * @param array $values
     * @param int $seasonLength
     * @return array
     */
    private function calculateSeasonalPattern(array $values, int $seasonLength = 7): array
    {
        if (count($values) < $seasonLength) {
            return array_fill(0, $seasonLength, 0.0);
        }

        $pattern = array_fill(0, $seasonLength, 0.0);
        $counts = array_fill(0, $seasonLength, 0);

        foreach ($values as $idx => $val) {
            $slot = $idx % $seasonLength;
            $pattern[$slot] += $val;
            $counts[$slot]++;
        }

        for ($i = 0; $i < $seasonLength; $i++) {
            if ($counts[$i] > 0) {
                $pattern[$i] = $pattern[$i] / $counts[$i];
            } else {
                $pattern[$i] = 0.0;
            }
        }

        // Normalize to average effect (differences from overall mean)
        $overallAvg = array_sum($pattern) / $seasonLength;
        for ($i = 0; $i < $seasonLength; $i++) {
            $pattern[$i] = round($pattern[$i] - $overallAvg, 4);
        }

        return $pattern;
    }

    /**
     * Simple linear trend (slope) using least squares
     *
     * @param array $values
     * @return float
     */
    private function calculateTrend(array $values): float
    {
        $n = count($values);
        if ($n < 2) {
            return 0.0;
        }

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($values as $i => $y) {
            $x = $i + 1; // avoid zero-index for numerical stability
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }

        $den = ($n * $sumX2 - $sumX * $sumX);
        if ($den == 0) {
            return 0.0;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / $den;

        // slope per day
        return (float) $slope;
    }

    /**
     * Confidence decays with horizon; returns 0..1
     *
     * @param int $daysAhead
     * @param int $totalPredictionPeriod
     * @return float
     */
    private function calculateConfidence(int $daysAhead, int $totalPredictionPeriod): float
    {
        $baseConfidence = 0.85; // day 1
        $minConfidence = 0.5;   // floor
        if ($totalPredictionPeriod <= 1) {
            return $baseConfidence;
        }

        $decayPerDay = ($baseConfidence - $minConfidence) / $totalPredictionPeriod;
        $confidence = $baseConfidence - ($decayPerDay * ($daysAhead - 1));
        return max($minConfidence, round($confidence, 3));
    }

    /**
     * Fallback predictions (repeat last observed value)
     *
     * @param array $data
     * @param int $periods
     * @return array
     */
    private function generateFallbackPredictions(array $data, int $periods): array
    {
        $lastValue = !empty($data['values']) ? end($data['values']) : 0.0;
        $lastDate = !empty($data['dates']) ? end($data['dates']) : Carbon::now()->toDateString();

        $predictions = [];
        for ($i = 1; $i <= $periods; $i++) {
            $predictions[] = [
                'date' => Carbon::parse($lastDate)->addDays($i)->toDateString(),
                'predicted_kgs' => round($lastValue, 2),
                'confidence' => 0.5
            ];
        }

        return $predictions;
    }
}
