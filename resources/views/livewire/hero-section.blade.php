<div>
    <div class="carousel w-full">
        @foreach ($slides as $index => $slide)
            <div id="slide{{ $index + 1 }}" class="carousel-item relative w-full">
                <div class="hero min-h-screen"
                    style="background-image: url({{ asset('storage/' . $slide->background_image) }});">
                    <div class="hero-overlay bg-opacity-60"></div>
                    <div class="hero-content text-center text-neutral-content">
                        <div class="max-w-md">
                            <h1 class="mb-5 text-5xl font-bold">{{ $slide->title }}</h1>
                            <p class="mb-5">{{ $slide->description }}</p>
                            <a href="{{ $slide->button_link }}" class="btn btn-primary">{{ $slide->button_text }}</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="flex justify-center w-full py-2 gap-2 relative -mt-8">
        @foreach ($slides as $index => $slide)
            <a href="#slide{{ $index + 1 }}" class="btn btn-xs btn-circle bg-gray-500"></a>
        @endforeach
    </div>
</div>
