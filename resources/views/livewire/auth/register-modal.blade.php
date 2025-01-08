<div class="modal @if ($showRegisterModal) modal-open @endif">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Register</h3>
        <form wire:submit.prevent="register">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Name</span>
                </label>
                <input type="text" wire:model="name" class="input input-bordered" required>
            </div>
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
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Confirm Password</span>
                </label>
                <input type="password" wire:model="password_confirmation" class="input input-bordered" required>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li class="text-error">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="modal-action">
                <button type="submit" class="btn btn-primary">Register</button>
                <button type="button" class="btn" wire:click="closeRegisterModal">Close</button>
            </div>
        </form>
    </div>
</div>
