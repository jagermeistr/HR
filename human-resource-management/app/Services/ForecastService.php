<?php

namespace App\Services;

use App\Models\ProductionRecord;
use Exception;

class ForecastService
{
    /**
     * Generate SARIMA forecast by calling the Python script.
     *
     * @param int $days Number of days to forecast
     * @return array ['dates' => [], 'values' => [], 'avg' => float, 'confidence' => float]
     * @throws Exception
     */
    public function generateForecast(int $days = 30): array
    {
        // Load historical data
        $records = ProductionRecord::orderBy('production_date', 'asc')->get();

        if ($records->isEmpty()) {
            // No data — return fallback constant zeros
            $today = date('Y-m-d');
            $dates = [];
            $values = [];
            for ($i = 1; $i <= $days; $i++) {
                $dates[] = date('Y-m-d', strtotime("$today +{$i} days"));
                $values[] = 0.0;
            }

            return [
                'dates' => $dates,
                'values' => $values,
                'avg' => 0.0,
                'confidence' => 0.0,
            ];
        }

        $dates = $records->pluck('production_date')->map(function ($d) {
            return \Carbon\Carbon::parse($d)->toDateString();
        })->toArray();

        $values = $records->pluck('total_kgs')->map(function ($v) {
            return (float) $v;
        })->toArray();

        // Prepare JSON payload
        $payload = json_encode([
            'dates' => $dates,
            'values' => $values,
            'forecast_days' => $days,
        ]);

        // Path to Python script (ensure this file exists)
        $pythonScript = base_path('python/sarima_forecast.py');

        if (!file_exists($pythonScript)) {
            throw new Exception("Python SARIMA script not found at: {$pythonScript}");
        }

        // Use proc_open to call Python and send JSON via STDIN
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        // Use "python" — adjust to "python3" if your environment requires it
        $command = 'python "' . $pythonScript . '"';

        $process = proc_open($command, $descriptorspec, $pipes, base_path());

        if (!is_resource($process)) {
            throw new Exception('Failed to start Python process');
        }

        // Write input, close stdin
        fwrite($pipes[0], $payload);
        fclose($pipes[0]);

        // Read stdout and stderr
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        if ($returnCode !== 0 || !empty($stderr)) {
            // Return fallback if Python failed — include stderr for debugging
            throw new Exception("Python SARIMA error: {$stderr}");
        }

        $data = json_decode($stdout, true);

        if (!is_array($data) || !isset($data['forecast_dates']) || !isset($data['forecast_values'])) {
            throw new Exception('Invalid response from SARIMA script');
        }

        $forecastDates = $data['forecast_dates'];
        $forecastValues = array_map(function ($v) {
            return (float) round($v, 2);
        }, $data['forecast_values']);

        $avg = count($forecastValues) ? round(array_sum($forecastValues) / count($forecastValues), 2) : 0.0;

        // Basic confidence estimate (placeholder)
        $confidence = 0.85;

        return [
            'dates' => $forecastDates,
            'values' => $forecastValues,
            'avg' => $avg,
            'confidence' => round($confidence * 100, 1), // percent
        ];
    }
}
