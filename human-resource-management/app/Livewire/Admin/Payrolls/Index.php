<?php

namespace App\Livewire\Admin\Payrolls;

use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use PDO;
use Carbon\Carbon;
use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Validation\ValidationException;

class Index extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $monthYear;
    public function rules(): array
    {
        return [
            'monthYear' => 'required',
        ];
    }

    public function generatePayroll(): void
    {
        $this->validate();
        $date = Carbon::parse($this->monthYear);
        if (Payroll::inCompany()->where('month', $date->format('m'))->where('year', $date->format('Y'))->exists()) {
            throw ValidationException::withMessages(['monthYear' => 'Payroll for the selected month and year already exists.']);
        }else{
            $payroll = new Payroll();
            $payroll->month = $date->format('m');
            $payroll->year = $date->format('Y');
            $payroll->company_id = session('company_id');
            $payroll->save();
            foreach(Employee::inCompany()->get() as $employee) {
                $contract = $employee->activeContract(start_date: $date->startOfMonth()->toDateString(), end_date: $date->endOfMonth()->toDateString());
                if($contract) {
                    $payroll->salaries()->create([
                        'employee_id' => $employee->id,
                        'gross_salary' => $contract->getTotalEarnings($date->format('Y-m')),
                    ]);
                }
            }
            session()->flash('success', 'Payroll generated successfully.');
        }
    }

    public function updatePayroll($id): void
    {
        $payroll = Payroll::inCompany()->find($id);
        $payroll->salaries()->delete();
        foreach(Employee::inCompany()->get() as $employee) {
            $contract = $employee->activeContract(start_date: $payroll->year.'-'.$payroll->month.'-01', end_date: $payroll->year.'-'.$payroll->month.'-'.Carbon::parse($payroll->year.'-'.$payroll->month.'-01')->endOfMonth()->format('d'));
            if($contract) {
                $payroll->salaries()->create([
                    'employee_id' => $employee->id,
                    'gross_salary' => $contract->getTotalEarnings($payroll->year.'-'.$payroll->month),
                ]);
            }
        }
        session()->flash('success', 'Payroll updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.payrolls.index', [
            'payrolls' => Payroll::inCompany()->orderBy('year', 'desc')->orderBy('month', 'desc')->paginate(perPage: 10),
        ]);
    }
}
