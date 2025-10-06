<style>
  [x-cloak] { display: none; }
  .inner-swiper .swiper-wrapper {
      display: flex;
      position: relative;
      width: 100%;
      height: 100%;
  }
  .inner-swiper .swiper-slide {
      flex-shrink: 0;
      width: 100%;
      height: 100%;
      position: relative;
  }
  .inner-swiper .swiper-pagination {
    bottom: 10px !important;
  }
  .inner-swiper .swiper-pagination-bullet {
    background-color: #185D31 !important;
    opacity: 0.5 !important;
  }
  .inner-swiper .swiper-pagination-bullet-active {
    opacity: 1 !important;
  }
</style>

<div class="bg-white p-[64px]">
    <div class="flex flex-col md:flex-row justify-between">
        <h2 class="text-[40px] font-bold mb-4">{{ __('messages.myOffers') }}</h2>

        <div class="flex items-center space-x-4 mb-6">
            <a href="{{ route('offers.create') }}" class="flex bg-[#185D31] text-white px-4 py-2 rounded-xl items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor" class="size-6 rtl:ml-2 ltr:mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('messages.addOffer') }}
            </a>
        </div>
    </div>

    @if ($offers->isEmpty())
        <div class="flex flex-col items-center p-4">
            <img src="{{ asset('/images/Chats illustration.svg') }}" alt="">
            <p class="mt-4 text-[24px] text-[#696969]">{{ __('messages.no_offers') }}</p>
        </div>
    @else

            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="offers-grid">
                @foreach ($offers as $offer)
                    <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                        {{-- Product Image Carousel (Inner Swiper) --}}
                        <div class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
                                 <div class="swiper-wrapper">
                                @php
                                    $images = collect(is_string($offer->product->images) ? json_decode($offer->product->images, true) : ($offer->product->images ?? []));
                                @endphp

                                @forelse ($images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ Storage::url($image) }}"
                                             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                             class="w-full h-full object-contain bg-[#F8F9FA]">
                                    </div>
                                @empty
                                    <div class="swiper-slide">
                                        <img src="{{ asset($offer->product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                             class="w-full h-full object-contain bg-[#F8F9FA]">
                                    </div>
                                @endforelse
                            </div>
                            {{-- Inner Swiper Pagination --}}
                            @php
                                $images = is_string($offer->product->images) ? json_decode($offer->product->images, true) : ($offer->product->images ?? []);
                            @endphp

                            <div class="swiper-pagination image-pagination"
                                style="{{ count($images) <= 1 ? 'display:none;' : '' }}"></div>


                            {{-- DISCOUNT BADGE - MOVED HERE --}}
                            @if ($offer->discount_percent)
                                <span
                                    class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                    {{-- Translated: Discount X % --}}
                                    {{ __('messages.discount_percentage', ['percent' => $offer->discount_percent]) }}
                                </span>
                            @endif
                            {{-- FAVORITE BUTTON - MOVED HERE --}}
                             @if (!Auth::check() || Auth::user()->account_type !== 'supplier')
                            <button
                                class="offer-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-gray-500 hover:text-red-500 transition-colors duration-200 z-10"
                                data-product-id="{{ $offer->product->id }}" aria-label="Add to offers">
                                {{-- Conditional SVG for filled/unfilled heart --}}
                                @if (Auth::check() && Auth::user()->hasFavorited($offer->product->id))
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
                            @endif
                        </div> {{-- End of product-image-swiper --}}

                        {{-- Product Details --}}
                        <div class="p-4 flex flex-col flex-grow">
                            <div class="flex w-full items-center text-sm mb-2 justify-between">
<h3 class="text-[24px] font-bold text-[#212121] mb-1">
    {{ app()->getLocale() === 'en' ? $offer->product->name_en : $offer->product->name }}
</h3>
                                <div class="flex items-center ">
                                    @if($offer->product->rating)
                                        <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                    @endif
                                    <span class="text-[18px]">{{ $offer->product->rating }}</span>
                                </div>
                            </div>
                       <span class="text-[#696969] text-[20px]">
    {{ app()->getLocale() === 'ar' 
        ? ($offer->product->subCategory->category->name ?? 'غير مصنف') 
        : ($offer->product->subCategory->category->name_en ?? 'Uncategorized') }}
</span>
 <div class="flex mt-2">
                                @if ($offer->product->supplier->supplier_confirmed)
                                    <span class="flex items-center text-[#185D31]">
                                        <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                            src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                        <p class="text-[20px] text-[#212121] ">{{ $offer->product->supplier->company_name }}</p>
                                    </span>
                                @else
                                    <p class="text-[20px] text-[#212121]">{{ $offer->product->supplier->company_name }}</p>
                                @endif
                            </div>
                            <div class="flex items-center mb-2">
                             <span class="flex text-lg font-bold text-gray-800">
    {{ number_format($offer->product->price_range['min'], 2) }}
    @if($offer->product->price_range['min'] != $offer->product->price_range['max'])
        - {{ number_format($offer->product->price_range['max'], 2) }}
    @endif
    <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}" alt="">
</span>

                                @if ($offer->offer_start && $offer->discount_percent)
                                    <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                        {{ number_format($offer->product->price, 2) }}
                                         <img class="mx-1 w-[14px] h-[14px] mt-1 inline-block"
                                            src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="currency">
                                    </span>
                                @endif
                            </div>

                            {{-- Translated: Minimum Order --}}
                            <p class="text-sm text-gray-600 mb-4">
                                {{ __('messages.minimum_order_quantity', ['quantity' => $offer->product->min_order_quantity ?? '1']) }}
                            </p>

                           <div class="mt-auto flex justify-between">
                                <div class="w-2/3 h-full">
                                    <a href="{{ route('products.show', $offer->product->slug) }}"
                                       class="block bg-[#185D31] text-white text-center py-[10px] px-[15px] rounded-[12px] font-medium transition-colors duration-200">
                                        {{-- Translated: View Details --}}
                                        {{ __('messages.view_details') }}
                                    </a>
                                </div>
                                <div class="flex justify-between w-1/3 gap-1 h-full rtl:mr-2 ltr:ml-2">
                                    <a href="{{ route('offers.edit', $offer->id) }}"
                                       class="flex-1 flex items-center justify-center gap-1 text-center text-[#185D31] py-2 bg-[#EDEDED] rounded-xl transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>

                                    <button type="button" @click="confirmingId = {{ $offer->id }}"
                                            class="flex-1 flex items-center justify-center bg-[#EDEDED] gap-1 text-center text-[#185D31] py-2 rounded-xl transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
                 <div class="mt-8 flex justify-center " id="offers-pagination-links">
    {{ $offers->links() }}
            </div>
    @endif

</div>
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.inner-swiper').forEach(el => {
            new Swiper(el, {
                loop: true,
                pagination: {
                    el: el.querySelector('.swiper-pagination'),
                    clickable: true
                },
            });
        });-
    });
</script>
