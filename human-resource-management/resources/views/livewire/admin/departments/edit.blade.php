<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Departments Form -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="relative mb-6 w-full">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Departments</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">Update Departments for {{ getCompany()->name}}</p>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                </div>

                <form wire:submit="save" class="my-6 w-full space-y-6">
                    <!-- Department Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Department Name *
                        </label>
                        <input 
                            type="text"
                            wire:model.live="department.name"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-200 @error('department.name') border-red-500 @enderror"
                            placeholder="Enter department name"
                        >
                        @error('department.name') 
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button 
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600 text-white font-medium py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors"
                        >
                            Update Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>