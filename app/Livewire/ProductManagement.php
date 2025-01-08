<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductStock;
use Livewire\WithPagination;
use Intervention\Image\Image;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\HasMedia;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Spatie\MediaLibrary\Conversions\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductManagement extends Component
{
    use WithPagination, WithFileUploads;
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $filterCategory = '';

    public $product_type = 'single'; // 'single' atau 'variant'
    public $variants = []; // Input field untuk varian
    public $variant_name; // Temp variant name
    public $variant_price; // Temp variant price
    public $variant_stock; // Temp variant stock
    public $variant_sku; // Temp variant SKU
    public $variant_weight; // Temp variant weight

    public $name = '', $description = '', $sku = '', $price = 0, $discount_price = '', $stock, $current_stock, $adjusted_stock, $stock_action_type, $stock_change, $stock_note = '', $category_id, $is_active = true, $weight;
    public $formattedPrice = 'Rp 0'; // Formatted value
    public $formattedDiscountPrice = '';

    public $images = [];
    public $imagePreviews = [
        'existing' => [],
        'new' => []
    ];

    public $removedImages = [];
    public $editingProductId;
    public $isModalOpen = false;
    public $confirmingProductDeletion = false;
    public $viewingProduct = null;

    public Product $selectedProduct;


    // This method ensures formattedPrice is set when modal opens for edit
    // public function mount($product = null)
    // {
    //     if ($product) {
    //         $this->price = $product->price;
    //         $this->formattedPrice = 'Rp. ' . number_format($this->price, 0, ',', '.');
    //     }
    // }


    public function updatedFormattedPrice($value)
    {
        // Remove any non-numeric characters
        $numericValue = preg_replace('/\D/', '', $value);

        // Convert to numeric and update the price property
        $this->price = $numericValue ? intval($numericValue) : 0;

        // Update formattedPrice
        $this->formattedPrice = 'Rp. ' . number_format($this->price, 0, ',', '.');
    }

    public function updatedFormattedDiscountPrice($value)
    {
        // Hapus karakter non-numerik dan konversi ke integer
        $discount = intval(preg_replace('/\D/', '', $value));

        // Pastikan diskon tidak negatif
        $discount = max(0, $discount);

        // Simpan diskon ke dalam properti
        $this->discount_price = $discount;

        // Format diskon dengan simbol mata uang dan pemisah
        $this->formattedDiscountPrice = 'Rp. ' . number_format($discount, 0, ',', '.');
    }

    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'required',
        'sku' => 'required_if:product_type,single|unique:products,sku,',
        'price' => 'required_if:product_type,single|numeric|min:0',
        'discount_price' => 'nullable|numeric|min:0',
        'stock' => 'required_if:product_type,single|integer|min:0',
        'category_id' => 'required|exists:categories,id',
        'is_active' => 'boolean',
        'images.*' => 'image|max:1024',
        'product_type' => 'required|in:single,variant',
        'variants.*.name' => 'required_if:product_type,variant|min:1',
        'variants.*.price' => 'required_if:product_type,variant|numeric|min:0',
        'variants.*.stock' => 'required_if:product_type,variant|integer|min:0',
        'variants.*.sku' => 'required_if:product_type,variant|unique:product_variants,sku',
        'variants.*.weight' => 'required_if:product_type,variant|numeric|min:0', // Add weight validation for variants
        'weight' => 'required_if:product_type,single|numeric|min:0', // Add weight validation for single products
    ];


    public function render()
    {
        $products = Product::search($this->search)
            ->when($this->filterCategory, function ($query) {
                return $query->where('category_id', $this->filterCategory);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(5);

        $categories = Category::all();

        return view('livewire.product-management', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {

        $this->validate();

        // Create the product
        $product = Product::create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'sku' => $this->product_type === 'single' ? $this->sku : null,
            'price' => $this->product_type === 'single' ? $this->price : null,
            'discount_price' => $this->discount_price,
            'category_id' => $this->category_id,
            'is_active' => $this->is_active,
            'product_type' => $this->product_type,
            'weight' => $this->product_type === 'single' ? $this->weight : null,
        ]);

        // Handle variants if product type is 'variant'
        if ($this->product_type === 'variant') {
            foreach ($this->variants as $variantData) {
                // Remove 'stock' from the variant data array
                $stockQuantity = $variantData['stock'];
                unset($variantData['stock']);

                $variant = $product->variants()->updateOrCreate(
                    ['sku' => $variantData['sku']],
                    $variantData
                );

                // Add entry to product_stocks for each variant
                ProductStock::create([
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'quantity' => $stockQuantity,
                    'action_type' => 'addition',
                    'note' => 'Initial stock for variant'
                ]);
            }
        } else {
            // Add entry to product_stocks for single product
            ProductStock::create([
                'product_id' => $product->id,
                'variant_id' => null,
                'quantity' => $this->stock,
                'action_type' => 'addition',
                'note' => 'Initial stock for single product'
            ]);
        }

        if ($this->images) {
            // Initialize ImageManager with GD Driver
            $manager = new ImageManager(Driver::class);

            foreach ($this->images as $image) {
                // Read the uploaded image
                $img = $manager->read($image->getRealPath());

                // Resize the image to 800x1200 pixels
                $img->cover(800, 1200);

                // Save the resized image to a temporary path
                $tempPath = tempnam(sys_get_temp_dir(), 'resized_') . '.jpg';
                $img->toJpeg(80)->save($tempPath);

                // Add the resized image to the media collection
                $product->addMedia($tempPath)->toMediaCollection('product_images');

                // Generate and store a thumbnail (e.g., 200x300)
                $img->cover(200, 300);
                $thumbnailPath = tempnam(sys_get_temp_dir(), 'thumb_') . '.jpg';
                $img->toJpeg(80)->save($thumbnailPath);

                // Add the thumbnail image to the media collection
                $product->addMedia($thumbnailPath)->toMediaCollection('thumbnails');
            }
        }

        session()->flash('message', 'Product successfully created.');
        $this->closeModal();
        $this->resetInputFields();
    }


    public function edit($id)
    {

        $product = Product::findOrFail($id);
        $this->editingProductId = $id;
        $this->product_type = $product->product_type;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->sku = $product->sku;
        $this->price = $product->price;
        $this->formattedPrice = 'Rp. ' . number_format($this->price, 0, ',', '.');
        $this->discount_price = $product->discount_price;
        $this->current_stock = $product->currentStock(); // Fetch current stock
        $this->category_id = $product->category_id;
        $this->is_active = $product->is_active;
        $this->weight = $product->weight;

        if ($product->discount_price) {
            $this->formattedDiscountPrice = 'Rp. ' . number_format($product->discount_price, 0, ',', '.');
        } else {
            $this->formattedDiscountPrice = '';
        }

        $this->imagePreviews['existing'] = $product->getMedia('product_images')->map(function ($media) {
            return [
                'id' => $media->id,
                'url' => $media->getUrl()
            ];
        })->toArray();
        $this->imagePreviews['new'] = [];

        $this->dispatch('contentUpdated', $this->description);

        if ($product->isVariantProduct()) {
            $this->variants = $product->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
                    'current_stock' => $variant->currentStock(), // Fetch current stock for the variant
                    'weight' => $variant->weight,
                ];
            })->toArray();
        } else {
            $this->variants = [];
        }

        $this->openModal();
    }

    public function update()
    {
        $rules = [
            'name' => 'required|min:3',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'images.*' => 'image|max:1024',
            'product_type' => 'required|in:single,variant',
        ];



        if ($this->product_type === 'single') {
            $rules['sku'] = 'required|unique:products,sku,' . $this->editingProductId;
            $rules['price'] = 'required|numeric|min:0';
            // $rules['stock'] = 'required|integer|min:0'; //untuk update tidak butuh ini
            $rules['adjusted_stock'] = 'nullable|integer'; // New rule for adjusted stock
            $rules['weight'] = 'required|numeric|min:0';
        }

        if ($this->product_type === 'variant') {
            foreach ($this->variants as $index => $variant) {
                $rules['variants.' . $index . '.name'] = 'required|min:1';
                $rules['variants.' . $index . '.price'] = 'required|numeric|min:0';
                // $rules['variants.' . $index . '.stock'] = 'required|integer|min:0';
                $variantId = isset($variant['id']) ? $variant['id'] : 'NULL';
                $rules['variants.' . $index . '.sku'] = 'required|unique:product_variants,sku,' . $variantId;
                $rules['variants.' . $index . '.weight'] = 'required|numeric|min:0'; // Add weight validation for variants
            }
        }


        $this->validate($rules);



        $product = Product::find($this->editingProductId);
        $product->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'sku' => $this->product_type === 'single' ? $this->sku : null,
            'price' => $this->product_type === 'single' ? $this->price : null,
            'discount_price' => $this->discount_price,
            'category_id' => $this->category_id,
            'is_active' => $this->is_active,
            'weight' => $this->product_type === 'single' ? $this->weight : null,
        ]);


        // Handle stock adjustment for single products
        if ($this->product_type === 'single' && $this->stock_action_type && $this->stock_change) {
            $quantity = ($this->stock_action_type === 'reduction') ? -$this->stock_change : $this->stock_change;

            ProductStock::create([
                'product_id' => $product->id,
                'variant_id' => null,
                'quantity' => $quantity,
                'action_type' => $this->stock_action_type,
                'note' => $this->stock_note,
            ]);
        }


        // if ($this->product_type === 'variant') {
        //     foreach ($this->variants as $variant) {
        //         $existingVariant = $product->variants()->find($variant['id']);

        //         if ($existingVariant) {
        //             // Update existing variant
        //             $existingVariant->update($variant);

        //             // Check for stock adjustment
        //             if (isset($variant['stock_action_type']) && isset($variant['stock_change']) && $variant['stock_change'] > 0) {
        //                 $quantity = ($variant['stock_action_type'] === 'reduction') ? -$variant['stock_change'] : $variant['stock_change'];

        //                 ProductStock::create([
        //                     'product_id' => $product->id,
        //                     'variant_id' => $existingVariant->id,
        //                     'quantity' => $quantity,
        //                     'action_type' => $variant['stock_action_type'],
        //                     'note' => 'Stock adjustment during update',
        //                 ]);
        //             }
        //         } else {
        //             // Create new variant if it doesn't exist
        //             $newVariant = $product->variants()->create($variant);

        //             // Initial stock entry for the new variant
        //             ProductStock::create([
        //                 'product_id' => $product->id,
        //                 'variant_id' => $newVariant->id,
        //                 'quantity' => $variant['stock'], // Assuming 'stock' is provided for new variants
        //                 'action_type' => 'addition',
        //                 'note' => 'Initial stock for variant',
        //             ]);
        //         }
        //     }
        // }

        // if ($this->product_type === 'variant') {
        //     foreach ($this->variants as $variant) {
        //         if (isset($variant['id'])) {
        //             // Update existing variant
        //             $existingVariant = $product->variants()->find($variant['id']);
        //             if ($existingVariant) {
        //                 $existingVariant->update([
        //                     'name' => $variant['name'],
        //                     'sku' => $variant['sku'],
        //                     'price' => $variant['price'],
        //                     'weight' => $variant['weight'],
        //                 ]);

        //                 // Handle stock adjustment for the existing variant
        //                 if ($variant['stock_action_type'] && $variant['stock_change']) {
        //                     $quantity = ($variant['stock_action_type'] === 'reduction') ? -$variant['stock_change'] : $variant['stock_change'];
        //                     ProductStock::create([
        //                         'product_id' => $product->id,
        //                         'variant_id' => $existingVariant->id,
        //                         'quantity' => $quantity,
        //                         'action_type' => $variant['stock_action_type'],
        //                         'note' => $variant['stock_note'],
        //                     ]);
        //                 }
        //             }
        //         } else {
        //             // Create a new variant
        //             $newVariant = $product->variants()->create([
        //                 'name' => $variant['name'],
        //                 'sku' => $variant['sku'],
        //                 'price' => $variant['price'],
        //                 'weight' => $variant['weight'],
        //             ]);

        //             // Add entry to product_stocks for the new variant
        //             ProductStock::create([
        //                 'product_id' => $product->id,
        //                 'variant_id' => $newVariant->id,
        //                 'quantity' => $variant['stock'], // Assuming you have 'stock' in the $variant array for new variants
        //                 'action_type' => 'addition',
        //                 'note' => 'Initial stock for variant',
        //             ]);
        //         }
        //     }
        // }
        if ($this->product_type === 'variant') {
            foreach ($this->variants as $variant) {
                // Check if this is an existing variant
                if (isset($variant['id'])) {
                    $existingVariant = $product->variants()->find($variant['id']);
                    if ($existingVariant) {
                        $existingVariant->update([
                            'name' => $variant['name'],
                            'sku' => $variant['sku'],
                            'price' => $variant['price'],
                            'weight' => $variant['weight'],
                        ]);

                        // Handle stock adjustment
                        if (isset($variant['stock_action_type']) && $variant['stock_action_type'] && $variant['stock_change']) {
                            $quantity = ($variant['stock_action_type'] === 'reduction') ? -$variant['stock_change'] : $variant['stock_change'];
                            ProductStock::create([
                                'product_id' => $product->id,
                                'variant_id' => $existingVariant->id,
                                'quantity' => $quantity,
                                'action_type' => $variant['stock_action_type'],
                                'note' => $variant['stock_note'] ?? '',
                            ]);
                        }
                    }
                } else {
                    // Handling new variant addition
                    $newVariant = $product->variants()->create([
                        'name' => $variant['name'],
                        'sku' => $variant['sku'],
                        'price' => $variant['price'],
                        'weight' => $variant['weight'],
                    ]);

                    // Initial stock for the new variant
                    ProductStock::create([
                        'product_id' => $product->id,
                        'variant_id' => $newVariant->id,
                        'quantity' => $variant['stock'], // Assuming 'stock' is provided for new variants
                        'action_type' => 'addition',
                        'note' => 'Initial stock for new variant',
                    ]);
                }
            }
        }



        if (!empty($this->removedImages)) {
            foreach ($this->removedImages as $mediaId) {
                $media = $product->media()->find($mediaId);
                if ($media) {
                    $media->delete();
                }
            }
        }

        if ($this->images) {
            // Initialize ImageManager with GD Driver
            $manager = new ImageManager(Driver::class);

            foreach ($this->images as $image) {
                // Read the uploaded image
                $img = $manager->read($image->getRealPath());

                // Resize the image to 800x1200 pixels
                $img->cover(800, 1200);

                // Save the resized image to a temporary path
                $tempPath = tempnam(sys_get_temp_dir(), 'resized_') . '.jpg';
                $img->toJpeg(80)->save($tempPath);

                // Add the resized image to the media collection
                $product->addMedia($tempPath)->toMediaCollection('product_images');

                // Generate and store a thumbnail (e.g., 200x300)
                $img->cover(200, 300);
                $thumbnailPath = tempnam(sys_get_temp_dir(), 'thumb_') . '.jpg';
                $img->toJpeg(80)->save($thumbnailPath);

                // Add the thumbnail image to the media collection
                $product->addMedia($thumbnailPath)->toMediaCollection('thumbnails');
            }
        }


        $this->removedImages = [];

        session()->flash('message', 'Product successfully updated.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function addVariant()
    {
        $this->variants[] = [
            'name' => $this->variant_name,
            'price' => $this->variant_price,
            'stock' => $this->variant_stock,
            'sku' => $this->variant_sku,
            'weight' => $this->variant_weight, // Add weight to variant data
        ];

        $this->resetVariantFields();
    }

    public function removeVariant($index)
    {
        array_splice($this->variants, $index, 1);
    }

    private function resetVariantFields()
    {
        $this->variant_name = '';
        $this->variant_price = '';
        $this->variant_stock = '';
        $this->variant_sku = '';
        $this->variant_weight = ''; // Reset weight field
    }

    public function updated($propertyName)
    {

        if ($this->product_type === 'single') {
            $this->validateOnly($propertyName, [
                'name' => 'required|min:3',
                'description' => 'required',
                'sku' => 'required|unique:products,sku,' . $this->editingProductId,
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,id',
                'is_active' => 'boolean',
                'images.*' => 'image|max:1024',
                'weight' => 'required|numeric|min:0', // Add weight validation for single products
            ]);
        } elseif ($this->product_type === 'variant') {
            $rules = [
                'name' => 'required|min:3',
                'description' => 'required',
                'category_id' => 'required|exists:categories,id',
                'is_active' => 'boolean',
                'images.*' => 'image|max:1024',
            ];
            foreach ($this->variants as $index => $variant) {
                $rules['variants.' . $index . '.name'] = 'required|min:1';
                $rules['variants.' . $index . '.price'] = 'required|numeric|min:0';
                // $rules['variants.' . $index . '.stock'] = 'required|integer|min:0';
                $rules['variants.' . $index . '.sku'] = 'required|unique:product_variants,sku';
                $rules['variants.' . $index . '.weight'] = 'required|numeric|min:0'; // Add weight validation for variants

                // Check if the variant has an 'id' key to include it in the unique validation rule
                if (isset($variant['id'])) {
                    $rules['variants.' . $index . '.sku'] .= ',' . $variant['id'];
                }
            }
            $this->validateOnly($propertyName, $rules);
        }
    }

    public function updatedImages()
    {

        foreach ($this->images as $image) {
            $this->imagePreviews['new'][] = $image->temporaryUrl();
        }
    }

    public function removeImagePreview($index)
    {

        array_splice($this->images, $index, 1);
        array_splice($this->imagePreviews['new'], $index, 1);
    }


    public function removeExistingImage($mediaId)
    {

        $this->imagePreviews['existing'] = array_filter(
            $this->imagePreviews['existing'],
            function ($preview) use ($mediaId) {
                return $preview['id'] != $mediaId;
            }
        );

        if (!isset($this->removedImages)) {
            $this->removedImages = [];
        }
        $this->removedImages[] = $mediaId;
    }

    public function confirmProductDeletion($id)
    {
        // $this->confirmingProductDeletion = true;
        $this->editingProductId = $id;
        $this->dispatch('open-modal', name: 'confirm-product-deletion');
    }

    public function deleteProduct()
    {
        Product::find($this->editingProductId)->delete();
        // $this->confirmingProductDeletion = false;
        $this->editingProductId = null;
        session()->flash('message', 'Product deleted successfully.');
        $this->dispatch('close-modal');
    }

    public function openModal()
    {

        // $this->isModalOpen = true;
        $this->dispatch('open-modal', name: 'product-modal');
    }

    public function closeModal()
    {
        // $this->isModalOpen = false;
        $this->resetInputFields();
        $this->dispatch('close-modal');
    }

    public function viewProduct($id)
    {
        $this->viewingProduct = Product::with(['category', 'variants', 'media'])
            ->findOrFail($id);
        $this->viewingProduct->load(['stockHistory' => function ($query) {
            $query->latest()->take(10);
        }]);


        $this->dispatch('open-modal', name: 'product-view');
    }

    public function closeViewModal()
    {
        $this->viewingProduct = null;
    }


    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->sku = '';
        $this->price = '';
        $this->formattedPrice = 'Rp 0';
        $this->formattedDiscountPrice = null;
        $this->discount_price = '';
        $this->stock = '';
        $this->category_id = '';
        $this->is_active = true;
        $this->editingProductId = null;
        $this->product_type = 'single';
        $this->variants = [];
        $this->weight = ''; // Reset weight field
        $this->stock_change = '';
        $this->stock_action_type = '';
        $this->stock_note = '';


        $this->images = [];
        $this->imagePreviews = [
            'existing' => [],
            'new' => []
        ];
        $this->removedImages = [];
    }
}
