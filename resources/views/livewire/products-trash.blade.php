<div class="container mx-auto p-6">
  @if (session('success'))
    <div class="bg-green-100 p-2 rounded">{{ session('success') }}</div>
  @endif

  <a href="{{ route('products.index') }}" class="underline">← Back</a>

  <table class="w-full">
    <thead>
      <tr>
        <th>Name</th>
        <th>Category</th>
        <th>Deleted At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    @foreach($products as $p)
      <tr class="border-b">
        <td>{{ $p->name }}</td>
        <td>{{ $p->category->name ?? '-' }}</td>
        <td>{{ $p->deleted_at }}</td>
        <td class="space-x-2">
          @can('restore', $p)
            <button 
              wire:click="restore({{ $p->id }})" 
              wire:confirm="Are you sure you want to restore this product?"
              class="underline">
              Restore
            </button>
          @endcan

        @can('forceDelete', $p)
            <button 
                wire:click="destroyForever({{ $p->id }})" 
                wire:confirm.prompt="Type DELETE to confirm|DELETE"
                class="text-red-600 underline">
                Force Delete
            </button>
        @endcan
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <div class="mt-4">
    {{ $products->links() }}
  </div>
</div>
