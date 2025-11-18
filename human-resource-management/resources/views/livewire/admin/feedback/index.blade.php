<div>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                            Employee Feedback System
                        </h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('feedback.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                            Give Feedback
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Feedback System') }}
                </h2>
            </div>
        </header>

        <!-- Page Content -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 rounded">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Header -->
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-2xl font-bold">Employee Feedback</h3>
                            <a href="{{ route('feedback.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                Give Feedback
                            </a>
                        </div>

                        <!-- Tabs -->
                        <div class="mb-6">
                            <div class="border-b border-gray-200 dark:border-gray-700">
                                <nav class="-mb-px flex space-x-8">
                                    <button
                                        wire:click="switchTab('sent')"
                                        class="@if($activeTab === 'sent') border-blue-500 text-blue-600 dark:text-blue-400 @else border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 @endif whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                                        Sent Feedback ({{ $sentFeedback->total() }})
                                    </button>
                                </nav>
                            </div>
                        </div>

                        <!-- Sent Feedback -->
                        @if($activeTab === 'sent')
                            <div>
                                @forelse($sentFeedback as $feedback)
                                    <div class="mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-lg">
                                                        To: 
                                                        @if($feedback->employee)
                                                            {{ $feedback->employee->name }}
                                                            @if($feedback->employee->designation)
                                                                ({{ $feedback->employee->designation->name }})
                                                            @endif
                                                        @else
                                                            Employee Not Found
                                                        @endif
                                                    </h4>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $feedback->created_at->format('M d, Y \\a\\t g:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                                {{ $feedback->type === 'positive' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                   ($feedback->type === 'constructive' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                                   'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                                {{ ucfirst($feedback->type) }}
                                            </span>
                                        </div>
                                        
                                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                                            {{ Str::limit($feedback->message, 200) }}
                                        </p>
                                        
                                        <div class="flex justify-between items-center">
                                            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                                @if($feedback->responses->count() > 0)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                    <span>{{ $feedback->responses->count() }} response(s)</span>
                                                @endif
                                            </div>
                                            <div class="flex space-x-2">
                                                @if($feedback->status === 'draft')
                                                    <button 
                                                        wire:click="deleteFeedback({{ $feedback->id }})" 
                                                        wire:confirm="Are you sure you want to delete this feedback?"
                                                        class="bg-red-600 hover:bg-red-700 text-white font-medium py-1 px-3 rounded text-sm">
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-12">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-lg">You haven't sent any feedback yet.</p>
                                        <a href="{{ route('feedback.create') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                                            Send Your First Feedback
                                        </a>
                                    </div>
                                @endforelse

                                @if($sentFeedback->hasPages())
                                    <div class="mt-6">
                                        {{ $sentFeedback->links() }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>