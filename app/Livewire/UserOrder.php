<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Address;
use App\Models\Product;
use Livewire\Component;
use App\Models\Discount;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use App\Models\ProductStock;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Models\ShippingMethod;
use App\Models\PaymentInformation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserOrder extends Component
{
    public $cartItems;
    public $addresses = [];
    public $selectedAddress;
    public $shippingMethods;
    public $selectedShippingMethod;
    public $paymentMethods;
    public $selectedPaymentMethod;
    public $shippingCost = 0;
    public $cartTotal;
    public $totalAmount;
    public $subtotal;  // Subtotal sebelum diskon dan biaya pengiriman
    public $discountCode = '';
    public $appliedDiscountCode = null;
    public $discountAmount = 0;

    public $showAddressModal = false;
    public $showConfirmOrderModal = false;

    public $name, $address_line_1, $address_line_2, $city, $state, $country, $zip_code, $phone;

    protected $listeners = ['addressAdded' => 'refreshAddresses'];

    // Method to open the modal
    public function openAddressModal()
    {
        $this->showAddressModal = true;
    }

    // Method to close the modal
    public function closeAddressModal()
    {
        $this->showAddressModal = false;
    }

    public function openConfirmOrderModal()
    {
        $this->showConfirmOrderModal = true;
    }

    // Method to close the confirmation modal
    public function closeConfirmOrderModal()
    {
        $this->showConfirmOrderModal = false;
    }


    protected $rules = [
        // 'selectedAddress' => 'required|exists:addresses,id',
        'selectedShippingMethod' => 'required|exists:shipping_methods,id',
        'selectedPaymentMethod' => 'required|exists:payment_methods,id',
        'addresses' => 'required|array',
    ];

    public function mount()
    {
        $this->loadCartItems();
        $this->addresses = Address::where('user_id', Auth::id())->get();
        $this->shippingMethods = ShippingMethod::where('is_active', true)->get();
        $this->paymentMethods = PaymentMethod::where('is_active', true)->get();
        $this->calculateTotals();
        $this->refreshAddresses();
    }

    public function refreshAddresses()
    {
        $this->addresses = auth()->user()->addresses;
    }

    public function render()
    {
        return view('livewire.user-order')->layout('layouts.customer');
    }

    public function loadCartItems()
    {
        $this->cartItems = Cart::where('user_id', Auth::id())->with(['product', 'variant'])->get();
        $this->cartTotal = $this->cartItems->sum(function ($item) {
            return $item->quantity * ($item->variant ? $item->variant->price : $item->product->price);
        });
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->cartItems->sum(function ($item) {
            return $item->quantity * ($item->variant ? $item->variant->price : $item->product->price);
        });

        $totalWeight = $this->cartItems->sum(function ($item) {
            return $item->quantity * ($item->variant ? $item->variant->weight : $item->product->weight);
        });

        $totalWeight = ceil($totalWeight);

        if ($this->selectedShippingMethod) {
            $shippingMethod = ShippingMethod::find($this->selectedShippingMethod);
            $this->shippingCost = $shippingMethod->cost * $totalWeight;
        } else {
            $this->shippingCost = 0;
        }

        // Hitung total amount setelah diskon dan biaya pengiriman
        $this->totalAmount = $this->subtotal - $this->discountAmount + $this->shippingCost;
    }

    public function updatedSelectedShippingMethod()
    {
        $this->calculateTotals();
    }

    // public function applyDiscount()
    // {
    //     // Cari kode diskon yang valid
    //     $discount = Discount::where('code', $this->discountCode)->first();

    //     if ($discount && $discount->isValid($this->subtotal)) {
    //         $this->discountAmount = $discount->calculateDiscount($this->subtotal);
    //         $this->appliedDiscountCode = $this->discountCode;
    //         $this->calculateTotals(); // Hitung ulang total amount
    //         session()->flash('message', 'Discount applied successfully!');
    //     } else {
    //         $this->discountAmount = 0;
    //         session()->flash('error', 'Invalid or expired discount code.');
    //     }
    // }

    public function applyDiscount()
    {
        $discount = Discount::where('code', Str::upper($this->discountCode))->first();

        if (!$discount) {
            session()->flash('error', 'Discount code does not exist.');
            return;
        }

        $now = now();

        if ($discount->start_date > $now) {
            session()->flash('error', 'Discount code is not yet active.');
            return;
        }

        if ($discount->end_date < $now) {
            session()->flash('error', 'Discount code has expired.');
            return;
        }

        if ($this->subtotal < $discount->minimum_order_value) {
            session()->flash('error', 'Order total does not meet the minimum required for this discount.');
            return;
        }

        if ($discount->usage_limit !== null && $discount->usage_count >= $discount->usage_limit) {
            session()->flash('error', 'Discount code usage limit has been reached.');
            return;
        }

        // Check if the discount code has already been used by this user
        $existingOrder = Order::where('user_id', auth()->id())
            ->where('discount_id', $discount->id) // Match discount_id, not the code, since it's stored as a relation
            ->exists();

        if ($existingOrder) {
            session()->flash('error', 'You have already used this discount code.');
            return;
        }

        // Apply the discount
        $this->discountAmount = $discount->calculateDiscount($this->subtotal);
        $this->appliedDiscountCode = $this->discountCode;

        // Increment the usage count
        $discount->increment('usage_count');
        $this->calculateTotals();

        session()->flash('message', 'Discount applied successfully!');
    }

    public function removeDiscount()
    {
        // $this->discountAmount = 0;
        // $this->reset('appliedDiscountCode', 'discountCode'); // Reset variabel discountCode dan appliedDiscountCode

        // $this->calculateTotals(); // Hitung ulang total tanpa diskon
        // session()->flash('message', 'Discount removed successfully!');

        $this->discountAmount = 0;
        $this->appliedDiscountCode = null;
        $this->discountCode = null;

        $this->calculateTotals(); // Hitung ulang total tanpa diskon
        session()->flash('message', 'Discount removed successfully!');
    }


    // public function confirmOrder()
    // {
    //     $this->validate();

    //     $selectedAddress = $this->addresses->firstWhere('id', $this->selectedAddress);

    //     if (!$selectedAddress) {
    //         session()->flash('error', 'Invalid address selected.');
    //         return;
    //     }

    //     $addressData = [
    //         'label' => $selectedAddress->label,
    //         'name' => $selectedAddress->name,
    //         'address_line_1' => $selectedAddress->address_line_1,
    //         'address_line_2' => $selectedAddress->address_line_2,
    //         'kelurahan' => $selectedAddress->kelurahan,
    //         'kecamatan' => $selectedAddress->kecamatan,
    //         'kota_kab' => $selectedAddress->kota_kab,
    //         'provinsi' => $selectedAddress->provinsi,
    //         'country' => $selectedAddress->country,
    //         'zip_code' => $selectedAddress->zip_code,
    //         'phone' => $selectedAddress->phone,
    //     ];

    //     // Find the applied discount if there is one
    //     $discount = Discount::where('code', $this->appliedDiscountCode)->first();

    //     $order = Order::create([
    //         'user_id' => Auth::id(),
    //         'address' => $addressData,
    //         'weight' => $this->cartItems->sum(function ($item) {
    //             return $item->quantity * ($item->variant ? $item->variant->weight : $item->product->weight);
    //         }),
    //         'subtotal' => $this->subtotal,
    //         'discount_amount' => $this->discountAmount,
    //         'shipping_cost' => $this->shippingCost,
    //         'total_amount' => $this->totalAmount,
    //         'discount_id' => $discount ? $discount->id : null, // Store discount_id if discount applied
    //         'status_id' => 1,
    //         'shipping_method_id' => $this->selectedShippingMethod,
    //         'payment_method_id' => $this->selectedPaymentMethod,
    //     ]);

    //     // Order Items dan Payment Information tetap sama...
    //     foreach ($this->cartItems as $item) {
    //         OrderItem::create([
    //             'order_id' => $order->id,
    //             'product_id' => $item->product_id,
    //             'variant_id' => $item->variant_id,
    //             'quantity' => $item->quantity,
    //             'price' => $item->variant ? $item->variant->price : $item->product->price,
    //         ]);
    //     }

    //     PaymentInformation::create([
    //         'order_id' => $order->id,
    //         'payment_status' => 'Pending',
    //         'amount_paid' => 0,
    //         'payment_date' => now(),
    //     ]);

    //     // Clear cart after order is successfully processed 

    //     Cart::where('user_id', Auth::id())->delete();

    //     $this->closeConfirmOrderModal();

    //     return redirect()->route('order-detail', ['orderId' => $order->id]);
    // }
    public function confirmOrder()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $selectedAddress = $this->addresses->firstWhere('id', $this->selectedAddress);
            if (!$selectedAddress) {
                throw new \Exception('Invalid address selected.');
            }

            $addressData = [
                'label' => $selectedAddress->label,
                'name' => $selectedAddress->name,
                'address_line_1' => $selectedAddress->address_line_1,
                'address_line_2' => $selectedAddress->address_line_2,
                'kelurahan' => $selectedAddress->kelurahan,
                'kecamatan' => $selectedAddress->kecamatan,
                'kota_kab' => $selectedAddress->kota_kab,
                'provinsi' => $selectedAddress->provinsi,
                'country' => $selectedAddress->country,
                'zip_code' => $selectedAddress->zip_code,
                'phone' => $selectedAddress->phone,
            ];

            $discount = Discount::where('code', $this->appliedDiscountCode)->first();

            $order = Order::create([
                'user_id' => Auth::id(),
                'address' => $addressData,
                'weight' => $this->cartItems->sum(function ($item) {
                    return $item->quantity * ($item->variant ? $item->variant->weight : $item->product->weight);
                }),
                'subtotal' => $this->subtotal,
                'discount_amount' => $this->discountAmount,
                'shipping_cost' => $this->shippingCost,
                'total_amount' => $this->totalAmount,
                'discount_id' => $discount ? $discount->id : null,
                'status_id' => 1,
                'shipping_method_id' => $this->selectedShippingMethod,
                'payment_method_id' => $this->selectedPaymentMethod,
            ]);

            foreach ($this->cartItems as $item) {
                $product = Product::lockForUpdate()->findOrFail($item->product_id);

                if ($product->product_type === 'single') {
                    $currentStock = $product->currentStock();
                    if ($currentStock < $item->quantity) {
                        throw new \Exception("Insufficient stock for product {$product->name}");
                    }

                    $newStock = $currentStock - $item->quantity;

                    ProductStock::create([
                        'product_id' => $product->id,
                        'quantity' => -$item->quantity,
                        'action_type' => 'reduction',
                        'order_id' => $order->id,
                        'note' => 'Stock reduction from order',
                    ]);
                } elseif ($product->product_type === 'variant') {
                    $variant = $product->variants()->lockForUpdate()->findOrFail($item->variant_id);
                    $currentStock = $variant->currentStock();
                    if ($currentStock < $item->quantity) {
                        throw new \Exception("Insufficient stock for variant {$variant->id} of product {$product->id}");
                    }

                    $newStock = $currentStock - $item->quantity;

                    ProductStock::create([
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'quantity' => -$item->quantity,
                        'action_type' => 'reduction',
                        'order_id' => $order->id,
                        'note' => 'Stock reduction from order',
                    ]);
                } else {
                    throw new \Exception("Unknown product type for product {$product->id}");
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->variant ? $item->variant->price : $product->price,
                ]);
            }

            PaymentInformation::create([
                'order_id' => $order->id,
                'payment_status' => 'Pending',
                'amount_paid' => 0,
                'payment_date' => now(),
            ]);

            // Clear cart after order is successfully processed
            Cart::where('user_id', Auth::id())->delete();

            DB::commit();

            $this->closeConfirmOrderModal();
            return redirect()->route('order-detail', ['orderId' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'An error occurred while processing your order: ' . $e->getMessage());
            return;
        }
    }
}
