<div class="container mx-auto p-6">
  <form wire:submit.prevent="save" class="space-y-6 max-w-2xl">
    @if (session('success'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
    @endif

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
      <input 
        type="text" 
        wire:model.live="name" 
        class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
        placeholder="Enter product name"
      >
      @error('name') 
        <div class="text-red-600 text-sm mt-1 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          {{ $message }}
        </div> 
      @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Price</label>
        <input 
          type="number" 
          step="0.01" 
          wire:model.live="price" 
          class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror"
          placeholder="0.00"
        >
        @error('price') 
          <div class="text-red-600 text-sm mt-1 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            {{ $message }}
          </div> 
        @enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Stock</label>
        <input 
          type="number" 
          wire:model.live="stock" 
          class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stock') border-red-500 @enderror"
          placeholder="0"
        >
        @error('stock') 
          <div class="text-red-600 text-sm mt-1 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            {{ $message }}
          </div> 
        @enderror
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
      <select 
        wire:model.live="category_id" 
        class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror"
      >
        <option value="">-- Choose Category --</option>
        @foreach($categories as $c)
          <option value="{{ $c->id }}">{{ $c->name }}</option>
        @endforeach
      </select>
      @error('category_id') 
        <div class="text-red-600 text-sm mt-1 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          {{ $message }}
        </div> 
      @enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
      <textarea 
        wire:model.live="description" 
        rows="4"
        class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
        placeholder="Enter product description (optional)"
      ></textarea>
      @error('description') 
        <div class="text-red-600 text-sm mt-1 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          {{ $message }}
        </div> 
      @enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Image (max 1MB)</label>
      <input 
        type="file" 
        wire:model.live="image" 
        accept="image/*"
        class="border p-3 w-full rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('image') border-red-500 @enderror"
      >
      <div wire:loading wire:target="image" class="text-blue-600 text-sm mt-1">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Uploading...
      </div>
      @error('image') 
        <div class="text-red-600 text-sm mt-1 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
          </svg>
          {{ $message }}
        </div> 
      @enderror

      @if ($image)
        <div class="mt-3">
          <span class="text-sm text-gray-600">Preview:</span>
          <img src="{{ $image->temporaryUrl() }}" class="h-24 w-24 object-cover rounded border mt-1">
        </div>
      @elseif($product && $product->image_path)
        <div class="mt-3">
          <span class="text-sm text-gray-600">Current Image:</span>
          <img src="{{ Storage::url($product->image_path) }}" class="h-24 w-24 object-cover rounded border mt-1">
        </div>
      @endif
    </div>

    <div class="flex items-center space-x-4">
      <button 
        type="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded font-medium transition-colors duration-200"
        wire:loading.attr="disabled"
        wire:loading.class="opacity-50 cursor-not-allowed"
      >
        <span wire:loading.remove wire:target="save">
          {{ $mode === 'edit' ? 'Update Product' : 'Create Product' }}
        </span>
        <span wire:loading wire:target="save">
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          {{ $mode === 'edit' ? 'Updating...' : 'Creating...' }}
        </span>
      </button>
      
      <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-800 underline">
        Cancel
      </a>
    </div>
  </form>
</div>
