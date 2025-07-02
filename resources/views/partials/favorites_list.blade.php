{{-- This partial will be rendered when the page loads, and also via AJAX --}}
@if ($favorites->isEmpty())
    <div class="flex flex-col justify-center items-center w-full py-10 text-gray-600 empty-state">
        <img src="{{ asset('images/Illustrations.svg') }}" alt="No favorites illustration" class=" mb-10 ">
        <p class="text-[#696969] text-[24px]">لم تقم باضافة أي منتج الي المفضلة بعد</p>
        <a href="{{ route('products.index') ?? '/' }}" class="mt-6 inline-block bg-[#185D31] text-white py-2 px-4 rounded-xl hover:bg-[#154a2a] transition-colors">
            تصفح المنتجات
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="favorites-grid">
        @foreach ($favorites as $favorite)
            {{-- Added product-card class for easier JS selection --}}
            <div class="bg-white rounded-xl overflow-hidden shadow-md flex flex-col product-card">
                {{-- Product Image --}}
                <div class="relative w-full h-48 sm:h-56 overflow-hidden">
                    <img src="{{ asset($favorite->product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                        alt="{{ $favorite->product->name }}"
                        onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                        class="w-full h-full object-contain">

                    {{-- Favorite Button (with active state for easy removal) --}}
                    <button
                        class="favorite-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-red-500 hover:text-gray-500 transition-colors duration-200 z-10"
                        data-product-id="{{ $favorite->product->id }}" aria-label="Remove from favorites">
                        {{-- Always display filled heart for favorited products --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="size-6 text-red-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                        </svg>
                    </button>
                </div>

                {{-- Product Details --}}
                <div class="p-4 flex flex-col flex-grow">
                    <div class="flex w-full items-center text-sm mb-2 justify-between">
                        <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $favorite->product->name }}</h3>
                        <div class="flex items-center ">
                            <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                            <span class="text-[18px]">{{ $favorite->product->rating ?? '4.5' }}</span>
                        </div>
                    </div>
                    <span
                        class="text-[#696969] text-[20px]">{{ $favorite->product->subCategory->category->name ?? 'غير مصنف' }}</span>
                    <div class="flex mt-2">
                        @if ($favorite->product->supplier_confirmed)
                            <span class="flex items-center text-[#185D31]">
                                <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                    src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                <p class="text-[20px] text-[#212121] ">{{ $favorite->product->supplier_name }}</p>
                            </span>
                        @else
                            <p class="text-[20px] text-[#212121] mb-3">{{ $favorite->product->supplier_name }}</p>
                        @endif
                    </div>
                    <div class="flex items-center mb-3">
                        <span class=" flex text-lg font-bold text-gray-800">
                            {{ number_format($favorite->product->price * (1 - ($favorite->product->discount_percent ?? 0) / 100), 2) }}
                            <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}"
                                alt="">
                        </span>
                        @if ($favorite->product->is_offer && $favorite->product->discount_percent)
                            <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                {{ number_format($favorite->product->price, 2) }}
                                <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}"
                                    alt="">
                            </span>
                        @endif
                    </div>

                    <p class="text-sm text-gray-600 mb-4">
                        الحد الأدنى للطلب: {{ $favorite->product->min_order_quantity ?? '1' }} قطعة
                    </p>

                    <div class="mt-auto">
                        <a href="{{ route('products.show', $favorite->product->slug) }}"
                            class="block w-full bg-[#185D31] text-white text-center py-[10px] px-[16px] rounded-[12px] font-medium transition-colors duration-200">
                           {{ __('messages.show_details') }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- PAGINATION LINKS HERE --}}
    <div class="mt-8 flex justify-center" id="favorites-pagination-links">
        {{ $favorites->links() }}
    </div>
@endif