<div class="dropdown">
    <label tabindex="0" class="btn btn-ghost lg:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
        </svg>
    </label>
    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('product-list') }}">Products</a></li>
        <li>
            <a>Collections</a>
            <ul class="p-2">
                @foreach ($categories as $category)
                    <li><a href="/product?categories[0]={{ $category->slug }}">{{ $category->name }}</a></li>
                @endforeach
            </ul>
        </li>
        <li><a>About</a></li>
        <li><a>Contact</a></li>
    </ul>
</div>
