<?php

namespace App\Livewire;

use App\Models\Address;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserAddress extends Component
{
    public $addresses;
    public $showModal = false;
    public $address_id;
    public $label;
    public $name = '';
    public $address_line_1 = '';
    public $address_line_2 = '';

    public $country = 'Indonesia';
    public $zip_code = '';
    public $phone = '';
    public $is_default = false;


    public $provinsi = [];
    public $kabupaten = [];
    public $kecamatan = [];
    public $kelurahan = [];

    public $selectedProvinsi = '';
    public $selectedKabupaten = '';
    public $selectedKecamatan = '';
    public $selectedKelurahan = '';

    protected $rules = [
        'address_line_1' => 'required',
        'selectedKelurahan' => 'required',
        'selectedKecamatan' => 'required',
        'selectedKabupaten' => 'required',
        'selectedProvinsi' => 'required',
        'country' => 'required',
        'zip_code' => 'required',
        'phone' => 'required',
    ];

    public function mount()
    {
        $this->loadProvinsi();
        $this->addresses = Auth::user()->addresses;
    }

    public function render()
    {
        // dd($this->provinsi); // 
        return view('livewire.user-address', [
            'provinsi' => $this->provinsi,
            'kabupaten' => $this->kabupaten,
            'kecamatan' => $this->kecamatan,
            'kelurahan' => $this->kelurahan,
        ]);
    }

    public function loadProvinsi()
    {
        $response = Http::get('https://ibnux.github.io/data-indonesia/provinsi.json');

        if ($response->successful()) {
            $this->provinsi = $response->json();
        } else {
            // Log error or handle failure
            logger()->error('Failed to fetch provinsi data', ['status' => $response->status()]);
            $this->provinsi = [];
        }
    }

    public function loadKabupaten()
    {
        if ($this->selectedProvinsi) {
            $response = Http::get("https://ibnux.github.io/data-indonesia/kabupaten/{$this->selectedProvinsi}.json");

            if ($response->successful()) {
                $this->kabupaten = $response->json();
            } else {
                // Log error or handle failure
                logger()->error('Failed to fetch kabupaten data', ['status' => $response->status()]);
                $this->kabupaten = [];
            }

            // Hilangkan reset pada $this->selectedKabupaten
            $this->kecamatan = [];
            $this->kelurahan = [];
        } else {
            $this->kabupaten = [];
        }
    }

    public function loadKecamatan()
    {
        if ($this->selectedKabupaten) {
            $response = Http::get("https://ibnux.github.io/data-indonesia/kecamatan/{$this->selectedKabupaten}.json");

            if ($response->successful()) {
                $this->kecamatan = $response->json();
            } else {
                // Log error or handle failure
                logger()->error('Failed to fetch kecamatan data', ['status' => $response->status()]);
                $this->kecamatan = [];
            }

            // Hilangkan reset pada $this->selectedKecamatan
            $this->kelurahan = [];
        } else {
            $this->kecamatan = [];
        }
    }

    public function loadKelurahan()
    {
        if ($this->selectedKecamatan) {
            $response = Http::get("https://ibnux.github.io/data-indonesia/kelurahan/{$this->selectedKecamatan}.json");

            if ($response->successful()) {
                $this->kelurahan = $response->json();
            } else {
                // Log error or handle failure
                logger()->error('Failed to fetch kelurahan data', ['status' => $response->status()]);
                $this->kelurahan = [];
            }

            // Hilangkan reset pada $this->selectedKelurahan
        } else {
            $this->kelurahan = [];
        }
    }

    public function updatedSelectedProvinsi($provinsiId)
    {
        $this->selectedProvinsi = $provinsiId;
        $this->loadKabupaten();
    }

    public function updatedSelectedKabupaten($kabupatenId)
    {
        $this->selectedKabupaten = $kabupatenId;
        $this->loadKecamatan();
    }

    public function updatedSelectedKecamatan($kecamatanId)
    {
        $this->selectedKecamatan = $kecamatanId;
        $this->loadKelurahan();
    }


    public function openModal($isEditing = false)
    {
        if (!$isEditing) {
            $this->resetForm(); // Reset form hanya jika menambahkan alamat baru
        }
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function saveAddress()
    {
        $this->validate();

        $address = Address::updateOrCreate(
            ['id' => $this->address_id],
            [
                'user_id' => Auth::id(),
                'label' => $this->label,
                'name' => $this->name,
                'address_line_1' => $this->address_line_1,
                'address_line_2' => $this->address_line_2,
                'kelurahan' => collect($this->kelurahan)->firstWhere('id', $this->selectedKelurahan)['nama'] ?? '',
                'kecamatan' => collect($this->kecamatan)->firstWhere('id', $this->selectedKecamatan)['nama'] ?? '',
                'kota_kab' => collect($this->kabupaten)->firstWhere('id', $this->selectedKabupaten)['nama'] ?? '',
                'provinsi' => collect($this->provinsi)->firstWhere('id', $this->selectedProvinsi)['nama'] ?? '',
                'kelurahan_id' => $this->selectedKelurahan,
                'kecamatan_id' => $this->selectedKecamatan,
                'kabupaten_id' => $this->selectedKabupaten,
                'provinsi_id' => $this->selectedProvinsi,
                'country' => $this->country,
                'zip_code' => $this->zip_code,
                'phone' => $this->phone,
                'is_default' => $this->is_default,
            ]
        );

        if ($this->is_default) {
            Address::where('user_id', Auth::id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $this->closeModal();
        $this->addresses = Auth::user()->addresses;
        $this->dispatch('addressAdded');
    }

    public function editAddress($id)
    {
        $address = Address::findOrFail($id);

        $this->address_id = $address->id;
        $this->label = $address->label;
        $this->name = $address->name;
        $this->address_line_1 = $address->address_line_1;
        $this->address_line_2 = $address->address_line_2;
        $this->country = $address->country;
        $this->zip_code = $address->zip_code;
        $this->phone = $address->phone;
        $this->is_default = $address->is_default;

        // Set selected IDs for dropdowns
        $this->selectedProvinsi = $address->provinsi_id;
        $this->selectedKabupaten = $address->kabupaten_id;
        $this->selectedKecamatan = $address->kecamatan_id;
        $this->selectedKelurahan = $address->kelurahan_id;

        // Load dependent data for kabupaten, kecamatan, and kelurahan
        $this->updatedSelectedProvinsi($this->selectedProvinsi);
        $this->updatedSelectedKabupaten($this->selectedKabupaten);
        $this->updatedSelectedKecamatan($this->selectedKecamatan);

        $this->openModal(true); // Buka modal dengan isEditing = true
    }

    public function deleteAddress($id)
    {
        Address::destroy($id);
        $this->addresses = Auth::user()->addresses;
    }

    private function resetForm()
    {
        $this->address_id = null; // Reset ID alamat
        $this->label = ''; // Reset label
        $this->name = ''; // Reset nama
        $this->address_line_1 = ''; // Reset alamat baris 1
        $this->address_line_2 = ''; // Reset alamat baris 2
        $this->selectedKelurahan = ''; // Reset kelurahan yang dipilih
        $this->selectedKecamatan = ''; // Reset kecamatan yang dipilih
        $this->selectedKabupaten = ''; // Reset kabupaten yang dipilih
        $this->selectedProvinsi = ''; // Reset provinsi yang dipilih
        $this->country = 'Indonesia'; // Reset negara
        $this->zip_code = ''; // Reset kode pos
        $this->phone = ''; // Reset nomor telepon
        $this->is_default = false; // Reset status default

        // Reset data pilihan dropdown
        $this->kabupaten = [];
        $this->kecamatan = [];
        $this->kelurahan = [];
    }
}
