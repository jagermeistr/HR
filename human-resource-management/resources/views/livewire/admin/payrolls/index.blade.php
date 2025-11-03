<div>
    <div class="mb-8">
        <flux:heading size="xl" class="text-gray-100 font-bold">Payrolls</flux:heading>
        <flux:subheading size="lg" class="mb-4 text-gray-400">Payrolls for {{ getCompany()->name }}</flux:subheading>
        <flux:separator class="mb-6 bg-gray-700"/>
    </div>

    <div class="flex justify-between items-center">
        <div class="w-full pr-4">
            <flux:input type="month" name="month" wire:model="monthYear" placeholder="Select Month and Year" class="w-full max-w-xs"/> 
        </div>
        <div>
            <flux:button variant="primary" wire:click="generatePayrolls">Generate Payrolls</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-4">
        @foreach($payrolls as $payroll)
            <div class="p-6 bg-nos-100 dark:bg-nos-900 text-nos-900 dark:text-white rounded-lg shadow-md hover:shadow-lg hover:bg-nos-200 dark:hover:bg-nos-500 transition duration-300 ease-in-out" wire:click="viewPayroll({{ $payroll->id }})">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold">{{ $payroll->month_string }}</h2>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ getCompany()->name }}
                    </p>
                </div>
                <div class="text-right flex flex-col justify-end text-green-600 dark:text-green-400">
                    <sup >KES</sup>
                    <span class="font-bold text-xl dark:text-green-200">{{ number_format($payroll->salaries?->sum('gross_salary')) }}</span>
                </div>
            </div>
        @endforeach            
        
    </div>
    
</div>
