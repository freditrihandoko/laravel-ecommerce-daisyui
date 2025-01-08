<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use Livewire\WithFileUploads;
use App\Models\GeneralSetting;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\Storage;

class PaymentShippingOrderSettings extends Component
{
    use WithFileUploads;

    public $paymentMethods;
    public $shippingMethods;
    public $orderStatuses;

    public $paymentMethod_id;
    public $paymentMethod_name;
    public $paymentMethod_instructions;
    public $paymentMethod_is_active;

    public $shippingMethod_id;
    public $shippingMethod_name;
    public $shippingMethod_description;
    public $shippingMethod_cost;
    public $shippingMethod_is_active;

    public $orderStatus_id;
    public $orderStatus_name;

    public $showPaymentModal = false;
    public $showShippingModal = false;
    public $showOrderStatusModal = false;

    // General settings properties
    public $website_name;
    public $slogan;
    public $description;
    public $contact_email;
    public $contact_phone;
    public $address;
    public $logo;
    public $favicon;
    public $temp_logo;
    public $temp_favicon;
    public $old_logo;
    public $old_favicon;

    public $activeTab = 'general';


    protected $rules = [
        'paymentMethod_name' => 'required|string|max:255',
        'paymentMethod_instructions' => 'nullable|string',
        'paymentMethod_is_active' => 'boolean',

        'shippingMethod_name' => 'required|string|max:255',
        'shippingMethod_description' => 'nullable|string',
        'shippingMethod_cost' => 'required|numeric|min:0',
        'shippingMethod_is_active' => 'boolean',

        'orderStatus_name' => 'required|string|max:255',

        // Rules for general settings
        'website_name' => 'required|string|max:255',
        'slogan' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'contact_email' => 'required|email|max:255',
        'contact_phone' => 'required|string|max:20',
        'address' => 'nullable|string|max:255',
        'logo' => 'nullable|image|max:512',
        'favicon' => 'nullable|image|max:128',

        'temp_logo' => 'nullable|image|max:512',
        'temp_favicon' => 'nullable|image|max:128',
    ];

    public function mount()
    {
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->paymentMethods = PaymentMethod::all();
        $this->shippingMethods = ShippingMethod::all();
        $this->orderStatuses = OrderStatus::all();

        // Load general settings from the database
        $settings = GeneralSetting::first();
        if ($settings) {
            $this->website_name = $settings->website_name;
            $this->slogan = $settings->slogan;
            $this->description = $settings->description;
            $this->contact_email = $settings->contact_email;
            $this->contact_phone = $settings->contact_phone;
            $this->address = $settings->address;
            $this->logo = $settings->logo;
            $this->favicon = $settings->favicon;
            $this->old_logo = $settings->logo;
            $this->old_favicon = $settings->favicon;
        }
    }

    public function editPaymentMethod($id = null)
    {
        if ($id) {
            $paymentMethod = PaymentMethod::find($id);
            $this->paymentMethod_id = $paymentMethod->id;
            $this->paymentMethod_name = $paymentMethod->name;
            $this->paymentMethod_instructions = $paymentMethod->instructions;
            $this->paymentMethod_is_active = $paymentMethod->is_active;
        } else {
            $this->resetPaymentFields();
        }
        $this->showPaymentModal = true;
    }

    public function editShippingMethod($id = null)
    {
        if ($id) {
            $shippingMethod = ShippingMethod::find($id);
            $this->shippingMethod_id = $shippingMethod->id;
            $this->shippingMethod_name = $shippingMethod->name;
            $this->shippingMethod_description = $shippingMethod->description;
            $this->shippingMethod_cost = $shippingMethod->cost;
            $this->shippingMethod_is_active = $shippingMethod->is_active;
        } else {
            $this->resetShippingFields();
        }
        $this->showShippingModal = true;
    }

    public function editOrderStatus($id = null)
    {
        if ($id) {
            $orderStatus = OrderStatus::find($id);
            $this->orderStatus_id = $orderStatus->id;
            $this->orderStatus_name = $orderStatus->name;
        } else {
            $this->resetOrderStatusFields();
        }
        $this->showOrderStatusModal = true;
    }

    public function savePaymentMethod()
    {
        $this->validate([
            'paymentMethod_name' => 'required|string|max:255',
            'paymentMethod_instructions' => 'nullable|string',
            'paymentMethod_is_active' => 'boolean',
        ]);

        PaymentMethod::updateOrCreate(
            ['id' => $this->paymentMethod_id],
            [
                'name' => $this->paymentMethod_name,
                'instructions' => $this->paymentMethod_instructions,
                'is_active' => $this->paymentMethod_is_active,
            ]
        );

        $this->showPaymentModal = false;
        $this->refreshData();
    }

    public function saveShippingMethod()
    {
        $this->validate([
            'shippingMethod_name' => 'required|string|max:255',
            'shippingMethod_description' => 'nullable|string',
            'shippingMethod_cost' => 'required|numeric|min:0',
            'shippingMethod_is_active' => 'boolean',
        ]);

        ShippingMethod::updateOrCreate(
            ['id' => $this->shippingMethod_id],
            [
                'name' => $this->shippingMethod_name,
                'description' => $this->shippingMethod_description,
                'cost' => $this->shippingMethod_cost,
                'is_active' => $this->shippingMethod_is_active,
            ]
        );

        $this->showShippingModal = false;
        $this->refreshData();
    }

    public function saveOrderStatus()
    {
        dump($this->orderStatus_name);
        $this->validate([
            'orderStatus_name' => 'required|string|max:255',
        ]);

        OrderStatus::updateOrCreate(
            ['id' => $this->orderStatus_id],
            ['name' => $this->orderStatus_name]
        );

        $this->showOrderStatusModal = false;
        $this->refreshData();
    }

    public function closeModal()
    {
        $this->showPaymentModal = false;
        $this->showShippingModal = false;
        $this->showOrderStatusModal = false;
    }

    private function resetPaymentFields()
    {
        $this->paymentMethod_id = null;
        $this->paymentMethod_name = '';
        $this->paymentMethod_instructions = '';
        $this->paymentMethod_is_active = false;
    }

    private function resetShippingFields()
    {
        $this->shippingMethod_id = null;
        $this->shippingMethod_name = '';
        $this->shippingMethod_description = '';
        $this->shippingMethod_cost = 0;
        $this->shippingMethod_is_active = false;
    }

    private function resetOrderStatusFields()
    {
        $this->orderStatus_id = null;
        $this->orderStatus_name = '';
    }

    public function updatedTempLogo()
    {
        $this->validate([
            'temp_logo' => 'image|max:512'
        ]);
    }

    public function updatedTempFavicon()
    {
        $this->validate([
            'temp_favicon' => 'image|max:128'
        ]);
    }

    public function saveGeneralSettings()
    {
        $this->validate([
            'website_name' => 'required|string|max:255',
            'slogan' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $data = [
            'website_name' => $this->website_name,
            'slogan' => $this->slogan,
            'description' => $this->description,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'address' => $this->address,
        ];

        // Handle logo upload
        if ($this->temp_logo) {
            // Delete old logo if exists
            if ($this->old_logo && Storage::disk('public')->exists($this->old_logo)) {
                Storage::disk('public')->delete($this->old_logo);
            }

            // Store new logo
            $logoPath = $this->temp_logo->store('logos', 'public');
            $data['logo'] = $logoPath;
            $this->logo = $logoPath;
        }

        // Handle favicon upload
        if ($this->temp_favicon) {
            // Delete old favicon if exists
            if ($this->old_favicon && Storage::disk('public')->exists($this->old_favicon)) {
                Storage::disk('public')->delete($this->old_favicon);
            }

            // Store new favicon
            $faviconPath = $this->temp_favicon->store('favicons', 'public');
            $data['favicon'] = $faviconPath;
            $this->favicon = $faviconPath;
        }

        GeneralSetting::updateOrCreate(
            ['id' => 1],
            $data
        );

        // Update old file references
        $this->old_logo = $this->logo;
        $this->old_favicon = $this->favicon;

        // Reset temporary upload fields
        $this->temp_logo = null;
        $this->temp_favicon = null;

        session()->flash('message', 'General settings updated successfully!');
        // $this->refreshData();
    }

    public function render()
    {
        return view('livewire.payment-shipping-order-settings');
    }
}
