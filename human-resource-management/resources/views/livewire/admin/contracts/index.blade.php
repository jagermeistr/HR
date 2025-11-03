<div class="p-8 bg-gray-900 rounded-lg shadow-lg">
    <div class="mb-8">
        <flux:heading size="xl" class="text-gray-100 font-bold">Contracts</flux:heading>
        <flux:subheading size="lg" class="mb-4 text-gray-400">List of Contracts for {{ getCompany()->name }}</flux:subheading>
        <flux:separator class="mb-6 bg-gray-700" />
    </div>
    <div class="flex flex-col">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full align-middle">
                <div class="shadow border border-gray-800 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-800">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">#</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Employee Details</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Contract Details</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">Rate</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-semibold text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-800">
                            @foreach($contracts as $key => $contract)
                            <tr class="hover:bg-gray-800 transition">
                                <td class="px-4 py-3 text-sm text-gray-100">{{ $key+1 }}</td>
                                <td class="px-4 py-3 text-sm text-gray-100 flex flex-col font-medium">
                                    <span class="font-semibold text-lg">{{ $contract->employee->name }}</span>
                                    <p> </p>
                                    <span>{{ $contract->employee->email }}</span>
                                    <p> </p>
                                    <span>{{ $contract->employee->phone }}</span>
                                    <p> </p>
                                    <span class="font-bold">{{ $contract->employee->designation->name }}</span>

                                </td>
                                <td class="px-4 py-3 text-sm text-gray-400">
                                    <h5>Start: {{ $contract->start_date}}</h5>
                                    <p>End: {{ $contract->end_date}}</p>
                                    <p class="font-semibold text-lg">Duration: {{ $contract->duration}} </p>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-100"> KES {{ $contract->rate }} {{$contract->rate_type}}</td>

                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <flux:button variant="filled" icon="pencil" :href="route('contracts.edit', $contract->id)" class="!px-2 !py-1 !bg-gray-700 !text-gray-100 hover:!bg-gray-600" />
                                        <flux:button variant="danger" icon="trash" wire:click="delete({{ $contract->id }})" class="!px-2 !py-1 !bg-red-700 !text-gray-100 hover:!bg-red-600" />
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-4 px-4">
                        {{ $contracts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>