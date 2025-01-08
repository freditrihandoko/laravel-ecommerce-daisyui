<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class DashboardAdmin extends Component
{
    public function render()
    {
        $stats = [
            'totalOrders' => Order::count(),
            'totalCustomers' => User::where('role', 'customer')->count(),
            'totalProducts' => Product::count(),
            'totalRevenue' => Order::sum('total_amount'),
        ];

        $recentOrders = Order::with(['user'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.dashboard-admin', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
