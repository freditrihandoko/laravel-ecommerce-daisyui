<div class="dropdown dropdown-end">
    @auth
        <label tabindex="0" class="btn btn-ghost normal-case">{{ Auth::user()->name }}</label>
        <ul tabindex="0"
            class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 dark:bg-gray-700 rounded-box w-52">
            <li><a href="{{ route('dashboard-user') }}">My Order</a></li>
            {{-- <li><a href="#">Profile</a></li> --}}
            <li><a wire:click="logout">Logout</a></li>
        </ul>
    @else
        <label tabindex="0" class="btn btn-ghost btn-circle avatar">
            <div class="w-10 rounded-full">
                <button class="btn btn-square btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        class="inline-block h-5 w-5 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z">
                        </path>
                    </svg>
                </button>
            </div>
        </label>
        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
            <li><a wire:click="openLoginModal">Login</a></li>
            <li><a wire:click="openRegisterModal">Register</a></li>
            <li><a href="{{ route('password.request') }}">Reset Password</a></li>
        </ul>
    @endauth

    @if ($showLoginModal)
        <livewire:auth.login-modal :showLoginModal="$showLoginModal" />
    @endif

    @if ($showRegisterModal)
        <livewire:auth.register-modal :showRegisterModal="$showRegisterModal" />
    @endif
</div>
