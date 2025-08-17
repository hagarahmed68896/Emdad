@extends('layouts.app') {{-- Assuming you have a layout file --}}

@section('content')
    <h2 class="text-2xl font-semibold mb-4">
        @if (!empty($query))
            Search Results for "{{ $query }}"
        @else
            Search Results
        @endif
    </h2>
    

    @if ($results->isEmpty())
        <p class="text-gray-600">No results found matching your search criteria.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($results as $item)
                @if ($item['type'] === 'product')
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="{{ $item['data']->image ? asset('storage/' . $item['data']->image) : 'https://via.placeholder.com/300' }}"
                             alt="{{ $item['data']->name }}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2">{{ $item['data']->name }}</h3>
                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($item['data']->description, 70) }}</p>
                            <p class="text-xl font-bold text-blue-600 mb-4">${{ number_format($item['data']->price, 2) }}</p>
                            <span class="text-xs text-gray-500 font-bold">Product</span>
                            <a href="{{ route('products.show', $item['data']->slug ?? $item['data']->id) }}"
                               class="block bg-blue-600 text-white text-center py-2 rounded-md hover:bg-blue-700 mt-2">View Details</a>
                        </div>
                    </div>
                @elseif ($item['type'] === 'supplier')
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4">
                            <h3 class="text-lg font-semibold mb-2">{{ $item['data']->name }}</h3>
                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($item['data']->description, 70) }}</p>
                            <p class="text-md text-gray-700 mb-2">Email: {{ $item['data']->email }}</p>
                            <p class="text-md text-gray-700 mb-4">Phone: {{ $item['data']->phone }}</p>
                            <span class="text-xs text-gray-500 font-bold">Supplier</span>
                            <a href="{{ route('suppliers.show', $item['data']->id) }}" {{-- Assuming a supplier details route --}}
                               class="block bg-green-600 text-white text-center py-2 rounded-md hover:bg-green-700 mt-2">View Profile</a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- If you implement manual pagination for combined results, add it here --}}
        {{-- For instance, if you pass a paginator for each type and display them separately. --}}
    @endif
@endsection