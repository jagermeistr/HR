<div>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                            HR Feedback System
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('feedback.index') }}" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                            Back to Feedback
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Give Feedback') }}
                </h2>
            </div>
        </header>

        <!-- Page Content -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Feedback Form -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <div class="relative mb-6 w-full">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Employee Feedback</h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">Provide constructive feedback to your colleagues</p>
                            <div class="border-t border-gray-200 dark:border-gray-700"></div>
                        </div>

                        <form wire:submit="save" class="my-6 w-full space-y-6">
                            <!-- Flash Messages -->
                            @if (session('success'))
                            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded">
                                {{ session('success') }}
                            </div>
                            @endif

                            <!-- Recipient Information Section -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Recipient Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                             <!-- Recipient Selection - FIXED: Changed receiver_id to employee_id -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Recipient *
                                        </label>
                                        <select
                                            wire:model="employee_id"
                                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('employee_id') border-red-500 @enderror">
                                            <option value="">Select Employee</option>
                                            @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}">
                                                {{ $employee->name }}
                                                @if($employee->designation)
                                                    - {{ $employee->designation->name }}
                                                @endif
                                                @if($employee->designation && $employee->designation->department)
                                                    ({{ $employee->designation->department->name }})
                                                @endif
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('employee_id')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>


                                    <!-- Feedback Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Feedback Type *
                                        </label>
                                        <select
                                            wire:model="type"
                                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('type') border-red-500 @enderror">
                                            <option value="positive">👍 Positive Feedback</option>
                                            <option value="constructive">💡 Constructive Feedback</option>
                                            <option value="general">💬 General Feedback</option>
                                        </select>
                                        @error('type')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Feedback Details Section -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Feedback Details</h3>
                                <div class="grid grid-cols-1 gap-6">
                                    <!-- Feedback Message -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Your Feedback Message *
                                        </label>
                                        <textarea
                                            wire:model="message"
                                            rows="8"
                                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('message') border-red-500 @enderror"
                                            placeholder="Provide specific, actionable, and respectful feedback. Focus on behaviors and outcomes rather than personal attributes..."></textarea>
                                        @error('message')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                                            Be specific, constructive, and focus on observable behaviors or outcomes.
                                        </p>
                                    </div>

                                    <!-- Anonymous Option -->
                                    <div class="flex items-center">
                                        <input
                                            type="checkbox"
                                            wire:model="is_anonymous"
                                            id="is_anonymous"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <label for="is_anonymous" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Send feedback anonymously
                                        </label>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Your identity will be hidden from the recipient when this option is selected.
                                    </p>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-4">
                                <button
                                    type="button"
                                    onclick="window.history.back()"
                                    class="bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                                    Send Feedback
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>