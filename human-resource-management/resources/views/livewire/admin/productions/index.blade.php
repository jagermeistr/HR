<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tea Production Records -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <!-- Header Section -->
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Tea Production Records</h3>
                        @if($production_records->total() > 0)
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Showing {{ $production_records->firstItem() }} to {{ $production_records->lastItem() }} of {{ $production_records->total() }} records
                            </p>
                        @endif
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        <!-- Search Box -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live="search"
                                placeholder="Search records..." 
                                class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-600 w-full sm:w-64"
                            >
                        </div>

                        <!-- Add New Record Button -->
                        <a 
                            href="{{ route('productions.create') }}" 
                            class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors whitespace-nowrap text-center"
                        >
                            + Add New Record
                        </a>
                    </div>
                </div>

                @if($production_records->total() === 0)
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                            @if($search)
                                No records found for "{{ $search }}"
                            @else
                                No production records
                            @endif
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            @if($search)
                                Try adjusting your search terms
                            @else
                                Get started by creating a new production record.
                            @endif
                        </p>
                        @if(!$search)
                        <div class="mt-6">
                            <a 
                                href="{{ route('productions.create') }}" 
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                            >
                                + Add New Record
                            </a>
                        </div>
                        @endif
                    </div>
                @else
                    <!-- Table Section -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            #
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('collection_center_id')">
                                        <div class="flex items-center">
                                            Collection Center
                                            <span class="ml-1">
                                                @if($sortBy === 'collection_center_id')
                                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                                @else
                                                    ↕
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('total_kgs')">
                                        <div class="flex items-center">
                                            Total Kgs
                                            <span class="ml-1">
                                                @if($sortBy === 'total_kgs')
                                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                                @else
                                                    ↕
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer" wire:click="sortBy('production_date')">
                                        <div class="flex items-center">
                                            Produced On
                                            <span class="ml-1">
                                                @if($sortBy === 'production_date')
                                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                                @else
                                                    ↕
                                                @endif
                                            </span>
                                        </div>
                                    </th>
                                    
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($production_records as $production)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-gray-100">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <div class="flex items-center">
                                                <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                                                {{ $production->collection_center ? $production->collection_center->name : 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <span class="font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                {{ number_format($production->total_kgs, 2) }} kgs
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($production->production_date)->format('M j, Y') }}
                                        </td>
                                       
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $production_records->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>