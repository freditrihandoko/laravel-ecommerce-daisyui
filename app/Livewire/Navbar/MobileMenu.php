<?php

namespace App\Livewire\Navbar;

use Livewire\Component;
use App\Models\Category;

class MobileMenu extends Component
{
    public $categories;
    public function render()
    {
        $this->categories = Category::where('is_active', true)->get();
        return view('livewire.navbar.mobile-menu', [
            'category' => $this->categories,
        ]);
    }
}
