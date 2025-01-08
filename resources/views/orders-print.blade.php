<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->id }} Details</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white text-primary-content shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4">
            <h1 class="text-3xl font-bold mb-4">Order #{{ $order->id }}</h1>

            <!-- Two-column layout starts here -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left side: Customer Information -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-2">Customer Information</h2>
                    <p><strong>Name:</strong> {{ $order->user->name }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p><strong>Address:</strong><br>
                        @if (is_array($order->address))
                            {{ $order->address['name'] ?? 'N/A' }},<br>
                            {{ $order->address['address_line_1'] ?? 'N/A' }},<br>
                            {{ $order->address['address_line_2'] ?? '' }}<br>
                            {{ $order->address['kelurahan'] ?? 'N/A' }}
                            {{ $order->address['kecamatan'] ?? 'N/A' }},<br>
                            {{ $order->address['kota_kab'] ?? 'N/A' }},
                            {{ $order->address['provinsi'] ?? 'N/A' }},<br>
                            {{ $order->address['country'] ?? 'N/A' }},<br>
                            {{ $order->address['phone'] ?? 'N/A' }}
                        @else
                            {{ $order->address ?? 'N/A' }}
                        @endif
                    </p>
                </div>

                <!-- Right side: Order Details -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-2">Order Details</h2>
                    <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                    <p><strong>Status:</strong> {{ $order->status->name }}</p>
                    <p><strong>Shipping Method:</strong> {{ $order->shippingMethod->name }}</p>
                    <p><strong>Tracking Number:</strong>
                        {{ $order->shippingInformation->tracking_number ?? 'Process..' }}
                    </p>
                </div>
            </div>
            <!-- Two-column layout ends here -->

            <!-- Order Items -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2">Order Items</h2>
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Product</th>
                            <th class="text-left">Variant</th>
                            <th class="text-right">Quantity</th>
                            <th class="text-right">Price</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->variant ? $item->variant->name : 'N/A' }}</td>
                                <td class="text-right">{{ $item->quantity }}</td>
                                <td class="text-right">{{ number_format($item->price, 0, '.', '.') }}</td>
                                <td class="text-right">{{ number_format($item->quantity * $item->price, 0, '.', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-right">
                <p><strong>Subtotal:</strong> Rp. {{ number_format($order->subtotal, 0, '.', '.') }}</p>
                @if ($order->discount_amount)
                    <p class="font-semibold">Diskon:
                        <span class="text-success">-{{ number_format($order->discount_amount, 0, '.', '.') }}</span>
                    </p>
                @endif
                <p><strong>Shipping:</strong>
                    Rp. {{ $order->shipping_cost ? number_format($order->shipping_cost, 0, '.', '.') : '' }}
                </p>
                </p>
                <p class="font-bold "><strong>Total:</strong>
                    Rp. {{ number_format($order->total_amount, 0, '.', '.') }}
                </p>
            </div>
        </div>
    </div>

    <div class="mt-6 text-center no-print">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Print Order
        </button>
    </div>
</body>

</html>
