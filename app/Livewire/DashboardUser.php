<?php

namespace App\Livewire;

use Livewire\Component;

class DashboardUser extends Component
{
    public $activeTab = 'orders';

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.dashboard-user')->layout('layouts.customer');
    }
}
