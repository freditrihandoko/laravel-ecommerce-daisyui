<x-slot name="header">
    <h2 class="text-2xl font-bold text-primary">
        {{ __('Settings') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-100 shadow-xl rounded-box">
            @if (session()->has('message'))
                <div class="toast toast-center toast-middle">
                    <div class="alert alert-success">
                        <span> {{ session('message') }}</span>
                    </div>
                </div>
            @endif

            <div class="p-6">
                <div class="tabs tabs-boxed mb-6">
                    <a class="tab tab-lg {{ $activeTab === 'general' ? 'tab-active' : '' }}"
                        wire:click="$set('activeTab', 'general')">General Settings</a>
                    <a class="tab tab-lg {{ $activeTab === 'payment' ? 'tab-active' : '' }}"
                        wire:click="$set('activeTab', 'payment')">Payment Methods</a>
                    <a class="tab tab-lg {{ $activeTab === 'shipping' ? 'tab-active' : '' }}"
                        wire:click="$set('activeTab', 'shipping')">Shipping Methods</a>
                    <a class="tab tab-lg {{ $activeTab === 'status' ? 'tab-active' : '' }}"
                        wire:click="$set('activeTab', 'status')">Order Statuses</a>
                </div>


                <!-- General Settings -->
                @if ($activeTab === 'general')
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold text-secondary">General Settings</h2>
                        </div>

                        <form wire:submit.prevent="saveGeneralSettings">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Website Name</span>
                                </label>
                                <input type="text" class="input input-bordered" wire:model="website_name">
                                @error('website_name')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Slogan</span>
                                </label>
                                <input type="text" class="input input-bordered" wire:model="slogan">
                                @error('slogan')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Description</span>
                                </label>
                                <textarea class="textarea textarea-bordered" wire:model="description"></textarea>
                                @error('description')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Contact Email</span>
                                </label>
                                <input type="email" class="input input-bordered" wire:model="contact_email">
                                @error('contact_email')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Contact Phone</span>
                                </label>
                                <input type="text" class="input input-bordered" wire:model="contact_phone">
                                @error('contact_phone')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Address</span>
                                </label>
                                <textarea class="textarea textarea-bordered" wire:model="address"></textarea>
                                @error('address')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Logo</span>
                                </label>
                                @if($logo)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($logo) }}" alt="Current Logo" class="w-32 h-32 object-contain">
                                    </div>
                                @endif
                                <input type="file" class="file-input file-input-bordered w-full" wire:model="temp_logo">
                                <div wire:loading wire:target="temp_logo">Uploading...</div>
                                @error('temp_logo')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Favicon</span>
                                </label>
                                @if($favicon)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($favicon) }}" alt="Current Favicon" class="w-8 h-8 object-contain">
                                    </div>
                                @endif
                                <input type="file" class="file-input file-input-bordered w-full" wire:model="temp_favicon">
                                <div wire:loading wire:target="temp_favicon">Uploading...</div>
                                @error('temp_favicon')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Save Settings</button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Payment Methods -->
                @if ($activeTab === 'payment')
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold text-secondary">Payment Methods</h2>
                            <button class="btn btn-primary" wire:click="editPaymentMethod">Add Payment Method</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Instructions</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paymentMethods as $method)
                                        <tr>
                                            <td>{{ $method->name }}</td>
                                            <td>{{ $method->instructions }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $method->is_active ? 'badge-success' : 'badge-error' }}">
                                                    {{ $method->is_active ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-ghost"
                                                    wire:click="editPaymentMethod({{ $method->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Shipping Methods -->
                @if ($activeTab === 'shipping')
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold text-secondary">Shipping Methods</h2>
                            <button class="btn btn-primary" wire:click="editShippingMethod">Add Shipping Method</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Cost</th>
                                        <th>Active</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($shippingMethods as $method)
                                        <tr>
                                            <td>{{ $method->name }}</td>
                                            <td>{{ $method->description }}</td>
                                            <td>{{ $method->cost }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $method->is_active ? 'badge-success' : 'badge-error' }}">
                                                    {{ $method->is_active ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-ghost"
                                                    wire:click="editShippingMethod({{ $method->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Order Statuses -->
                @if ($activeTab === 'status')
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold text-secondary">Order Statuses</h2>
                            <button class="btn btn-primary" wire:click="editOrderStatus">Add Order Status</button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra w-full">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderStatuses as $status)
                                        <tr>
                                            <td>{{ $status->id }}</td>
                                            <td>{{ $status->name }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-ghost"
                                                    wire:click="editOrderStatus({{ $status->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Modals -->
                <!-- Payment Method Modal -->
                @if ($showPaymentModal)
                    <div class="modal modal-open">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg">{{ $paymentMethod_id ? 'Edit' : 'Add' }} Payment Method</h3>
                            <form wire:submit.prevent="savePaymentMethod">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Name</span>
                                    </label>
                                    <input type="text" class="input input-bordered"
                                        wire:model="paymentMethod_name">
                                    @error('paymentMethod_name')
                                        <span class="text-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Instructions</span>
                                    </label>
                                    <textarea class="textarea textarea-bordered" wire:model="paymentMethod_instructions"></textarea>
                                    @error('paymentMethod_instructions')
                                        <span class="text-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">Active</span>
                                        <input type="checkbox" class="checkbox checkbox-success"
                                            wire:model="paymentMethod_is_active"
                                            {{ $paymentMethod_is_active ? 'checked' : '' }}>
                                    </label>
                                </div>
                                <div class="modal-action">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn" wire:click="closeModal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Shipping Method Modal -->
                @if ($showShippingModal)
                    <div class="modal modal-open">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg">{{ $shippingMethod_id ? 'Edit' : 'Add' }} Shipping Method
                            </h3>
                            <form wire:submit.prevent="saveShippingMethod">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Name</span>
                                    </label>
                                    <input type="text" class="input input-bordered"
                                        wire:model="shippingMethod_name">
                                    @error('shippingMethod_name')
                                        <span class="text-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Description</span>
                                    </label>
                                    <textarea class="textarea textarea-bordered" wire:model="shippingMethod_description"></textarea>
                                    @error('shippingMethod_description')
                                        <span class="text-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Cost</span>
                                    </label>
                                    <input type="number" step="0.01" class="input input-bordered"
                                        wire:model="shippingMethod_cost">
                                    @error('shippingMethod_cost')
                                        <span class="text-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">Active</span>
                                        <input type="checkbox" class="checkbox checkbox-success"
                                            wire:model="shippingMethod_is_active"
                                            {{ $shippingMethod_is_active ? 'checked' : '' }}>
                                    </label>
                                </div>
                                <div class="modal-action">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn" wire:click="closeModal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Order Status Modal -->
                @if ($showOrderStatusModal)
                    <div class="modal modal-open">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg">{{ $orderStatus_id ? 'Edit' : 'Add' }} Order Status</h3>
                            <form wire:submit.prevent="saveOrderStatus">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Name</span>
                                    </label>
                                    <input type="text" class="input input-bordered" wire:model="orderStatus_name">
                                    @error('orderStatus_name')
                                        <span class="text-error">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="modal-action">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                    <button type="button" class="btn" wire:click="closeModal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
