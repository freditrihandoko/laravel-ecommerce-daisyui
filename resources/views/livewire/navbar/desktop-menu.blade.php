<ul class="menu menu-horizontal px-1">
    <li><a href="{{ route('home') }}">Home</a></li>
    <li><a href="{{ route('product-list') }}">Products</a></li>
    <li tabindex="0">
        <details>
            <summary>Collections</summary>
            <ul class="p-2">
                @foreach ($categories as $category)
                    <li><a href="/product?categories[0]={{ $category->slug }}">{{ $category->name }}</a></li>
                @endforeach
            </ul>
        </details>
    </li>
    <li><a>About</a></li>
    <li><a>Contact</a></li>
</ul>
