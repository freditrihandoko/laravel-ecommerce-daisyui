<!-- product-detail.blade.php -->
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <div class="text-sm breadcrumbs mb-6">
        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('product-list') }}">Products</a></li>
            <li>{{ $product->name }}</li>
        </ul>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div class="space-y-4 px-4">
            <!-- Adjust the main image size to 75% -->
            <div class="carousel lg:w-3/4 md:w-3/4 sm:w-auto mx-auto aspect-w-2 aspect-h-3 ">
                @foreach ($product->getMedia('product_images') as $index => $image)
                    <div id="slide{{ $index }}" class="carousel-item relative w-full"
                        wire:key="slide-{{ $index }}">
                        <img src="{{ $image->getUrl() }}" class="w-full h-full object-cover rounded-lg"
                            alt="{{ $product->name }}">
                        <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
                            <a href="#slide{{ $index == 0 ? count($product->getMedia('product_images')) - 1 : $index - 1 }}"
                                class="btn btn-circle">❮</a>
                            <a href="#slide{{ $index == count($product->getMedia('product_images')) - 1 ? 0 : $index + 1 }}"
                                class="btn btn-circle">❯</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Thumbnail Navigation -->
            <div class="flex justify-center w-full py-2 gap-2">
                @foreach ($product->getMedia('thumbnails') as $index => $image)
                    <a href="#slide{{ $index }}" class="cursor-pointer">
                        <img src="{{ $image->getUrl() }}"
                            class="w-16 h-24 object-cover rounded-lg transition-opacity duration-300 @if ($index === $activeSlide) opacity-100 @else opacity-30 @endif"
                            wire:click="$set('activeSlide', {{ $index }})" wire:key="thumb-{{ $index }}"
                            alt="{{ $product->name }}">
                    </a>
                @endforeach
            </div>
        </div>

        {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-8"> --}}
        <!-- Product Info -->
        <div class="space-y-6">
            <h1 class="text-3xl font-bold">{{ $product->name }}</h1>

            <p class="text-2xl font-bold text-primary">
                @if ($product->product_type === 'variant')
                    Rp. {{ number_format($product->variants->min('price'), 0, '.', '.') }} -
                    Rp. {{ number_format($product->variants->max('price'), 0, '.', '.') }}
                @else
                    Rp. {{ number_format($product->price, 0, '.', '.') }}
                @endif
            </p>
            @if ($product->discount_price)
                <p class="text-lg text-gray-500 line-through">Rp.
                    {{ number_format($product->discount_price, 0, '.', '.') }}</p>
            @endif
            <div class="prose max-w-none">
                {!! $product->description !!}
            </div>

            @if ($product->product_type === 'variant')
                <!-- Product Variants -->
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Variants</label>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach ($product->variants as $variant)
                                <button
                                    class="btn btn-sm {{ $selectedVariantId === $variant->id ? 'btn-primary' : 'btn-outline' }}"
                                    wire:click="selectVariant({{ $variant->id }})">
                                    {{ $variant->name }} - Rp. {{ number_format($variant->price, 0, '.', '.') }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Add to Cart -->
            <div class="flex space-x-4 items-center">
                <div class="flex border rounded-lg">
                    <button class="px-3 py-1 text-gray-600 hover:bg-gray-100" wire:click="decrementQuantity">-</button>
                    <input type="text" class="w-12 text-center border-none" wire:model.live="quantity"
                        value={{ $quantity }}>
                    <button class="px-3 py-1 text-gray-600 hover:bg-gray-100" wire:click="incrementQuantity">+</button>
                </div>
                {{-- <button
                    class="btn btn-primary flex-grow {{ $product->product_type === 'variant' && !$selectedVariantId ? 'btn-disabled' : '' }}"
                    wire:click="addToCart">
                    Add to Cart
                </button> --}}
                <button class="btn btn-primary flex-grow {{ $this->isOutOfStock() ? 'btn-disabled' : '' }}"
                    wire:click="addToCart" {{ $this->isOutOfStock() ? 'disabled' : '' }}>
                    {{ $this->isOutOfStock() ? 'Out of Stock' : 'Add to Cart' }}
                </button>
            </div>

            @if (session()->has('message'))
                <div class="toast toast-center toast-middle">
                    <div class="alert alert-success">
                        <span> {{ session('message') }}</span>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="toast toast-center">
                    <div class="alert alert-error">
                        <span> {{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if ($showLoginModal)
                <livewire:auth.login-modal />
            @endif
            <!-- Additional Info -->
            <div class="divide-y">
                <div class="py-3 flex justify-between">
                    <span class="text-gray-500">Availability</span>
                    <span class="font-medium">
                        @if ($product->product_type === 'variant')
                            Variant Product
                        @else
                            {{ $product->currentStock() > 0 ? 'In Stock' : 'Out of Stock' }}
                        @endif
                    </span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="text-gray-500">Category</span>
                    <span class="font-medium">{{ $product->category->name }}</span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="text-gray-500">SKU</span>
                    <span class="font-medium">
                        @if ($product->product_type === 'variant')
                            @foreach ($product->variants as $variant)
                                {{ $variant->name }} : {{ $variant->sku }} -
                                {{ $variant->currentStock() > 0 ? 'In Stock' : 'Out of Stock' }} <br>
                            @endforeach
                        @else
                            {{ $product->sku }}
                        @endif
                    </span>
                </div>
                <div class="py-3 flex justify-between">
                    <span class="text-gray-500">Weight</span>
                    <span class="font-medium">
                        @if ($product->product_type === 'variant')
                            @foreach ($product->variants as $variant)
                                {{ $variant->name }} : {{ $variant->weight ?? '0' }} Kg <br>
                            @endforeach
                        @else
                            {{ $product->weight ?? '0' }} Kg
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-4">Product Description</h2>
        <div class="prose max-w-none">
            {!! $product->description !!}
        </div>
    </div>

    <!-- Related Products -->
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-4">Related Products</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ($relatedProducts as $relatedProduct)
                <a href="{{ route('product-detail', $relatedProduct->slug) }}">
                    <div class="card bg-base-100 shadow-sm">
                        <figure><img src="{{ $relatedProduct->getFirstMediaUrl('product_images') }}"
                                alt="{{ $relatedProduct->name }}" /></figure>
                        <div class="card-body p-4">
                            <h3 class="card-title text-sm">{{ $relatedProduct->name }}</h3>
                            <p class="text-primary font-bold">
                                @if ($relatedProduct->product_type === 'variant')
                                    Rp. {{ number_format($relatedProduct->variants->min('price'), 0, '.', '.') }} -
                                    Rp. {{ number_format($relatedProduct->variants->max('price'), 0, '.', '.') }}
                                @else
                                    Rp. {{ number_format($relatedProduct->price, 0, '.', '.') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

</div>
