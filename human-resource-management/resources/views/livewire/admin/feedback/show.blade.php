<div>
    
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
                            <x-flux::button :href="route('feedback.index')" variant="outline" icon="arrow-left" wire:navigate>
                                Back to Feedback
                            </x-flux::button>
                        </div>

                        <!-- Feedback Details -->
                        <div class="mb-8">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">From</label>
                                    <p class="mt-1 text-lg">
                                        {{ $feedback->is_anonymous ? 'Anonymous' : $feedback->sender->name }}
                                        @if($feedback->is_anonymous)
                                            <x-heroicon-s-user-circle class="w-5 h-5 text-gray-400 inline ml-1" />
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">To</label>
                                    <p class="mt-1 text-lg">{{ $feedback->receiver->name }}</p>
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
                                <x-heroicon-s-clock class="w-4 h-4 inline mr-1" />
                                Sent: {{ $feedback->sent_at->format('F j, Y \\a\\t g:i A') }}
                            </div>
                        </div>

                        <!-- Responses Section -->
                        <div>
                            <h3 class="text-xl font-semibold mb-4 flex items-center">
                                <x-heroicon-s-chat-bubble-left-right class="w-6 h-6 mr-2" />
                                Responses
                                <span class="ml-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-2 py-1 rounded-full text-sm">
                                    {{ $feedback->responses->count() }}
                                </span>
                            </h3>

                            <!-- Responses List -->
                            <div class="space-y-4 mb-6">
                                @foreach($feedback->responses as $response)
                                    <div class="border-l-4 border-primary-500 pl-4 py-2">
                                        <div class="flex justify-between items-start mb-2">
                                            <strong class="text-primary-600 dark:text-primary-400">
                                                {{ $response->user->name }}
                                            </strong>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $response->created_at->format('M j, Y \\a\\t g:i A') }}
                                            </span>
                                        </div>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $response->response }}</p>
                                    </div>
                                @endforeach

                                @if($feedback->responses->isEmpty())
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <x-heroicon-s-chat-bubble-left-right class="w-12 h-12 mx-auto mb-3" />
                                        <p>No responses yet.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Add Response Form -->
                            @if($feedback->canRespond(Auth::user()))
                                <form wire:submit="addResponse">
                                    <flux:input.group label="Your Response" required>
                                        <flux:input.textarea 
                                            wire:model="response" 
                                            rows="4" 
                                            placeholder="Share your thoughts or acknowledge the feedback..."
                                        />
                                        @error('response')
                                            <flux:input.error>{{ $message }}</flux:input.error>
                                        @enderror
                                    </flux:input.group>

                                    <div class="flex justify-end mt-4">
                                        <x-flux::button type="submit" icon="paper-airplane" variant="primary">
                                            Submit Response
                                        </x-flux::button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
</div>