<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class ProductsIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryId = '';
    public $sortField = 'name';
    public $sortDir = 'asc';
    public $selectedProducts = [];
    public $selectAll = false;
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryId' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDir' => ['except' => 'asc'],
        'perPage' => ['except' => 10],
        'page' => ['except' => 1]
    ];
    
    protected $listeners = ['deleteConfirmed' => 'delete'];

    public function updatingSearch() 
    { 
        $this->resetPage(); 
    }
    
    public function updatingCategoryId() 
    { 
        $this->resetPage(); 
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
    
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->categoryId = '';
        $this->sortField = 'name';
        $this->sortDir = 'asc';
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Get current page product IDs
            $currentPageIds = $this->products->pluck('id')->map(fn($id) => (string) $id)->toArray();
            $this->selectedProducts = $currentPageIds;
        } else {
            $this->selectedProducts = [];
        }
    }

    public function updatedSelectedProducts()
    {
        // Check if all current page products are selected
        $currentPageIds = $this->products->pluck('id')->map(fn($id) => (string) $id)->toArray();
        
        $this->selectAll = !empty($currentPageIds) && 
                          count(array_intersect($currentPageIds, $this->selectedProducts)) === count($currentPageIds);
    }

    private function buildQuery()
    {
        $query = Product::with('category');

        // Apply search filter - simplified for testing
        if (!empty($this->search)) {
            Log::info('Applying search filter: ' . $this->search);
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Apply category filter
        if (!empty($this->categoryId)) {
            Log::info('Applying category filter: ' . $this->categoryId);
            $query->where('category_id', $this->categoryId);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDir);

        return $query;
    }

    public function bulkDelete()
    {
        if (empty($this->selectedProducts)) {
            return;
        }

        $products = Product::whereIn('id', $this->selectedProducts)->get();
        $deletedCount = 0;
        
        foreach ($products as $product) {
            try {
                if ($this->authorize('delete', $product)) {
                    $product->delete();
                    $deletedCount++;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        $this->selectedProducts = [];
        $this->selectAll = false;
        
        if ($deletedCount > 0) {
            session()->flash('success', $deletedCount . ' product(s) moved to trash');
        } else {
            session()->flash('error', 'No products were deleted. You may not have permission to delete these products.');
        }
    }

    public function bulkMoveToTrash()
    {
        $this->bulkDelete(); // Same functionality
    }

    public function clearSelection()
    {
        $this->selectedProducts = [];
        $this->selectAll = false;
    }

    public function render()
    {
        Log::info('Render method called with search: ' . $this->search . ', categoryId: ' . $this->categoryId);
        
        $query = $this->buildQuery();
        $products = $query->paginate($this->perPage);
        $categories = Category::orderBy('name')->get();
        
        // Get counts for filters
        $totalProducts = Product::count();
        $filteredCount = $query->count();

        Log::info('Query results - Total: ' . $totalProducts . ', Filtered: ' . $filteredCount . ', Products count: ' . $products->total());

        return view('livewire.products-index', [
            'products' => $products,
            'categories' => $categories,
            'totalProducts' => $totalProducts,
            'filteredCount' => $filteredCount
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