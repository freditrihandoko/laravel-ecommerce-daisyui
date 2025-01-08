<div>
    @if ($orders->isEmpty())
        <p class="text-center text-gray-500">You have no orders yet.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($orders as $order)
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">Order ID: {{ $order->id }}</h2>
                        <p>Total: Rp. {{ number_format($order->total_amount, 0, '.', '.') }}</p>
                        <p>Status:
                            <span
                                class="badge badge-md badge-outline 
                            @if ($order->status->name === 'Pending') badge-warning 
                            @elseif($order->status->name === 'Approved') badge-success 
                            @elseif($order->status->name === 'Shipped') badge-info 
                            @elseif($order->status->name === 'Packing') badge-secondary
                            @elseif($order->status->name === 'Canceled') badge-danger @endif">
                                {{ $order->status->name }}
                            </span>
                        </p>
                        <p>Payment: {{ $order->paymentMethod->name }}</p>
                        <p>Shipping: {{ $order->shippingMethod->name }}</p>
                        <p>Date: {{ $order->created_at->format('d M Y') }}</p>
                        <div class="card-actions justify-end">
                            <a href="{{ route('order-detail', ['orderId' => $order->id]) }}"
                                class="btn btn-primary btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
