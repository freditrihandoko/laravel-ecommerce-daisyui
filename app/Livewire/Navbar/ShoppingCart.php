<?php

namespace App\Livewire\Navbar;

use App\Models\Cart;
use Livewire\Component;

class ShoppingCart extends Component
{

    public $cartItems;
    public $cartTotal;

    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function render()
    {
        return view('livewire.navbar.shopping-cart');
    }

    public function updateCart()
    {
        $this->cartItems = Cart::where('user_id', auth()->id())->with(['product', 'variant'])->get();
        $this->cartTotal = $this->cartItems->sum(function ($item) {
            return $item->quantity * ($item->variant ? $item->variant->price : $item->product->price);
        });
    }
}
