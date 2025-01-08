<div class="container mx-auto py-16 px-4 sm:px-6 lg:px-4">
    <h2 class="text-3xl font-bold text-center mb-8">Shop by Category</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($categories as $category)
            <div
                class="group relative overflow-hidden rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 ease-in-out">
                <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}"
                    class="w-full h-64 object-cover transition-transform duration-300 ease-in-out group-hover:scale-110" />
                <div
                    class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 ease-in-out">
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-white mb-2">{{ $category->name }}</h3>
                        {{-- <a href="{{ route('category.show', $category->slug) }}"
                            class="inline-block bg-white text-black px-4 py-2 rounded-full hover:bg-gray-200 transition-colors duration-300 ease-in-out">
                            Shop Now
                        </a> --}}
                        <a href="{{ $category->slug }}"
                            class="inline-block bg-white text-black px-4 py-2 rounded-full hover:bg-gray-200 transition-colors duration-300 ease-in-out">
                            Shop Now
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
