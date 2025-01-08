<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GeneralSetting;

class FooterSection extends Component
{
    // General settings properties
    public $website_name;
    public $slogan;
    public $description;
    public $contact_email;
    public $contact_phone;
    public $address;
    public $logo;

    public function render()
    {
        // Load general settings from the database
        $settings = GeneralSetting::first();
        if ($settings) {
            $this->website_name = $settings->website_name ?? 'YourStore';
            $this->slogan = $settings->slogan ?? '';
            $this->description = $settings->description ?? '';
            $this->contact_email = $settings->contact_email ?? '';
            $this->contact_phone = $settings->contact_phone ?? '';
            $this->address = $settings->address ?? '';
            $this->logo = $settings->logo ?? '';
        }
        return view('livewire.footer-section');
    }
}
