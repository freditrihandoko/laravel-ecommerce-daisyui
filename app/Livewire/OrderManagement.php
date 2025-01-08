<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderStatus;
use App\Models\ProductStock;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderManagement extends Component
{
    use WithPagination;

    public $statuses;
    // public $selectedOrder;
    public $search = '';
    public $customerSearch = '';
    public $statusFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $dateFrom;
    public $dateTo;

    // Payment Information form fields
    public $transactionId;
    public $paymentStatus;
    public $amountPaid = '';
    public $formattedAmountPaid = 'Rp 0';
    public $paymentDate;

    public $selectedOrder = null;
    public $orderDetails = [];

    // Modal control
    public $showPaymentModal = false;
    public $showOrderModal = false;
    public $showCancelOrderModal = false;



    protected $queryString = ['search', 'customerSearch', 'statusFilter', 'sortField', 'sortDirection', 'dateFrom', 'dateTo'];

    public function mount()
    {
        $this->statuses = OrderStatus::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // public function selectOrder($orderId)
    // {
    //     // $this->selectedOrder = Order::with(['user', 'items.product', 'items.variant', 'shippingMethod', 'paymentMethod', 'paymentInformation'])->find($orderId);
    //     // $this->showOrderModal = true;
    //     // $this->loadPaymentInfo();
    //     $this->selectedOrder = Order::with(['user', 'items.product', 'items.variant', 'shippingMethod', 'paymentMethod', 'paymentInformation'])->find($orderId);
    //     $this->loadPaymentInfo();
    //     $this->dispatch('open-modal', name: 'order-modal');
    // }

    public function updatedFormattedAmountPaid($value)
    {
        // Remove any non-numeric characters
        $numericValue = preg_replace('/\D/', '', $value);

        // Convert to numeric and update the price property
        $this->amountPaid = $numericValue ? intval($numericValue) : 0;

        // Update formattedPrice
        $this->formattedAmountPaid = 'Rp. ' . number_format($this->amountPaid, 0, ',', '.');
    }


    public function selectOrder($orderId)
    {
        $this->selectedOrder = Order::with(['user', 'items.product', 'items.variant', 'shippingMethod', 'paymentMethod', 'paymentInformation', 'items'])->find($orderId);
        $this->loadPaymentInfo();
        $this->orderDetails = $this->getOrderDetails();
        $this->dispatch('open-modal', name: 'order-modal');
    }


    // private function getOrderDetails()
    // {
    //     if (!$this->selectedOrder) return [];

    //     return [
    //         'id' => $this->selectedOrder->id,
    //         'total_amount' => $this->formatRupiah($this->selectedOrder->total_amount),
    //         'status' => $this->selectedOrder->status->name,
    //         'status_id' => $this->selectedOrder->status_id,
    //         'created_at' => $this->selectedOrder->created_at->format('Y-m-d H:i:s'),
    //         'items' => $this->selectedOrder->items->map(function ($item) {
    //             return [
    //                 'quantity' => $item->quantity,
    //                 'product_name' => $item->product->name,
    //                 'variant_name' => $item->variant ? $item->variant->name : null,
    //                 'price' => $this->formatRupiah($item->price),
    //             ];
    //         }),
    //         'payment_proof' => $this->selectedOrder->paymentInformation->payment_proof ?? null,
    //     ];
    // }

    // Livewire component
    public function getOrderDetails()
    {
        if (!$this->selectedOrder) return [];
        return [
            'id' => $this->selectedOrder->id,
            'total_amount' => $this->formatRupiah($this->selectedOrder->total_amount),
            'status' => $this->selectedOrder->status->name,
            'status_id' => $this->selectedOrder->status_id,
            'created_at' => $this->selectedOrder->created_at->format('Y-m-d H:i:s'),
            'items' => $this->selectedOrder->items->map(function ($item) {
                return [
                    'quantity' => $item->quantity,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant ? $item->variant->name : null,
                    'price' => $this->formatRupiah($item->price),
                    'is_variant' => $item->variant ? true : false,
                ];
            }),
            'payment_proof' => $this->selectedOrder->paymentInformation->payment_proof ?? null,
        ];
    }


    private function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public function getOrderDetailsProperty()
    {
        return $this->orderDetails;
    }

    public function loadPaymentInfo()
    {
        if ($this->selectedOrder && $this->selectedOrder->paymentInformation) {
            $this->transactionId = $this->selectedOrder->paymentInformation->transaction_id;
            $this->paymentStatus = $this->selectedOrder->paymentInformation->payment_status;
            $this->amountPaid = $this->selectedOrder->paymentInformation->amount_paid;
            $this->paymentDate = $this->selectedOrder->paymentInformation->payment_date;

            $this->formattedAmountPaid = 'Rp. ' . number_format($this->amountPaid, 0, ',', '.');
        }
    }

    public function approveOrder()
    {
        if ($this->selectedOrder) {
            $this->selectedOrder->update(['status_id' => 2]); // Set status to 'Approved'
            session()->flash('message', 'Order has been approved successfully.');
            $this->closeModals();
        }
    }

    public function updateOrderStatus()
    {
        if ($this->selectedOrder) {
            $this->selectedOrder->update(['status_id' => 1]); // Set status to 'Pending'
            session()->flash('message', 'Order status has been updated to pending.');
            $this->closeModals();
        }
    }

    public function cancelOrder()
    {
        if ($this->selectedOrder) {
            DB::transaction(function () {
                $this->selectedOrder->update(['status_id' => 3]); // Set status to 'Cancelled'

                foreach ($this->selectedOrder->items as $item) {
                    $product = $item->product;
                    $variant = $item->variant;

                    if ($variant) {
                        ProductStock::create([
                            'product_id' => $product->id,
                            'variant_id' => $variant->id,
                            'quantity' => $item->quantity,
                            'action_type' => 'addition',
                            'order_id' => $this->selectedOrder->id,
                            'note' => 'Stock returned from cancelled order',
                        ]);
                    } else {
                        ProductStock::create([
                            'product_id' => $product->id,
                            'variant_id' => null,
                            'quantity' => $item->quantity,
                            'action_type' => 'addition',
                            'order_id' => $this->selectedOrder->id,
                            'note' => 'Stock returned from cancelled order',
                        ]);
                    }
                }

                session()->flash('message', 'Order has been cancelled successfully and stock has been returned.');
            });

            $this->closeModals();
        }
    }

    public function updatePaymentInformation()
    {
        if ($this->selectedOrder && $this->selectedOrder->paymentInformation) {
            $this->selectedOrder->paymentInformation->update([
                'transaction_id' => $this->transactionId,
                'payment_status' => $this->paymentStatus,
                'amount_paid' => $this->amountPaid,
                'payment_date' => $this->paymentDate,
            ]);
            session()->flash('message', 'Payment information has been updated successfully.');
            $this->closeModals();
        }
    }

    public function resetForm()
    {
        $this->transactionId = null;
        $this->paymentStatus = null;
        $this->amountPaid = null;
        $this->paymentDate = null;
    }

    public function sortBy($field)
    {
        $this->sortField = $field;
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function closeModals()
    {
        // $this->showOrderModal = false;
        // $this->showPaymentModal = false;
        // $this->selectedOrder = null;
        // $this->resetForm();
        $this->dispatch('close-modal', name: 'order-modal');
        // $this->dispatch('close-modal', name: 'payment-modal');
        $this->closePaymentModal();
        $this->closeCancelOrderModal();
        $this->selectedOrder = null;
        $this->resetForm();
    }


    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->dispatch('close-modal', name: 'payment-modal');
    }

    public function openPaymentModal()
    {
        $this->showPaymentModal = true;
        // $this->showOrderModal = false;
        $this->dispatch('open-modal', name: 'payment-modal');
    }

    public function closeCancelOrderModal()
    {
        $this->showCancelOrderModal = false;
        $this->dispatch('close-modal', name: 'cancel-order-modal');
    }

    public function openCancelOrderModal()
    {
        $this->showCancelOrderModal = true;
        $this->dispatch('open-modal', name: 'cancel-order-modal');
    }


    public function render()
    {
        $orders = Order::with(['user', 'status', 'shippingMethod', 'paymentMethod', 'paymentInformation'])
            ->when($this->statusFilter, function ($query) {
                $query->where('status_id', $this->statusFilter);
            })
            ->when($this->search, function ($query) {
                $query->where('id', 'like', '%' . $this->search . '%');
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

        return view('livewire.order-management', ['orders' => $orders, 'statuses' => $this->statuses]);
    }
}
