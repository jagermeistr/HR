<?php

namespace App\Livewire\Admin\Payments;

use App\Models\Employee;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\Company;
use App\Services\MpesaB2CService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $employees;
    public $selectedEmployee;
    public $amount;
    public $paymentMethod = 'cash';
    public $remarks = 'Salary Payment';
    public $showPaymentForm = false;

    protected $rules = [
        'selectedEmployee' => 'required|exists:employees,id',
        'amount' => 'required|numeric|min:10',
        'paymentMethod' => 'required|in:cash,mpesa,bank',
        'remarks' => 'required|string|max:255'
    ];

    public function mount()
    {
        $this->employees = Employee::all();
    }

    public function processPayment()
    {
        $this->validate();

        $employee = Employee::find($this->selectedEmployee);

        if ($this->paymentMethod === 'mpesa') {
            $this->processMpesaPayment($employee);
        } else {
            $this->createPaymentRecord($employee, $this->paymentMethod, 'completed');
            session()->flash('message', 'Payment processed successfully!');
            $this->resetForm();
        }
    }

    public function processMpesaPayment($employee)
    {
        try {
            $mpesaService = app(MpesaB2CService::class);

            $response = $mpesaService->sendSalary(
                $employee->phone,
                floatval($this->amount),
                $this->remarks
            );

            if ($response['success']) {
                $payment = $this->createPaymentRecord($employee, 'mpesa', 'processing');

                // Save the COMPLETE M-Pesa response with Conversation IDs
                $payment->update([
                    'transaction_id' => $response['data']['TransactionID'] ?? null,
                    'mpesa_response' => [
                        'initial_response' => $response['data'],
                        'ConversationID' => $response['data']['ConversationID'] ?? null,
                        'OriginatorConversationID' => $response['data']['OriginatorConversationID'] ?? null,
                        'request_sent_at' => now()->toDateTimeString()
                    ]
                ]);

                session()->flash('message', 'M-Pesa payment initiated successfully! Status will update automatically.');
                $this->resetForm();
            } else {
                session()->flash('error', 'M-Pesa payment failed: ' . $response['message']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing M-Pesa payment: ' . $e->getMessage());
        }
    }

    protected function createPaymentRecord($employee, $method, $status)
    {
        // Get or create payroll with ALL required fields
        $payroll = Payroll::whereNotNull('company_id')
            ->whereNotNull('year')
            ->whereNotNull('month')
            ->whereNotNull('period')
            ->first();

        if (!$payroll) {
            // Get the first company or create a default one
            $company = \App\Models\Company::first();
            if (!$company) {
                $company = \App\Models\Company::create([
                    'name' => 'Default Company'
                ]);
            }

            // Create payroll with ALL required fields
            $payroll = Payroll::create([
                'company_id' => $company->id,
                'year' => now()->year,
                'month' => now()->month,
            ]);
        }

        return Payment::create([
            'employee_id' => $employee->id,
            'payroll_id' => $payroll->id,
            'amount' => $this->amount,
            'payment_method' => $method,
            'payment_date' => now(),
            'payment_status' => $status
        ]);
    }

    public function resetForm()
    {
        $this->reset(['selectedEmployee', 'amount', 'remarks']);
        $this->paymentMethod = 'cash';
    }

    public function render()
    {
        $payments = Payment::with(['employee', 'payroll'])->latest()->paginate(10);

        return view('livewire.admin.payments.index', compact('payments'));
    }
}
