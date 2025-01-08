<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Featured Outfits</h2>
        <a href="#" class="btn btn-outline btn-sm">
            View All ({{ $totalProducts }})
        </a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-5 lg:gap-6">
        @foreach ($products as $product)
            <a href="{{ route('product-detail', $product->slug) }}">
                <div class="card bg-base-100 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <figure class="relative pt-[150%] overflow-hidden">
                        <img src="{{ $product->getFirstMediaUrl('product_images') }}" alt="{{ $product->name }}"
                            class="absolute inset-0 w-full h-full object-cover object-center" />
                    </figure>
                    <div class="card-body p-2 sm:p3 md:p-4">
                        <h3 class="card-title text-xs sm:text-sm md:text-base font-semibold mb-1 line-clamp-1">
                            {{ $product->name }}
                        </h3>
                        {{-- <p class="text-xs text-gray-600 mb-2 line-clamp-2 hidden sm:block">{!! $product->description !!}</p> --}}
                        <div class="flex justify-between items-center">
                            <span class="text-xs sm:text-sm md:text-base font-bold">
                                @if ($product->product_type === 'variant')
                                    Rp. {{ number_format($product->variants->min('price'), 0, '.', '.') }} -
                                    Rp. {{ number_format($product->variants->max('price'), 0, '.', '.') }}
                                @else
                                    Rp. {{ number_format($product->price, 0, '.', '.') }}
                                @endif
                            </span>
                            {{-- <button class="btn btn-primary btn-xs sm:btn-sm">View Product</button> --}}
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
