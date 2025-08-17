<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;  
use Illuminate\Support\Facades\Storage;

class ProductsTrash extends Component
{
    use WithPagination;

    public function restore($id)
    {
        $p = Product::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $p);
        $p->restore();
        session()->flash('success', 'Product restored');
    }

    public function destroyForever($id)
    {
        $p = Product::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $p);

        if ($p->image_path) {
            Storage::disk('public')->delete($p->image_path);
        }

        $p->forceDelete();
        session()->flash('success', 'Product deleted permanently');
    }

    public function render()
    {
        $products = Product::onlyTrashed()->with('category')->paginate(10);
        return view('livewire.products-trash', compact('products'));
    }
}
