<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Manage Addresses</h2>

    <button wire:click="openModal" class="btn btn-primary mb-4">Add New Address</button>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($addresses as $address)
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title">
                        {{ $address->label }}
                        @if ($address->is_default)
                            <span class="badge badge-primary">Default</span>
                        @endif
                    </h3>
                    <p>{{ $address->name }}</p>
                    <p>{{ $address->address_line_1 }}</p>
                    <p>{{ $address->address_line_2 }}</p>
                    <p>{{ $address->kelurahan }}, {{ $address->kecamatan }}</p>
                    <p>{{ $address->kota_kab }}, {{ $address->provinsi }}</p>
                    <p>{{ $address->country }}, {{ $address->zip_code }}</p>
                    <p>Phone: {{ $address->phone }}</p>
                    <div class="card-actions justify-end">
                        <button wire:click="editAddress({{ $address->id }})" class="btn btn-sm btn-info">Edit</button>
                        <button wire:click="deleteAddress({{ $address->id }})"
                            class="btn btn-sm btn-error">Delete</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if ($showModal)
        <div class="modal modal-open" id="my-modal">
            <div class="modal-box">
                <div class="mt-3 text-center">
                    <h3 class="font-bold text-lg">{{ $address_id ? 'Edit' : 'Add' }} Address</h3>
                    <form wire:submit.prevent="saveAddress" class="mt-2">
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Label Input -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Label Name</span>
                                </label>
                                <input type="text" wire:model="label" class="input input-bordered">
                                @error('label')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Name Input -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Name Recipient</span>
                                </label>
                                <input type="text" wire:model="name" class="input input-bordered">
                                @error('name')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Address Line 1 Input -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Address Line 1</span>
                                </label>
                                <input type="text" wire:model="address_line_1" class="input input-bordered">
                                @error('address_line_1')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Address Line 2 Input -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Address Line 2</span>
                                </label>
                                <input type="text" wire:model="address_line_2" class="input input-bordered">
                            </div>


                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Provinsi</span>
                                </label>
                                <select wire:model.live="selectedProvinsi" class="select select-bordered">
                                    <option value="">Pilih Provinsi</option>
                                    @foreach ($provinsi as $prov)
                                        <option value="{{ $prov['id'] }}"
                                            {{ $prov['id'] == $selectedProvinsi ? 'selected' : '' }}>
                                            {{ $prov['nama'] }}</option>
                                    @endforeach
                                </select>
                                @dump($selectedProvinsi)
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Kabupaten/Kota</span>
                                </label>
                                <select wire:model.live="selectedKabupaten" class="select select-bordered">
                                    <option value="">Pilih Kabupaten/Kota</option>
                                    @foreach ($kabupaten as $kab)
                                        <option value="{{ $kab['id'] }}"
                                            {{ $kab['id'] == $selectedKabupaten ? 'selected' : '' }}>
                                            {{ $kab['nama'] }}</option>
                                    @endforeach
                                </select>
                                @dump($selectedKabupaten)
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Kecamatan</span>
                                </label>
                                <select wire:model.live="selectedKecamatan" class="select select-bordered">
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach ($kecamatan as $kec)
                                        <option value="{{ $kec['id'] }}"
                                            {{ $kec['id'] == $selectedKecamatan ? 'selected' : '' }}>
                                            {{ $kec['nama'] }}</option>
                                    @endforeach
                                </select>
                                @dump($selectedKecamatan)
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Kelurahan</span>
                                </label>
                                <select wire:model.live="selectedKelurahan" class="select select-bordered">
                                    <option value="">Pilih Kelurahan</option>
                                    @foreach ($kelurahan as $kel)
                                        <option value="{{ $kel['id'] }}"
                                            {{ $kel['id'] == $selectedKelurahan ? 'selected' : '' }}>
                                            {{ $kel['nama'] }}</option>
                                    @endforeach
                                </select>
                                @dump($selectedKelurahan)
                            </div>

                            <!-- Country Input -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Country</span>
                                </label>
                                <input type="text" wire:model="country" class="input input-bordered" disabled>
                                @error('country')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Zip Code Input -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Zip Code</span>
                                </label>
                                <input type="text" wire:model="zip_code" class="input input-bordered">
                                @error('zip_code')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone Input -->
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Phone</span>
                                </label>
                                <input type="text" wire:model="phone" class="input input-bordered">
                                @error('phone')
                                    <span class="text-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Is Default Checkbox -->
                            <div class="form-control">
                                <label class="cursor-pointer label">
                                    <span class="label-text">Set as default address</span>
                                    <input type="checkbox" wire:model="is_default" class="checkbox checkbox-primary">
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="modal-action">
                                <button type="submit" class="btn btn-primary">Save Address</button>
                                <button type="button" class="btn" wire:click="closeModal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    {{-- @php
        dd($provinsi);
    @endphp --}}

</div>
