<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public $categories = [];
    public $selectedCategories = [];
    public $sortBy = 'default';
    public $perPage = 12;



    protected function queryString()
    {
        return [
            'selectedCategories' =>
            [
                'as' => 'categories',
                'except' => '[]'
            ],
            'sortBy' =>
            [
                'as' => 'sort',
                'except' => 'default'
            ],
            'perPage' =>
            [
                'as' => 'page',
                'except' => 12
            ],
        ];
    }

    // protected $queryString = [
    //     'selectedCategories' => ['except' => []],
    //     'sortBy' => ['except' => 'default'],
    //     'perPage' => ['except' => 12],
    // ];

    public function mount()
    {
        $this->categories = Category::where('is_active', true)->get();
    }

    public function render()
    {
        $query = Product::query()->where('is_active', true);

        // Apply category filter using slugs
        if (!empty($this->selectedCategories)) {
            $query->whereHas('category', function ($q) {
                $q->whereIn('slug', $this->selectedCategories);
            });
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate($this->perPage);

        return view('livewire.product-list', [
            'products' => $products,
        ])->layout('layouts.customer');
    }

    public function updatingSelectedCategories()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }
}
