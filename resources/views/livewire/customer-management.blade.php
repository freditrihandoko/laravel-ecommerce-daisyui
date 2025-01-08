<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Customers') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col h-[calc(100vh-200px)]">
                <div class="mb-4 flex justify-between items-center">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search customers..."
                        class="input input-bordered w-full max-w-xs" />
                    <button wire:click="create" class="btn btn-primary">Add New Customer</button>
                </div>

                <div class="overflow-x-auto overflow-y-auto flex-grow">
                    <table class="table w-full">
                        <thead class="sticky top-0 bg-white dark:bg-gray-800 z-10">
                            <tr>
                                <th class="p-3">Name</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Phone</th>
                                <th class="p-3">Total Orders</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Registered Date</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr wire:key="{{ $customer->id }}">
                                    <td class="p-3">{{ $customer->name }}</td>
                                    <td class="p-3">{{ $customer->email }}</td>
                                    <td class="p-3">{{ $customer->phone ?? '-' }}</td>
                                    <td class="p-3">{{ $customer->orders_count }}</td>
                                    <td class="p-3">{{ $customer->created_at->format('d M Y') }}</td>
                                    <td class="p-3">
                                        <button wire:click="edit({{ $customer->id }})"
                                            class="btn btn-sm btn-info">Edit</button>
                                        <button wire:click="confirmCustomerDeletion({{ $customer->id }})"
                                            class="btn btn-sm btn-error">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>

    <x-modal2 name="customer-modal" title="Customer">
        <x-slot:body>
            @if ($errors->any())
                <div role="alert" class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            @endif
            <h3 class="font-bold text-lg">
                {{ $editingCustomerId ? 'Edit Customer' : 'Create New Customer' }}</h3>
            <form wire:submit.prevent="{{ $editingCustomerId ? 'update' : 'store' }}">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Name</span>
                    </label>
                    <input type="text" wire:model.lazy="name" class="input input-bordered" required>
                    @error('name')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" wire:model.lazy="email" class="input input-bordered" required>
                    @error('email')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Phone</span>
                    </label>
                    <input type="text" wire:model.lazy="phone" class="input input-bordered">
                    @error('phone')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-action">
                    <button type="submit" class="btn btn-primary">
                        {{ $editingCustomerId ? 'Update' : 'Save' }}
                    </button>
                    <button type="button" wire:click="closeModal" class="btn">Cancel</button>
                </div>
            </form>
        </x-slot:body>
    </x-modal2>

    <x-modal2 name="confirm-customer-deletion" title="Confirm Deletion">
        <x-slot:body>
            <h3 class="font-bold text-lg">Are you sure you want to delete this customer?</h3>
            <div class="modal-action">
                <button wire:click="deleteCustomer" class="btn btn-error">Yes, Delete</button>
                <button wire:click="closeModal" class="btn">Cancel</button>
            </div>
        </x-slot:body>
    </x-modal2>

    @if (session()->has('message'))
        <div class="toast toast-top toast-center">
            <div class="alert alert-success">
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif
</div>