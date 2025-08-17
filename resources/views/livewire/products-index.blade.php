<div class="container mx-auto p-6">
    @if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif


    <!-- Bulk Actions Bar -->
    @if(count($selectedProducts) > 0)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <span class="text-sm font-medium text-blue-800">
                    {{ count($selectedProducts) }} product(s) selected
                </span>
            </div>
            <div class="flex items-center space-x-2">
                <button type="button" wire:click="bulkDelete"
                    wire:confirm="Are you sure you want to move {{ count($selectedProducts) }} product(s) to trash?"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm transition-colors duration-200">
                    Move to Trash
                </button>
                <button type="button" wire:click="clearSelection"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded text-sm transition-colors duration-200">
                    Clear Selection
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Search and Filters Section -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search Input -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Products</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" placeholder="Search by name, description, or category..."
                        wire:model.live.debounce.500ms="search"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <p class="mt-1 text-xs text-gray-500">Search in product names, descriptions, and categories</p>
                <button wire:click="$set('search', 'test')"
                    class="mt-1 px-2 py-1 bg-green-500 text-white text-xs rounded">Test Search</button>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select wire:model.live="categoryId"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Per Page Selector -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Per Page</label>
                <select wire:model.live="perPage"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md leading-5 bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- Filter Actions and Results Count -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center space-x-4">
                <button type="button" wire:click="clearFilters"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    Clear Filters
                </button>

                @if($search || $categoryId)
                <span class="text-sm text-gray-600">
                    Showing {{ $filteredCount }} of {{ $totalProducts }} products
                </span>
                @else
                <span class="text-sm text-gray-600">
                    Total: {{ $totalProducts }} products
                </span>
                @endif
            </div>

            <div class="flex items-center space-x-2">
                @can('create', App\Models\Product::class)
                <a href="{{ route('products.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Product
                </a>
                @endcan
                <a href="{{ route('products.trash') }}"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    View Trash
                </a>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading class="text-center py-8">
        <div class="inline-flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-lg text-gray-600">Loading products...</span>
        </div>
    </div>

    <!-- Products Table -->
    <div wire:loading.remove class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <!-- <th class="text-left py-3 px-4">
                        <input type="checkbox" wire:model="selectAll"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </th> -->
                    <th class="text-left py-3 px-4 cursor-pointer hover:bg-gray-100 transition-colors duration-150"
                        wire:click="sortBy('name')">
                        <div class="flex items-center">
                            Name
                            @if($sortField === 'name')
                            <svg class="w-4 h-4 ml-1 {{ $sortDir === 'asc' ? 'rotate-0' : 'rotate-180' }} transition-transform duration-200"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            @endif
                        </div>
                    </th>
                    <th class="text-left py-3 px-4">Category</th>
                    <th class="text-left py-3 px-4 cursor-pointer hover:bg-gray-100 transition-colors duration-150"
                        wire:click="sortBy('price')">
                        <div class="flex items-center">
                            Price
                            @if($sortField === 'price')
                            <svg class="w-4 h-4 ml-1 {{ $sortDir === 'asc' ? 'rotate-0' : 'rotate-180' }} transition-transform duration-200"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            @endif
                        </div>
                    </th>
                    <th class="text-left py-3 px-4 cursor-pointer hover:bg-gray-100 transition-colors duration-150"
                        wire:click="sortBy('stock')">
                        <div class="flex items-center">
                            Stock
                            @if($sortField === 'stock')
                            <svg class="w-4 h-4 ml-1 {{ $sortDir === 'asc' ? 'rotate-0' : 'rotate-180' }} transition-transform duration-200"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            @endif
                        </div>
                    </th>
                    <th class="text-left py-3 px-4">Image</th>
                    <th class="text-left py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $p)
                <tr class="border-b hover:bg-gray-50 transition-colors duration-150">
                    <!-- <td class="py-3 px-4">
                        <input type="checkbox" wire:model="selectedProducts" value="{{ $p->id }}"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </td> -->
                    <td class="py-3 px-4">
                        <div>
                            <div class="font-medium text-gray-900">{{ $p->name }}</div>
                            @if($p->description)
                            <div class="text-sm text-gray-500 truncate max-w-xs">{{ Str::limit($p->description, 50) }}
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $p->category->name }}
                        </span>
                    </td>
                    <td class="py-3 px-4 font-mono text-gray-900">Rp {{ number_format($p->price,0,',','.') }}</td>
                    <td class="py-3 px-4">
                        @php
                        $badge = $p->stock == 0 ? 'bg-red-100 text-red-700' :
                        ($p->stock < 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' ); @endphp
                            <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                            {{ $p->stock }}
                            </span>
                    </td>
                    <td class="py-3 px-4">
                        @if($p->image_path)
                        <img src="{{ Storage::url($p->image_path) }}" class="h-10 w-10 object-cover rounded border">
                        @else
                        <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('products.show',$p) }}"
                                class="text-blue-600 hover:text-blue-800 underline text-sm">View</a>
                            @can('update',$p)
                            <a href="{{ route('products.edit',$p) }}"
                                class="text-green-600 hover:text-green-800 underline text-sm">Edit</a>
                            @endcan
                            @can('delete', $p)
                            <button type="button" wire:click="delete({{ $p->id }})"
                                wire:confirm="Are you sure you want to delete this product?"
                                class="text-red-600 hover:text-red-800 underline text-sm">
                                Delete
                            </button>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 px-4 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-xl font-medium text-gray-900 mb-2">No products found</p>
                            <p class="text-gray-500 mb-4">
                                @if($search || $categoryId)
                                No products match your current search criteria. Try adjusting your filters.
                                @else
                                Get started by creating your first product.
                                @endif
                            </p>
                            @if($search || $categoryId)
                            <button wire:click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Clear All Filters
                            </button>
                            @else
                            @can('create', App\Models\Product::class)
                            <a href="{{ route('products.create') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Create First Product
                            </a>
                            @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="mt-6">
        {{ $products->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('livewire:confirmDelete', event => {
    if (confirm('Delete this product?')) {
        Livewire.dispatch('deleteConfirmed', {
            id: event.detail.id
        });
    }
});
</script>
@endpush