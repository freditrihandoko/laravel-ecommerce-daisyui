<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Discount;
use Livewire\WithPagination;

class DiscountManagement extends Component
{
    use WithPagination;

    public $code;
    public $description;
    public $discount_type;
    public $discount_value;
    public $start_date;
    public $end_date;
    public $minimum_order_value;
    public $maximum_discount_amount;
    public $usage_limit;

    public $isOpen = false;
    public $isConfirmingDelete = false;
    public $editingId;
    public $deleteId;

    protected $rules = [
        'code' => 'required|unique:discounts,code',
        'description' => 'required',
        'discount_type' => 'required|in:percentage,fixed',
        'discount_value' => 'required|numeric|min:0',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'minimum_order_value' => 'nullable|numeric|min:0',
        'maximum_discount_amount' => 'nullable|numeric|min:0',
        'usage_limit' => 'nullable|integer|min:0',
    ];

    public function render()
    {
        $discounts = Discount::latest()->paginate(10);
        return view('livewire.discount-management', compact('discounts'));
    }

    public function openModal($isEditing = false)
    {
        if (!$isEditing) {
            $this->resetInputFields(); // Reset form hanya jika menambahkan alamat baru
        }
        // $this->resetInputFields();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->reset(['code', 'description', 'discount_type', 'discount_value', 'start_date', 'end_date', 'minimum_order_value', 'maximum_discount_amount', 'usage_limit', 'editingId']);
    }

    public function store()
    {
        $this->validate();

        Discount::create([
            'code' => $this->code,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'minimum_order_value' => $this->minimum_order_value,
            'maximum_discount_amount' => $this->maximum_discount_amount,
            'usage_limit' => $this->usage_limit,
        ]);

        session()->flash('message', 'Diskon berhasil ditambahkan.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        $this->editingId = $id;
        $this->code = $discount->code;
        $this->description = $discount->description;
        $this->discount_type = $discount->discount_type;
        $this->discount_value = $discount->discount_value;
        $this->start_date = $discount->start_date->format('Y-m-d');
        $this->end_date = $discount->end_date->format('Y-m-d');
        $this->minimum_order_value = $discount->minimum_order_value;
        $this->maximum_discount_amount = $discount->maximum_discount_amount;
        $this->usage_limit = $discount->usage_limit;

        // $this->openModal();
        $this->openModal(true); // Buka modal dengan isEditing = true
    }

    public function update()
    {
        $this->validate([
            'code' => 'required|unique:discounts,code,' . $this->editingId,
            'description' => 'required',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
        ]);

        if ($this->editingId) {
            $discount = Discount::find($this->editingId);
            $discount->update([
                'code' => $this->code,
                'description' => $this->description,
                'discount_type' => $this->discount_type,
                'discount_value' => $this->discount_value,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'minimum_order_value' => $this->minimum_order_value,
                'maximum_discount_amount' => $this->maximum_discount_amount,
                'usage_limit' => $this->usage_limit,
            ]);
            session()->flash('message', 'Diskon berhasil diperbarui.');
            $this->closeModal();
            $this->editingId = null;
        }
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->isConfirmingDelete = true;
    }

    public function cancelDelete()
    {
        $this->isConfirmingDelete = false;
        $this->deleteId = null;
    }

    public function delete()
    {
        Discount::find($this->deleteId)->delete();
        session()->flash('message', 'Diskon berhasil dihapus.');
        $this->isConfirmingDelete = false;
        $this->deleteId = null;
    }
}
