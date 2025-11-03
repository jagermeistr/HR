<div>
     <div class="relative mb-6 w-full">
        <flux:heading size="xl">Departments</flux:heading>
        <flux:subheading size="lg" class="mb-6"> List of Departments for {{ getCompany()->name}}</flux:subheading>
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
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Department Name</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Number of Designations</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Number of Employees</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Actions</th>
                            
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departments as $key => $department)
                            <tr class="text-center bg-nos-100 hoverg-nos-50">
                                <td>{{ $key+1 }}</td>
                                <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-900 ">{{ $department->name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">{{ $department->designations->count() }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">{{ $department->employees->count() }}</td>
                                <td>
                                    <div>
                                        <flux:button variant="filled" icon="pencil" :href="route('departments.edit', $department->id) "/>
                                        <flux:button variant="danger" icon="trash" wire:click="delete({{ $department->id}})"/>

                                    </div>
                                </td>

                            </tr> 
                            @endforeach
                        </tbody>
                    </table>
                    <div>
                        {{ $departments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
