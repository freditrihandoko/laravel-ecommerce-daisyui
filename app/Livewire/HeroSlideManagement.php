<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HeroSlide;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class HeroSlideManagement extends Component
{
    use WithFileUploads;

    public $slides;
    public $title;
    public $description;
    public $buttonText;
    public $buttonLink;
    public $backgroundImage;
    public $order;
    public $isActive;
    public $editingSlideId = null;
    public $isModalOpen = false;
    public $existingBackgroundImage;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'buttonText' => 'required|string|max:255',
            'buttonLink' => 'required|url',
            'backgroundImage' => $this->editingSlideId ? 'nullable|image|max:1024' : 'required|image|max:1024',
            'order' => 'required|integer|min:1',
            'isActive' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->loadSlides();
    }

    public function loadSlides()
    {
        $this->slides = HeroSlide::orderBy('order')->get();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function addSlide()
    {
        $this->resetForm();
        $this->editingSlideId = null;
        $this->order = $this->getNextAvailableOrder();
        $this->openModal();
    }

    public function editSlide($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $this->editingSlideId = $id;
        $this->title = $slide->title;
        $this->description = $slide->description;
        $this->buttonText = $slide->button_text;
        $this->buttonLink = $slide->button_link;
        $this->order = $slide->order;
        $this->isActive = $slide->is_active;
        $this->existingBackgroundImage = $slide->background_image;
        $this->openModal();
    }

    public function saveSlide()
    {
        // dump($this->order);
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'buttonText' => 'required|string|max:255',
            'buttonLink' => 'required|url',
            'backgroundImage' => $this->editingSlideId ? 'nullable|image|max:1024' : 'required|image|max:1024',
            'order' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if ($this->slides->where('order', $value)->where('id', '!=', $this->editingSlideId)->count()) {
                        $fail("The order {$value} is already in use. Please choose a different order.");
                    }
                },
            ],
            'isActive' => 'boolean',
        ]);


        if ($this->editingSlideId) {
            $slide = HeroSlide::findOrFail($this->editingSlideId);
        } else {
            $slide = new HeroSlide();
        }

        $slide->title = $this->title;
        $slide->description = $this->description;
        $slide->button_text = $this->buttonText;
        $slide->button_link = $this->buttonLink;
        $slide->order = $this->order;
        $slide->is_active = $this->isActive;

        if ($this->backgroundImage) {
            if ($slide->background_image) {
                Storage::disk('public')->delete($slide->background_image);
            }
            $imagePath = $this->backgroundImage->store('hero-slides', 'public');
            $slide->background_image = $imagePath;
        }

        $slide->save();

        $this->resetForm();
        $this->loadSlides();
        $this->closeModal();
    }

    public function resetForm()
    {
        $this->reset(['title', 'description', 'buttonText', 'buttonLink', 'backgroundImage', 'order', 'isActive', 'editingSlideId', 'existingBackgroundImage']);
    }

    public function deleteSlide($id)
    {
        $slide = HeroSlide::findOrFail($id);
        if ($slide->background_image) {
            Storage::disk('public')->delete($slide->background_image);
        }
        $slide->delete();
        $this->loadSlides();
    }

    public function getAvailableOrders()
    {
        $maxOrder = $this->slides->max('order') ?? 0;
        return range(1, $maxOrder + 1);
    }

    public function getNextAvailableOrder()
    {
        return ($this->slides->max('order') ?? 0) + 1;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'order') {
            $this->validateOnly($propertyName, [
                'order' => [
                    'required',
                    'integer',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        if ($this->slides->where('order', $value)->where('id', '!=', $this->editingSlideId)->count()) {
                            $fail("The order {$value} is already in use. Please choose a different order.");
                        }
                    },
                ],
            ]);
        }
    }

    public function toggleActive($id)
    {
        $slide = HeroSlide::findOrFail($id);
        $slide->is_active = !$slide->is_active;
        $slide->save();
        $this->loadSlides();
        session()->flash('message', 'Status Updated successfully.');
    }

    public function render()
    {
        return view('livewire.hero-slide-management', [
            'availableOrders' => $this->getAvailableOrders(),
        ]);
    }
}
