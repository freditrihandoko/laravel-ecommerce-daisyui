<?php

namespace App\Livewire\Navbar;

use App\Models\Product;
use Livewire\Component;

class SearchButton extends Component
{
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
        return view('livewire.navbar.search-button', [
            'products' => $products,
        ]);
    }
}
