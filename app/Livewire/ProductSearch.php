<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ProductSearch extends Component
{
    //sudah tidak dipakai, gunakan search button
    public $searchTerm = '';
    public $isOpen = false;

    public function search()
    {
        $this->isOpen = true;
    }

    public function render()
    {
        $products = [];
        if (strlen($this->searchTerm) > 2) {
            // sleep(3);
            $products = Product::where('name', 'like', '%' . $this->searchTerm . '%')
                ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                ->take(10)
                ->get();
        }
        return view('livewire.product-search', [
            'products' => $products,
        ]);
    }
}
