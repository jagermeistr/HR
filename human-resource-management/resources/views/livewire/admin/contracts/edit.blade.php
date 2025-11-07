<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl">Contracts</flux:heading>
        <flux:subheading size="lg" class="mb-6">Edit Contract</flux:subheading>
        <flux:separator />
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
            {{ session('warning') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="save" class="my-6 w-full space-y-6">
        <!-- Employee Search -->
        <div class="relative">
            <flux:input 
                type="search" 
                name="search" 
                wire:model.live="search" 
                placeholder="Search Employee" 
                :invalid="$errors->has('contract.employee_id')"
                wire:loading.attr="readonly"
            />
            
            <!-- Loading Indicator -->
            <div wire:loading class="absolute right-3 top-3">
                <svg class="animate-spin h-5 w-5 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            @if($search && $employees->count() > 0)
                <div class="absolute z-10 w-full mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md shadow-lg max-h-60 overflow-auto">
                    <ul class="w-full">
                        @foreach($employees as $employee)
                            <li 
                                class="p-3 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer transition-colors"
                                wire:click="selectEmployee({{ $employee->id }})"
                            >
                                <div class="flex justify-between items-center">
                                    <span>{{ $employee->name }}</span>
                                    @if($employee->id == $contract->employee_id)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Current</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($search && $employees->count() === 0 && !$isLoading)
                <div class="absolute z-10 w-full mt-1 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-md shadow-lg p-3">
                    <p class="text-zinc-500 text-center">No employees found</p>
                </div>
            @endif
        </div>
        <flux:error name="contract.employee_id" />

        <!-- Department and Designation -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <flux:select name="department" label="Department" wire:model.live="department_id">
                    <option value="" selected>Select Department</option>
                    @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div>
                <flux:select name="designation" label="Designation" wire:model="contract.designation_id" :invalid="$errors->has('contract.designation_id')">
                    <option value="" selected>Select Designation</option>
                    @foreach($designations as $designation)
                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="contract.designation_id" />
            </div>
        </div>

        <!-- Contract Dates -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <flux:input type="date" name="start_date" label="Start Date" wire:model.live="contract.start_date" :invalid="$errors->has('contract.start_date')" />
                <flux:error name="contract.start_date" />
            </div>
            <div>
                <flux:input type="date" name="end_date" label="End Date" wire:model.live="contract.end_date" :invalid="$errors->has('contract.end_date')" />
                <flux:error name="contract.end_date" />
            </div>
        </div>

        <!-- Rate and Rate Type -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <flux:input type="number" name="rate" label="Rate" wire:model.live="contract.rate" :invalid="$errors->has('contract.rate')" min="0" step="0.01" />
                <flux:error name="contract.rate" />
            </div>
            <div>
                <flux:select name="rate_type" label="Rate Type" wire:model.live="contract.rate_type" :invalid="$errors->has('contract.rate_type')">
                    <option value="" selected>Select Rate Type</option>
                    <option value="hourly">Hourly</option>
                    <option value="daily">Daily</option>
                    <option value="monthly">Monthly</option>
                </flux:select>
                <flux:error name="contract.rate_type" />
            </div>
        </div>

        <!-- Submit Button -->
        <flux:button type="submit" variant="filled" class="mt-4" wire:loading.attr="disabled">
            <span wire:loading.remove>Update Contract</span>
            <span wire:loading>
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Updating...
            </span>
        </flux:button>
    </form>
</div>