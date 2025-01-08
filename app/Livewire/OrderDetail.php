<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use App\Models\OrderItem;
use Livewire\WithFileUploads;
use App\Models\PaymentInformation;
use App\Models\ShippingInformation;

class OrderDetail extends Component
{
    use WithFileUploads;

    public $order;
    public $orderItems;
    public $shippingInformation;
    public $paymentInformation;
    public $paymentProof;

    public function mount($orderId)
    {
        $this->order = Order::with(['user', 'shippingMethod', 'paymentMethod'])->findOrFail($orderId);
        $this->orderItems = OrderItem::where('order_id', $orderId)->with(['product', 'variant'])->get();
        $this->shippingInformation = ShippingInformation::where('order_id', $orderId)->first();
        $this->paymentInformation = PaymentInformation::where('order_id', $orderId)->first();
    }

    public function render()
    {
        return view('livewire.order-detail')->layout('layouts.customer');
    }

    public function uploadPaymentProof()
    {
        $this->validate([
            'paymentProof' => 'image|max:2048', // maksimal 2MB
        ]);

        if ($this->paymentProof) {
            $paymentProofPath = $this->paymentProof->store('payment_proofs', 'public');
            $this->paymentInformation->update([
                'payment_proof' => $paymentProofPath,
                'payment_date' => now()
            ]);

            session()->flash('message', 'Bukti pembayaran berhasil diunggah.');
        }
    }
}
