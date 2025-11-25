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
                        <div class="flex space-x-3">
                            <!-- Import Button -->
                            <button 
                                type="button"
                                wire:click="openImportModal"
                                class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                            >
                                📥 Import CSV
                            </button>
                            <a 
                                href="{{ route('leave.index') }}"
                                class="bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors"
                            >
                                ← Back to Leaves
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Leave Request Form -->
                <form wire:submit="save" class="space-y-6">
                    <!-- Your existing form content remains the same -->
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

    <!-- Import Modal -->
    @if($showImportModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <!-- Modal Header -->
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-200">Import Leave Requests</h3>
                <button wire:click="closeImportModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="space-y-4">
                <!-- Download Template -->
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-md">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-medium text-blue-800 dark:text-blue-300">Download Template</h4>
                            <p class="text-sm text-blue-600 dark:text-blue-400">Use our CSV template to ensure proper formatting</p>
                        </div>
                        <button 
                            wire:click="downloadTemplate"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm"
                        >
                            📥 Download Template
                        </button>
                    </div>
                </div>

                <!-- File Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Upload CSV File *
                    </label>
                    <input 
                        type="file"
                        wire:model="importFile"
                        accept=".csv,.txt"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200"
                    >
                    @error('importFile') 
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">Supported formats: .csv, .txt (Max: 10MB)</p>
                </div>

                <!-- Import Errors -->
                @if(count($importErrors) > 0)
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                    <h4 class="font-medium text-red-800 dark:text-red-300 mb-2">Import Errors ({{ count($importErrors) }}):</h4>
                    <div class="max-h-40 overflow-y-auto">
                        @foreach($importErrors as $error)
                        <div class="text-sm text-red-600 dark:text-red-400 mb-1">
                            {{ $error }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Success Message -->
                @if($importSuccess)
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-green-800 dark:text-green-300">Successfully imported {{ $importedCount }} leave requests!</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-3 pt-6">
                <button 
                    wire:click="closeImportModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                >
                    Cancel
                </button>
                <button 
                    wire:click="importLeaveRequests"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 dark:bg-green-700 border border-transparent rounded-md hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500"
                >
                    <span wire:loading.remove>Import File</span>
                    <span wire:loading>Importing...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>