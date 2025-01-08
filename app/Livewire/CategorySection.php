<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class CategorySection extends Component
{
    public function render()
    {
        $categories = Category::where('is_active', true)->get();
        return view('livewire.category-section', [
            'categories' => $categories,
        ]);
    }
}
