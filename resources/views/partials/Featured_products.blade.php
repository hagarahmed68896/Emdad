<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<section class="px-[64px] py-8 font-inter">
<div class="flex sm:flex-row flex-col justify-between mt-1">
    <div class="flex flex-col justify-between mb-5 mt-3">
        <p class="bg-[#F3F4F5] rounded-[40px] px-[16px] py-[8px] w-[97px] text-[16px] mb-4 text-[#1F2B45]">
            {{ $siteTexts['chosen_for_you'] ?? __('messages.chosen_for_you') }}
        </p>
        <h2 class="text-3xl md:text-[40px] font-bold mb-4 md:mb-0">
            {{ $siteTexts['discover_our_products'] ?? __('messages.discover_our_products') }}
        </h2>
        <p class="text-[#696969] text-[20px]">
            {{ $siteTexts['discover_our_products_description'] ?? __('messages.discover_our_products_description') }}
        </p>
    </div>

    <a href="{{ route('products.index') }}"
       class="text-[#FFFFFF] bg-[#185D31] text-[16px] px-4 py-3 mb-1 rounded-[12px]
              h-12 w-[140px]
              flex items-center justify-center
              hover:bg-green-700 transition-colors duration-200">
        {{ $siteTexts['show_more'] ?? __('messages.show_more') }}
    </a>
</div>

 

    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="favorites-grid">
        @foreach ($featuredProducts as $featuredProduct)
            
            
                    <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                        {{-- Product Image Carousel (Inner Swiper) --}}
                        <div class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
                                <div class="swiper-wrapper">
                                @php
                                    $images = collect(is_string($featuredProduct->images) ? json_decode($featuredProduct->images, true) : ($featuredProduct->images ?? []));
                                @endphp

                           @forelse ($images as $image)
    <div class="swiper-slide">
        <img src="{{ asset('storage/' . $image) }}"
             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
             class="w-full h-full object-contain">
    </div>
@empty
    <div class="swiper-slide">
        <img src="{{ $featuredProduct->product && $featuredProduct->product->image 
                        ? asset('storage/' . $featuredProduct->product->image) 
                        : 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image' }}"
             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
             class="w-full h-full object-contain">
    </div>
@endforelse

                            </div>
                            {{-- Inner Swiper Pagination --}}
                            @php
                                $images = is_string($featuredProduct->images) ? json_decode($featuredProduct->images, true) : ($featuredProduct->images ?? []);
                            @endphp

                            <div class="swiper-pagination image-pagination" style="{{ count($images) <= 1 ? 'display:none;' : '' }}"></div>


                            {{-- DISCOUNT BADGE - MOVED HERE --}}
                            @if ( $featuredProduct->offer->discount_percent)
                                <span
                                    class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                    {{-- Translated: Discount X % --}}
                                    {{ __('messages.discount_percentage', ['percent' => $featuredProduct->offer->discount_percent]) }}
                                </span>
                            @endif
                            {{-- FAVORITE BUTTON - MOVED HERE --}}
                            <button
                                class="favorite-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-gray-500 hover:text-red-500 transition-colors duration-200 z-10"
                                data-product-id="{{ $featuredProduct->id }}" aria-label="Add to favorites">
                                {{-- Conditional SVG for filled/unfilled heart --}}
                                @if (Auth::check() && Auth::user()->hasFavorited($featuredProduct->id))
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
                        </div> {{-- End of product-image-swiper --}}

                        {{-- Product Details --}}
                        <div class="p-4 flex flex-col flex-grow">
                            <div class="flex w-full items-center text-sm mb-2 justify-between">
                                <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $featuredProduct->name }}</h3>
                                <div class="flex items-center ">
                                    <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                    <span class="text-[18px]">{{ $featuredProduct->rating ?? '4.5' }}</span>
                                </div>
                            </div>
                           <span class="text-[#696969] text-[20px]">
    {{ app()->getLocale() === 'ar' 
        ? ($featuredProduct->subCategory->category->name ?? 'غير مصنف') 
        : ($featuredProduct->subCategory->category->name_en ?? 'Uncategorized') }}
</span>
 <div class="flex mt-2">
                                @if ($featuredProduct->supplier->supplier_confirmed)
                                    <span class="flex items-center text-[#185D31]">
                                        <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                            src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
<a href="{{ route('suppliers.show', $featuredProduct->supplier->id) }}"
   class="inline-block py-1 rounded-lg text-[#185D31] text-[18px] font-medium  transition">
    {{ $featuredProduct->supplier->company_name }}
</a>                                    </span>
                                @else
<a href="{{ route('suppliers.show', $featuredProduct->supplier->id) }}"
   class="inline-block py-1 rounded-lg text-[#185D31] text-[18px] font-medium  transition">
    {{ $featuredProduct->supplier->company_name }}
</a>                                @endif
                            </div>
                            <div class="flex items-center mb-2">
                <span class="flex text-lg font-bold text-gray-800">
    {{ number_format($featuredProduct->price_range['min'], 2) }}
    @if($featuredProduct->price_range['min'] != $featuredProduct->price_range['max'])
        - {{ number_format($featuredProduct->price_range['max'], 2) }}
    @endif
    <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}" alt="">
</span>

                                @if ($featuredProduct->offer->discount_percent)
                                    <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                        {{ number_format($featuredProduct->price, 2) }}
                                         <img class="mx-1 w-[14px] h-[14px] mt-1 inline-block"
                                            src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="currency">
                                    </span>
                                @endif
                            </div>

                            {{-- Translated: Minimum Order --}}
                            <p class="text-sm text-gray-600 mb-4">
                                {{ __('messages.minimum_order_quantity', ['quantity' => $featuredProduct->min_order_quantity ?? '1']) }}
                            </p>

                            <div class="mt-auto">
                                <a href="{{ route('products.show', $featuredProduct->slug) }}"
                                    class="block w-full bg-[#185D31] text-white text-center py-[10px] px-[16px] rounded-[12px] font-medium transition-colors duration-200">
                                    {{-- Translated: View Details --}}
                                    {{ __('messages.view_details') }}
                                </a>
                            </div>
                        </div>
                    </div>
        @endforeach
    </div>
</section>

<script>
    // Carousel JavaScript
    document.addEventListener('DOMContentLoaded', () => {
        const carouselImages = document.getElementById('carousel-images');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const carouselDots = document.getElementById('carousel-dots');
        const images = carouselImages.querySelectorAll('img');
        let currentIndex = 0;

        // Function to update carousel position
        const updateCarousel = () => {
            carouselImages.style.transform = `translateX(-${currentIndex * 100}%)`;
            updateDots();
        };

        // Function to create and update dots
        const updateDots = () => {
            carouselDots.innerHTML = ''; // Clear existing dots
            images.forEach((_, index) => {
                const dot = document.createElement('span');
                dot.classList.add('w-3', 'h-3', 'rounded-full', 'bg-gray-400', 'cursor-pointer',
                    'transition-colors', 'duration-300');
                if (index === currentIndex) {
                    dot.classList.add('bg-green-600');
                }
                dot.addEventListener('click', () => {
                    currentIndex = index;
                    updateCarousel();
                });
                carouselDots.appendChild(dot);
            });
        };

        // Event listeners for navigation buttons
        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex > 0) ? currentIndex - 1 : images.length - 1;
            updateCarousel();
        });

        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex < images.length - 1) ? currentIndex + 1 : 0;
            updateCarousel();
        });

        // Initial setup
        updateCarousel();
    });
</script>
