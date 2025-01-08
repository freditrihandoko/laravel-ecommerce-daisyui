<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use Livewire\Component;

class ProductDetail extends Component
{
    public $showLoginModal;
    public $product;
    public $relatedProducts;
    public $quantity = '1';
    public $selectedVariantId = null;
    public $activeSlide = 0;

    public function mount($productSlug)
    {
        $this->product = Product::with(['category', 'variants', 'media'])->where('slug', $productSlug)->firstOrFail();
        $this->relatedProducts = Product::where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->take(4)
            ->get();
    }

    public function render()
    {
        return view('livewire.product-detail')->layout('layouts.customer');
    }

    public function selectVariant($variantId)
    {
        $this->selectedVariantId = $variantId;
    }

    public function incrementQuantity()
    {
        $this->quantity++;
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function isOutOfStock()
    {
        if ($this->product->product_type === 'single') {
            return $this->product->currentStock() <= 0;
        } elseif ($this->product->product_type === 'variant') {
            if (!$this->selectedVariantId) {
                return true; // Disable button if no variant is selected
            }
            $selectedVariant = $this->product->variants->find($this->selectedVariantId);
            return $selectedVariant && $selectedVariant->currentStock() <= 0;
        }
        return true; // Default to out of stock if product type is unknown
    }

    public function addToCart()
    {
        if (!auth()->check()) {
            session()->flash('error', 'Please log in to add items to your cart.');
            $this->dispatch('triggerLoginModal');
            return;
        }

        if ($this->product->product_type === 'variant' && !$this->selectedVariantId) {
            session()->flash('error', 'Please select a variant before adding to cart.');
            return;
        }

        $cartItem = Cart::firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $this->product->id,
            'variant_id' => $this->selectedVariantId,
        ]);

        $cartItem->quantity += $this->quantity;
        $cartItem->save();

        $this->dispatch('cartUpdated');
        session()->flash('message', 'Product added to cart successfully.');
    }
}
