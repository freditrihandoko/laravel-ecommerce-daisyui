<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserOrderList extends Component
{
    public $orders;

    public function mount()
    {
        // Ambil semua pesanan pengguna saat ini
        $this->orders = Order::with(['shippingMethod', 'paymentMethod', 'status'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.user-order-list');
    }
}
