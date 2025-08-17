<x-app-layout>
    <div class="container mx-auto p-6">
        <div class="bg-white shadow rounded-xl p-6 space-y-4">
            <h1 class="text-xl font-bold">{{ $product->name }}</h1>
            <p>Category: {{ $product->category->name }}</p>
            <p>Price: Rp {{ number_format($product->price,0,',','.') }}</p>
            <p>Stock: {{ $product->stock }}</p>
            <p>Description: {{ $product->description }}</p>
            @if($product->image_path)
                <img src="{{ Storage::url($product->image_path) }}" class="h-48 mt-4">
            @endif

            <a href="{{ route('products.index') }}" class="text-blue-600 underline">← Back</a>
        </div>
    </div>
</x-app-layout>
