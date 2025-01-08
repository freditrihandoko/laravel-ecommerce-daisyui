<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-8">Our Products</h1>
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar with filters -->
        <div class="w-full md:w-1/4">
            <div class="bg-base-100 shadow-sm p-4 rounded-lg">
                <h2 class="text-xl font-semibold mb-4">Filters</h2>
                <!-- Category Filter -->
                <div class="mb-4">
                    <h3 class="font-medium mb-2">Category</h3>
                    <div class="space-y-2">
                        @foreach ($categories as $category)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.live="selectedCategories"
                                    value="{{ $category->slug }}" class="checkbox checkbox-sm" />
                                <span class="ml-2">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <!-- Sort By -->
                <div class="mb-4">
                    <h3 class="font-medium mb-2">Sort By</h3>
                    <select wire:model.live="sortBy" class="select select-bordered w-full">
                        <option value="default">Default</option>
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- Product Grid -->
        <div class="w-full md:w-3/4">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
                @foreach ($products as $product)
                    <a href="{{ route('product-detail', $product->slug) }}">
                        <div class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow duration-300">

                            <figure class="relative pt-[150%] overflow-hidden">
                                <img src="{{ $product->getFirstMediaUrl('product_images') }}" alt="{{ $product->name }}"
                                    class="absolute inset-0 w-full h-full object-cover object-center" />
                            </figure>
                            <div class="card-body p-2 sm:p-3 md:p-4">
                                <h3 class="card-title text-sm sm:text-base md:text-lg font-semibold mb-1 line-clamp-1">
                                    {{ $product->name }}
                                </h3>
                                {{-- <p class="text-xs sm:text-sm text-gray-600 mb-2 line-clamp-2 hidden sm:block">
                                    {!! $product->description !!}</p> --}}
                                <div class="flex justify-between items-center">
                                    <span class="text-sm sm:text-base md:text-lg font-bold">
                                        @if ($product->product_type === 'variant')
                                            Rp. {{ number_format($product->variants->min('price'), 0, '.', '.') }} -
                                            Rp. {{ number_format($product->variants->max('price'), 0, '.', '.') }}
                                        @else
                                            Rp. {{ number_format($product->price, 0, '.', '.') }}
                                        @endif
                                    </span>
                                    <button class="btn btn-primary btn-xs sm:btn-sm">View Product</button>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
