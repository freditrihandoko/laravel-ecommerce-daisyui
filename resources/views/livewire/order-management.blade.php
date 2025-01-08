<x-slot name="header">
    <h2 class="text-2xl font-bold text-primary">
        {{ __('Manage Orders') }}
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

                <!-- Filters and Search -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by Order ID"
                        class="input input-bordered w-full" />
                    <input type="text" wire:model.live.debounce.300ms="customerSearch"
                        placeholder="Search by Customer Name" class="input input-bordered w-full" />
                    <input type="date" wire:model.live="dateFrom" class="input input-bordered w-full"
                        placeholder="From Date" />
                    <input type="date" wire:model.live="dateTo" class="input input-bordered w-full"
                        placeholder="To Date" />
                </div>

                <div class="flex justify-between mb-4">
                    <select wire:model.live.debounce.300ms="statusFilter" class="select select-bordered">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    <button wire:click="sortBy('created_at')" class="btn btn-sm btn-ghost">
                        Date {{ $sortField === 'created_at' && $sortDirection === 'asc' ? '▲' : '▼' }}
                    </button>
                </div>

                <!-- Orders Table -->
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Status Payment</th>
                                <th>Order Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr wire:key="{{ $order->id }}">
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>Rp. {{ number_format($order->total_amount, 0, '.', '.') }}</td>
                                    <td>
                                        <span
                                            class="badge badge-md badge-outline 
                                            @if ($order->status->name === 'Pending') badge-warning 
                                            @elseif($order->status->name === 'Approved') badge-success 
                                            @elseif($order->status->name === 'Shipped') badge-info 
                                            @elseif($order->status->name === 'Packing') badge-secondary
                                            @elseif($order->status->name === 'Canceled') badge-error @endif">
                                            {{ $order->status->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-md badge-outline 
                                            @if ($order->paymentInformation->payment_status === 'Pending') badge-warning 
                                            @elseif($order->paymentInformation->payment_status === 'Approved') badge-success 
                                            @elseif($order->paymentInformation->payment_status === 'Canceled') badge-error @endif">
                                            {{ $order->paymentInformation->payment_status }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <button wire:click="selectOrder({{ $order->id }})"
                                            class="btn btn-sm btn-primary">View Order</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>

                <!-- Modal for Order Details -->
                {{-- @if ($showOrderModal)
                    <div class="modal modal-open">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg">Order Details</h3>
                            <p>Order ID: {{ $selectedOrder->id }}</p>
                            <p>Total Amount: Rp. {{ number_format($selectedOrder->total_amount, 0, '.', '.') }}</p>
                            <p>Status: {{ $selectedOrder->status->name }}</p>
                            <p>Order Date: {{ $selectedOrder->created_at->format('Y-m-d H:i:s') }}</p>

                            <!-- Order Items -->
                            <div class="mt-4">
                                <h4 class="font-semibold">Ordered Products:</h4>
                                @if ($selectedOrder->items)
                                    <ul>
                                        @foreach ($selectedOrder->items as $item)
                                            <li>{{ $item->quantity }} x {{ $item->product->name }}
                                                @if ($item->variant)
                                                    ({{ $item->variant->name }})
                                                @endif
                                                - Rp. {{ number_format($item->price, 0, '.', '.') }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p>No items found for this order.</p>
                                @endif
                            </div>
                            <div class="mt-4">
                                @if ($selectedOrder->paymentInformation->payment_proof)
                                    <a href="{{ Storage::url($selectedOrder->paymentInformation->payment_proof) }}"
                                        target="_blank" class="link link-primary">View Payment Proof</a>
                                @else
                                    <p>No payment proof uploaded.</p>
                                @endif
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-action">
                                @if ($selectedOrder->status_id == 1)
                                    <button wire:click="approveOrder" class="btn btn-success">Approve Order</button>
                                @elseif ($selectedOrder->status_id == 2)
                                    <button wire:click="updateOrderStatus" class="btn btn-warning">Set to
                                        Pending</button>
                                @else
                                    <p class="text-sm text-gray-500">Order is already processed.</p>
                                @endif
                                <button wire:click="openPaymentModal" class="btn btn-info">Update Payment</button>
                                <button type="button" class="btn" wire:click="closeModals">Close</button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Modal for Payment Information -->
                @if ($showPaymentModal)
                    <div class="modal modal-open">
                        <div class="modal-box w-11/12 max-w-5xl"> <!-- Increase the modal width -->
                            <div class="flex flex-col lg:flex-row">
                                <!-- Left side: Payment Proof -->
                                <div class="lg:w-1/2">
                                    <div>
                                        <h3 class="font-bold text-lg">Total Amount:
                                            Rp. {{ number_format($selectedOrder->total_amount, 0, '.', '.') }}
                                        </h3>
                                    </div>
                                    @if ($selectedOrder->paymentInformation->payment_proof)
                                        <img src="{{ Storage::url($selectedOrder->paymentInformation->payment_proof) }}"
                                            alt="Payment Proof" class="rounded-lg shadow-lg w-full object-cover">
                                        <a href="{{ Storage::url($selectedOrder->paymentInformation->payment_proof) }}"
                                            target="_blank" class="link link-primary">View Full Screen</a>
                                    @else
                                        <p class="text-center">No payment proof uploaded.</p>
                                    @endif
                                </div>

                                <!-- Right side: Form -->
                                <div class="lg:w-1/2 lg:pl-6 mt-6 lg:mt-0">
                                    <h3 class="font-bold text-lg">Update Payment Information</h3>
                                    <form wire:submit.prevent="updatePaymentInformation">
                                        <div class="mb-4">
                                            <label class="label">Transaction ID</label>
                                            <input type="text" wire:model="transactionId"
                                                class="input input-bordered w-full" />
                                        </div>
                                        <div class="mb-4">
                                            <label class="label">Payment Status</label>
                                            <select wire:model="paymentStatus" class="select select-bordered w-full">
                                                <option value="Pending">Pending</option>
                                                <option value="Approved">Approved</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label class="label">Amount Paid</label>
                                            <input type="text" wire:model="amountPaid"
                                                class="input input-bordered w-full" type-currency="IDR" />
                                        </div>
                                        <div class="mb-4">
                                            <label class="label">Payment Date</label>
                                            <input type="date" wire:model="paymentDate"
                                                class="input input-bordered w-full" />
                                        </div>

                                        <div class="modal-action">
                                            <button type="submit" class="btn btn-primary">Update Payment Info</button>
                                            <button type="button" class="btn"
                                                wire:click="closeModals">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif --}}



            </div>
        </div>
    </div>
    
    {{-- <x-modal2 name="order-modal" title="Order Details">
        <x-slot:body>

            <div x-data="{ orderDetails: @entangle('orderDetails') }">
                <template x-if="orderDetails.id">
                    <div>
                        <p>Order ID: <span x-text="orderDetails.id"></span></p>
                        <p>Total Amount: <span x-text="orderDetails.total_amount"></span></p>
                        <p>Status: <span x-text="orderDetails.status"></span></p>
                        <p>Status id: <span x-text="orderDetails.status_id"></span></p>
                        <p>Order Date: <span x-text="orderDetails.created_at"></span></p>

                        <!-- Order Items -->
                        <div class="mt-4">
                            <h4 class="font-semibold">Ordered Products:</h4>
                            <template x-if="orderDetails.items && orderDetails.items.length > 0">
                                <ul>
                                    <template x-for="item in orderDetails.items" :key="item.product_name">
                                        <li>
                                            <span x-text="`${item.quantity} x ${item.product_name}`"></span>
                                            <template x-if="item.variant_name">
                                                <span x-text="` (${item.variant_name})`"></span>
                                            </template>
                                            <span x-text="` - ${item.price}`"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                            <template x-if="!orderDetails.items || orderDetails.items.length === 0">
                                <p>No items found for this order.</p>
                            </template>
                        </div>

                        <div class="mt-4">
                            <template x-if="orderDetails.payment_proof">
                                <a :href="'/storage/' + orderDetails.payment_proof" target="_blank"
                                    class="link link-primary">View Payment Proof</a>
                            </template>
                            <template x-if="!orderDetails.payment_proof">
                                <p>No payment proof uploaded.</p>
                            </template>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-action">

                            <template x-if="orderDetails.status_id == 1">
                                <button wire:click="approveOrder" class="btn btn-success">Approve
                                    Order</button>
                            </template>
                            <template x-if="orderDetails.status_id == 2">
                                <button wire:click="updateOrderStatus" class="btn btn-warning">Set to
                                    Pending</button>
                            </template>
                            <template x-if="orderDetails.status_id != 1 && orderDetails.status_id != 2">
                                <p class="text-sm text-gray-500">Order is already processed.</p>
                            </template>
                            <button wire:click="openPaymentModal" class="btn btn-info">Update
                                Payment</button>
                        </div>
                    </div>
                </template>
            </div>
        </x-slot:body>
    </x-modal2> --}}

    <x-modal2 name="order-modal" title="Order Details">
        <x-slot:body>
            <div x-data="{ orderDetails: @entangle('orderDetails') }">
                <template x-if="orderDetails.id">
                    <div>
                        <p>Order ID: <span x-text="orderDetails.id"></span></p>
                        <p>Total Amount: <span x-text="orderDetails.total_amount"></span></p>
                        <p>Status: <span x-text="orderDetails.status"></span></p>
                        <p>Order Date: <span x-text="orderDetails.created_at"></span></p>

                        <!-- Order Items -->
                        <div class="mt-4">
                            <h4 class="font-semibold">Ordered Products:</h4>
                            <template x-if="orderDetails.items && orderDetails.items.length > 0">
                                <ul>
                                    <template x-for="(item, index) in orderDetails.items" :key="index">
                                        <li>
                                            <span x-text="`${item.quantity} x ${item.product_name}`"></span>
                                            <template x-if="item.is_variant">
                                                <span x-text="` - Variant: ${item.variant_name}`"></span>
                                            </template>
                                            <span x-text="` - ${item.price}`"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                            <template x-if="!orderDetails.items || orderDetails.items.length === 0">
                                <p>No items found for this order.</p>
                            </template>
                        </div>

                        <!-- Payment Proof -->
                        <div class="mt-4">
                            <template x-if="orderDetails.payment_proof">
                                <a :href="'/storage/' + orderDetails.payment_proof" target="_blank"
                                    class="link link-primary">View Payment Proof</a>
                            </template>
                            <template x-if="!orderDetails.payment_proof">
                                <p>No payment proof uploaded.</p>
                            </template>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-action">
                            <template x-if="orderDetails.status_id == 1">
                                <form method="dialog">
                                    <button wire:click="openCancelOrderModal" class="btn btn-error">Cancel Order</button>
                                <button wire:click="approveOrder" class="btn btn-success">Approve Order</button>
                                </form>
                            </template>
                            <template x-if="orderDetails.status_id == 2">
                                <form method="dialog">
                                <button wire:click="openCancelOrderModal" class="btn btn-error">Cancel Order</button>
                                <button wire:click="updateOrderStatus" class="btn btn-warning">Set to Pending</button>
                                </form>
                            </template>
                            <template x-if="orderDetails.status_id == 3">
                                <p class="text-sm text-gray-500">Order has been cancelled.</p>
                                </template>
                            <template x-if="orderDetails.status_id != 1 && orderDetails.status_id != 2 && orderDetails.status_id != 3">
                                <p class="text-sm text-gray-500">Order is already processed.</p>
                            </template>
                            <button wire:click="openPaymentModal" class="btn btn-info">Update Payment</button>
                        </div>
                        
                    </div>
                </template>
            </div>
        </x-slot:body>
    </x-modal2>

    <x-modal2 name="payment-modal" title="Update Payment Information">
        <x-slot:body>
            <div x-data="{ orderDetails: @entangle('orderDetails'), showPaymentModal: @entangle('showPaymentModal') }">
                <div x-show="showPaymentModal">
                    <template x-if="orderDetails.id">
                        <div class="flex flex-col lg:flex-row">
                            <!-- Left side: Payment Proof -->
                            <div class="lg:w-1/2">
                                <div>
                                    <h3 class="font-bold text-lg">Total Amount: <span
                                            x-text="orderDetails.total_amount"></span></h3>
                                </div>
                                <template x-if="orderDetails.payment_proof">
                                    <div>
                                        <img :src="'/storage/' + orderDetails.payment_proof" alt="Payment Proof"
                                            class="rounded-lg shadow-lg w-full object-cover">
                                        <a :href="'/storage/' + orderDetails.payment_proof" target="_blank"
                                            class="link link-primary">View Full Screen</a>
                                    </div>
                                </template>
                                <template x-if="!orderDetails.payment_proof">
                                    <p class="text-center">No payment proof uploaded.</p>
                                </template>
                            </div>

                            <!-- Right side: Form -->
                            <div class="lg:w-1/2 lg:pl-6 mt-6 lg:mt-0">
                                <form wire:submit.prevent="updatePaymentInformation">
                                    <div class="mb-4">
                                        <label class="label">Transaction ID</label>
                                        <input type="text" wire:model="transactionId"
                                            class="input input-bordered w-full" />
                                    </div>
                                    <div class="mb-4">
                                        <label class="label">Payment Status</label>
                                        <select wire:model="paymentStatus" class="select select-bordered w-full">
                                            <option value="Pending">Pending</option>
                                            <option value="Approved">Approved</option>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="label">Amount Paid</label>
                                        <input type="text" wire:model.lazy="formattedAmountPaid"
                                            class="input input-bordered w-full" type-currency="IDR"
                                            type-currency="IDR" placeholder="Rp">

                                    </div>
                                    @dump($amountPaid)

                                    <div class="mb-4">
                                        <label class="label">Payment Date</label>
                                        <input type="date" wire:model="paymentDate"
                                            class="input input-bordered w-full" />
                                    </div>

                                    <div class="modal-action">
                                        <button type="submit" class="btn btn-primary">Update Payment
                                            Info</button>
                                        <button type="button" class="btn"
                                            @click="$wire.closePaymentModal()">Close</button>
                                    </div>


                                    <script>
                                        console.log('tes script');
                                        document.querySelectorAll('input[type-currency="IDR"]').forEach((element) => {
                                            element.addEventListener('keyup', function(e) {
                                                let cursorPostion = this.selectionStart;
                                                let value = parseInt(this.value.replace(/[^,\d]/g, ''));
                                                let originalLenght = this.value.length;
                                                if (isNaN(value)) {
                                                    this.value = "";
                                                } else {
                                                    this.value = value.toLocaleString('id-ID', {
                                                        currency: 'IDR',
                                                        style: 'currency',
                                                        minimumFractionDigits: 0
                                                    });
                                                    cursorPostion = this.value.length - originalLenght + cursorPostion;
                                                    this.setSelectionRange(cursorPostion, cursorPostion);
                                                }
                                            });
                                        });
                                    </script>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </x-slot:body>
    </x-modal2>

    <x-modal2 name="cancel-order-modal" title="Cancel Order">
        <x-slot:body>
            <p class="py-4">Are you sure you want to cancel this order?</p>
            <div class="modal-action">
                <button wire:click="cancelOrder" class="btn btn-error">Yes, Cancel Order</button>
                <button wire:click="$dispatch('close-modal')" class="btn">Cancel</button>
            </div>
        </x-slot:body>
    </x-modal2>

</div>
