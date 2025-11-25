<div>
    <!-- Use your app layout -->
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Feedback Details') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Flash Messages -->
                        @if (session('message'))
                        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded">
                            {{ session('message') }}
                        </div>
                        @endif

                        <!-- Back Button -->
                        <div class="mb-6">
                            <a href="{{ route('feedback.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                ← Back to Feedback
                            </a>
                        </div>

                        <!-- Feedback Details -->
                        <div class="mb-8">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">From</label>
                                    <p class="mt-1 text-lg">
                                        {{ $feedback->is_anonymous ? 'Anonymous' : $feedback->sender->name }}
                                        @if($feedback->is_anonymous)
                                        <svg class="w-5 h-5 text-gray-400 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">To</label>
                                    <p class="mt-1 text-lg">
                                        @if($feedback->employee)
                                        {{ $feedback->employee->name }}
                                        @else
                                        Employee Not Found
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</label>
                                    <p class="mt-1">
                                        <span class="px-3 py-1 rounded-full text-sm font-medium 
                                            {{ $feedback->type === 'positive' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                               ($feedback->type === 'constructive' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                               'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                            {{ ucfirst($feedback->type) }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 block">Message</label>
                                <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $feedback->message }}</p>
                            </div>

                            <!-- Timestamp -->
                            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Sent: {{ $feedback->created_at->format('F j, Y \\a\\t g:i A') }}
                            </div>
                        </div>

                        <div>
                            <<!-- Responses Section -->
                                <div class="mt-8">
                                    <h3 class="text-xl font-semibold mb-4">Employee Responses ({{ $feedback->responses->count() }})</h3>

                                    @foreach($feedback->responses as $response)
                                    <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex justify-between items-start mb-2">
                                            <strong class="text-blue-600 dark:text-blue-400">
                                                {{ $response->responder_name }}
                                            </strong>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $response->created_at->format('M j, Y \\a\\t g:i A') }}
                                            </span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $response->response }}</p>
                                    </div>
                                    @endforeach

                                    @if($feedback->responses->isEmpty())
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <p>No responses from employee yet.</p>
                                    </div>
                                    @endif
                                </div>

                                <!-- Add Response Form -->
                                @if(auth()->check())
                                <form wire:submit="addResponse">
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Your Response *
                                        </label>
                                        <textarea
                                            wire:model="response"
                                            rows="4"
                                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('response') border-red-500 @enderror"
                                            placeholder="Share your thoughts or acknowledge the feedback..."></textarea>
                                        @error('response')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                            Submit Response
                                        </button>
                                    </div>
                                </form>
                                @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>