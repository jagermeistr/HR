<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Tea Production Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Add Tea Production</h3>
                </div>

                <!-- Tabs for Manual Entry vs CSV Import -->
                <div class="mb-6">
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="-mb-px flex space-x-8">
                            <button
                                type="button"
                                wire:click="$set('activeTab', 'manual')"
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 ease-in-out"
                                :class="$activeTab === 'manual' 
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                                Manual Entry
                            </button>
                            <button
                                type="button"
                                wire:click="$set('activeTab', 'import')"
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200 ease-in-out"
                                :class="$activeTab === 'import' 
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400' 
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                                Import from CSV
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Manual Entry Form -->
                @if($activeTab === 'manual')
                <div>
                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 gap-4 mb-4">
                            <!-- Collection Center Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Collection Center *
                                </label>
                                <select
                                    wire:model="collection_center_id"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200">
                                    <option value="">-- Select Center --</option>
                                    @foreach($collectioncenters as $center)
                                    <option value="{{ $center->id }}">{{ $center->name }}</option>
                                    @endforeach
                                </select>
                                @error('collection_center_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Total Kilograms -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Total Kilograms Produced *
                                </label>
                                <input
                                    type="number"
                                    wire:model="total_kgs"
                                    min="0"
                                    step="0.01"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200"
                                    placeholder="Enter total kilograms">
                                @error('total_kgs')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                                Save Record
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- CSV Import Form -->
                @if($activeTab === 'import')
                <div>
                    <form wire:submit.prevent="importExcel">
                        <div class="mb-6">
                            <div class="flex items-center justify-center w-full">
                                <label for="excel-file" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 transition-colors duration-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="font-semibold">Click to upload</span> or drag and drop
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">CSV files only (.csv, .txt)</p>
                                        @if($excelFile)
                                        <p class="mt-2 text-sm font-medium text-blue-600 dark:text-blue-400">{{ $excelFile->getClientOriginalName() }}</p>
                                        @endif
                                    </div>
                                    <input
                                        id="excel-file"
                                        type="file"
                                        class="hidden"
                                        wire:model="excelFile"
                                        accept=".csv,.txt" />
                                </label>
                            </div>
                            @error('excelFile')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Template Download Section -->
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                        CSV Template
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-400">
                                        <p class="mb-2">Download our CSV template with the correct format:</p>
                                        <ul class="list-disc pl-5 space-y-1">
                                            <li>Column 1: Collection Center ID</li>
                                            <li>Column 2: Collection Center Name</li>
                                            <li>Column 3: Total Kilograms</li>
                                            <li>Column 4: Date (DD/MM/YYYY) - Use exact format: 24/11/2025</li>
                                        </ul>
                                        <button
                                            type="button"
                                            wire:click="downloadTemplate"
                                            class="mt-3 inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-800 dark:text-blue-200 dark:hover:bg-blue-700">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download Template
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Import Button -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove>Import Data</span>
                                <span wire:loading>Importing...</span>
                            </button>
                        </div>
                    </form>
                </div>
                @endif

                <!-- Success Message -->
                @if (session()->has('success'))
                <div class="mt-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-800 text-green-700 dark:text-green-300 rounded-md">
                    {{ session('success') }}
                </div>
                @endif

                @if (session()->has('warning'))
                <div class="mt-4 p-4 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-400 dark:border-yellow-800 text-yellow-700 dark:text-yellow-300 rounded-md">
                    {{ session('warning') }}
                </div>
                @endif

                @if (session()->has('error'))
                <div class="mt-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-800 text-red-700 dark:text-red-300 rounded-md">
                    {{ session('error') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>