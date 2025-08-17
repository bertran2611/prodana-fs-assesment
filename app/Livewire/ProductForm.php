<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public ?Product $product = null;
    public $name, $price, $stock, $category_id, $description, $image; // $image is UploadedFile
    public $mode = 'create';

    public function mount(Product $product = null)
    {
        if ($product && $product->exists) {
            $this->authorize('update', $product);
            $this->product = $product;
            $this->fill($product->only('name', 'price', 'stock', 'category_id', 'description'));
            $this->mode = 'edit';
        } else {
            $this->authorize('create', Product::class);
            $this->mode = 'create';
        }
    }

    protected function rules()
    {
        $id = $this->product?->id;
        return [
            'name'        => 'required|min:3|max:255|unique:products,name' . ($id ? ',' . $id : ''),
            'price'       => 'required|numeric|min:0|max:999999999.99',
            'stock'       => 'required|integer|min:0|max:999999',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|max:1024|mimes:jpeg,png,jpg,gif', // 1MB
            'description' => 'nullable|string|max:1000'
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedName()
    {
        $this->validateOnly('name');
    }

    public function updatedPrice()
    {
        $this->validateOnly('price');
    }

    public function updatedStock()
    {
        $this->validateOnly('stock');
    }

    public function updatedCategoryId()
    {
        $this->validateOnly('category_id');
    }

    public function updatedDescription()
    {
        $this->validateOnly('description');
    }

    public function updatedImage()
    {
        $this->validateOnly('image');
    }

    public function save()
    {
        $data = $this->validate();

        if ($this->image) {
            $path = $this->image->store('products', 'public');
            $data['image_path'] = $path;
        }

        if ($this->mode === 'edit' && $this->product) {
            $this->product->update($data);
            session()->flash('success', 'Product updated successfully');
        } else {
            Product::create($data);
            session()->flash('success', 'Product created successfully');
        }

        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.product-form', [
            'categories' => Category::orderBy('name')->get()
        ]);
    }
}