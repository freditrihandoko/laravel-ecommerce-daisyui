<x-slot name="header">
    <h2 class="text-2xl font-bold text-primary">
        {{ __('Manage Discount') }}
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

    <button wire:click="openModal" class="btn btn-primary mb-4">Tambah Diskon Baru</button>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tipe</th>
                    <th>Nilai</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Berakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($discounts as $discount)
                    <tr>
                        <td>{{ $discount->code }}</td>
                        <td>{{ $discount->discount_type == 'percentage' ? 'Persentase' : 'Nominal Tetap' }}</td>
                        <td>{{ $discount->discount_type == 'percentage' ? $discount->discount_value . '%' : 'Rp ' . number_format($discount->discount_value, 0, ',', '.') }}
                        </td>
                        <td>{{ $discount->start_date->format('d/m/Y') }}</td>
                        <td>{{ $discount->end_date->format('d/m/Y') }}</td>
                        <td>
                            <button wire:click="edit({{ $discount->id }})" class="btn btn-sm btn-info">Edit</button>
                            <button wire:click="confirmDelete({{ $discount->id }})"
                                class="btn btn-sm btn-error ml-2">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $discounts->links() }}
    </div>

    <!-- Modal Tambah/Edit Diskon -->

    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                @dump($editingId)
                <h3 class="text-lg font-bold mb-4">{{ $editingId ? 'Edit Diskon' : 'Tambah Diskon Baru' }}</h3>
                <form wire:submit.prevent="{{ $editingId ? 'update' : 'store' }}">
                    <div class="form-control mb-4">
                        <label class="label" for="code">Kode Diskon</label>
                        <input type="text" id="code" wire:model="code" class="input input-bordered" required>
                        @error('code')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="description">Deskripsi</label>
                        <textarea id="description" wire:model="description" class="textarea textarea-bordered" required></textarea>
                        @error('description')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="discount_type">Tipe Diskon</label>
                        <select id="discount_type" wire:model="discount_type" class="select select-bordered" required>
                            <option value="">Pilih tipe diskon</option>
                            <option value="percentage">Persentase</option>
                            <option value="fixed">Nominal Tetap</option>
                        </select>
                        @error('discount_type')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="discount_value">Nilai Diskon</label>
                        <input type="number" id="discount_value" wire:model="discount_value"
                            class="input input-bordered" required step="0.01">
                        @error('discount_value')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="start_date">Tanggal Mulai</label>
                        <input type="date" id="start_date" wire:model="start_date" class="input input-bordered"
                            required>
                        @error('start_date')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="end_date">Tanggal Berakhir</label>
                        <input type="date" id="end_date" wire:model="end_date" class="input input-bordered"
                            required>
                        @error('end_date')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="minimum_order_value">Nilai Pemesanan Minimum</label>
                        <input type="number" id="minimum_order_value" wire:model="minimum_order_value"
                            class="input input-bordered" step="0.01">
                        @error('minimum_order_value')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="maximum_discount_amount">Jumlah Diskon Maksimum</label>
                        <input type="number" id="maximum_discount_amount" wire:model="maximum_discount_amount"
                            class="input input-bordered" step="0.01">
                        @error('maximum_discount_amount')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label" for="usage_limit">Batas Penggunaan</label>
                        <input type="number" id="usage_limit" wire:model="usage_limit" class="input input-bordered">
                        @error('usage_limit')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Batal</button>
                        <button type="submit"
                            class="btn btn-primary">{{ $editingId ? 'Perbarui' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Penghapusan -->
    @if ($isConfirmingDelete)
        <div class="modal modal-open">
            <div class="modal-box">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium">Hapus Diskon</h3>
                        <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus diskon ini? Tindakan ini
                            tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="modal-action">
                    <button type="button" wire:click="delete" class="btn btn-error">Hapus</button>
                    <button type="button" wire:click="cancelDelete" class="btn btn-secondary">Batal</button>
                </div>
            </div>
        </div>
    @endif

</div>
