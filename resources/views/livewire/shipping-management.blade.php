<x-slot name="header">
    <h2 class="text-2xl font-bold text-primary">
        {{ __('Manage Shippings') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-base-100 shadow-xl rounded-box">
            <div class="p-6">
                @if (session()->has('message'))
                    <div class="alert alert-success mb-4">
                        {{ session('message') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search orders..."
                        class="input input-bordered w-full" />
                    <input type="text" wire:model.live.debounce.300ms="customerSearch"
                        placeholder="Search customers..." class="input input-bordered w-full" />
                    <input type="date" wire:model.live="dateFrom" class="input input-bordered w-full"
                        placeholder="From Date" />
                    <input type="date" wire:model.live="dateTo" class="input input-bordered w-full"
                        placeholder="To Date" />
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th wire:click="sortBy('id')" class="cursor-pointer">
                                    Order ID
                                    @if ($sortField == 'id')
                                        <span class="ml-1">{!! $sortDirection == 'asc' ? '&uarr;' : '&darr;' !!}</span>
                                    @endif
                                </th>
                                <th>Customer</th>
                                <th>Products</th>
                                <th>Shipping Method</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th wire:click="sortBy('created_at')" class="cursor-pointer">
                                    Created At
                                    @if ($sortField == 'created_at')
                                        <span class="ml-1">{!! $sortDirection == 'asc' ? '&uarr;' : '&darr;' !!}</span>
                                    @endif
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>
                                        @foreach ($order->items as $item)
                                            {{ $item->quantity }} x {{ $item->product->name }}@if ($item->variant)
                                                , {{ $item->variant->name }}
                                            @endif
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>{{ $order->shippingMethod->name }}</td>
                                    <td>Rp. {{ number_format($order->total_amount, 0, '.', '.') }}</td>
                                    <td>
                                        <span
                                            class="badge badge-md badge-outline  @if ($order->status->name === 'Packing') badge-secondary @elseif($order->status->name === 'Approved') badge-success @elseif($order->status->name === 'Shipped') badge-info @elseif($order->status->name === 'Canceled') badge-danger @endif">
                                            {{ $order->status->name }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="flex space-x-2">
                                            @if ($order->status_id == 2)
                                                <button wire:click="selectOrder({{ $order->id }})"
                                                    class="btn btn-sm btn-info">Set Packing</button>
                                            @elseif($order->status_id == 4)
                                                <button wire:click="selectOrder({{ $order->id }})"
                                                    class="btn btn-sm btn-success">Set Shipped</button>
                                            @endif
                                            <button wire:click="printOrder({{ $order->id }})"
                                                class="btn btn-sm btn-secondary">Print</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $orders->links() }}
                </div>

                @if ($selectedOrder)
                    <div class="modal modal-open">
                        <div class="modal-box">
                            <div class="mt-3 ">
                                <h3 class="font-bold text-xl">Order #{{ $selectedOrder->id }} Details
                                </h3>
                                <div class="mt-2 px-7 py-3">
                                    <p class="font-medium text-lg">
                                        <strong>Customer:</strong> {{ $selectedOrder->user->name }}<br>
                                        @if (is_array($selectedOrder->address))
                                            <strong>Recipient:</strong>
                                            {{ $selectedOrder->address['name'] ?? 'N/A' }}<br>
                                            <strong>Address:</strong>
                                            {{ $selectedOrder->address['address_line_1'] ?? 'N/A' }},
                                            {{ $selectedOrder->address['address_line_2'] ?? '' }},
                                            {{ $selectedOrder->address['kelurahan'] ?? 'N/A' }}
                                            {{ $selectedOrder->address['kecamatan'] ?? 'N/A' }},
                                            {{ $selectedOrder->address['kota_kab'] ?? 'N/A' }},
                                            {{ $selectedOrder->address['provinsi'] ?? 'N/A' }},
                                            {{ $selectedOrder->address['country'] ?? 'N/A' }},
                                            {{ $selectedOrder->address['phone'] ?? 'N/A' }},
                                        @else
                                            {{ $selectedOrder->address ?? 'N/A' }}
                                        @endif
                                        <br>
                                        <strong>Products:</strong><br>
                                        @foreach ($selectedOrder->items as $item)
                                            {{ $item->quantity }} x {{ $item->product->name }}
                                            @if ($item->variant)
                                                , {{ $item->variant->name }}
                                            @endif
                                            <br>
                                        @endforeach
                                        <strong>Total:</strong>
                                        Rp. {{ number_format($selectedOrder->total_amount, 0, '.', '.') }}
                                    </p>
                                </div>
                                @if ($selectedOrder->status_id == 4)
                                    <div class="mt-2">
                                        <input type="text" wire:model="trackingNumber"
                                            placeholder="Enter tracking number"
                                            class="input input-bordered w-full max-w-xs" />
                                    </div>
                                @endif
                                <div class="modal-action">
                                    @if ($selectedOrder->status_id == 2)
                                        <button wire:click="setPackingStatus" class="btn btn-primary mr-2">Set
                                            Packing</button>
                                    @elseif($selectedOrder->status_id == 4)
                                        <button wire:click="setShippedStatus" class="btn btn-success mr-2">Set
                                            Shipped</button>
                                    @endif
                                    <button wire:click="closeModal" class="btn btn-ghost">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
