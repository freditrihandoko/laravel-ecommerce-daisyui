<div class="container mx-auto p-6 bg-base-100 shadow-xl rounded-box">
    <h2 class="text-3xl font-bold mb-6 text-primary">Order Summary</h2>
    <!-- Items in Cart -->
    <div class="mb-8">
        <h3 class="text-2xl font-semibold mb-4 text-secondary">Items in Your Cart</h3>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr>
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="avatar">
                                        <div class="mask mask-squircle w-12 h-12">
                                            <img src="{{ $item->product->getFirstMediaUrl('thumbnails') }}"
                                                alt="{{ $item->product->name }}">
                                        </div>
                                    </div>
                                    <div>
                                        {{ $item->product->name }}
                                        @if ($item->variant)
                                            <span class="badge badge-ghost">{{ $item->variant->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->variant ? $item->variant->price : $item->product->price, 0, '.', '.') }}
                            </td>
                            <td>{{ number_format($item->quantity * ($item->variant ? $item->variant->price : $item->product->price), 0, '.', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Address Section -->
    <div class="mb-8 lg:w-1/2 lg:mx-auto">
        <h3 class="text-2xl font-semibold mb-4 text-secondary">Shipping Address</h3>
        @if ($addresses->isEmpty())
            <div class="alert alert-warning">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>You have no saved addresses.</span>
            </div>
        @else
            <fieldset class="space-y-4">
                @foreach ($addresses as $address)
                    <label for="address{{ $address->id }}"
                        class="flex cursor-pointer items-start gap-4 rounded-lg border border-gray-100 bg-white p-4 text-sm font-medium shadow-sm hover:border-gray-200 has-[:checked]:border-blue-500 has-[:checked]:ring-1 has-[:checked]:ring-blue-500 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700">
                        <div class="flex-1">
                            <p class="text-lg text-gray-700 dark:text-gray-200 font-bold">{{ $address->name }}</p>
                            <p class="text-gray-700 dark:text-gray-200">{{ $address->address_line_1 }}</p>
                            @if ($address->address_line_2)
                                <p class="text-gray-500 dark:text-gray-400">{{ $address->address_line_2 }}</p>
                            @endif
                            <p class="text-gray-500 dark:text-gray-400">{{ $address->kelurahan }},
                                {{ $address->kecamatan }}
                                {{ $address->zip_code }}</p>
                            <p class="text-gray-500 dark:text-gray-400">{{ $address->kota_kab }}</p>
                            <p class="text-gray-500 dark:text-gray-400">{{ $address->provinsi }}</p>
                            <p class="text-gray-500 dark:text-gray-400">{{ $address->country }}</p>
                            <p class="text-gray-500 dark:text-gray-400">{{ $address->phone }}</p>
                        </div>
                        <input type="radio" name="selectedAddress" wire:model="selectedAddress"
                            value="{{ $address->id }}" id="address{{ $address->id }}"
                            class="size-5 border-gray-300 text-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:ring-offset-gray-900" />
                    </label>
                @endforeach
            </fieldset>
        @endif

        <!-- Add Address Button -->
        <button wire:click="openAddressModal" class="mt-4 btn btn-outline btn-primary">
            Add New Address
        </button>
    </div>

    <!-- Modal for Adding a New Address -->
    @if ($showAddressModal)
        <div class="modal modal-open">
            <div class="modal-box w-full max-w-5xl h-full">
                <button wire:click="closeAddressModal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>

                <!-- Include the Livewire component for adding an address -->
                @livewire('user-address')
            </div>
        </div>
    @endif

    <!-- Shipping Method -->
    <div class="mb-8 lg:w-1/2 lg:mx-auto">
        <h3 class="text-2xl font-semibold mb-4 text-secondary">Shipping Method</h3>
        <fieldset class="space-y-4">
            @foreach ($shippingMethods as $method)
                <label for="shippingMethod{{ $method->id }}"
                    class="flex cursor-pointer justify-between gap-4 rounded-lg border border-gray-100 bg-white p-4 text-sm font-medium shadow-sm hover:border-gray-200 has-[:checked]:border-blue-500 has-[:checked]:ring-1 has-[:checked]:ring-blue-500 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700">
                    <div>
                        <p class="text-gray-700 dark:text-gray-200">{{ $method->name }}</p>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ number_format($method->cost, 0, '.', '.') }}
                        </p>
                    </div>
                    <input type="radio" name="shippingMethod" wire:model.live="selectedShippingMethod"
                        value="{{ $method->id }}" id="shippingMethod{{ $method->id }}"
                        class="size-5 border-gray-300 text-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:ring-offset-gray-900" />
                </label>
            @endforeach
        </fieldset>
    </div>

    <!-- Payment Method -->
    <div class="mb-8 lg:w-1/2 lg:mx-auto">
        <h3 class="text-2xl font-semibold mb-4 text-secondary">Payment Method</h3>
        <fieldset class="space-y-4">
            @foreach ($paymentMethods as $method)
                <label for="paymentMethod{{ $method->id }}"
                    class="flex cursor-pointer justify-between gap-4 rounded-lg border border-gray-100 bg-white p-4 text-sm font-medium shadow-sm hover:border-gray-200 has-[:checked]:border-blue-500 has-[:checked]:ring-1 has-[:checked]:ring-blue-500 dark:border-gray-800 dark:bg-gray-900 dark:hover:border-gray-700">
                    <div>
                        <p class="text-gray-700 dark:text-gray-200">{{ $method->name }}</p>
                    </div>
                    <input type="radio" name="paymentMethod" wire:model.live="selectedPaymentMethod"
                        value="{{ $method->id }}" id="paymentMethod{{ $method->id }}"
                        class="size-5 border-gray-300 text-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:ring-offset-gray-900" />
                </label>
            @endforeach
        </fieldset>
    </div>

    <!-- Discount Code Input -->
    <div class="mb-8 lg:w-1/2 lg:mx-auto">
        <h3 class="text-2xl font-semibold mb-4 text-secondary">Discount Code</h3>
        <div class="form-control">
            <div class="join w-full">
                <input type="text" wire:model="discountCode" placeholder="Enter discount code"
                    class="input input-bordered join-item flex-grow uppercase"
                    {{ $appliedDiscountCode ? 'disabled' : '' }} />


                <button wire:click="removeDiscount"
                    class=" {{ !$appliedDiscountCode ? 'hidden' : '' }} btn btn-error join-item"
                    title="Remove discount">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <button wire:click="applyDiscount"
                    class="{{ $appliedDiscountCode ? 'hidden' : '' }} btn btn-primary join-item">
                    Apply
                </button>

            </div>

            @if (session()->has('message'))
                <div class="alert alert-success mt-2">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-error mt-2">
                    {{ session('error') }}
                </div>
            @endif

            @if ($appliedDiscountCode)
                <div class="mt-2 text-success">
                    Discount code "<span class="uppercase">{{ $appliedDiscountCode }}</span>" applied successfully!
                </div>
            @endif

            {{-- @if (session()->has('message'))
                <div class="mt-2 text-green-500">{{ session('message') }}</div>
            @elseif(session()->has('error'))
                <div class="mt-2 text-red-500">{{ session('error') }}</div>
            @endif --}}
        </div>
    </div>


    <!-- Order Summary -->
    <div class="mb-8 bg-base-200 p-6 rounded-box">
        <h3 class="text-2xl font-semibold mb-4 text-secondary">Order Summary</h3>
        <div class="flex justify-between items-center mb-2">
            <span>Subtotal:</span>
            <span class="font-semibold">{{ number_format($subtotal, 0, '.', '.') }}</span>
        </div>
        @if ($discountAmount > 0)
            <div class="flex justify-between items-center mb-2">
                <span>Discount:</span>
                <span class="font-semibold text-red-500">-{{ number_format($discountAmount, 0, '.', '.') }}</span>
            </div>
        @endif
        <div class="flex justify-between items-center mb-2">
            <span>Shipping Cost:</span>
            <span class="font-semibold">{{ number_format($shippingCost, 0, '.', '.') }}</span>
        </div>
        <div class="divider"></div>
        <div class="flex justify-between items-center text-lg font-bold">
            <span>Total Amount:</span>
            <span>Rp. {{ number_format($totalAmount, 0, '.', '.') }}</span>
        </div>
    </div>


    <!-- Confirm Order Button -->
    <button wire:click="openConfirmOrderModal" class="btn btn-primary btn-block">Confirm Order</button>

    <!-- DaisyUI Modal for Order Confirmation -->
    @if ($showConfirmOrderModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <button wire:click="closeConfirmOrderModal"
                    class="btn btn-sm btn-circle absolute right-2 top-2">✕</button>
                <h3 class="text-lg font-bold">Confirm Your Order</h3>
                <p class="py-4">
                    Are you sure you want to place this order? Please review all the details carefully before
                    confirming.
                </p>
                @if ($errors->any())
                    <div role="alert" class="alert alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current"
                            fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @foreach ($errors->all() as $error)
                            <span>{{ $error }}</span>
                        @endforeach

                    </div>
                @endif

                @if (session()->has('message'))
                    <div class="alert alert-success mt-2">
                        {{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-error mt-2">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="modal-action">
                    <button wire:click="confirmOrder" class="btn btn-primary">Yes, Confirm Order</button>
                    <button wire:click="closeConfirmOrderModal" class="btn btn-outline">Cancel</button>
                </div>
            </div>
        </div>
    @endif

</div>
