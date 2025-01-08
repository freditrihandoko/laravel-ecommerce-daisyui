<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class CategoryManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $name = '', $description = '', $image = '', $is_active = true, $parent_id = null;
    public $editingCategoryId;
    public $tempImage;
    public $categoryIdBeingDeleted;

    protected $listeners = ['openModal', 'closeModal'];

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'min:3',
                Rule::unique('categories', 'name')->ignore($this->editingCategoryId)
            ],
            'description' => 'required',
            'image' => 'nullable|image|max:1024', // max 1MB
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:categories,id'
        ];
    }

    public function render()
    {
        $categories = Category::where('name', 'like', '%' . $this->search . '%')
            ->with('parent')
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $parentCategories = Category::whereNull('parent_id')->get();

        return view('livewire.category-management', [
            'categories' => $categories,
            'parentCategories' => $parentCategories
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
        $this->dispatch('open-modal', name: 'category-modal');
    }

    public function closeModal()
    {
        $this->resetInputFields();
        $this->dispatch('close-modal');
    }

    public function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->image = null;
        $this->is_active = true;
        $this->parent_id = null;
        $this->editingCategoryId = null;
        $this->tempImage = null;
    }

    public function store()
    {
        // dd('ok');
        $validatedData = $this->validate();

        $category = new Category();
        $category->name = $this->name;
        $category->slug = Str::slug($this->name);
        $category->description = $this->description;
        $category->is_active = $this->is_active;
        $category->parent_id = $this->parent_id ?? null;

        if ($this->image) {
            $imagePath = $this->image->store('categories', 'public');
            $category->image_path = $imagePath;
        }

        $category->save();

        session()->flash('message', 'Category created successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->editingCategoryId = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->is_active = $category->is_active;
        $this->parent_id = $category->parent_id ?? null;
        $this->tempImage = $category->image_path;

        $this->openModal();
    }

    public function update()
    {
        // dd('update method called'); // Debugging line

        $validatedData = $this->validate();

        $category = Category::find($this->editingCategoryId);

        if (!$category) {
            session()->flash('error', 'Category not found.');
            $this->closeModal();
            return;
        }

        $category->name = $this->name;
        $category->slug = Str::slug($this->name);
        $category->description = $this->description;
        $category->is_active = $this->is_active;
        $category->parent_id = $this->parent_id;

        if ($this->image) {
            $imagePath = $this->image->store('categories', 'public');
            $category->image_path = $imagePath;
        }

        $category->save();

        session()->flash('message', 'Category updated successfully.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function confirmCategoryDeletion($id)
    {
        $this->categoryIdBeingDeleted = $id;
        $this->dispatch('open-modal', name: 'confirm-category-deletion');
    }

    public function deleteCategory()
    {
        Category::find($this->categoryIdBeingDeleted)->delete();
        session()->flash('message', 'Category deleted successfully.');
        $this->dispatch('close-modal');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
}
