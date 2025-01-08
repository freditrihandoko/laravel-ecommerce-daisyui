<div class="navbar bg-base-100 shadow-lg sticky top-0 z-50">
    <div class="navbar-start">
        <livewire:navbar.mobile-menu />
        @if($logo)
                  <img src="{{ config('app.url'). '/' .$logo }}" class="h-8 w-8" alt="">
        @endif
        <a class="btn btn-ghost normal-case text-xl">{{ $storeName }}</a>
    </div>
    <div class="navbar-center hidden lg:flex">
        <livewire:navbar.desktop-menu />
    </div>
    <div class="navbar-end">
        <livewire:navbar.search-button />
        <livewire:navbar.shopping-cart />
        <livewire:navbar.user-menu />
    </div>
</div>
