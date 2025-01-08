<div class="container mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Sidebar -->
        <div class="bg-base-100 p-4 shadow-lg rounded-lg">
            <ul class="menu bg-base-100 rounded-box">
                <li>
                    <a href="#" class="{{ $activeTab === 'orders' ? 'active' : '' }}"
                        wire:click.prevent="setActiveTab('orders')">My Orders</a>
                </li>
                <li>
                    <a href="#" class="{{ $activeTab === 'profile' ? 'active' : '' }}"
                        wire:click.prevent="setActiveTab('profile')">My Profile</a>
                </li>
                <li>
                    <a href="#" class="{{ $activeTab === 'address' ? 'active' : '' }}"
                        wire:click.prevent="setActiveTab('address')">Address</a>
                </li>
                <li>
                    <a href="#" wire:click.prevent="logout">Logout</a>
                </li>
            </ul>
        </div>

        <!-- Content Area -->
        <div class="col-span-3 bg-base-100 p-6 shadow-lg rounded-lg">
            @if ($activeTab === 'orders')
                <h2 class="text-2xl font-bold mb-4">My Orders</h2>
                <livewire:user-order-list />
            @elseif ($activeTab === 'profile')
                <h2 class="text-2xl font-bold mb-4">My Profile</h2>
                <!-- Profile content -->
            @elseif ($activeTab === 'address')
                <h2 class="text-2xl font-bold mb-4">Address</h2>
                <livewire:UserAddress />
            @endif
        </div>
    </div>

</div>
