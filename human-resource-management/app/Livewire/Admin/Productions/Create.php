<?php

namespace App\Livewire\Admin\Productions;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ProductionRecord;
use App\Models\CollectionCenter;
use App\Models\Farmer;
use Illuminate\Support\Facades\Log;

class Create extends Component
{
    use WithFileUploads;

    public $collection_center_id;
    public $total_kgs;
    public $farmers;
    public $activeTab = 'manual';
    public $excelFile;

    public function render()
    {
        return view('livewire.admin.productions.create', [
            'farmers' => Farmer::where('company_id', session('company_id'))->get(),
            'collectioncenters' => CollectionCenter::all(),
        ]);
    }

    public function save()
    {
        $this->validate([
            'collection_center_id' => 'required',
            'total_kgs' => 'required|numeric|min:1',
        ]);

        ProductionRecord::create([
            'collection_center_id' => $this->collection_center_id,
            'total_kgs' => $this->total_kgs,
            'production_date' => now()->format('Y-m-d'), // Explicit format
        ]);

        session()->flash('success', 'Production record added successfully.');
        $this->reset(['collection_center_id', 'total_kgs']);
    }

    public function importExcel()
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $path = $this->excelFile->getRealPath();
            $importedCount = 0;
            $failedCount = 0;

            Log::info("Starting CSV import process");

            if (($handle = fopen($path, 'r')) !== FALSE) {
                $rowNumber = 0;

                while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $rowNumber++;

                    // Skip header row
                    if ($rowNumber === 1) {
                        Log::info("CSV Header: " . implode(', ', $row));
                        continue;
                    }

                    Log::info("Processing row {$rowNumber}: " . implode(', ', $row));

                    // Ensure we have at least 3 columns
                    if (count($row) >= 3 && !empty(trim($row[2]))) {
                        try {
                            $collectionCenter = null;

                            // Try to find collection center by ID (column 0)
                            if (!empty(trim($row[0]))) {
                                $collectionCenter = CollectionCenter::find(trim($row[0]));
                                Log::info("Looking for collection center by ID: " . trim($row[0]));
                            }

                            // If not found by ID, try by name (column 1)
                            if (!$collectionCenter && !empty(trim($row[1]))) {
                                Log::info("Looking for collection center by name: " . trim($row[1]));
                                $collectionCenter = CollectionCenter::where('name', 'like', '%' . trim($row[1]) . '%')->first();
                            }

                            if ($collectionCenter) {
                                Log::info("Found collection center: " . $collectionCenter->id . " - " . $collectionCenter->name);

                                // Parse the date safely
                                $dateInput = !empty(trim($row[3])) ? trim($row[3]) : null;
                                Log::info("Date input: " . $dateInput);

                                $productionDate = $this->parseDateSafely($dateInput);
                                Log::info("Parsed production date: " . $productionDate);

                                Log::info("Creating record with:", [
                                    'collection_center_id' => $collectionCenter->id,
                                    'total_kgs' => floatval(trim($row[2])),
                                    'production_date' => $productionDate
                                ]);

                                ProductionRecord::create([
                                    'collection_center_id' => $collectionCenter->id,
                                    'total_kgs' => floatval(trim($row[2])),
                                    'production_date' => $productionDate,
                                ]);

                                $importedCount++;
                                Log::info("Successfully imported row {$rowNumber}");
                            } else {
                                $failedCount++;
                                Log::warning("Collection center not found for row {$rowNumber}");
                            }
                        } catch (\Exception $e) {
                            $failedCount++;
                            Log::error("Import error on row {$rowNumber}: " . $e->getMessage());
                            Log::error("Stack trace: " . $e->getTraceAsString());
                        }
                    } else {
                        Log::warning("Row {$rowNumber} skipped - insufficient data or empty total_kgs");
                    }
                }
                fclose($handle);
            }

            if ($importedCount > 0 && $failedCount === 0) {
                session()->flash('success', "Successfully imported {$importedCount} production records!");
            } elseif ($importedCount > 0) {
                session()->flash('warning', "Imported {$importedCount} records successfully, {$failedCount} records failed.");
            } else {
                session()->flash('error', "No records were imported. Please check your CSV format.");
            }

            $this->reset('excelFile');
        } catch (\Exception $e) {
            session()->flash('error', 'Error importing file: ' . $e->getMessage());
            Log::error('Import file error: ' . $e->getMessage());
            Log::error('Import file stack trace: ' . $e->getTraceAsString());
        }
    }

    

    
    private function parseDateSafely($dateString)
    {
        if (empty($dateString)) {
            return now()->format('Y-m-d');
        }

        // Remove any time portion if present
        $dateString = explode(' ', $dateString)[0];
        $dateString = trim($dateString);

        // Handle DD/MM/YYYY format specifically
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateString, $matches)) {
            $day = (int)$matches[1];
            $month = (int)$matches[2];
            $year = (int)$matches[3];

            // Convert to YYYY-MM-DD format
            return sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        // If not in DD/MM/YYYY format, just use today's date
        return now()->format('Y-m-d');
    }

    public function downloadTemplate()
    {
        $templateData = "collection_center_id,collection_center_name,total_kgs,date\n";
        $templateData .= "1,Main Collection Center,150.5,24/11/2025\n";
        $templateData .= "2,North Collection Center,200.75,24/11/2025\n";
        $templateData .= "3,South Collection Center,180.25,24/11/2025\n";

        return response()->streamDownload(function () use ($templateData) {
            echo $templateData;
        }, 'tea-production-template.csv');
    }
}
