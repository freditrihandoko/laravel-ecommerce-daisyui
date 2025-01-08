<?php

namespace App\Livewire\Navbar;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserMenu extends Component
{
    public $showLoginModal = false;
    public $showRegisterModal = false;

    protected $listeners = ['closeLoginModal', 'triggerLoginModal', 'openRegisterModalFromLogin'];


    public function render()
    {
        return view('livewire.navbar.user-menu');
    }

    public function logout()
    {
        Auth::logout();
        session()->flash('message', 'You have been logged out.');
        return redirect('/');
    }

    public function openLoginModal()
    {
        $this->showLoginModal = true;
    }

    public function closeLoginModal()
    {
        $this->showLoginModal = false;
    }

    public function openRegisterModal()
    {
        $this->showRegisterModal = true;
    }

    public function closeRegisterModal()
    {
        $this->showRegisterModal = true;
    }

    public function triggerLoginModal()
    {
        $this->showLoginModal = true;
    }

    public function openRegisterModalFromLogin()
    {
        $this->showRegisterModal = true;
    }
}
