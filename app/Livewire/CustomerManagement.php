<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class CustomerManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $name = '', $email = '', $phone = '';
    public $editingCustomerId;
    public $customerIdBeingDeleted;

    protected $listeners = ['openModal', 'closeModal'];

    protected function rules()
    {
        return [
            'name' => ['required', 'min:3'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->editingCustomerId)
            ],
            'phone' => 'nullable|min:10',
        ];
    }

    public function render()
    {
        $customers = User::where('role', 'customer')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->withCount('orders')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.customer-management', [
            'customers' => $customers
        ]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->dispatch('open-modal', name: 'customer-modal');
    }

    public function closeModal()
    {
        $this->resetInputFields();
        $this->dispatch('close-modal');
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->editingCustomerId = null;
    }

    public function store()
    {
        $validatedData = $this->validate();

        $customer = new User();
        $customer->name = $this->name;
        $customer->email = $this->email;
        $customer->phone = $this->phone;
        $customer->role = 'customer';
        $customer->password = Hash::make('password123'); // Default password
        $customer->save();

        session()->flash('message', 'Customer created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $customer = User::findOrFail($id);
        $this->editingCustomerId = $id;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;

        $this->openModal();
    }

    public function update()
    {
        $validatedData = $this->validate();

        $customer = User::find($this->editingCustomerId);

        if (!$customer) {
            session()->flash('error', 'Customer not found.');
            $this->closeModal();
            return;
        }

        $customer->name = $this->name;
        $customer->email = $this->email;
        $customer->phone = $this->phone;
        $customer->save();

        session()->flash('message', 'Customer updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function confirmCustomerDeletion($id)
    {
        $this->customerIdBeingDeleted = $id;
        $this->dispatch('open-modal', name: 'confirm-customer-deletion');
    }

    public function deleteCustomer()
    {
        User::find($this->customerIdBeingDeleted)->delete();
        session()->flash('message', 'Customer deleted successfully.');
        $this->dispatch('close-modal');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
