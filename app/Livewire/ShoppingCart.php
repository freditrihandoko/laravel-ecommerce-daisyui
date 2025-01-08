<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Order;
use Livewire\Component;
use App\Models\OrderItem;

class ShoppingCart extends Component
{

    public $cartItems;
    public $cartTotal;
    public $unavailableItems = [];
    protected $listeners = ['cartUpdated' => 'updateCart'];

    public function mount()
    {
        $this->updateCart();
    }

    public function render()
    {
        return view('livewire.shopping-cart')->layout('layouts.customer');
    }

    public function updateCart()
    {
        $this->cartItems = Cart::where('user_id', auth()->id())->with(['product', 'variant'])->get();
        $this->cartTotal = 0;
        $this->unavailableItems = [];

        foreach ($this->cartItems as $item) {
            $price = $item->variant ? $item->variant->price : $item->product->price;
            $stock = $item->variant ? $item->variant->currentStock() : $item->product->currentStock();

            if ($stock <= 0 || $item->quantity <= 0) {
                $this->unavailableItems[] = $item->id;
                $item->quantity = 0;
                $item->save();
            } elseif ($stock < $item->quantity) {
                $this->unavailableItems[] = $item->id;
                $item->quantity = $stock;
                $item->save();
            }

            $this->cartTotal += $item->quantity * $price;
        }
    }


    public function incrementQuantity($cartItemId)
    {
        $cartItem = Cart::find($cartItemId);
        $stock = $cartItem->variant ? $cartItem->variant->currentStock() : $cartItem->product->currentStock();
        if ($cartItem->quantity < $stock) {
            $cartItem->quantity++;
            $cartItem->save();
        }
        $this->updateCart();
    }


    public function decrementQuantity($cartItemId)
    {
        $cartItem = Cart::find($cartItemId);
        if ($cartItem->quantity > 1) {
            $cartItem->quantity--;
            $cartItem->save();
        } else {
            $cartItem->delete();
        }
        $this->updateCart();
    }

    public function removeItem($cartItemId)
    {
        Cart::destroy($cartItemId);
        $this->updateCart();
    }

    public function proceedToCheckout()
    {
        $this->updateCart(); // Ensure we have the latest cart status

        if (empty($this->unavailableItems) && $this->cartTotal > 0) {
            return redirect()->route('user-order.index');
        } else {
            if ($this->cartTotal <= 0) {
                $this->addError('checkout', 'Your cart is empty. Please add items before proceeding to checkout.');
            } else {
                $this->addError('checkout', 'Please remove out-of-stock items before proceeding to checkout.');
            }
        }
    }
}
