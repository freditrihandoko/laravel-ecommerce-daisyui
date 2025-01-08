<div class="container mx-auto p-6 bg-base-100 shadow-xl rounded-box">
    <h2 class="text-3xl font-bold mb-6 text-primary">Detail Pesanan</h2>

    <div class="grid gap-6 md:grid-cols-2">
        <!-- Informasi Pesanan -->
        <div class="card bg-base-200 shadow-md">
            <div class="card-body">
                <h3 class="card-title text-secondary mb-4">Informasi Pesanan</h3>
                <div class="grid grid-cols-2 gap-y-2">
                    <p class="font-semibold">ID Pesanan:</p>
                    <p>{{ $order->id }}</p>
                    <p class="font-semibold">Subtotal:</p>
                    <p>Rp. {{ number_format($order->subtotal, 0, '.', '.') }}</p>
                    <p class="font-semibold">Biaya Pengiriman:</p>
                    <p>Rp. {{ number_format($order->shipping_cost, 0, '.', '.') }}</p>
                    @if ($order->discount_amount)
                        <p class="font-semibold">Diskon:</p>
                        <p class="text-success">-{{ number_format($order->discount_amount, 0, '.', '.') }}</p>
                    @endif
                    <p class="font-semibold">Total:</p>
                    <p class="font-bold text-primary">Rp. {{ number_format($order->total_amount, 0, '.', '.') }}</p>
                    <p class="font-semibold">Metode Pengiriman:</p>
                    <p>{{ $order->shippingMethod->name }}</p>
                    <p class="font-semibold">Metode Pembayaran:</p>
                    <p>{{ $order->paymentMethod->name }}</p>

                </div>
            </div>
        </div>

        <!-- Informasi Pengiriman -->
        <div class="card bg-base-200 shadow-md">
            <div class="card-body">
                <h3 class="card-title text-secondary mb-4">Informasi Pengiriman</h3>
                @if ($shippingInformation)
                    <div class="space-y-2">
                        <p><span class="font-semibold">Metode:</span> {{ $shippingInformation->shipping_method }}</p>
                        <p><span class="font-semibold">Biaya Pengiriman:</span>
                            {{ number_format($shippingInformation->shipping_cost, 0, '.', '.') }}</p>
                        <p><span class="font-semibold">Nomor Pelacakan:</span>
                            {{ $shippingInformation->tracking_number ?? 'Belum tersedia' }}</p>
                    </div>
                @elseif ($order->status_id == 4)
                    <p class="alert alert-info">Barang dalam proses packing.</p>
                @else
                    <p class="alert alert-info">Informasi pengiriman belum tersedia.</p>
                @endif

                <div class="divider"></div>

                <h4 class="font-semibold mb-2">Alamat Pengiriman:</h4>
                @php
                    $shippingAddress = $order->address ?? json_decode($order->address, true);
                @endphp
                <address class="not-italic">
                    {{ $shippingAddress['address_line_1'] }}<br>
                    {{ $shippingAddress['address_line_2'] }}<br>
                    {{ $shippingAddress['kelurahan'] }}, {{ $shippingAddress['kecamatan'] }}
                    {{ $shippingAddress['zip_code'] }}<br>
                    {{ $shippingAddress['kota_kab'] }}, {{ $shippingAddress['provinsi'] }},
                    {{ $shippingAddress['country'] }}<br>
                    <span class="font-semibold">Telepon:</span> {{ $shippingAddress['phone'] }}
                </address>
            </div>
        </div>
    </div>

    <!-- Produk yang Dipesan -->
    <div class="card bg-base-200 shadow-md mt-6">
        <div class="card-body">
            <h3 class="card-title text-secondary mb-4">Produk yang Dipesan</h3>
            <div class="overflow-x-auto">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Varian</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orderItems as $item)
                            <tr>
                                <td class="flex items-center space-x-3">
                                    <div class="mask mask-squircle w-12 h-12">
                                        <img src="{{ $item->product->getFirstMediaUrl('thumbnails') }}"
                                            alt="{{ $item->product->name }}">
                                    </div>
                                    <span>{{ $item->product->name }}</span>
                                </td>
                                <td>{{ $item->variant ? $item->variant->name : '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price, 0, '.', '.') }}</td>
                                <td>{{ number_format($item->quantity * $item->price, 0, '.', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Informasi Pembayaran -->
    <div class="card bg-base-200 shadow-md mt-6">
        <div class="card-body">
            <h3 class="card-title text-secondary mb-4">Informasi Pembayaran</h3>

             {{-- Instruksi Pembayaran --}}
             @if ($order->paymentMethod && $order->paymentMethod->instructions)
                <div class="col-span-2 mt-4"> {{-- Membuat instruksi full width --}}
                    <p class="font-semibold">Instruksi Pembayaran:</p>
                    <div class="prose"> {{-- Menggunakan prose untuk formatting teks --}}
                        {!! $order->paymentMethod->instructions !!}
                    </div>
                </div>
            @endif

            @if ($paymentInformation)
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p><span class="font-semibold">Status Pembayaran:</span>
                            <span
                                class="badge badge-outline @if ($paymentInformation->payment_status == 'Approved') badge-success @elseif($paymentInformation->payment_status == 'Shipped') badge-info @elseif($paymentInformation->payment_status == 'Canceled') badge-danger @else badge-warning @endif">
                                {{ $paymentInformation->payment_status }}
                            </span>
                        </p>
                        <p><span class="font-semibold">Jumlah yang Harus Dibayar:</span>
                            Rp. {{ number_format($order->total_amount, 0, '.', '.') }}</p>
                        <p><span class="font-semibold">Jumlah Dibayar:</span>
                            Rp. {{ number_format($paymentInformation->amount_paid, 0, '.', '.') }}</p>
                        @if ($paymentInformation->payment_proof)
                            <p><span class="font-semibold">Tanggal Pembayaran:</span>
                                {{ $paymentInformation->payment_date }}</p>
                        @endif
                    </div>
                    <div>
                        @if (!$paymentInformation->payment_proof)
                            <form wire:submit.prevent="uploadPaymentProof" class="space-y-4">
                                <label for="paymentProof" class="block text-sm font-medium">Unggah Bukti
                                    Pembayaran:</label>
                                <input type="file" wire:model="paymentProof"
                                    class="file-input file-input-bordered w-full max-w-xs">
                                @error('paymentProof')
                                    <span class="text-error text-sm">{{ $message }}</span>
                                @enderror
                                <button type="submit" class="btn btn-primary">Unggah Bukti</button>
                            </form>
                        @else
                            <p><span class="font-semibold">Bukti Pembayaran:</span>
                                <a href="{{ Storage::url($paymentInformation->payment_proof) }}" target="_blank"
                                    class="btn btn-link">Lihat Bukti</a>
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <p class="alert alert-warning">Informasi pembayaran belum tersedia.</p>
            @endif
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success mt-6">
            {{ session('message') }}
        </div>
    @endif
</div>
