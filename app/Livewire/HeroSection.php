<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HeroSlide;

class HeroSection extends Component
{
    public function render()
    {
        $slides = HeroSlide::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('livewire.hero-section', ['slides' => $slides]);
    }
}
