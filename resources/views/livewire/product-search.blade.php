<div>
    <button class="btn btn-ghost btn-circle" wire:click="search">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </button>

    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Search Products</h3>
                <div class="form-control">
                    <div class="input-group flex items-center">
                        <input type="text" placeholder="Search…" wire:model.live.debounce.300ms="searchTerm"
                            class="input input-bordered w-full" />
                        <button class="btn btn-square ml-2" type="button" wire:click="search">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21
                   21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <span wire:loading wire:target="searchTerm" class="loading loading-spinner loading-md"></span>
                    @if (strlen($searchTerm) > 2)
                        @if ($products->isEmpty())
                            <p class="text-gray-500">No products found.</p>
                        @else
                            <ul class="space-y-4">
                                @foreach ($products as $product)
                                    <li class="flex items-center">
                                        <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}"
                                            alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded mr-4">
                                        <div>
                                            <p class="text-sm font-semibold">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500">
                                                @if ($product->product_type === 'variant')
                                                    ${{ number_format($product->variants->min('price'), 2) }} -
                                                    ${{ number_format($product->variants->max('price'), 2) }}
                                                @else
                                                    ${{ number_format($product->price, 2) }}
                                                @endif
                                            </p>
                                            //jangan pakai ini, sudah tidak dipakai
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @else
                        <p class="text-gray-500">Type at least 3 characters to search.</p>
                    @endif
                </div>
                <div class="modal-action">
                    <button class="btn" wire:click="$set('isOpen', false)">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
