<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Category') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-col h-[calc(100vh-200px)]">
                <div class="mb-4 flex justify-between items-center">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search categories..."
                        class="input input-bordered w-full max-w-xs" />
                    <button wire:click="create" class="btn btn-primary">Add New Category</button>
                </div>

                <div class="overflow-x-auto overflow-y-auto flex-grow">
                    <table class="table w-full">
                        <thead class="sticky top-0 bg-white dark:bg-gray-800 z-10">
                            <tr>
                                <th class="p-3">Image</th>
                                <th class="p-3">Name</th>
                                <th class="p-3">Description</th>
                                <th class="p-3">Parent Category</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr wire:key="{{ $category->id }}">
                                    <td class="p-3">
                                        @if ($category->image_path)
                                            <img src="{{ asset('storage/' . $category->image_path) }}"
                                                alt="{{ $category->name }}" class="w-16 h-16 object-cover rounded">
                                        @else
                                            <div
                                                class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                No Image
                                            </div>
                                        @endif
                                    </td>
                                    <td class="p-3">{{ $category->name }}</td>
                                    <td class="p-3">{{ Str::limit($category->description, 50) }}</td>
                                    <td class="p-3">{{ $category->parent ? $category->parent->name : 'None' }}</td>
                                    <td class="p-3">
                                        <span
                                            class="badge {{ $category->is_active ? 'badge-success' : 'badge-error' }}">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <button wire:click="edit({{ $category->id }})"
                                            class="btn btn-sm btn-info">Edit</button>
                                        <button wire:click="confirmCategoryDeletion({{ $category->id }})"
                                            class="btn btn-sm btn-error">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>

    <x-modal2 name="category-modal" title="Category">
        <x-slot:body>
            @if ($errors->any())
                <div role="alert" class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    @foreach ($errors->all() as $error)
                        <span>{{ $error }}</span>
                    @endforeach
                </div>
            @endif
            <h3 class="font-bold text-lg">
                {{ $editingCategoryId ? 'Edit Category' : 'Create New Category' }}</h3>
            <form wire:submit.prevent=" {{ $editingCategoryId ? 'update' : 'store' }}">
                @dump($editingCategoryId)
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Name</span>
                    </label>
                    @dump($name)
                    <input type="text" wire:model.lazy="name" class="input input-bordered" required>
                    @error('name')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea wire:model="description" class="textarea textarea-bordered" required></textarea>
                    @error('description')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Parent Category (Optional)</span>
                    </label>
                    <select wire:model="parent_id" class="select select-bordered">
                        <option value="">None</option>
                        @foreach ($parentCategories as $parentCategory)
                            <option value="{{ $parentCategory->id }}">{{ $parentCategory->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Image</span>
                    </label>
                    <input type="file" wire:model="image" class="input input-bordered" accept="image/*">
                    @error('image')
                        <span class="text-error">{{ $message }}</span>
                    @enderror

                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}" class="mt-2 w-32 h-32 object-cover rounded">
                    @elseif ($tempImage)
                        <img src="{{ asset('storage/' . $tempImage) }}" class="mt-2 w-32 h-32 object-cover rounded">
                    @endif
                </div>

                <div class="form-control">
                    <label class="label cursor-pointer">
                        <span class="label-text">Active</span>
                        <input type="checkbox" wire:model="is_active" class="toggle toggle-primary">
                    </label>
                </div>

                <div class="modal-action">
                    <button type="submit"
                        class="btn btn-primary">{{ $editingCategoryId ? 'Update' : 'Save' }}</button>

                    <button type="button" wire:click="closeModal" class="btn">Cancel</button>
                </div>
            </form>
        </x-slot:body>
    </x-modal2>

    <x-modal2 name="confirm-category-deletion" title="Confirm Deletion">
        <x-slot:body>
            <h3 class="font-bold text-lg">Are you sure you want to delete this category?</h3>
            <div class="modal-action">
                <button wire:click="deleteCategory" class="btn btn-error">Yes, Delete</button>
                <button wire:click="closeModal" class="btn">Cancel</button>
            </div>
        </x-slot:body>
    </x-modal2>


    @if (session()->has('message'))
        <div class="toast toast-top toast-center">
            <div class="alert alert-success">
                <span>{{ session('message') }}</span>
            </div>
        </div>
    @endif
</div>
