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
    public $name,$price,$stock,$category_id,$description,$image; // $image is UploadedFile
    public $mode = 'create';

    public function mount(Product $product = null)
    {
        if ($product && $product->exists) {
            $this->authorize('update', $product);
            $this->product = $product;
            $this->fill($product->only('name','price','stock','category_id','description'));
            $this->mode = 'edit';
        } else {
            $this->authorize('create', Product::class);
            $this->mode = 'create';
        }
    }

    protected function rules(){
        $id = $this->product?->id;
        return [
          'name'        => 'required|unique:products,name'.($id?','.$id:''),
          'price'       => 'required|numeric|min:0',
          'stock'       => 'required|integer|min:0',
          'category_id' => 'required|exists:categories,id',
          'image'       => 'nullable|image|max:1024', // 1MB
          'description' => 'nullable|string'
        ];
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
            session()->flash('success', 'Product updated');
        } else {
            Product::create($data);
            session()->flash('success', 'Product created');
        }

        return redirect()->route('products.index');
    }


    public function render(){
        return view('livewire.product-form',[
          'categories'=>Category::orderBy('name')->get()
        ]);
    }
}

