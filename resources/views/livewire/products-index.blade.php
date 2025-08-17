<div class="container mx-auto p-6">
  @if (session('success'))
    <div class="bg-green-100 p-2 rounded">{{ session('success') }}</div>
    <br>
  @endif
  <div class="flex items-center gap-2">
    <input type="text" placeholder="Search name or category..."
           wire:model.debounce.500ms="search"
           class="border p-2 rounded w-64">
    <select wire:model="categoryId" class="border p-2 rounded">
      <option value="">All Categories</option>
      @foreach($categories as $c)
        <option value="{{ $c->id }}">{{ $c->name }}</option>
      @endforeach
    </select>
    @can('create', App\Models\Product::class)
      <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-3 py-2 rounded"
         wire:loading.attr="disabled">+ New</a>
    @endcan
    <a href="{{ route('products.trash') }}" class="ml-auto underline">Trash</a>
  </div>

  <div wire:loading>Loading...</div>

  <table class="w-full border-collapse">
    <thead>
      <tr class="border-b">
        <th class="text-left py-2 cursor-pointer" wire:click="sortBy('name')">Name</th>
        <th class="text-left py-2">Category</th>
        <th class="text-left py-2 cursor-pointer" wire:click="sortBy('price')">Price</th>
        <th class="text-left py-2 cursor-pointer" wire:click="sortBy('stock')">Stock</th>
        <th class="text-left py-2">Image</th>
        <th class="text-left py-2">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $p)
        <tr class="border-b">
          <td class="py-2">{{ $p->name }}</td>
          <td class="py-2">{{ $p->category->name }}</td>
          <td class="py-2">Rp {{ number_format($p->price,0,',','.') }}</td>
          <td class="py-2">
            @php
              $badge = $p->stock == 0 ? 'bg-red-100 text-red-700' :
                       ($p->stock < 10 ? 'bg-yellow-100 text-yellow-700' :
                                         'bg-green-100 text-green-700');
            @endphp
            <span class="px-2 py-1 rounded {{ $badge }}">{{ $p->stock }}</span>
          </td>
          <td class="py-2">
            @if($p->image_path)
              <img src="{{ Storage::url($p->image_path) }}" class="h-10">
            @endif
          </td>
          <td class="py-2">
            <a href="{{ route('products.show',$p) }}" class="underline">View</a>
            @can('update',$p)
              | <a href="{{ route('products.edit',$p) }}" class="underline">Edit</a>
            @endcan
            <td class="py-2">
              @can('delete', $p)
                <button
                  type="button"
                  wire:click="delete({{ $p->id }})"
                  wire:confirm="Are you sure you want to delete this product?"
                  class="text-red-600"
                >
                  Delete
                </button>
              @endcan
            </td>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div>{{ $products->links() }}</div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:confirmDelete', event => {
        if (confirm('Delete this product?')) {
            Livewire.dispatch('deleteConfirmed', { id: event.detail.id });
        }
    });
</script>
@endpush
