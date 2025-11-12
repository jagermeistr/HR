<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- farmers Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="relative mb-6 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Farmers</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">Create Farmers for {{ getCompany()->name}}</p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <form wire:submit="save" class="my-6 w-full space-y-6">
                    <!-- Farmer Information Section -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Farmer Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                           
                            <!-- Farmer Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Farmer Name *
                                </label>
                                <input
                                    type="text"
                                    wire:model.live="farmer.name"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('farmer.name') border-red-500 @enderror"
                                    placeholder="Enter farmer name">
                                @error('farmer.name')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Farmer Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Farmer Email *
                                </label>
                                <input
                                    type="email"
                                    wire:model.live="farmer.email"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('farmer.email') border-red-500 @enderror"
                                    placeholder="Enter farmer email">
                                @error('farmer.email')
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
                                    wire:model.live="farmer.phone"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('farmer.phone') border-red-500 @enderror"
                                    placeholder="Enter phone number">
                                @error('farmer.phone')
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
                                    wire:model.live="farmer.address"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('farmer.address') border-red-500 @enderror"
                                    placeholder="Enter address">
                                @error('farmer.address')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Production Details Section -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Production Details</h3>
                        <div class="grid grid-cols-1 gap-6">
                            <!-- KGs Supplied -->
                            <div class="max-w-xs">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    KGs Supplied *
                                </label>
                                <input
                                    type="number"
                                    wire:model.live="kgs_supplied"
                                    step="0.01"
                                    min="0"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('kgs_supplied') border-red-500 @enderror"
                                    placeholder="Enter KGs supplied">
                                @error('kgs_supplied')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                            Save Farmer & Production
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>