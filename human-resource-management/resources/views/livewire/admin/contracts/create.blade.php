<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Contracts Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="relative mb-6 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Contracts</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">Create Contracts for {{ getCompany()->name}}</p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <form wire:submit="save" class="my-6 w-full space-y-6">
                    <!-- Employee Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search Employee *
                        </label>
                        <input 
                            type="search" 
                            wire:model.live="search"
                            placeholder="Search Employee"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('contract.employee_id') border-red-500 @enderror"
                        >
                        @error('contract.employee_id') 
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                        
                        <!-- Search Results Dropdown -->
                        @if($search != '' && $employees->count() > 0)
                        <div class="bg-white dark:bg-gray-700 w-full border border-gray-200 dark:border-gray-600 rounded-md shadow-md mt-1">
                            <ul class="w-full">
                                @foreach($employees as $employee)
                                <li 
                                    class="p-3 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-gray-100 cursor-pointer transition-colors border-b border-gray-100 dark:border-gray-600 last:border-b-0"
                                    wire:click="selectEmployee({{ $employee->id }})"
                                >
                                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ $employee->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->email }}</div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>

                    <!-- Department and Designation -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Department
                            </label>
                            <select 
                                wire:model="department_id"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                            >
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Designation *
                            </label>
                            <select 
                                wire:model="contract.designation_id"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('contract.designation_id') border-red-500 @enderror"
                            >
                                <option value="">Select Designation</option>
                                @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                @endforeach
                            </select>
                            @error('contract.designation_id') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Start Date *
                            </label>
                            <input 
                                type="date"
                                wire:model.live="contract.start_date"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('contract.start_date') border-red-500 @enderror"
                            >
                            @error('contract.start_date') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                End Date *
                            </label>
                            <input 
                                type="date"
                                wire:model.live="contract.end_date"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('contract.end_date') border-red-500 @enderror"
                            >
                            @error('contract.end_date') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Rate and Rate Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rate *
                            </label>
                            <input 
                                type="number"
                                wire:model.live="contract.rate"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('contract.rate') border-red-500 @enderror"
                                placeholder="Enter rate"
                            >
                            @error('contract.rate') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rate Type *
                            </label>
                            <select 
                                wire:model.live="contract.rate_type"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('contract.rate_type') border-red-500 @enderror"
                            >
                                <option value="">Select Rate Type</option>
                                <option value="daily">Daily</option>
                                <option value="monthly">Monthly</option>
                            </select>
                            @error('contract.rate_type') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                        >
                            Create Contract
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>