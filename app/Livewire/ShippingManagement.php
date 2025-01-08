<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ShippingInformation;

class ShippingManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $customerSearch = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedOrder;
    public $trackingNumber;
    public $dateFrom;
    public $dateTo;

    protected $queryString = ['search', 'customerSearch', 'sortField', 'sortDirection', 'dateFrom', 'dateTo'];

    protected $rules = [
        'trackingNumber' => 'nullable|string|max:255',
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function selectOrder($orderId)
    {
        $this->selectedOrder = Order::with(['items.product', 'items.variant', 'shippingMethod', 'user'])->findOrFail($orderId);
    }

    public function setPackingStatus()
    {
        if ($this->selectedOrder && $this->selectedOrder->status_id == 2) {
            $this->selectedOrder->update(['status_id' => 4]); // Set status to 'Packing'
            $this->reset('selectedOrder');
        }
    }

    public function setShippedStatus()
    {
        $this->validate();
        if ($this->selectedOrder && $this->selectedOrder->status_id == 4) {
            $shippingCost = $this->selectedOrder->shippingMethod->cost * ceil($this->selectedOrder->weight);
            if ($this->trackingNumber) {
                ShippingInformation::updateOrCreate(
                    ['order_id' => $this->selectedOrder->id],
                    ['tracking_number' => $this->trackingNumber, 'shipping_method' => $this->selectedOrder->shippingMethod->name, 'shipping_cost' => $shippingCost]
                );
            }
            $this->selectedOrder->update([
                'status_id' => 5
            ]); // Set status to 'Shipped'
            $this->reset(['selectedOrder', 'trackingNumber']);
        }
    }

    public function printOrder($orderId)
    {
        return redirect()->route('order.print', $orderId);
    }

    public function render()
    {
        $orders = Order::with(['items.product', 'items.variant', 'shippingMethod', 'user', 'status'])
            ->approvedOrders()
            ->where(function ($query) {
                $query->where('id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('items.product', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('address->address_line_1', 'like', '%' . $this->search . '%')
                    ->orWhere('address->city', 'like', '%' . $this->search . '%');
            })
            ->when($this->customerSearch, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->customerSearch . '%');
                });
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.shipping-management', compact('orders'));
    }

    public function closeModal()
    {
        $this->selectedOrder = null;
        // $this->resetForm();
    }
}
