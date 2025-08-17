<div class="container mx-auto p-6">
  <form wire:submit.prevent="save" class="space-y-4 max-w-xl">
    @if (session('success'))
      <div class="bg-green-100 p-2 rounded">{{ session('success') }}</div>
    @endif

    <div>
      <label>Name</label>
      <input type="text" wire:model.lazy="name" class="border p-2 w-full rounded">
      @error('name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label>Price</label>
        <input type="number" step="0.01" wire:model.lazy="price" class="border p-2 w-full rounded">
        @error('price') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
      </div>
      <div>
        <label>Stock</label>
        <input type="number" wire:model.lazy="stock" class="border p-2 w-full rounded">
        @error('stock') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
      </div>
    </div>

    <div>
      <label>Category</label>
      <select wire:model="category_id" class="border p-2 w-full rounded">
        <option value="">-- choose --</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </select>
      @error('category_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
      <label>Description</label>
      <textarea wire:model.lazy="description" class="border p-2 w-full rounded"></textarea>
    </div>

    <div>
      <label>Image (max 1MB)</label>
      <input type="file" wire:model="image" accept="image/*">
      <div wire:loading wire:target="image">Uploading...</div>
      @error('image') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

      @if ($image)
        <div class="mt-2">
          <span class="text-sm">Preview:</span>
          <img src="{{ $image->temporaryUrl() }}" class="h-24 rounded">
        </div>
      @elseif($product && $product->image_path)
        <img src="{{ Storage::url($product->image_path) }}" class="h-24 rounded mt-2">
      @endif
    </div>

    <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded"
            wire:loading.attr="disabled">
      {{ $mode === 'edit' ? 'Update' : 'Create' }}
    </button>
    <span wire:loading class="ml-2">Saving...</span>
  </form>
</div>
