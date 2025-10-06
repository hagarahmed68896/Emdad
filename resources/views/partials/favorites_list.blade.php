{{-- This partial will be rendered when the page loads, and also via AJAX --}}
@if ($favorites->isEmpty())
<div class="flex flex-col justify-center items-center w-full py-10 text-gray-600 empty-state">
    <img src="{{ asset('images/Illustrations.svg') }}" alt="{{ __('messages.no_favorites_illustration') }}" class=" mb-10 ">
    <p class="text-[#696969] text-[24px]">{{ __('messages.no_favorites_message') }}</p>
    <a href="{{ route('products.index') ?? '/' }}" 
       class="mt-6 inline-block bg-[#185D31] text-white py-2 px-4 rounded-xl hover:bg-[#154a2a] transition-colors">
        {{ __('messages.browse_products') }}
    </a>
</div>

@else
    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="favorites-grid">
        @foreach ($favorites as $favorite)
            {{-- Added product-card class for easier JS selection --}}
            <div class="bg-white rounded-xl overflow-hidden shadow-md flex flex-col product-card">
                {{-- Product Image --}}
<div class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
    <div class="swiper-wrapper">
        @php
            $images = collect(
                is_string($favorite->product->images)
                    ? json_decode($favorite->product->images, true)
                    : $favorite->product->images ?? []
            );
        @endphp

          @forelse ($images as $image)
            <div class="swiper-slide flex items-center justify-center bg-[#F8F9FA]">
                <img src="{{ asset('storage/' . $image) }}"
                     onerror="this.onerror=null;this.src='https://placehold.co/600x400/F0F0F0/ADADAD?text=Image+Error';"
                     class="max-h-56 w-auto object-contain p-2">
            </div>
        @empty
            <div class="swiper-slide flex items-center justify-center bg-[#F8F9FA]">
                <img src="{{ $favorite->product && $favorite->product->image 
                                ? asset('storage/' . $favorite->product->image) 
                                : 'https://placehold.co/600x400/F0F0F0/ADADAD?text=No+Image' }}"
                     onerror="this.onerror=null;this.src='https://placehold.co/600x400/F0F0F0/ADADAD?text=Image+Error';"
                     class="max-h-56 w-auto object-contain p-2">
            </div>
        @endforelse
    </div>

    {{-- DISCOUNT BADGE --}}
    @if ($favorite->product->offer && $favorite->product->offer->discount_percent)
        <span class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
            {{ __('messages.discount_percentage', ['percent' => $favorite->product->offer->discount_percent]) }}
        </span>
    @endif

    {{-- FAVORITE BUTTON --}}
    <button
        class="favorite-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-gray-500 hover:text-red-500 transition-colors duration-200 z-10"
        data-product-id="{{ $favorite->product->id }}" aria-label="Add to favorites">
        @if (Auth::check() && Auth::user()->hasFavorited($favorite->product->id))
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-6 text-red-500">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
            </svg>
        @endif
    </button>
</div>


                {{-- Product Details --}}
                <div class="p-4 flex flex-col flex-grow">
           <div class="flex w-full items-center text-sm mb-2 justify-between">
<h3 class="text-[24px] font-bold text-[#212121] mb-1">
    {{ app()->getLocale() === 'en' ? $favorite->product->name_en : $favorite->product->name }}
</h3>
                           @php
    $averageRating = round($favorite->product->reviews->avg('rating'), 1);
@endphp

@if($averageRating > 0)
    <div class="flex items-center">
        <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
        <span class="text-[18px]">{{ $averageRating }}</span>
    </div>
@endif

                            </div>
<span class="text-[#696969] text-[20px]">
    {{ app()->getLocale() === 'ar' 
        ? ($favorite->product->subCategory->category->name ?? 'غير مصنف') 
        : ($favorite->product->subCategory->category->name_en ?? 'Uncategorized') }}
</span>

                    <div class="flex mt-2">
                        @if ($favorite->product->supplier->supplier_confirmed)
                            <span class="flex items-center text-[#185D31]">
                                <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                    src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                <p class="text-[20px] text-[#212121] ">{{ $favorite->product->supplier->company_name }}</p>
                            </span>
                        @else
                            <p class="text-[20px] text-[#212121]">{{ $favorite->product->supplier->company_name }}</p>
                        @endif
                    </div>
                    <div class="flex items-center mb-3">
                     <span class="flex text-lg font-bold text-gray-800">
    {{ number_format($favorite->product->price_range['min'], 2) }}
    @if($favorite->product->price_range['min'] != $favorite->product->price_range['max'])
        - {{ number_format($favorite->product->price_range['max'], 2) }}
    @endif
    <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}" alt="">
</span>

   @php
                                    $offer = $favorite->product->offer; // Relationship: Product hasOne Offer
                                @endphp

                                @if ($offer && $offer->discount_percent)                            <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                {{ number_format($favorite->product->price, 2) }}
                                   <img class="mx-1 w-[14px] h-[14px] mt-1 inline-block"
                                            src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="currency">
                            </span>
                        @endif
                    </div>

                 <p class="text-sm text-gray-600 mb-4">
    {{ __('messages.minimum_order_quantity', ['quantity' => $favorite->product->min_order_quantity ?? 1]) }}
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
    {{-- <div class="mt-8 flex justify-center" id="favorites-pagination-links">
        {{ $favorites->links() }}
    </div> --}}
@endif