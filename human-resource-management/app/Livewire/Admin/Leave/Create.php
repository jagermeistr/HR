<?php

namespace App\Livewire\Admin\Leave;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Create extends Component
{
    use WithFileUploads;

    public $employees;
    public $leaveTypes;
    public $form = [
        'employee_id' => '',
        'leave_type_id' => '',
        'start_date' => '',
        'end_date' => '',
        'reason' => '',
        'status' => 'approved'
    ];

    // Import properties
    public $showImportModal = false;
    public $importFile;
    public $importErrors = [];
    public $importSuccess = false;
    public $importedCount = 0;

    protected $rules = [
        'form.employee_id' => 'required|exists:employees,id',
        'form.leave_type_id' => 'required|exists:leave_types,id',
        'form.start_date' => 'required|date|after_or_equal:today',
        'form.end_date' => 'required|date|after_or_equal:form.start_date',
        'form.reason' => 'required|string|min:10|max:500',
        'form.status' => 'required|in:pending,approved,rejected',
    ];

    // Import validation rules
    protected $importRules = [
        'importFile' => 'required|file|mimes:csv,txt|max:10240' // 10MB max
    ];

    public function mount()
    {
        $this->employees = Employee::inCompany()->get();
        $this->leaveTypes = LeaveType::where('is_active', true)->get();
    }

    public function save()
    {
        $this->validate();

        // Calculate total days (excluding weekends)
        $startDate = Carbon::parse($this->form['start_date']);
        $endDate = Carbon::parse($this->form['end_date']);
        $totalDays = $startDate->diffInDaysFiltered(fn($date) => !$date->isWeekend(), $endDate) + 1;

        LeaveRequest::create([
            'employee_id' => $this->form['employee_id'],
            'leave_type_id' => $this->form['leave_type_id'],
            'start_date' => $this->form['start_date'],
            'end_date' => $this->form['end_date'],
            'total_days' => $totalDays,
            'reason' => $this->form['reason'],
            'status' => $this->form['status'],
        ]);

        session()->flash('success', 'Leave request created successfully.');
        return redirect()->route('leave.index');
    }

    // Import Methods
    public function openImportModal()
    {
        $this->showImportModal = true;
        $this->importFile = null;
        $this->importErrors = [];
        $this->importSuccess = false;
        $this->importedCount = 0;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
        $this->importErrors = [];
        $this->importSuccess = false;
        $this->importedCount = 0;
    }


    public function importLeaveRequests()
    {
        $this->validate($this->importRules);

        try {
            $filePath = $this->importFile->getRealPath();
            $file = fopen($filePath, 'r');

            // Get column headers
            $headers = fgetcsv($file);
            $imported = 0;
            $errors = [];
            $rowNumber = 1;

            // Validate headers
            $expectedHeaders = ['employee_email', 'leave_type', 'start_date', 'end_date', 'reason', 'status'];
            if ($headers !== $expectedHeaders) {
                $errors[] = "Invalid file format. Expected headers: " . implode(', ', $expectedHeaders);
                fclose($file);
                $this->importErrors = $errors;
                return;
            }

            // Process each row
            while (($row = fgetcsv($file)) !== FALSE) {
                $rowNumber++;

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Check if row has correct number of columns
                if (count($row) !== count($headers)) {
                    $errors[] = "Row {$rowNumber}: Incorrect number of columns";
                    continue;
                }

                // Combine headers with row data
                $data = array_combine($headers, $row);

                // Process the row
                $result = $this->processImportRow($data, $rowNumber);

                if ($result === true) {
                    $imported++;
                } else {
                    $errors[] = $result;
                }
            }

            fclose($file);

            $this->importedCount = $imported;
            $this->importErrors = $errors;

            if (empty($errors)) {
                $this->importSuccess = true;
                session()->flash('success', "Successfully imported {$imported} leave requests.");
                $this->closeImportModal();
            }
        } catch (\Exception $e) {
            $this->importErrors = ["There was an error processing your file: " . $e->getMessage()];
        }
    }

    private function processImportRow($data, $rowNumber)
    {
        try {
            // Clean data
            $employeeEmail = trim($data['employee_email'] ?? '');
            $leaveTypeName = trim($data['leave_type'] ?? '');
            $startDate = trim($data['start_date'] ?? '');
            $endDate = trim($data['end_date'] ?? '');
            $reason = trim($data['reason'] ?? '');
            $status = trim($data['status'] ?? 'pending');

            // Validate required fields
            if (empty($employeeEmail)) {
                return "Row {$rowNumber}: Employee email is required";
            }

            if (empty($leaveTypeName)) {
                return "Row {$rowNumber}: Leave type is required";
            }

            if (empty($startDate)) {
                return "Row {$rowNumber}: Start date is required";
            }

            if (empty($endDate)) {
                return "Row {$rowNumber}: End date is required";
            }

            if (empty($reason)) {
                return "Row {$rowNumber}: Reason is required";
            }

            // Find employee
            $employee = Employee::where('email', $employeeEmail)->first();
            if (!$employee) {
                return "Row {$rowNumber}: Employee with email '{$employeeEmail}' not found";
            }

            // Find leave type
            $leaveType = LeaveType::where('name', $leaveTypeName)->first();
            if (!$leaveType) {
                return "Row {$rowNumber}: Leave type '{$leaveTypeName}' not found";
            }

            // Validate dates
            try {
                // Try multiple date formats
                $startDateObj = $this->parseDate($startDate);
                $endDateObj = $this->parseDate($endDate);

                if (!$startDateObj) {
                    return "Row {$rowNumber}: Invalid start date format. Use DD/MM/YYYY or YYYY-MM-DD";
                }

                if (!$endDateObj) {
                    return "Row {$rowNumber}: Invalid end date format. Use DD/MM/YYYY or YYYY-MM-DD";
                }

                if ($endDateObj->lt($startDateObj)) {
                    return "Row {$rowNumber}: End date must be after start date";
                }
            } catch (\Exception $e) {
                return "Row {$rowNumber}: Invalid date format. Use DD/MM/YYYY or YYYY-MM-DD";
            }

            // Validate reason length
            if (strlen($reason) < 10) {
                return "Row {$rowNumber}: Reason must be at least 10 characters";
            }

            // Validate status
            $validStatus = in_array(strtolower($status), ['pending', 'approved', 'rejected']) ? strtolower($status) : 'pending';

            // Calculate total days (excluding weekends)
            $totalDays = $startDateObj->diffInDaysFiltered(fn($date) => !$date->isWeekend(), $endDateObj) + 1;

            // Check for duplicate leave request (optional)
            $existingRequest = LeaveRequest::where('employee_id', $employee->id)
                ->where('start_date', $startDateObj->format('Y-m-d'))
                ->where('end_date', $endDateObj->format('Y-m-d'))
                ->first();

            if ($existingRequest) {
                return "Row {$rowNumber}: Leave request already exists for this employee and date range";
            }

            // Create leave request
            LeaveRequest::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveType->id,
                'start_date' => $startDateObj->format('Y-m-d'),
                'end_date' => $endDateObj->format('Y-m-d'),
                'total_days' => $totalDays,
                'reason' => $reason,
                'status' => $validStatus,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return "Row {$rowNumber}: " . $e->getMessage();
        }
    }

   private function parseDate($dateString)
{
    $dateString = trim($dateString);
    
    // If empty, return null
    if (empty($dateString)) {
        return null;
    }
    
    // Remove any time portion if present
    $dateString = explode(' ', $dateString)[0];
    
    // Try different date formats in order of preference
    
    // 1. Try DD/MM/YYYY format (with slashes)
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateString, $matches)) {
        $day = (int)$matches[1];
        $month = (int)$matches[2];
        $year = (int)$matches[3];
        
        if (checkdate($month, $day, $year)) {
            return Carbon::createFromDate($year, $month, $day)->startOfDay();
        }
    }
    
    // 2. Try DD-MM-YYYY format (with dashes)
    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $dateString, $matches)) {
        $day = (int)$matches[1];
        $month = (int)$matches[2];
        $year = (int)$matches[3];
        
        if (checkdate($month, $day, $year)) {
            return Carbon::createFromDate($year, $month, $day)->startOfDay();
        }
    }
    
    // 3. Try YYYY-MM-DD format
    if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $dateString, $matches)) {
        $year = (int)$matches[1];
        $month = (int)$matches[2];
        $day = (int)$matches[3];
        
        if (checkdate($month, $day, $year)) {
            return Carbon::createFromDate($year, $month, $day)->startOfDay();
        }
    }
    
    // 4. Try YYYY/MM/DD format
    if (preg_match('/^(\d{4})\/(\d{1,2})\/(\d{1,2})$/', $dateString, $matches)) {
        $year = (int)$matches[1];
        $month = (int)$matches[2];
        $day = (int)$matches[3];
        
        if (checkdate($month, $day, $year)) {
            return Carbon::createFromDate($year, $month, $day)->startOfDay();
        }
    }
    
    // 5. Try D/M/YYYY (single digit day/month)
    if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateString, $matches)) {
        $day = (int)$matches[1];
        $month = (int)$matches[2];
        $year = (int)$matches[3];
        
        if (checkdate($month, $day, $year)) {
            return Carbon::createFromDate($year, $month, $day)->startOfDay();
        }
    }
    
    // 6. Try D-M-YYYY (single digit day/month with dashes)
    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $dateString, $matches)) {
        $day = (int)$matches[1];
        $month = (int)$matches[2];
        $year = (int)$matches[3];
        
        if (checkdate($month, $day, $year)) {
            return Carbon::createFromDate($year, $month, $day)->startOfDay();
        }
    }
    
    // 7. Final fallback - let Carbon try to parse it
    try {
        return Carbon::parse($dateString)->startOfDay();
    } catch (\Exception $e) {
        return null;
    }
}

    public function downloadTemplate()
    {
        $templateData = [
            ['employee_email', 'leave_type', 'start_date', 'end_date', 'reason', 'status'],
            ['john@example.com', 'Annual Leave', '15/01/2024', '20/01/2024', 'Family vacation for 2 weeks', 'pending'],
            ['jane@example.com', 'Sick Leave', '10/01/2024', '12/01/2024', 'Medical appointment and recovery time', 'approved'],
        ];

        $fileName = 'leave-request-template.csv';
        $filePath = storage_path('app/templates/' . $fileName);

        // Ensure directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $file = fopen($filePath, 'w');
        foreach ($templateData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.admin.leave.create');
    }
}
