<div class="modal @if ($showLoginModal) modal-open @endif">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Login</h3>
        <form wire:submit.prevent="login">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input type="email" wire:model="email" class="input input-bordered" required>
            </div>
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Password</span>
                </label>
                <input type="password" wire:model="password" class="input input-bordered" required>
            </div>
            @if (session()->has('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Login</button>
                <button type="button" class="btn" wire:click="closeLoginModal">Close</button>
            </div>
            <div>
                <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                    Don’t have an account yet? <a href="#" wire:click.prevent="openRegisterModal"
                        class="font-medium text-primary hover:underline ">Sign
                        up</a>
                </p>
            </div>
        </form>
    </div>
</div>
