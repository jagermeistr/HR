<div class="p-8 bg-gray-900 rounded-lg shadow-lg">
    <div class="mb-8">
        <flux:heading size="xl" class="text-gray-100 font-bold">Employees</flux:heading>
        <flux:subheading size="lg" class="mb-4 text-gray-400">List of Employees for {{ getCompany()->name }}</flux:subheading>
        <flux:separator class="mb-6 bg-gray-700"/>
    </div>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="shadow border border-gray-800 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">#</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Employee Name</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Designation</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-800">
                            @foreach($employees as $key => $employee)
                            <tr class="hover:bg-gray-800 transition">
                                <td class="px-4 py-3 text-sm text-gray-100">{{ $key+1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-100 flex flex-col font-medium">
                                    <p>{{ $employee->name }}</p>
                                    <p> </p>
                                    <p>{{ $employee->email }}</p>

                                </td>
                                <td class="px-4 py-3 text-sm text-gray-400">
                                    <div class="text-lg">{{ $employee->designation->name }}</div>
                                    <p>{{$employee->designation->department->name}}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <flux:button variant="filled" icon="pencil" :href="route('employees.edit', $employee->id)" class="!px-2 !py-1 !bg-gray-700 !text-gray-100 hover:!bg-gray-600"/>
                                        <flux:button variant="danger" icon="trash" wire:click="delete({{ $employee->id }})" class="!px-2 !py-1 !bg-red-700 !text-gray-100 hover:!bg-red-600"/>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4 px-4">
                        {{ $employees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
