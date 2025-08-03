<div class="bg-white p-[64px]">
    <div class="flex flex-col md:flex-row justify-between">
        {{-- Supplier Products Section --}}
        <h2 class="text-[40px] font-bold  mb-4">{{ __('messages.myProducts') }}</h2>
          @if (isset($products) && $products->isEmpty())

        <div class="flex items-center space-x-4 mb-6">
            <a href="{{route('products.create')}}" class="flex bg-[#185D31] text-white px-4 py-2 rounded-xl items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 rtl:ml-2 ltr:mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('messages.add_product') }}
            </a>
        </div>
        @endif
    </div>
 @if (isset($products) && $products->isEmpty())

        <div class="flex flex-col items-center p-4">
    <img src="{{ asset('/images/Chats illustration.svg') }}" alt="">
    <p class="mt-4 text-[24px] text-[#696969]">{{ __('messages.no_products') }}</p>
</div>
@else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($products as $product)
                <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition-shadow duration-300">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded mb-4">
                    <h3 class="text-xl font-semibold mb-2">{{ $product->name }}</h3>
                    <p class="text-gray-600 mb-2">{{ $product->description }}</p>
                    <p class="text-green-600 font-bold mb-4">${{ number_format($product->price, 2) }}</p>
                    <a href="{{ route('supplier.products.edit', $product->id) }}" class="text-blue-500 hover:underline">Edit</a>
                </div>
            @endforeach
        </div>
    @endif

        
    </div>
</div>
