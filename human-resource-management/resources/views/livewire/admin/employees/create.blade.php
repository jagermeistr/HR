<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Employees Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="relative mb-6 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Employees</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">Create Employees for {{ getCompany()->name}}</p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <form wire:submit="save" class="my-6 w-full space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Employee Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Employee Name *
                            </label>
                            <input 
                                type="text"
                                wire:model.live="employee.name"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('employee.name') border-red-500 @enderror"
                                placeholder="Enter employee name"
                            >
                            @error('employee.name') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Employee Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Employee Email *
                            </label>
                            <input 
                                type="email"
                                wire:model.live="employee.email"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('employee.email') border-red-500 @enderror"
                                placeholder="Enter employee email"
                            >
                            @error('employee.email') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Department *
                            </label>
                            <select 
                                wire:model.live="department_id"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('department_id') border-red-500 @enderror"
                            >
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Designation -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Designation *
                            </label>
                            <select 
                                wire:model.live="employee.designation_id"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('employee.designation_id') border-red-500 @enderror"
                            >
                                <option value="">Select Designation</option>
                                @foreach($designations as $designation)
                                    <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                @endforeach
                            </select>
                            @error('employee.designation_id') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number *
                            </label>
                            <input 
                                type="text"
                                wire:model.live="employee.phone"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('employee.phone') border-red-500 @enderror"
                                placeholder="Enter phone number"
                            >
                            @error('employee.phone') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Address *
                            </label>
                            <input 
                                type="text"
                                wire:model.live="employee.address"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('employee.address') border-red-500 @enderror"
                                placeholder="Enter address"
                            >
                            @error('employee.address') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        

                        <!-- Hidden Company ID Field -->
                        <input type="hidden" wire:model="employee.company_id" value="{{ getCompany()->id }}">
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                        >
                            Save Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>