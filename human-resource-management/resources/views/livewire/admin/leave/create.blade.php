<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Create Leave Request</h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400">Add new leave request for employee</p>
                        </div>
                        <a 
                            href="{{ route('leave.index') }}"
                            class="bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors"
                        >
                            ← Back to Leaves
                        </a>
                    </div>
                </div>

                <!-- Leave Request Form -->
                <form wire:submit="save" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Employee -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Employee *
                            </label>
                            <select 
                                wire:model="form.employee_id"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('form.employee_id') border-red-500 @enderror"
                            >
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }} - {{ $employee->designation->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('form.employee_id') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Leave Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Leave Type *
                            </label>
                            <select 
                                wire:model="form.leave_type_id"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('form.leave_type_id') border-red-500 @enderror"
                            >
                                <option value="">Select Leave Type</option>
                                @foreach($leaveTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('form.leave_type_id') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Start Date *
                            </label>
                            <input 
                                type="date"
                                wire:model="form.start_date"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('form.start_date') border-red-500 @enderror"
                            >
                            @error('form.start_date') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- End Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                End Date *
                            </label>
                            <input 
                                type="date"
                                wire:model="form.end_date"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('form.end_date') border-red-500 @enderror"
                            >
                            @error('form.end_date') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status *
                            </label>
                            <select 
                                wire:model="form.status"
                                class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('form.status') border-red-500 @enderror"
                            >
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                            @error('form.status') 
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reason *
                        </label>
                        <textarea 
                            wire:model="form.reason"
                            rows="4"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('form.reason') border-red-500 @enderror"
                            placeholder="Enter reason for leave request..."
                        ></textarea>
                        @error('form.reason') 
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 pt-6">
                        <a 
                            href="{{ route('leave.index') }}"
                            class="px-6 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition-colors"
                        >
                            Cancel
                        </a>
                        <button 
                            type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-green-600 dark:bg-green-700 border border-transparent rounded-md shadow-sm hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:focus:ring-offset-gray-800 transition-colors"
                        >
                            Create Leave Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>