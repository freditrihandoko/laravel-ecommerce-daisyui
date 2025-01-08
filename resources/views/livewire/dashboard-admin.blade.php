<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Total Orders -->
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Total Orders</div>
                        <div class="stat-value">{{ $stats['totalOrders'] }}</div>
                        <div class="stat-desc">Orders placed</div>
                    </div>
                </div>
    
                <!-- Total Customers -->
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Customers</div>
                        <div class="stat-value">{{ $stats['totalCustomers'] }}</div>
                        <div class="stat-desc">Registered users</div>
                    </div>
                </div>
    
                <!-- Total Products -->
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Products</div>
                        <div class="stat-value">{{ $stats['totalProducts'] }}</div>
                        <div class="stat-desc">Active products</div>
                    </div>
                </div>
    
                <!-- Total Revenue -->
                <div class="stats shadow">
                    <div class="stat">
                        <div class="stat-title">Estimate Revenue</div>
                        <div class="stat-value">Rp {{ number_format($stats['totalRevenue'], 0, ',', '.') }}</div>
                        <div class="stat-desc">Total earnings</div>
                    </div>
                </div>
            </div>
    
            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Quick Access Menu -->
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        <!-- Products -->
                        <a href="{{ route('product-management') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                            <div class="card-body">
                                <h2 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    Products
                                </h2>
                                <p>Manage products and variants</p>
                            </div>
                        </a>
    
                        <!-- Categories -->
                        <a href="{{ route('category-management') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                            <div class="card-body">
                                <h2 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    Categories
                                </h2>
                                <p>Manage product categories</p>
                            </div>
                        </a>
    
                        <!-- Orders -->
                        <a href="{{ route('order-management') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                            <div class="card-body">
                                <h2 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Orders
                                </h2>
                                <p>View and manage orders</p>
                            </div>
                        </a>
    
                        <!-- Customers -->
                        <a href="{{ route('customer-management') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                            <div class="card-body">
                                <h2 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Customers
                                </h2>
                                <p>Manage customer accounts</p>
                            </div>
                        </a>
    
                        <!-- Shipping -->
                        <a href="{{ route('shipping-management') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                            <div class="card-body">
                                <h2 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                    Shipping
                                </h2>
                                <p>Manage shipping methods</p>
                            </div>
                        </a>
    
                        <!-- Hero Slides -->
                        <a href="{{ route('hero-slide-management') }}" class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow">
                            <div class="card-body">
                                <h2 class="card-title">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Hero Slides
                                </h2>
                                <p>Manage homepage slides</p>
                            </div>
                        </a>
                    </div>
                </div>
    
                <!-- Recent Orders -->
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Recent Orders</h2>
                        <div class="space-y-4">
                            @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between p-4 bg-base-200 rounded-lg">
                                <div>
                                    <p class="font-medium">Order #{{ $order->id }}</p>
                                    <p class="text-sm opacity-70">{{ $order->user->name }}</p>
                                </div>
                                <div class="badge badge-primary">{{ $order->status->name }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('order-management') }}" class="btn btn-primary btn-sm">View All Orders</a>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>