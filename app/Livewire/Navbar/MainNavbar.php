<?php

namespace App\Livewire\Navbar;

use App\Models\GeneralSetting;
use Livewire\Component;

class MainNavbar extends Component
{
    public $storeName;
    public $logo;

    public function mount()
    {
        $this->logo =  GeneralSetting::first()->logo;
        $this->storeName = GeneralSetting::first()->website_name ?? 'YourStore'; // Default value
    }

    public function render()
    {
        return view('livewire.navbar.main-navbar');
    }
}
