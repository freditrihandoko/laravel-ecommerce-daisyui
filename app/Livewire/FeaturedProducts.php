<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class FeaturedProducts extends Component
{
    public function render()
    {
        // Fetch 8 random active products
        $products = Product::where('is_active', true)
            ->inRandomOrder()
            ->take(8)
            ->get();

        // Get the total count of active products
        $totalProducts = Product::where('is_active', true)->count();

        return view('livewire.featured-products', [
            'products' => $products,
            'totalProducts' => $totalProducts,
        ]);
    }
}
