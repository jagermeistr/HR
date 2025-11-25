<div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Feedback Details -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h2 class="text-xl font-bold mb-4">Feedback Received</h2>
                    
                    <div class="space-y-2">
                        <p><strong>From:</strong> {{ $feedback->is_anonymous ? 'Anonymous' : $feedback->sender->name }}</p>
                        <p><strong>Type:</strong> 
                            <span class="px-2 py-1 rounded-full text-xs font-medium 
                                {{ $feedback->type === 'positive' ? 'bg-green-100 text-green-800' : 
                                   ($feedback->type === 'constructive' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($feedback->type) }}
                            </span>
                        </p>
                        <p><strong>Date:</strong> {{ $feedback->created_at->format('F j, Y') }}</p>
                    </div>

                    <div class="mt-4 p-4 bg-white rounded border">
                        <strong>Message:</strong>
                        <p class="mt-2 whitespace-pre-wrap">{{ $feedback->message }}</p>
                    </div>
                </div>

                <!-- Response Form -->
                <form wire:submit="submitResponse">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Your Email Address *
                        </label>
                        <input 
                            type="email" 
                            wire:model="employee_email"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter your company email"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Your Response *
                        </label>
                        <textarea 
                            wire:model="response"
                            rows="6"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Share your thoughts, acknowledge the feedback, or ask for clarification..."
                            required
                        ></textarea>
                    </div>

                    <button 
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Submit Response
                    </button>
                </form>

                <!-- Previous Responses -->
                @if($feedback->responses->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Previous Responses</h3>
                        <div class="space-y-4">
                            @foreach($feedback->responses as $response)
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <div class="flex justify-between items-start mb-1">
                                        <strong class="text-blue-600">{{ $response->responder_name }}</strong>
                                        <span class="text-sm text-gray-500">{{ $response->created_at->format('M j, Y') }}</span>
                                    </div>
                                    <p class="text-gray-700">{{ $response->response }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>