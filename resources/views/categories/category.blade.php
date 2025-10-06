@extends('layouts.app')
@section('title', 'Clothings')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <style>
        .black-friday-banner {
            background-image: url('https://placehold.co/1200x250/000000/ffffff?text=Black+Friday+Sale+Banner');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .category-card,
        .product-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .category-card:hover,
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
    <style>
        .swiper-pagination-bullet {
            background-color: #e5e7eb;
            opacity: 1;
        }

        .swiper-pagination-bullet-active {
            background-color: #185D31 !important;
        }
    </style>
    <div class="min-h-screen flex flex-col w-full items-center">
<section class="w-full relative px-4 md:px-[64px] py-4 mb-4">
    <div class="swiper mySwiper w-full relative rounded-lg overflow-hidden">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="{{ asset('storage/products/5c4bef432cc19a3f32f14c6e46e1b12f63981477.png') }}"
                    class="w-full h-auto object-cover max-h-[500px] md:max-h-[547px] aspect-video md:aspect-auto"
                    alt="Slide 1">
            </div>
            <div class="swiper-slide">
                <img src="{{ asset('storage/products/5c4bef432cc19a3f32f14c6e46e1b12f63981477.png') }}"
                    class="w-full h-auto object-cover max-h-[500px] md:max-h-[547px] aspect-video md:aspect-auto"
                    alt="Slide 2">
            </div>
            <div class="swiper-slide">
                <img src="{{ asset('storage/products/5c4bef432cc19a3f32f14c6e46e1b12f63981477.png') }}"
                    class="w-full h-auto object-cover max-h-[500px] md:max-h-[547px] aspect-video md:aspect-auto"
                    alt="Slide 3">
            </div>
        </div>
        <div
            class="custom-swiper-button-prev absolute top-1/2 left-2 -translate-y-1/2 z-10 w-10 h-10 bg-white text-gray-800 rounded-full flex items-center justify-center shadow hover:bg-gray-200 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
            </svg>
        </div>
        <div
            class="custom-swiper-button-next absolute top-1/2 right-2 -translate-y-1/2 z-10 w-10 h-10 bg-white text-gray-800 rounded-full flex items-center justify-center shadow hover:bg-gray-200 cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    </div>
    <div id="banner-swiper-pagination"
        class="absolute bottom-[-2.5rem] md:bottom-[-3.5rem] left-0 right-0 flex justify-center space-x-2 z-30">
    </div>
</section>

        <main class="w-full py-8 md:py-12">
            <section class="mb-12 bg-[#F8F9FA] w-full px-[64px] py-8">
           <p
    class="text-center sm:text-right text-[16px] text-[#1F2B45] py-[8px] rounded-[40px] bg-[#FFFFFF] w-[112px] mb-3">
    {{ $siteTexts['choosen_categories'] ?? __('messages.choosen_categories') }}
</p>

<h2 class="text-3xl font-bold text-gray-800 mb-6 relative">
    {{ $siteTexts['main_categories'] ?? __('messages.main_categories') }}
</h2>

<p class="text-[#696969] text-[20px] mb-8  ">
    {{ $siteTexts['cosen_cat_description'] ?? __('messages.cosen_cat_description') }}
</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4  gap-6">
                    @foreach ($subCategories as $subCategory)
                        <div class="flex flex-col items-center text-center w-full">
                            <div
                                class="bg-[#EDEDED] rounded-lg shadow-md overflow-hidden transform transition duration-300 hover:scale-105 h-[312px] w-[310px] flex items-center justify-center">
                            <a href="{{ route('products.index', ['sub_category_id' => $subCategory->id]) }}" class="block h-full w-full">
    <img src="{{ asset('storage/products/' . $subCategory->iconUrl) }}" 
        alt="{{ $subCategory->name }}" class="w-full h-full object-cover" />
</a>
                            </div>
                            <div class="p-4 text-center flex-grow flex items-center justify-center">
                                <h3 class="text-[24px] font-bold text-gray-700">{{ $subCategory->name }}</h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            @isset($onOfferProducts)
    @if($onOfferProducts->isNotEmpty())
            <section class=" py-8 font-inter px-[64px]">
              <div class="flex sm:flex-row flex-col justify-between mt-1">
    <div class="flex flex-col justify-between mb-5 mt-3">
        <h2 class="text-[40px] font-bold text-[#212121] mb-6 relative">
            {{ $siteTexts['best_deals'] ?? __('messages.best_deals') }}
        </h2>
        <p class="text-gray-600 text-[20px] mb-8">
            {{ $siteTexts['offer_descriptions'] ?? __('messages.offer_descriptions') }}
        </p>
    </div>
</div>

                <div class="swiper offerSwiper mb-4 px-[64px] py-8 ">
                    <div class="swiper-wrapper mb-8 ">
                        @forelse ($onOfferProducts as $product)
                            <div class="swiper-slide mb-8">
                                <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                                    <div
                                        class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
                                <div class="swiper-wrapper">
                                @php
                                    $images = collect(is_string($product->images) ? json_decode($product->images, true) : ($product->images ?? []));
                                @endphp

                                @forelse ($images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ Storage::url($image) }}"
                                             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                             class="w-full h-full object-contain">
                                    </div>
                                @empty
                                    <div class="swiper-slide">
                                        <img src="{{ asset($product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                             class="w-full h-full object-contain">
                                    </div>
                                @endforelse
                            </div>
                                        @php
                                            $images = is_string($product->images)
                                                ? json_decode($product->images, true)
                                                : $product->images ?? [];
                                        @endphp
                                        <div class="swiper-pagination image-pagination"
                                            style="{{ count($images) <= 1 ? 'display:none;' : '' }}"></div>
                                        @if ($product->offer && $product->offer->discount_percent)
                                            <span
                                                class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                                {{ __('messages.discount_percentage', ['percent' => $product->offer->discount_percent]) }}
                                            </span>
                                        @endif
                                        <button
                                            class="favorite-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-gray-500 hover:text-red-500 transition-colors duration-200 z-10"
                                            data-product-id="{{ $product->id }}" aria-label="Add to favorites">
                                            @if (Auth::check() && Auth::user()->hasFavorited($product->id))
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6 text-red-500">
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
                                    <div class="p-4 flex flex-col flex-grow">
                                        <div class="flex w-full items-center text-sm mb-2 justify-between">
<h3 class="text-[24px] font-bold text-[#212121] mb-1">
    {{ app()->getLocale() === 'en' ? $product->name_en : $product->name }}
</h3>
                                            <div class="flex items-center ">
                                                <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}"
                                                    alt="">
@php
    $averageRating = round($product->reviews->avg('rating'), 1);
@endphp

@if($averageRating > 0)
    <span class="text-[18px]">{{ $averageRating }}</span>
@endif
                                            </div>
                                        </div>
 <span class="text-[#696969] text-[20px]">
    {{ app()->getLocale() === 'ar' 
        ? ($product->subCategory->category->name ?? 'غير مصنف') 
        : ($product->subCategory->category->name_en ?? 'Uncategorized') }}
</span>
                                        <div class="flex mt-2">
                                            @if ($product->supplier->supplier_confirmed)
                                                <span class="flex items-center text-[#185D31]">
                                                    <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                                        src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                                   <a href="{{ route('suppliers.show', $product->supplier->id) }}"
   class="inline-block py-1 text-[#185D31] rounded-lg text-[18px] font-medium  transition">
    {{ $product->supplier->company_name }}
</a>
                                                </span>
                                            @else
                                              <a href="{{ route('suppliers.show', $product->supplier->id) }}"
   class="inline-block py-1 text-[#185D31] rounded-lg text-[18px] font-medium  transition">
    {{ $product->supplier->company_name }}
</a>
                                            @endif
                                        </div>
                                        <div class="flex items-center mb-2">
                                          <span class="flex text-lg font-bold text-gray-800">
    {{ number_format($product->price_range['min'], 2) }}
    @if($product->price_range['min'] != $product->price_range['max'])
        - {{ number_format($product->price_range['max'], 2) }}
    @endif
    <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}" alt="">
</span>

                                            @if ($product->offer && $product->offer->discount_percent)
                                                <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                                    {{ number_format($product->price, 2) }}
                                             <img class="mx-1 w-[14px] h-[14px] mt-1 inline-block"
                                            src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="currency">
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mb-4">
                                            {{ __('messages.minimum_order_quantity', ['quantity' => $product->min_order_quantity ?? '1']) }}
                                        </p>
                                        <div class="mt-auto">
                                            <a href="{{ route('products.show', $product->slug) }}"
                                                class="block w-full bg-[#185D31] text-white text-center py-[10px] px-[16px] rounded-[12px] font-medium transition-colors duration-200">
                                                {{ __('messages.view_details') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide w-full text-center py-10 text-gray-600">
                                <p class="text-2xl font-bold mb-4">{{ __('messages.no_offers_available_title') }}</p>
                                <p>{{ __('messages.no_offers_available_description') }}</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-pagination offer-swiper-pagination mt-8"></div>
                </div>
            </section>
                @endif
@endisset


      @isset($onFeaturedProducts)
    @if($onFeaturedProducts->isNotEmpty())
    <section class=" px-[64px] py-4 font-inter">
    <div class="flex sm:flex-row flex-col  justify-between mt-1">
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
            class="text-[#FFFFFF] bg-[#185D31] h-[48px] text-[16px] px-[20px] py-[12px] rounded-[12px]">{{ __('messages.show_more') }}</a>
    </div>
 

    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="favorites-grid">
        @foreach ($onFeaturedProducts as $featuredProduct)

                    <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                        {{-- Product Image Carousel (Inner Swiper) --}}
                        <div class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
                                      <div class="swiper-wrapper">
                                @php
                                    $images = collect(is_string($featuredProduct->images) ? json_decode($featuredProduct->images, true) : ($featuredProduct->images ?? []));
                                @endphp

                                @forelse ($images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ Storage::url($image) }}"
                                             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                             class="w-full h-full object-contain">
                                    </div>
                                @empty
                                    <div class="swiper-slide">
                                        <img src="{{ asset($featuredProduct->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
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
                            @if ($featuredProduct->offer && $featuredProduct->offer->discount_percent)
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
<h3 class="text-[24px] font-bold text-[#212121] mb-1">
    {{ app()->getLocale() === 'en' ? $featuredProduct->name_en : $featuredProduct->name }}
</h3>
                                <div class="flex items-center ">
                                    <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
@php
    $averageRating = round($featuredProduct->reviews->avg('rating'), 1);
@endphp

@if($averageRating > 0)
    <span class="text-[18px]">{{ $averageRating }}</span>
@endif
                                </div>
                            </div>
<span class="text-[#696969] text-[20px]">
    {{ app()->getLocale() === 'ar' 
        ? ($featuredProduct->subCategory->category->name ?? 'غير مصنف') 
        : ($featuredProduct->subCategory->category->name_en ?? 'Uncategorized') }}
</span>
                            <div class="flex mt-2">
                                @if ($featuredProduct->supplier_confirmed)
                                    <span class="flex items-center text-[#185D31]">
                                        <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                            src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
<a href="{{ route('suppliers.show', $product->supplier->id) }}"
   class="inline-block py-1 text-[#185D31] rounded-lg text-[18px] font-medium  transition">
    {{ $product->supplier->company_name }}
</a>                                    </span>
                                @else
<a href="{{ route('suppliers.show', $product->supplier->id) }}"
   class="inline-block py-1 text-[#185D31] rounded-lg text-[18px] font-medium  transition">
    {{ $product->supplier->company_name }}
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

                                @if ($featuredProduct->offer && $featuredProduct->offer->discount_percent)
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
    @endif
@endisset
        </main>
    </div>

{{-- Login Popup HTML --}}
<div id="login-popup" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 max-w-sm mx-4">
        <h2 class="text-2xl font-bold mb-4 text-gray-900">{{ __('messages.login_important') }}</h2>
        <p class="mb-6 text-gray-700">{{ __('messages.login_important_for_fav') }}</p>
        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <button id="close-login-popup"
                class="bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors">
                {{ __('messages.ok') }}
            </button>
            {{-- <a href="{{ route('login') }}"
                class="bg-[#185D31] text-white py-2 px-4 rounded-md hover:bg-[#154a2a] transition-colors">
                {{ __('messages.login') }}
            </a> --}}
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: "#banner-swiper-pagination", 
                clickable: true,
            },
            navigation: {
                nextEl: ".custom-swiper-button-next",
                prevEl: ".custom-swiper-button-prev",
            },
        });
    });
</script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const offerSwiper = new Swiper('.offerSwiper', {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: true,
                rtl: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.offer-swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 24,
                    },
                    1024: {
                        slidesPerView: 4,
                        spaceBetween: 24,
                    }
                },
            });

            function initializeInnerSwipers() {
                document.querySelectorAll('.inner-swiper').forEach(swiperElement => {
                    if (!swiperElement.swiper) {
                        const imageSlides = swiperElement.querySelectorAll('.swiper-slide').length;
                        if (imageSlides > 1) {
                            new Swiper(swiperElement, {
                                loop: true,
                                autoplay: {
                                    delay: 2500,
                                    disableOnInteraction: false,
                                },
                                pagination: {
                                    el: swiperElement.querySelector('.image-pagination'),
                                    clickable: true,
                                },
                            });
                        }
                    }
                });
            }
            initializeInnerSwipers();

                 // --- Logic for Favorite Button and Login Popup ---

        // Determine user authentication status from Laravel
        const isUserLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute(
        'content'); // Get CSRF token once

        const favoriteButtons = document.querySelectorAll('.favorite-button');
        const loginPopup = document.getElementById('login-popup');
        const closeLoginPopupBtn = document.getElementById('close-login-popup');

        favoriteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                if (!isUserLoggedIn) {
                    event
                .preventDefault(); // Prevent default action (e.g., potential form submission or link follow)
                    loginPopup.classList.remove('hidden'); // Show the popup
                } else {
                    // User is logged in, proceed with favorite toggling logic
                    const productId = this.dataset.productId;
                    console.log('User is logged in. Toggling favorite for product ID:',
                        productId);

                    // AJAX CALL to toggle favorite status
                    fetch(`/products/${productId}/toggle-favorite`, { // Adjust this API endpoint to match your Laravel route
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json', // Important for Laravel to return JSON
                                'X-CSRF-TOKEN': csrfToken // Laravel CSRF token
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        })
                        .then(response => {
                            // Handle unauthenticated case (e.g., session expired)
                            if (response.status === 401) {
                                window.location.href = '/login'; // Redirect to login page
                                return Promise.reject(
                                'Unauthenticated'); // Stop promise chain
                            }
                            if (!response.ok) {
                                // If response is not OK (e.g., 500 Internal Server Error, 403 Forbidden)
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json(); // Parse response as JSON
                        })
                        .then(data => {
                            console.log(data
                            .message); // Log success or failure message from backend
                            // Update the heart icon visually based on the 'is_favorited' status from the response
                            const svg = this.querySelector('svg');
                            if (data
                                .is_favorited) { // If the backend says it's now favorited
                                svg.setAttribute('fill', 'currentColor'); // Fill the heart
                                svg.classList.add('text-red-500'); // Make it red
                                svg.classList.remove(
                                'text-gray-500'); // Remove gray if present
                            } else { // If the backend says it's no longer favorited
                                svg.setAttribute('fill', 'none'); // Unfill the heart
                                svg.classList.remove('text-red-500'); // Remove red
                                svg.classList.add(
                                'text-gray-500'); // Make it gray (unfilled color)
                            }
                        })
                        .catch(error => {
                            console.error('Error toggling favorite:', error);
                            // Optionally, revert the UI state or show an error message to the user
                        });
                }
            });
        });

        // Close popup when clicking the close button
        if (closeLoginPopupBtn) {
            closeLoginPopupBtn.addEventListener('click', function() {
                loginPopup.classList.add('hidden');
            });
        }

        // Close popup when clicking outside of it
        if (loginPopup) {
            loginPopup.addEventListener('click', function(event) {
                if (event.target === loginPopup) { // Check if the click was directly on the overlay
                    loginPopup.classList.add('hidden');
                }
            });
        }
    
        });

        
    </script>
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
@endsection
