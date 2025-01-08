<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Manage Hero Slide') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                @if (session()->has('message'))
                    <div class="toast toast-top toast-center">
                        <div class="alert alert-success">
                            <span>{{ session('message') }}</span>
                        </div>
                    </div>
                @endif

                <button class="btn btn-primary mb-4" wire:click="addSlide">Add New Slide</button>

                <!-- Tabel untuk menampilkan slides yang ada -->
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Order</th>
                                <th>Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($slides as $slide)
                                <tr>
                                    <td>
                                        @if ($slide->background_image)
                                            <img src="{{ asset('storage/' . $slide->background_image) }}"
                                                alt="{{ $slide->title }}" class="w-16 h-16 object-cover rounded">
                                        @else
                                            <div
                                                class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                No Image</div>
                                        @endif
                                    </td>
                                    <td>{{ $slide->title }}</td>
                                    <td>{{ $slide->order }}</td>
                                    <td>
                                        <input type="checkbox" wire:click="toggleActive({{ $slide->id }})"
                                            @if ($slide->is_active) checked @endif
                                            class="toggle toggle-primary">
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info"
                                            wire:click="editSlide({{ $slide->id }})">Edit</button>
                                        <button class="btn btn-sm btn-error"
                                            wire:click="deleteSlide({{ $slide->id }})">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modal untuk tambah/edit slide -->
                @if ($isModalOpen)
                    <div class="modal modal-open">
                        <div class="modal-box">
                            <h3 class="font-bold text-lg">{{ $editingSlideId ? 'Edit Hero Slide' : 'Add Hero Slide' }}
                            </h3>
                            <form wire:submit="saveSlide" class="space-y-4">
                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Title</span>
                                    </label>
                                    <input type="text" wire:model="title" class="input input-bordered"
                                        placeholder="Enter slide title">
                                    @error('title')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Description</span>
                                    </label>
                                    <textarea wire:model="description" class="textarea textarea-bordered h-24" placeholder="Enter slide description"></textarea>
                                    @error('description')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Button Text</span>
                                    </label>
                                    <input type="text" wire:model="buttonText" class="input input-bordered"
                                        placeholder="Enter button text">
                                    @error('buttonText')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Button Link</span>
                                    </label>
                                    <input type="url" wire:model="buttonLink" class="input input-bordered"
                                        placeholder="Enter button link">
                                    @error('buttonLink')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Order</span>
                                    </label>
                                    <select wire:model="order" class="select select-bordered w-full">
                                        @foreach ($availableOrders as $availableOrder)
                                            <option value="{{ $availableOrder }}">{{ $availableOrder }}</option>
                                        @endforeach
                                    </select>
                                    @error('order')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-control">
                                    <label class="label">
                                        <span class="label-text">Background Image</span>
                                    </label>
                                    @if ($editingSlideId && $existingBackgroundImage)
                                        <img src="{{ asset('storage/' . $existingBackgroundImage) }}"
                                            alt="Current background" class="mb-2 max-w-xs">
                                    @endif
                                    <input type="file" wire:model="backgroundImage"
                                        class="file-input file-input-bordered w-full" accept="image/*">
                                    @error('backgroundImage')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                    @if ($backgroundImage)
                                        <img src="{{ $backgroundImage->temporaryUrl() }}" alt="New background preview"
                                            class="mt-2 max-w-xs">
                                    @endif
                                </div>

                                <div class="form-control">
                                    <label class="label cursor-pointer">
                                        <span class="label-text">Active</span>
                                        <input wire:model="isActive" type="checkbox" {{ $isActive ? 'checked' : '' }}
                                            class="checkbox checkbox-success" />
                                    </label>
                                    @error('isActive')
                                        <span class="text-error text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="modal-action">
                                    <button type="submit" class="btn btn-primary">Save Slide</button>
                                    <button type="button" wire:click="closeModal" class="btn">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
