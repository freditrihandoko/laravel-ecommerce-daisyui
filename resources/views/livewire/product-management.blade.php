<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Product') }}
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
                <div class="mb-4 flex justify-between">
                    <div class="flex space-x-2">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search products..."
                            class="input input-bordered w-full max-w-xs" />
                        <select wire:model.live.debounce.300ms="filterCategory" class="select select-bordered">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button wire:click="create" class="btn btn-primary">Add New Product</button>
                </div>

                <div class="overflow-auto" style="max-height: calc(100vh - 400px);">
                    <table class="table w-full">
                        <thead class="sticky top-0 bg-white dark:bg-gray-800 z-10">
                            <tr>
                                <th>Image</th>
                                <th wire:click="sortBy('name')" class="cursor-pointer">
                                    Name
                                    @if ($sortField === 'name')
                                        <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                                    @endif
                                </th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th wire:click="sortBy('price')" class="cursor-pointer">
                                    Price
                                    @if ($sortField === 'price')
                                        <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                                    @endif
                                </th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr wire:key="{{ $product->id }}">
                                    <td>
                                        @if ($product->getFirstMediaUrl('product_images'))
                                            <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}"
                                                alt="{{ $product->name }}" class="w-16 h-16 object-cover rounded">
                                        @else
                                            <div
                                                class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                No
                                                Image</div>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>
                                        @if ($product->product_type === 'single')
                                            Rp. {{ number_format($product->price, 0, '.', '.') }}
                                        @else
                                            <ul>
                                                @foreach ($product->variants as $variant)
                                                    <li>{{ $variant->name }}: Rp.
                                                        {{ number_format($variant->price, 0, '.', '.') }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    {{-- <td>{{ $product->stock }}</td> --}}
                                    <td>
                                        @if ($product->product_type === 'single')
                                            {{ $product->currentStock() }}
                                        @else
                                            <ul>
                                                @foreach ($product->variants as $variant)
                                                    <li>{{ $variant->name }}: {{ $variant->currentStock() }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td>
                                        <span
                                            class="badge {{ $product->is_active ? 'badge-success' : 'badge-error' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button wire:click="viewProduct({{ $product->id }})"
                                            class="btn btn-sm btn-primary">View</button>
                                        <button wire:click="edit({{ $product->id }})"
                                            class="btn btn-sm btn-info">Edit</button>
                                        <button wire:click="confirmProductDeletion({{ $product->id }})"
                                            class="btn btn-sm btn-error">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </div>

    <x-modal2 name="product-modal" title="Product">
        <x-slot:body>
            <h3 class="font-bold text-lg mb-4">
                {{ $editingProductId ? 'Edit Product' : 'Create New Product' }}
            </h3>
            <form wire:submit.prevent="{{ $editingProductId ? 'update' : 'store' }}">
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Product Type</span>
                    </label>
                    <select wire:model.live="product_type" class="select select-bordered" required
                        {{ $editingProductId ? 'disabled' : '' }}>
                        <option value="">Select product type</option>
                        <option value="single">
                            Single Product</option>
                        <option value="variant">
                            Product with Variants</option>
                    </select>
                    @error('product_type')
                        <span class="text-error">{{ $message }}</span>
                    @enderror

                </div>

                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Name</span>
                    </label>
                    <input type="text" wire:model.lazy="name" value="{{ $name }}"
                        class="input input-bordered" required>
                    @error('name')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>


                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <div class="mb-6">
                        <input id="description" type="hidden" name="description" value="{{ $description }}"
                            wire:model.live="description">
                        <trix-editor input="description" class="trix-content"></trix-editor>
                        @error('description')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror

                        @script
                            <script>
                                document.addEventListener("trix-blur", function(event) {
                                    console.log('description');

                                    let trixEditor = document.querySelector('trix-editor');
                                    // let trixEditorContent = trixEditor.editor.getDocument().toString();
                                    // let trixEditorContent = trixEditor.editor.getHTML();

                                    let hiddenInput = document.getElementById('description');
                                    let trixEditorContent = hiddenInput.value; // Get the value from the hidden input


                                    @this.set('description', trixEditorContent);
                                });
                            </script>
                        @endscript
                    </div>

                </div>

                @if ($product_type === 'single')
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">SKU</span>
                        </label>
                        <input type="text" wire:model.lazy="sku" class="input input-bordered" required>
                        @error('sku')
                            <span class="text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Weight</span>
                        </label>
                        <input type="number" wire:model="weight" class="input input-bordered" step="0.1" required>
                        @error('weight')
                            <span class="text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Price</span>
                        </label>
                        <input type="text" wire:model.lazy="formattedPrice" class="input input-bordered" required
                            type-currency="IDR" placeholder="Rp">
                        @error('price')
                            <span class="text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">Discount Price</span>
                        </label>
                        <input type="text" wire:model.lazy="formattedDiscountPrice" type-currency="IDR"
                            class="input input-bordered" placeholder="Rp">
                        @error('discount_price')
                            <span class="text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($editingProductId)
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Current Stock</span>
                            </label>
                            <span>{{ $current_stock }}</span>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Stock Adjustment Type</span>
                            </label>
                            <select wire:model="stock_action_type" class="select select-bordered">
                                <option value="">Select action</option>
                                <option value="addition">Addition</option>
                                <option value="reduction">Reduction</option>
                            </select>
                            @error('stock_action_type')
                                <span class="text-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control mb-4" wire:loading.remove wire:target="stock_action_type">
                            <label class="label">
                                <span class="label-text">Stock Change Amount</span>
                            </label>
                            <input type="number" wire:model="stock_change" class="input input-bordered" required>
                            @error('stock_change')
                                <span class="text-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Stock Adjustment Note</span>
                            </label>
                            <textarea wire:model="stock_note" class="textarea textarea-bordered" placeholder="Enter a note"></textarea>
                            @error('stock_note')
                                <span class="text-error">{{ $message }}</span>
                            @enderror
                        </div>
                    @else
                        <div class="form-control mb-4">
                            <label class="label">
                                <span class="label-text">Stock</span>
                            </label>
                            <input type="number" wire:model="stock" class="input input-bordered" required>
                            @error('stock')
                                <span class="text-error">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                @endif

                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Category</span>
                    </label>
                   
                    <select wire:model="category_id" class="select select-bordered" required>
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            {{-- <option value="{{ $category->id }}">{{ $category->name }}</option> --}}
                            <option value="{{ $category->id }}"
                                x-bind:selected="$data.category_id == {{ $category->id }}">{{ $category->name }}
                            </option>
                        @endforeach

                    </select>
                    @error('category_id')
                        <span class="text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Images</span>
                    </label>
                    <input type="file" wire:model.live="images" multiple class="input input-bordered"
                        accept="image/*">
                    @error('images.*')
                        <span class="text-error">{{ $message }}</span>
                    @enderror

                    @if ($editingProductId)
                        @if (count($imagePreviews['existing']))
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach ($imagePreviews['existing'] as $preview)
                                    <div class="relative">
                                        <img src="{{ $preview['url'] }}" alt="Product Image"
                                            class="w-20 h-20 object-cover rounded">
                                        <button type="button" wire:click="removeExistingImage({{ $preview['id'] }})"
                                            class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-full">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if (count($imagePreviews['new']))
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach ($imagePreviews['new'] as $index => $preview)
                                    <div class="relative">
                                        <img src="{{ $preview }}" alt="Preview Image"
                                            class="w-20 h-20 object-cover rounded">
                                        <button type="button" wire:click="removeImagePreview({{ $index }})"
                                            class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-full">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        @if (count($imagePreviews['new']))
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach ($imagePreviews['new'] as $index => $preview)
                                    <div class="relative">
                                        <img src="{{ $preview }}" alt="Preview Image"
                                            class="w-20 h-20 object-cover rounded">
                                        <button type="button" wire:click="removeImagePreview({{ $index }})"
                                            class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-full">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif

                </div>

                <div class="form-control mb-4">
                    <label class="label cursor-pointer">
                        <span class="label-text">Active</span>
                        {{-- <input type="checkbox" wire:model="is_active" class="toggle toggle-primary"> --}}
                        <input wire:model="is_active" type="checkbox" {{ $is_active ? 'checked' : '' }}
                            class="checkbox checkbox-success" />
                    </label>
                </div>

                @if ($product_type === 'variant')
                    <div class="form-control mb-8">
                        <label class="label">
                            <span class="label-text text-lg font-semibold">Product Variants</span>
                        </label>
                        <div class="space-y-6">
                            @foreach ($variants as $index => $variant)
                                <div class="bg-base-200 p-4 rounded-lg shadow">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label class="label">
                                                <span class="label-text">Variant Name</span>
                                            </label>
                                            <input type="text" wire:model="variants.{{ $index }}.name"
                                                placeholder="e.g., Size, Color" class="input input-bordered w-full"
                                                required>
                                        </div>
                                        <div>
                                            <label class="label">
                                                <span class="label-text">SKU</span>
                                            </label>
                                            <input type="text" wire:model.lazy="variants.{{ $index }}.sku"
                                                placeholder="Unique SKU" class="input input-bordered w-full" required>
                                        </div>
                                        <div>
                                            <label class="label">
                                                <span class="label-text">Weight (kg)</span>
                                            </label>
                                            <input type="number" wire:model="variants.{{ $index }}.weight"
                                                placeholder="0.00" class="input input-bordered w-full" step="0.01"
                                                required>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label class="label">
                                                <span class="label-text">Price</span>
                                            </label>
                                            <input type="text"
                                                wire:model.lazy="variants.{{ $index }}.price" placeholder="Rp"
                                                class="input input-bordered w-full" type-currency="IDR" required>
                                        </div>
                                        @if (isset($variant['id']))
                                            <div>
                                                <label class="label">
                                                    <span class="label-text">Current Stock</span>
                                                </label>
                                                <input type="text" value="{{ $variant['current_stock'] ?? 0 }}"
                                                    class="input input-bordered w-full bg-gray-100" readonly>
                                            </div>
                                            <div>
                                                <label class="label">
                                                    <span class="label-text">Stock Action</span>
                                                </label>
                                                <select
                                                    wire:model.live="variants.{{ $index }}.stock_action_type"
                                                    class="select select-bordered w-full">
                                                    <option value="">Select Action</option>
                                                    <option value="addition">Add Stock</option>
                                                    <option value="reduction">Reduce Stock</option>
                                                </select>
                                            </div>
                                        @else
                                            <div>
                                                <label class="label">
                                                    <span class="label-text">Initial Stock</span>
                                                </label>
                                                <input type="number" wire:model="variants.{{ $index }}.stock"
                                                    placeholder="0" class="input input-bordered w-full" required>
                                            </div>
                                        @endif
                                    </div>
                                    @if (isset($variant['id']) && !empty($variant['stock_action_type']))
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="label">
                                                    <span class="label-text">Stock Change</span>
                                                </label>
                                                <input type="number"
                                                    wire:model="variants.{{ $index }}.stock_change"
                                                    placeholder="0" class="input input-bordered w-full">
                                            </div>
                                            <div>
                                                <label class="label">
                                                    <span class="label-text">Stock Change Note</span>
                                                </label>
                                                <input type="text"
                                                    wire:model="variants.{{ $index }}.stock_note"
                                                    placeholder="Reason for stock change"
                                                    class="input input-bordered w-full">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="flex justify-end">
                                        <button type="button" wire:click="removeVariant({{ $index }})"
                                            class="btn btn-error btn-sm">Remove Variant</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" wire:click="addVariant" class="btn btn-primary mt-4">
                            + Add Another Variant
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li class="text-error">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="modal-action">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" x-on:click="$dispatch('close-modal')" class="btn">Cancel</button>
                </div>
            </form>
        </x-slot:body>
    </x-modal2>

    @isset($viewingProduct)
        <x-modal2 name="product-view" title="Product Details">
            <x-slot:body>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl mx-auto">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">{{ $viewingProduct->name }}
                        </h2>

                        <!-- Product Images Carousel -->
                        <div class="mb-6">
                            <div class="carousel w-full rounded-lg overflow-hidden">
                                @foreach ($viewingProduct->getMedia('product_images') as $image)
                                    <div id="slide{{ $loop->index }}" class="carousel-item relative w-full">
                                        <img src="{{ $image->getUrl() }}" class="w-full object-cover h-64"
                                            alt="{{ $viewingProduct->name }}">
                                        <div
                                            class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
                                            <a href="#slide{{ $loop->index == 0 ? $viewingProduct->getMedia('product_images')->count() - 1 : $loop->index - 1 }}"
                                                class="btn btn-circle">❮</a>
                                            <a href="#slide{{ $loop->index == $viewingProduct->getMedia('product_images')->count() - 1 ? 0 : $loop->index + 1 }}"
                                                class="btn btn-circle">❯</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="flex justify-center w-full py-2 gap-2">
                                @foreach ($viewingProduct->getMedia('product_images') as $image)
                                    <a href="#slide{{ $loop->index }}" class="btn btn-xs">{{ $loop->index + 1 }}</a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Product Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Product Information</h3>
                                <dl class="space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-200">
                                            {{ $viewingProduct->category->name }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Price:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-200">Rp.
                                            {{ number_format($viewingProduct->price, 0, ',', '.') }}</dd>
                                    </div>
                                    @if ($viewingProduct->discount_price)
                                        <div class="flex justify-between">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Discount
                                                Price:</dt>
                                            <dd class="text-sm text-gray-900 dark:text-gray-200">Rp.
                                                {{ number_format($viewingProduct->discount_price, 0, ',', '.') }}</dd>
                                        </div>
                                    @endif
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-200">
                                            {{ $viewingProduct->currentStock() }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-200">{{ $viewingProduct->sku }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status:</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-200">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $viewingProduct->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $viewingProduct->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold mb-2">Description</h3>
                                <div class="text-sm text-gray-700 dark:text-gray-300 prose max-w-none">
                                    {!! $viewingProduct->description !!}
                                </div>
                            </div>
                        </div>

                        <!-- Variants (if applicable) -->
                        @if ($viewingProduct->product_type === 'variant')
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold mb-2">Variants</h3>
                                <div class="overflow-x-auto">
                                    <table class="table w-full">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>SKU</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($viewingProduct->variants as $variant)
                                                <tr>
                                                    <td>{{ $variant->name }}</td>
                                                    <td>Rp. {{ number_format($variant->price, 0, ',', '.') }}</td>
                                                    <td>{{ $variant->currentStock() }}</td>
                                                    <td>{{ $variant->sku }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <!-- Stock History -->
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold mb-2">Stock History</h3>
                            <div class="overflow-x-auto">
                                <table class="table w-full">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($viewingProduct->stockHistory()->latest()->take(10)->get() as $stockRecord)
                                            <tr>
                                                <td>{{ $stockRecord->created_at->format('Y-m-d H:i') }}</td>
                                                <td>
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $stockRecord->action_type === 'addition' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($stockRecord->action_type) }}
                                                    </span>
                                                </td>
                                                <td>{{ $stockRecord->quantity }}</td>
                                                <td>{{ $stockRecord->note ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-6 py-3 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="closeViewModal" class="btn btn-primary">
                            Close
                        </button>
                    </div>
                </div>
            </x-slot:body>
        </x-modal2>
    @endisset

    <x-modal2 name="confirm-product-deletion" title="Delete Product">
        <x-slot:body>
            <p class="py-4">Are you sure you want to delete this product?</p>
            <div class="modal-action">
                <button wire:click="deleteProduct" class="btn btn-error">Yes, Delete</button>
                <button wire:click="$dispatch('close-modal')" class="btn">Cancel</button>
            </div>
        </x-slot:body>
    </x-modal2>

</div>
@script
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
@endscript
