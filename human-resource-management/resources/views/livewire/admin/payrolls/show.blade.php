<div>
    <div class="mb-8">
        <flux:heading size="xl" class="text-gray-100 font-bold">Payroll Details</flux:heading>
        
        @if($payroll)
            <flux:subheading size="lg" class="mb-4 text-gray-400">
                Payroll Breakdown for {{ getCompany()->name }} during {{ $payroll->month_string }}
            </flux:subheading>
        @else
            <flux:subheading size="lg" class="mb-4 text-gray-400">
                Payroll Breakdown
            </flux:subheading>
        @endif
        
        <flux:separator class="mb-6 bg-gray-700"/>
    </div>

    <!-- Error Messages -->
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-500 text-white rounded">
            {{ session('error') }}
        </div>
    @endif

    @if(!$payroll)
        <div class="text-center p-8 bg-gray-800 rounded-lg">
            <p class="text-gray-400">Payroll not found or you don't have access to view it.</p>
        </div>
    @elseif($payroll->salaries->count() === 0)
        <div class="text-center p-8 bg-gray-800 rounded-lg">
            <p class="text-gray-400">No salary records found for this payroll.</p>
        </div>
    @else
        <div class="flex flex-col">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="shadow border border-gray-800 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-800">
                            <thead class="bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Employee Details</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Gross Salary</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">NSSF</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">SHIF</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">AHL</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">PAYE</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Net Pay</th>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-900 divide-y divide-gray-800">
                                @foreach($payroll->salaries as $key => $salary)
                                <tr class="hover:bg-gray-800 transition">
                                    <td class="px-4 py-3 text-sm text-gray-100">{{ $key + 1 }}</td>
                                    <td class="px-4 py-3 text-sm flex flex-col font-medium">
                                        <h3 class="text-zinc-900 dark:text-zinc-200">{{ $salary->employee->name }}</h3>
                                        <h5 class="text-zinc-700 dark:text-zinc-300">{{ $salary->employee->designation->name ?? 'No Designation' }}</h5>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-400">
                                        <span><sup>KSH</sup>{{ number_format($salary->gross_salary, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-400">
                                        <span><sup>KSH</sup>{{ number_format($salary->breakdown->getNssfDeduction(), 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-400">
                                        <span><sup>KSH</sup>{{ number_format($salary->breakdown->getShifDeduction(), 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-400">
                                        <span><sup>KSH</sup>{{ number_format($salary->breakdown->getAhlDeduction(), 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-400">
                                        <span><sup>KSH</sup>{{ number_format($salary->breakdown->getPaye(), 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-400">
                                        <span><sup>KSH</sup>{{ number_format($salary->net_pay, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <flux:tooltip content="Generate Payslip">
                                                <flux:button variant="filled" icon="document-arrow-down" wire:click="generatePayslips({{ $salary->id }})"/>
                                            </flux:tooltip>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>