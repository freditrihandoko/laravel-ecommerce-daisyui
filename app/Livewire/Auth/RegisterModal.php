<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterModal extends Component
{
    public $showRegisterModal;
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users',
        'password' => 'required|min:8|confirmed',
    ];

    public function render()
    {
        return view('livewire.auth.register-modal');
    }

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        Auth::login($user);

        session()->flash('message', 'Registered successfully');
        $this->redirect('/dashboard-user');
    }

    public function closeRegisterModal()
    {
        $this->showRegisterModal = false;
        $this->dispatch('closeRegisterModal'); // Tell the parent to update its state
    }
}
