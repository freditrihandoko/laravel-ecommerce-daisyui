<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Your Shopping Cart</h1>
    @if ($cartItems->count() > 0 && $cartTotal > 0)
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Variant</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cartItems as $item)
                        <tr class="{{ in_array($item->id, $unavailableItems) ? 'bg-red-100' : '' }}">
                            <td>
                                <div class="flex items-center space-x-3">
                                    <div class="avatar indicator">
                                        <span class="indicator-item badge badge-secondary">{{ $item->quantity }}</span>
                                        <div class="h-20 w-20 rounded-lg">
                                            <img alt="{{ $item->product->name }}"
                                                src="{{ $item->product->getFirstMediaUrl('thumbnails') }}" />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-bold">{{ $item->product->name }}</div>
                                        @if (in_array($item->id, $unavailableItems))
                                            <div class="text-red-500 text-sm">Out of stock</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->variant ? $item->variant->name : '-' }}</td>
                            <td>Rp.
                                {{ number_format($item->variant ? $item->variant->price : $item->product->price, 0, '.', '.') }}
                            </td>
                            <td>
                                <div class="flex items-center space-x-2">
                                    <button class="btn btn-sm" wire:click="decrementQuantity({{ $item->id }})"
                                        {{ in_array($item->id, $unavailableItems) ? 'disabled' : '' }}>-</button>
                                    <span>{{ $item->quantity }}</span>
                                    <button class="btn btn-sm" wire:click="incrementQuantity({{ $item->id }})"
                                        {{ in_array($item->id, $unavailableItems) ? 'disabled' : '' }}>+</button>
                                </div>
                            </td>
                            <td>Rp.
                                {{ number_format(($item->variant ? $item->variant->price : $item->product->price) * $item->quantity, 0, '.', '.') }}
                            </td>
                            <td>
                                <button class="btn btn-sm btn-error"
                                    wire:click="removeItem({{ $item->id }})">Remove</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-8 flex justify-between items-center">
            <div class="text-2xl font-bold">
                Total: Rp. {{ number_format($cartTotal, 0, '.', '.') }}
            </div>
            <button class="btn btn-primary" wire:click="proceedToCheckout"
                {{ !empty($unavailableItems) ? 'disabled' : '' }}>
                Proceed to Checkout
            </button>
        </div>
        @error('checkout')
            <div class="mt-4 text-red-500">{{ $message }}</div>
        @enderror
    @else
        <p class="text-xl">Your cart is empty or all items are out of stock.</p>
    @endif
</div>
