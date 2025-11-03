<?php

namespace App\Livewire\Admin\Payrolls;

use Livewire\Component;
use App\Models\Payroll;
use App\Models\Salary;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Str;

class Show extends Component
{
    public $payroll;
    public function mount($id): void
    {
        $this->payroll =  Payroll::find($id);
        
    }

    public function generatePayslips($id): BinaryFileResponse
    {
        $salary = Salary::find($id);
        $pdf = Pdf::loadView('pdf.payslip', ['salary' => $salary]); 
        $pdf->setPaper(array(0, 0, 400, 1500), 'portrait');
        $filepath = storage_path(Str::slug($salary->employee->name).'-payslip.pdf');
        $pdf->save($filepath);
        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.admin.payrolls.show');
    }
}
