<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginModal extends Component
{
    public $showLoginModal;
    // public $showRegisterModal;
    public $email = '';
    public $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function render()
    {
        return view('livewire.auth.login-modal');
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->flash('message', 'Logged in successfully');
            // $this->redirect('/dashboard-user');
            return redirect()->intended(url()->previous());
        } else {
            session()->flash('error', 'Invalid credentials');
        }
    }

    public function closeLoginModal()
    {
        $this->showLoginModal = false;
        $this->dispatch('closeLoginModal'); // Tell the parent to update its state
    }

    public function openRegisterModal()
    {
        $this->showLoginModal = false;
        // $this->showRegisterModal = true;
        $this->dispatch('openRegisterModalFromLogin');
    }
}
