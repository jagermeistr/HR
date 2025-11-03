<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl">
            Companies
        </flux:heading>
        <flux:subheading size="lg" class="mb-6">
            List of Companies
        </flux:subheading>
        <flux:separator/>
    </div>

    <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-gray-200 md:rounded-lg">
                    <table class="min-w-full table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">#</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Company Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Number of Employees</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Website</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($companies as $company)
                            <tr class="text-center bg-nos-100 hoverg-nos-50">
                                <td>{{ $company->id}}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900 ">{{ $company->name }}
                                    <img class="h-10 w-10 rounded-full " src="{{ $company->logo_url }}" alt="{{ $company->name }} Logo"/>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">{{ $company->email }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">{{ $company->departments->flatMap->employees->count() }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">{{ $company->website }}</td>
                                <td>
                                    <div>
                                        <flux:button variant="filled" icon="pencil" :href="route('companies.edit', $company->id) "/>
                                        <flux:button variant="danger" icon="trash" wire:click="delete({{ $company->id}})"/>
                                        
                                    </div>
                                </td>

                            </tr> 
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
