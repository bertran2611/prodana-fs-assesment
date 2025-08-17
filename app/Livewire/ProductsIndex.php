<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class ProductsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryId = '';
    public $sortField = 'name';
    public $sortDir = 'asc';

    protected $queryString = ['search','categoryId','sortField','sortDir','page'];
    protected $listeners = ['deleteConfirmed'=>'delete'];

    public function updatingSearch(){ $this->resetPage(); }
    public function updatingCategoryId(){ $this->resetPage(); }
    public function sortBy($field){
        if($this->sortField === $field) { $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc'; }
        else { $this->sortField = $field; $this->sortDir = 'asc'; }
    }

    public function render()
    {
        $products = Product::with('category') 
            ->when($this->search, function($q){
                $q->where('name','like','%'.$this->search.'%')
                  ->orWhereHas('category', fn($c)=>$c->where('name','like','%'.$this->search.'%'));
            })
            ->when($this->categoryId, fn($q)=>$q->where('category_id',$this->categoryId))
            ->orderBy($this->sortField, $this->sortDir)
            ->paginate(10);

        return view('livewire.products-index',[
            'products'=>$products,
            'categories'=>Category::orderBy('name')->get()
        ]);
    }

    public function emitConfirmDelete($id)
    {
        $this->dispatch('confirmDelete', id: $id); 
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('delete', $product);
        $product->delete();
        session()->flash('success', 'Product moved to Trash');
    }


}

