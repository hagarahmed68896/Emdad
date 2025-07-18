@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
{{-- Include Tailwind CSS (assuming it's already configured or loaded via CDN) --}}
<script src="https://cdn.tailwindcss.com"></script>
<style>
    .product-thumbnail-swiper .swiper-slide {
        opacity: 0.5;
        transition: opacity 0.3s ease;
    }

    .product-thumbnail-swiper .swiper-slide-thumb-active {
        opacity: 1;
        border-color: #185D31;
    }

    .swiper-slide-thumb-active {
        border-color: #185D31 !important;
    }

    /* Custom styles for color swatches */
    .color-swatch {
        width: 64px;
        /* Increased size to match image */
        height: 64px;
        background-color: #EDEDED;
        /* Increased size to match image */
        border-radius: 12px;
        /* Default light border */
        cursor: pointer;
        transition: transform 0.2s ease-in-out, border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        display: flex;
        /* To center any potential inner icon/checkmark */
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .color-swatch:hover {
        transform: scale(1.05);
    }

    .color-swatch.selected {
        border-color: #185D31;
        /* Green border for selected */
        box-shadow: 0 0 0 2px #185D31;
        /* Green shadow for selected */
    }

    /* Swiper custom pagination dots for main image */
    .swiper-pagination-bullet {
        /* background: #ccc; */
        opacity: 0.7;
    }

    .swiper-pagination-bullet-active {
        background: #185D31;
        /* Active dot color */
        opacity: 1;
    }

    /* Swiper thumbnail styling */
    .product-thumbnail-swiper .swiper-slide {
        border: 2px solid transparent;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color 0.2s ease-in-out;
    }

    .product-thumbnail-swiper .swiper-slide-thumb-active {
        border-color: #185D31;
        /* Active thumbnail border */
    }

    .product-thumbnail-swiper .swiper-slide img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        /* Use contain to prevent cropping */
    }

    /* RTL adjustments */
    html[dir="rtl"] .ltr\:ml-3 {
        margin-left: 0;
        margin-right: 0.75rem;
    }

    html[dir="rtl"] .rtl\:mr-3 {
        margin-right: 0;
        margin-left: 0.75rem;
    }

    html[dir="rtl"] .ltr\:left-3 {
        left: auto;
        right: 0.75rem;
    }

    html[dir="rtl"] .rtl\:right-3 {
        right: auto;
        left: 0.75rem;
    }

    html[dir="rtl"] .rtl\:ml-2 {
        margin-left: 0;
        margin-right: 0.5rem;
    }

    html[dir="rtl"] .ltr\:mr-2 {
        margin-right: 0;
        margin-left: 0.5rem;
    }

    /* Custom scrollbar for sidebar (if needed, though not directly in this page) */
    .sidebar-scroll {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .sidebar-scroll::-webkit-scrollbar {
        display: none;
    }

    .swiper-button-next::after,
    .swiper-button-prev::after {
        font-size: 18px !important;
        /* Make the arrow icon larger */
        font-weight: bold;
        color: #112211;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize thumbnail swiper
        const thumbnailSwiper = new Swiper('.product-thumbnail-swiper', {
            spaceBetween: 5,
            slidesPerView: 'auto',
            watchSlidesProgress: true,
        });

        // Initialize main swiper and link it to thumbnails
        const mainSwiper = new Swiper('.product-main-swiper', {
            spaceBetween: 5,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            thumbs: {
                swiper: thumbnailSwiper,
            }
        });
    });
</script>
<style>
    /* Basic styling for the modal overlay and content */
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    /* Hide spin buttons for number input */
    .no-spinners::-webkit-outer-spin-button,
    .no-spinners::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .no-spinners {
        -moz-appearance: textfield;
        /* Firefox */
    }

    [x-cloak] {
        display: none !important;
    }
</style>

@section('content')
    <div class="px-[64px]">
        {{-- Breadcrumbs --}}
        <p class="inline-flex flex-row text-[14px] px-[16px] pt-[10px] rounded-[12px] text-white bg-[#185D31] h-[40px]"
            id="breadcrumbs">
            <a href="{{ route('home') }}" class="hover:underline">{{ __('messages.home') }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-4 mx-1 mt-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>

            @if ($product->subCategory && $product->subCategory->category)
                <a href="#" class="hover:underline">{{ $product->subCategory->category->name_ar }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4 mx-1 mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            @endif
            @if ($product->subCategory)
                <a href="{{ route('products.index', ['sub_category_id' => $product->subCategory->id]) }}"
                    class="hover:underline">{{ $product->subCategory->name_ar }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4 mx-1 mt-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            @endif
            <span class="font-semibold">{{ $product->name }}</span>
        </p>

        {{-- product section --}}
        <div class="py-4  flex flex-col lg:flex-row gap-8 items-stretch">

            {{-- Product Image Gallery Section --}}
            <div class="w-full lg:w-1/2  rounded-[12px]  flex flex-col items-center">

                {{-- Main Product Image Swiper --}}
                <div class="relative w-full md:h-[1080px] bg-[#EDEDED] py-4 overflow-hidden rounded-[12px]">
                    <div class="relative w-full aspect-[3/4] md:aspect-[3/5] lg:aspect-[2/3] rounded-lg">

                        <div class="swiper product-main-swiper w-full h-full rounded-lg flex items-center justify-center">
                            <div class="swiper-wrapper">
                                @php
                                    $images = is_string($product->images)
                                        ? json_decode($product->images, true)
                                        : $product->images ?? [];
                                @endphp
                                @forelse ($images as $imagePath)
                                    <div class="swiper-slide flex items-center justify-center">
                                        <img src="{{ asset($imagePath) }}"
                                            onerror="this.onerror=null;this.src='https://placehold.co/600x600/F0F0F0/ADADAD?text=Image+Not+Found';"
                                            alt="{{ $product->name }}"
                                            class="max-w-full max-h-full object-contain rounded-lg">
                                    </div>
                                @empty
                                    <div class="swiper-slide flex items-center justify-center">
                                        <img src="https://placehold.co/600x600/F0F0F0/ADADAD?text=No+Images"
                                            alt="No image available"
                                            class="max-w-full max-h-full object-contain rounded-lg">
                                    </div>
                                @endforelse
                            </div>

                            {{-- Swiper Controls --}}
                            <div class="swiper-button-next text-[#212121] bg-white rounded-full p-3 shadow-sm"></div>
                            <div class="swiper-button-prev text-[#212121] bg-white rounded-full p-3 shadow-sm"></div>
                        </div>

                        {{-- Discount Badge  --}}
                        @if ($product->is_offer && $product->discount_percent)
                            <span
                                class="absolute top-3 right-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-4 py-2 rounded-full z-10">
                                {{ __('messages.discount_percentage', ['percent' => $product->discount_percent]) }}
                            </span>
                        @endif

                        {{-- Favorite Button (Top Left) --}}
                        <button
                            class="favorite-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-gray-500 hover:text-red-500 transition-colors duration-200 z-10"
                            data-product-id="{{ $product->id }}" aria-label="Add to favorites">
                            {{-- Conditional SVG for filled/unfilled heart --}}
                            @if (Auth::check() && Auth::user()->hasFavorited($product->id))
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
                </div>

                {{-- Thumbnail Swiper --}}
                <div class="swiper product-thumbnail-swiper w-full bg-white mt-4 px-1">
                    <div class="swiper-wrapper gap-3">
                        @forelse ($images as $imagePath)
                            <div
                                class="swiper-slide w-[120px] h-[150px] bg-[#EDEDED] rounded-md overflow-hidden border-2 border-transparent hover:border-[#185D31] transition-all duration-200 cursor-pointer">
                                <img src="{{ asset($imagePath) }}"
                                    onerror="this.onerror=null;this.src='https://placehold.co/120x120/F0F0F0/ADADAD?text=Thumb';"
                                    alt="Thumbnail" class="w-full h-full object-cover rounded-md">
                            </div>
                        @empty
                            {{-- No thumbnails if no images --}}
                        @endforelse
                    </div>
                </div>

            </div>


            {{-- Product Details Section --}}
            <div class="w-full lg:w-1/2 bg-white p-6 rounded-xl flex flex-col gap-4">

                {{-- Product Header --}}
                <div class=" items-center mb-1">
                    <h1 class="text-[32px] font-bold text-[#212121] mb-3">{{ $product->name }}</h1>
                    <div class="flex items-center">

                        @php
                            $averageRating = round($product->reviews->avg('rating'), 1);
                            $totalReviews = $product->reviews->count();
                            $ratingsCount = $product->reviews->groupBy('rating')->map->count();
                            $fullStars = floor($averageRating);
                            $halfStar = $averageRating - $fullStars >= 0.5 ? 1 : 0;
                            $emptyStars = 5 - $fullStars - $halfStar;
                        @endphp

                        {{-- Display Full Stars --}}
                        @for ($i = 0; $i < $fullStars; $i++)
                            <img class="ml-1 w-[20px] h-[20px]" src="{{ asset('images/Vector (4).svg') }}" alt="Full Star">
                        @endfor

                        {{-- Display Half Star (if any) --}}
                        @if ($halfStar)
                            {{-- IMPORTANT: Replace 'images/half-star.svg' with your actual half-star icon path --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="gold"
                                class="bi bi-star-half w-[20px] h-[20px] rounded-[14px]" viewBox="0 0 16 16">
                                <path
                                    d="M5.354 5.119 7.538.792A.52.52 0 0 1 8 .5c.183 0 .366.097.465.292l2.184 4.327 4.898.696A.54.54 0 0 1 16 6.32a.55.55 0 0 1-.17.445l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256a.5.5 0 0 1-.146.05c-.342.06-.668-.254-.6-.642l.83-4.73L.173 6.765a.55.55 0 0 1-.172-.403.6.6 0 0 1 .085-.302.51.51 0 0 1 .37-.245zM8 12.027a.5.5 0 0 1 .232.056l3.686 1.894-.694-3.957a.56.56 0 0 1 .162-.505l2.907-2.77-4.052-.576a.53.53 0 0 1-.393-.288L8.001 2.223 8 2.226z" />
                            </svg>
                        @endif


                        {{-- Display the numerical rating --}}
                        <span class="text-[16px] text-[#212121] rtl:mr-1 ltr:ml-1">{{ $averageRating }}
                            {{ __('messages.stars') }}</span>
                        <span class="text-[14px] text-bold mr-2">•</span>
                        <span class="text-[#212121] text-[16px] ltr:ml-2 rtl:mr-2">{{ $totalReviews }}
                            {{ __('messages.reviews') }}</span>
                    </div>
                </div>

                {{-- Supplier Info --}}
                <div class="flex items-center mb-2">
                    @if ($product->supplier_confirmed)
                        <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]" src="{{ asset('images/Success.svg') }}"
                            alt="Confirmed Supplier">
                    @endif
                    <p class="text-[20px] text-[#212121]">{{ $product->supplier_name }}</p>
                </div>

                <div class="mb-2 w-full bg-[#F8F9FA] p-4 rounded-[12px]">
                    {{-- Top flex row with "selectable" and discount --}}
                    <div class="flex flex-col md:flex-row">
                        <p
                            class="bg-white ml-4  md:mb-0 items-center text-center justify-center mb-2 h-[40px] px-[16px] py-[8px] rounded-[40px]">
                            {{ __('messages.selectable') }}</p>
                        @if ($product->is_offer && $product->discount_percent)
                            <span
                                class="h-[40px] bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-3 rounded-full z-10
           flex items-center justify-center">
                                {{ __('messages.discount_percentage', ['percent' => $product->discount_percent]) }}
                            </span>
                        @endif
                    </div>

                    {{-- Grid for price tiers --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-1">
                        @forelse ($product->price_tiers as $tier)
                            <div class="p-3">
                                <p class="text-[16px] text-[#696969]">
                                    @if (isset($tier['max_qty']))
                                        {{ $tier['min_qty'] }}-{{ $tier['max_qty'] }} {{ __('messages.pieces') }}
                                    @else
                                        {{ $tier['min_qty'] }}+ {{ __('messages.pieces') }}
                                    @endif
                                </p>
                                <p class="price-item text-[20px] md:text-[32px] text-[#212121] font-bold">
                                    {{ number_format($tier['price'] * (1 - ($product->discount_percent ?? 0) / 100)) }}

                                    <img class="currency-symbol inline-block mx-1 md:w-[24px] md:h-[27px] w-[20px] h-[22px]"
                                        src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="Currency">
                                </p>
                                @if ($product->is_offer && $product->discount_percent)
                                    <p class="flex text-sm text-gray-400 line-through ">
                                        {{ number_format($product->price, 2) }}
                                        <img class="mx-1 w-[14px] h-[14px] mt-1 inline-block"
                                            src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="currency">
                                    </p>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm col-span-3">{{ __('messages.no_pricing_tiers_available') }}
                            </p>
                        @endforelse
                    </div>
                </div>

                {{-- Variations: Colors --}}
                <div class=" border-b border-t border-[#EDEDED] py-3">
                    @php
                        // Safely access specifications and decode if it's a string
$specifications = $product->specifications;
if (is_string($specifications)) {
    $specifications = json_decode($specifications, true);
}
// Ensure $specifications is an array/object, then safely access 'colors'
$productColors =
    is_array($specifications) && isset($specifications['colors'])
        ? $specifications['colors']
        : [];

// Ensure $productColors is an array, even if it was null or malformed previously
if (!is_array($productColors)) {
    $productColors = [];
}

// Determine default selected color name (first available, or null if none)
$defaultSelectedColorName = null;
if (
    !empty($productColors) &&
    is_array($productColors[0] ?? null) &&
    isset($productColors[0]['name'])
) {
    $defaultSelectedColorName = $productColors[0]['name'];
                        }
                        $baseProductPrice = $product->price ?? 0;
                        $productDiscountPercent = $product->discount_percent ?? 0;
                        $shippingCost = $product->shipping_cost ?? 0;
                        $priceTiers = $product->price_tiers ?? [];
                    @endphp


                    {{-- select changes popup --}}
                    <div class="flex w-full justify-between">
                        <p class="text-[24px] font-bold mb-3">{{ __('messages.changes') }}</p>

                        <div x-cloak x-data="{
                            open_Poduct: false,
                            selectedQuantities: {},
                            totalItems: 0,
                            totalPrice: 0,
                        
                        
                        }">
                            <button
                                @click=" 
                          open_Poduct = true;
                           selectedQuantities: {};
                       totalItems: 0;
                       totalPrice: 0; "
                                class="underline text-[#696969] text-[14px] ">{{ __('messages.selectChanges') }}</button>

                            </button>

                            <div x-show="open_Poduct" x-cloak @click.away="open_Poduct = false;"
                                class="fixed inset-0 p-2 bg-black bg-opacity-50 flex  justify-between z-50">
                                <div
                                    class="bg-white p-4 rounded-xl shadow-2xl w-full  md:max-w-[700px] min-h-[50px]  overflow-y-auto flex flex-col md:flex-row rtl:md:flex-row-reverse relative">
                                    {{-- Close Button --}}
                                    <button x-on:click="open_Poduct = false"
                                        class="absolute top-3 rtl:left-3 ltr:right-3 p-2 transition-colors z-10"
                                        aria-label="Close">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1" stroke="currentColor" class="size-9">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18 18 6M6 6l12 12" />
                                        </svg>

                                    </button>

                                    {{-- Left Section: Product Details & Options --}}
                                    <div class="flex-1 p-6 md:p-8 flex flex-col">
                                        <h2 class="text-2xl md:text-3xl font-bold text-[#212121] mb-2">
                                            {{ $product->name }}
                                        </h2>
                                        <p class="text-gray-600 mb-6 text-sm md:text-base">
                                            {{ __('messages.select_changes') }}
                                        </p>

                                        {{-- Price Tiers --}}
                                        <div class="mb-6 border-b pb-4">
                                            <h3 class="text-lg font-bold text-gray-800 mb-3">
                                                {{ __('messages.q_price') }}</h3>
                                            <div class="grid grid-cols-4 gap-1">
                                                @forelse ($product->price_tiers as $tier)
                                                    <div class="p-1">
                                                        <p class="text-[16px] text-[#696969]">
                                                            @if (isset($tier['max_qty']))
                                                                {{ $tier['min_qty'] }}-{{ $tier['max_qty'] }}
                                                                {{ __('messages.pieces') }}
                                                            @else
                                                                {{ $tier['min_qty'] }}+ {{ __('messages.pieces') }}
                                                            @endif
                                                        </p>
                                                        <p class="price-item text-[24px] text-[#212121] font-bold">
                                                            <span>
                                                                {{ number_format($tier['price'] * (1 - ($product->discount_percent ?? 0) / 100)) }}
                                                            </span>
                                                            <img class="currency-symbol inline-block mx-1 w-[24px] h-[27px]"
                                                                src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}"
                                                                alt="Currency">
                                                        </p>



                                                    </div>
                                                @empty
                                                    <p class="text-gray-500 text-sm col-span-3">
                                                        {{ __('messages.no_pricing_tiers_available') }}
                                                    </p>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div x-data="{
                                            selected: [],
                                            discount: {{ $product->discount_percent ?? 0 }},
                                            shipping: 14.94,
                                            isPriceDetailsOpen: false,
                                        
                                            openSwatchModal: false,
                                            swiperInstance: null,
                                            activeSwatchIndex: 0,
                                        
                                        
                                            get subtotal() {
                                                return this.selected.reduce((sum, c) => sum + this.getUnitPrice(c) * c.count, 0);
                                            },
                                            get total() {
                                                return this.subtotal + this.shipping;
                                            },
                                            getUnitPrice(color) {
                                                return color.price ? color.price * (1 - this.discount / 100) : 0;
                                            }
                                        }" class="space-y-6">

                                            <h3 class="text-lg font-bold text-gray-800 mb-2">
                                                {{ __('messages.colors') }}
                                            </h3>

                                            <!-- Colors loop: each pushes to selected -->
                                            @foreach ($product->specifications['colors'] as $index => $color)
                                                @php
                                                    $colorName =
                                                        is_array($color) && isset($color['name'])
                                                            ? $color['name']
                                                            : $color;
                                                    $swatchImage =
                                                        is_array($color) && isset($color['swatch_image'])
                                                            ? asset($color['swatch_image'])
                                                            : 'https://placehold.co/40x40/F0F0F0/ADADAD?text=N/A';
                                                    $colorPrice =
                                                        is_array($color) && isset($color['price'])
                                                            ? $color['price']
                                                            : 0;
                                                @endphp

                                                <div x-init="selected.push({
                                                    name: '{{ $colorName }}',
                                                    swatchImage: '{{ $swatchImage }}',
                                                    price: {{ $colorPrice }},
                                                    count: 0
                                                })"
                                                    class="flex items-center justify-between border-b pb-2">
                                                    <div class="flex items-center ">
               <img
  :src="selected[{{ $index }}].swatchImage"
  alt="{{ $colorName }}"
  class="w-[64px] h-[64px] rounded-[12px] ml-3 bg-[#EDEDED] cursor-pointer object-cover"
  @click="
    openSwatchModal = true;
    $nextTick(() => {
      if (swiperInstance) swiperInstance.destroy(true, true);
      swiperInstance = new Swiper('.swatchSwiper', {
        initialSlide: {{ $index }},
        loop: true,
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
      });
    });
  "
/>


                                                        <span class="text-gray-800 font-medium"
                                                            x-text="selected[{{ $index }}].name"></span>
                                                    </div>

                                                    <div class="flex items-center">
                                                        <div class="flex text-[20px] ml-2">
                                                            <p
                                                                x-text="`${getUnitPrice(selected[{{ $index }}]).toFixed(2)} `">
                                                            </p>
                                                            <img class="mx-1 w-[16px] h-[16px] mt-2 inline-block"
                                                                src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}"
                                                                alt="">
                                                        </div>

                                                        <!-- Counter -->
                                                        <div
                                                            class="flex rounded-[12px] items-center py-1 w-[113px] bg-[#EDEDED] overflow-hidden ml-2">
                                                            <button type="button"
                                                                @click="selected[{{ $index }}].count = Math.max(0, selected[{{ $index }}].count - 1)"
                                                                class="px-3 py-1 ">
                                                                -

                                                            </button>
                                                            <input type="number" min="0"
                                                                x-model="selected[{{ $index }}].count"
                                                                class="flex-grow w-full text-center mr-2 border-0 bg-[#EDEDED]"
                                                                readonly>
                                                            <button type="button"
                                                                @click="selected[{{ $index }}].count++"
                                                                class="px-3 py-1">
                                                                +

                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            {{-- Shipping Details --}}
                                            <div>
                                                <p class="text-[24px] font-bold mb-3">{{ __('messages.shipping') }}</p>
                                                <div class="mb-2 w-full bg-[#F8F9FA] p-4 rounded-[12px]">
                                                    <h3 class="text-lg font-bold text-gray-800 mb-2">
                                                        {{ __('messages.shipping_details') }}</h3>
                                                    <div class="flex items-center text-gray-700 mb-1">
                                                        <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                                            src="{{ asset('images/shipping (2).svg') }}"
                                                            onerror="this.onerror=null;this.src='https://placehold.co/20x20/F0F0F0/ADADAD?text=S';"
                                                            alt="Shipping Cost">
                                                        <span>
                                                            @php
                                                                // $product->shipping_cost should contain the per-item shipping cost from the DB
                                                                $shippingCostPerItem = $product->shipping_cost ?? null;
                                                                $displayQuantity = 2; // Fixed quantity for display as per your example
                                                                $calculatedShippingCost = null;

                                                                if (is_numeric($shippingCostPerItem)) {
                                                                    $calculatedShippingCost = number_format(
                                                                        $shippingCostPerItem * $displayQuantity,
                                                                        2,
                                                                    );
                                                                }
                                                            @endphp

                                                            @if ($calculatedShippingCost !== null)
                                                                {{-- Using a new translation key for clarity --}}
                                                                {{ __('messages.shipping_cost_for_quantity', ['cost' => $calculatedShippingCost, 'quantity' => $displayQuantity]) }}
                                                            @else
                                                                {{ __('messages.shipping_cost_not_available') }}
                                                                {{-- Fallback message --}}
                                                            @endif
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center text-gray-700">
                                                        <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                                            src="{{ asset('images/shipping-box-2--box-package-label-delivery-shipment-shipping-3d--Streamline-Core.svg') }}"
                                                            onerror="this.onerror=null;this.src='https://placehold.co/20x20/F0F0F0/ADADAD?text=D';"
                                                            alt="Delivery Date">
                                                        <span>{{ __('messages.estimated_delivery_date', ['days' => $product->estimated_delivery_days ?? 'N/A']) }}</span>
                                                    </div>
                                                </div>
                                            </div>





                                            <!-- Price Section with Collapse -->
                                            <div x-data="{ isPriceDetailsOpen: false }" class="tex border-t pt-4 mb-4">

                                                <!-- 1️⃣ Collapsed: total + arrow on the right -->
                                                <template x-if="!isPriceDetailsOpen">
                                                    <div class="font-bold flex text-[20px] justify-between cursor-pointer"
                                                        @click="isPriceDetailsOpen = true">
                                                        <span>{{ __('messages.total') }}</span>
                                                        <div class="flex items-center">
                                                            <span x-text="`${total.toFixed(2)}`"></span>
                                                            <img class="mx-1 w-[16px] h-[16px] mt-2 inline-block"
                                                                src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}"
                                                                alt="">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke-width="1.5"
                                                                stroke="currentColor" class="size-6"
                                                                :class="{ 'rotate-180': isPriceDetailsOpen }">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- 2️⃣ Expanded: details + arrow moved next to Price heading -->
                                                <div x-show="isPriceDetailsOpen" x-collapse>
                                                    <div class="flex justify-between items-center mb-3">
                                                        <p class="text-[24px] font-bold">{{ __('messages.price') }}</p>
                                                        <!-- Arrow here next to heading -->
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6 cursor-pointer"
                                                            @click="isPriceDetailsOpen = false"
                                                            :class="{ 'rotate-180': isPriceDetailsOpen }">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                                        </svg>
                                                    </div>

                                                    <!-- Details -->
                                                    <div class="flex justify-between mb-2">
                                                        <span
                                                            class="text-[20px] text-[#212121]">{{ __('messages.total_elements') }}</span>
                                                        <div class="flex">
                                                            <span x-text="`${subtotal.toFixed(2)}`"></span>
                                                            <img class="mx-1 w-[16px] h-[16px] mt-1 inline-block"
                                                                src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}"
                                                                alt="">
                                                        </div>
                                                    </div>

                                                    <div class="flex justify-between border-b pb-3 mb-2">
                                                        <span
                                                            class="text-[20px] text-[#212121]">{{ __('messages.shipping_price') }}</span>
                                                        <div class="flex">
                                                            <span x-text="`${shipping.toFixed(2)}`"></span>
                                                            <img class="mx-1 w-[16px] h-[16px] mt-1 inline-block"
                                                                src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}"
                                                                alt="">
                                                        </div>
                                                    </div>

                                                    <!-- Bottom total: no arrow -->
                                                    <div class="font-bold flex text-[20px] justify-between cursor-pointer mt-4"
                                                        @click="isPriceDetailsOpen = false">
                                                        <span>{{ __('messages.total') }}</span>
                                                        <div class="flex items-center">
                                                            <span x-text="`${total.toFixed(2)}`"></span>
                                                            <img class="mx-1 w-[16px] h-[16px] mt-2 inline-block"
                                                                src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}"
                                                                alt="">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>






                                            <!-- Swatch Modal with Swiper -->
                                            <div x-show="openSwatchModal" x-transition
                                                class="fixed inset-0 items-center justify-end pl-[100px] flex z-50">
                                                <div
                                                    class="bg-white rounded-lg  max-w-[40%] max-h-[80%] overflow-hidden relative">

                                                    <!-- Close -->

                                                    <button @click="openSwatchModal = false"
                                                        class="absolute top-3 rtl:left-3 ltr:right-3 p-2 transition-colors z-10"
                                                        aria-label="Close">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"
                                                            class="size-9">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M6 18 18 6M6 6l12 12" />
                                                        </svg>

                                                    </button>

                                                    <!-- Swiper -->
                                                    <div class="swiper swatchSwiper">
                                                        <div class="swiper-wrapper">
                                                            @foreach ($product->specifications['colors'] as $index => $color)
                                                                @php
                                                                    $colorName =
                                                                        is_array($color) && isset($color['name'])
                                                                            ? $color['name']
                                                                            : $color;
                                                                    $swatchImage =
                                                                        is_array($color) &&
                                                                        isset($color['swatch_image'])
                                                                            ? asset($color['swatch_image'])
                                                                            : 'https://placehold.co/40x40/F0F0F0/ADADAD?text=N/A';
                                                                @endphp

                                                                <div class="swiper-slide flex flex-col items-center">
                                                                    <img src="{{ $swatchImage }}"
                                                                    
                                                                        class="max-w-full max-h-[70vh] rounded-lg" />
                                                                    <h2 class="text-lg font-bold text-center">
                                                                       {{__('messages.color_name')}}: {{ $colorName }}</h2>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <!-- Swiper navigation -->
                                                        <div class="swiper-button-next"></div>
                                                        <div class="swiper-button-prev"></div>
                                                    </div>

                                                </div>
                                            </div>






                                        </div>









                                        {{-- Action Buttons --}}
                                        <div class="flex flex-col md:flex-row gap-3 mb-3 py-4">
                                            <button x-on:click="handleAddToCart()" x-bind:disabled="totalItems === 0"
                                                class="flex flex-1 px-6 py-3 bg-[#185D31] text-white rounded-lg font-semibold hover:bg-green-700 transition-colors shadow-md text-center justify-center items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6 ml-2"> {{-- Changed ml-1 to mr-2 for spacing --}}
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                                                </svg>
                                                {{ __('messages.add_to_cart') }}
                                            </button>
                                            <button x-on:click="handleContactSupplier()"
                                                class="flex-1 px-6 py-3 bg-[#EDEDED] text-[#696969] rounded-lg font-semibold hover:bg-gray-200 transition-colors shadow-md">
                                                {{ __('messages.connect_to_supplier') }}
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">
                        {{ __('messages.colors') }} (<span id="colorCount">{{ count($productColors ?? []) }}</span>):
                        <span id="selectedColorName" class="font-normal text-[#212121]"></span>
                    </h3>
                    <div class="flex gap-2">

                        @forelse ($productColors as $colorOption)
                            @if (is_array($colorOption) && isset($colorOption['name']) && isset($colorOption['swatch_image']))
                                <div class="color-swatch cursor-pointer {{ $defaultSelectedColorName == $colorOption['name'] ? 'selected' : '' }}"
                                    data-color-name="{{ $colorOption['name'] }}"
                                    data-swatch-image="{{ asset($colorOption['swatch_image']) }}"
                                    title="{{ $colorOption['name'] }}">
                                    <img src="{{ asset($colorOption['swatch_image']) }}"
                                        alt="{{ $colorOption['name'] }} Swatch">
                                </div>
                            @endif
                        @empty
                            <p class="text-gray-500 text-sm">{{ __('messages.no_colors_available') }}</p>
                        @endforelse
                    </div>
                </div>



                {{-- Shipping Details --}}
                <div>
                    <p class="text-[24px] font-bold mb-3">{{ __('messages.shipping') }}</p>
                    <div class="mb-2 w-full bg-[#F8F9FA] p-4 rounded-[12px]">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">{{ __('messages.shipping_details') }}</h3>
                        <div class="flex items-center text-gray-700 mb-1">
                            <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]" src="{{ asset('images/shipping (2).svg') }}"
                                onerror="this.onerror=null;this.src='https://placehold.co/20x20/F0F0F0/ADADAD?text=S';"
                                alt="Shipping Cost">
                            <span>
                                @php
                                    // $product->shipping_cost should contain the per-item shipping cost from the DB
                                    $shippingCostPerItem = $product->shipping_cost ?? null;
                                    $displayQuantity = 2; // Fixed quantity for display as per your example
                                    $calculatedShippingCost = null;

                                    if (is_numeric($shippingCostPerItem)) {
                                        $calculatedShippingCost = number_format(
                                            $shippingCostPerItem * $displayQuantity,
                                            2,
                                        );
                                    }
                                @endphp

                                @if ($calculatedShippingCost !== null)
                                    {{-- Using a new translation key for clarity --}}
                                    {{ __('messages.shipping_cost_for_quantity', ['cost' => $calculatedShippingCost, 'quantity' => $displayQuantity]) }}
                                @else
                                    {{ __('messages.shipping_cost_not_available') }} {{-- Fallback message --}}
                                @endif
                            </span>
                        </div>
                        <div class="flex items-center text-gray-700">
                            <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                src="{{ asset('images/shipping-box-2--box-package-label-delivery-shipment-shipping-3d--Streamline-Core.svg') }}"
                                onerror="this.onerror=null;this.src='https://placehold.co/20x20/F0F0F0/ADADAD?text=D';"
                                alt="Delivery Date">
                            <span>{{ __('messages.estimated_delivery_date', ['days' => $product->estimated_delivery_days ?? 'N/A']) }}</span>
                        </div>
                    </div>
                </div>



                {{-- Action Buttons --}}
                <div class="flex gap-4">
                    <button
                        class="flex-1 bg-[#185D31] text-white py-3 rounded-[12px] font-semibold transition duration-300 shadow-md flex items-center justify-center gap-2">
                        {{ __('messages.place_order') }}
                    </button>
                    <button
                        class="flex-1 bg-[#EDEDED] text-[#696969] py-3 rounded-[12px] font-semibold transition duration-300 shadow-md flex items-center justify-center gap-2">
                        {{ __('messages.contact_supplier') }}
                    </button>
                </div>

                {{-- 4 Interest-Free Payments (Conditional Logic) --}}
                <div class="rounded-lg border-b border-[#EDEDED] pb-4">
                    @if (($product->price ?? 0) >= 100)
                        <div class="flex items-start text-gray-700 mt-4 pt-4 border-t border-gray-200">
                            <img class="rtl:ml-2 ltr:mr-2 mt-1 w-[16px] h-[16px] flex-shrink-0"
                                src="{{ asset('images/interface-alert-warning-circle--warning-alert-frame-exclamation-caution-circle--Streamline-Core.svg') }}"
                                onerror="this.onerror=null;this.src='https://placehold.co/24x24/F0F0F0/ADADAD?text=I';"
                                alt="{{ __('messages.interest_free_payments_info') }} Icon">

                            <p class="font-semibold text-[16px]">{{ __('messages.interest_free_payments_info') }}</p>
                            <div class="flex gap-2 rtl:mr-2 ltr:ml-2">
                                <img src="{{ asset('images/Tabby.svg') }}"
                                    onerror="this.onerror=null;this.src='https://placehold.co/60x20/F0F0F0/ADADAD?text=Tabby';"
                                    alt="Tabby Logo" class="h-5 object-contain">
                                <img src="{{ asset('images/tamara.svg') }}"
                                    onerror="this.onerror=null;this.src='https://placehold.co/60x20/F0F0F0/ADADAD?text=Tamara';"
                                    alt="Tamara Logo" class="h-5 object-contain">
                            </div>

                        </div>
                    @endif
                </div>

                {{-- Product Protection --}}
                <div class="rounded-lg pb-4">
                    <h3 class="text-[24px] font-bold text-gray-800 mb-2">{{ __('messages.product_protection') }}</h3>

                    {{-- Secure Payments --}}
                    <div class="flex items-start text-gray-700 mb-3">
                        <img class="rtl:ml-2 ltr:mr-2 w-[23.5px] h-[23.5px] mt-1 flex-shrink-0"
                            src="{{ asset('images/interface-security-shield-3--shield-pay-product-secure-money-cash-currency-security-business--Streamline-Core.svg') }}"
                            onerror="this.onerror=null;this.src='https://placehold.co/24x24/F0F0F0/ADADAD?text=P';"
                            alt="{{ __('messages.secure_payments') }} Icon">
                        <div>
                            <p class="font-bold text-[20px]">{{ __('messages.secure_payments') }}</p>
                            <p class="text-[16px] text-[#212121]">{{ __('messages.secure_payments_description') }}</p>
                        </div>
                    </div>

                    {{-- Easy Returns and Refunds --}}
                    <div class="flex items-start text-gray-700 mb-3">
                        <img class="rtl:ml-2 ltr:mr-2 w-[23.5px] h-[23.5px] mt-1 flex-shrink-0"
                            src="{{ asset('images/Vector (8).svg') }}"
                            onerror="this.onerror=null;this.src='https://placehold.co/24x24/F0F0F0/ADADAD?text=R';"
                            alt="{{ __('messages.easy_returns_refunds') }} Icon">
                        <div>
                            <p class="font-bold text-[20px]">{{ __('messages.easy_returns_refunds') }}</p>
                            <p class="text-[16px] text-[#212121]">{{ __('messages.easy_returns_refunds_description') }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <div x-data="{ tab: 'overview' }" class="w-full bg-white py-6 rounded-xl">

            {{-- Tabs --}}
            <div class="flex  mb-6">
                <button @click="tab = 'overview'"
                    :class="tab === 'overview' ? 'text-[#185D31] border-[#185D31]' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-lg font-bold border-b-2 focus:outline-none">
                    {{ __('messages.product_overview') }}
                </button>
                <button @click="tab = 'reviews'"
                    :class="tab === 'reviews' ? 'text-[#185D31] border-[#185D31]' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-lg font-bold border-b-2 focus:outline-none">
                    {{ __('messages.product_reviews') }}
                </button>
            </div>

            {{-- Overview Tab --}}
            <div x-show="tab === 'overview'" class="space-y-8 ">

                {{-- Product Description --}}
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ __('messages.product_description') }}</h3>
                    <p class="text-gray-700 leading-relaxed">
                        {{ $product->description ?? __('messages.no_description_available') }}
                    </p>
                </div>

                {{-- Key Features --}}
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ __('messages.key_features') }}</h3>
                    @php
                        $features =
                            is_array($product->specifications) && isset($product->specifications['features'])
                                ? $product->specifications['features']
                                : [];
                        if (!is_array($features)) {
                            $features = [];
                        }
                    @endphp
                    @if (!empty($features))
                        <ul class="list-none p-0 m-0">
                            @foreach ($features as $feature)
                                <li class="flex items-center text-gray-700 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-5 h-5 text-[#185D31] rtl:ml-2 ltr:mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span class="text-base">{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm">{{ __('messages.no_features_available') }}</p>
                    @endif
                </div>

                {{-- Specifications --}}
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">{{ __('messages.specifications') }}</h3>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                        {{ __('messages.color_name') }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-700">
                                        @php
                                            $firstColorName = null;
                                            if (
                                                is_array($product->specifications) &&
                                                isset($product->specifications['colors']) &&
                                                is_array($product->specifications['colors']) &&
                                                !empty($product->specifications['colors'][0]['name'])
                                            ) {
                                                $firstColorName = $product->specifications['colors'][0]['name'];
                                            }
                                        @endphp
                                        {{ $firstColorName ?? __('messages.not_available') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ __('messages.gender') }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700">
                                        {{ $product->specifications['gender'] ?? __('messages.not_available') }}
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ __('messages.material') }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700">
                                        {{ $product->specifications['material'] ?? __('messages.not_available') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                        {{ __('messages.model_number') }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-700">
                                        {{ $product->model_number ?? __('messages.not_available') }}
                                    </td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ __('messages.quality') }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-700">
                                        {{ $product->quality ?? __('messages.not_available') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Reviews Tab --}}
            <div x-show="tab === 'reviews'" class="flex flex-col lg:flex-row gap-6">

                {{-- Average Rating --}}
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 sm:h-[450px] lg:w-1/3">
                    <h3 class="text-lg font-bold mb-2">{{ __('messages.customer_rating') }}</h3>

                    @php
                        $averageRating = round($product->reviews->avg('rating'), 1);
                        $totalReviews = $product->reviews->count();
                        $ratingsCount = $product->reviews->groupBy('rating')->map->count();
                        $ratingsPercent = [];

                        for ($i = 1; $i <= 5; $i++) {
                            $ratingsPercent[$i] =
                                $totalReviews > 0 ? round((($ratingsCount[$i] ?? 0) * 100) / $totalReviews) : 0;
                        }
                    @endphp

                    <div class="text-4xl font-bold text-gray-900">{{ $averageRating }}</div>

                    <div class="flex items-center mb-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $i <= $averageRating ? 'gold' : 'none' }}"
                                viewBox="0 0 24 24" class="w-5 h-5">
                                <path
                                    d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                            </svg>
                        @endfor
                    </div>

                    <p class="text-sm text-gray-600 mb-2">
                        {{ __('messages.based_on_reviews', ['count' => $totalReviews ?? 0]) }}
                    </p>
                    {{-- Rating Bars --}}
                    @foreach (array_reverse($ratingsPercent, true) as $star => $percent)
                        <div class="flex items-center text-[16px] text-[#212121] mb-1">
                            <span class="w-12">star {{ $star }} </span>
                            <div class="flex-1 h-2 bg-gray-200 mx-2 rounded-full overflow-hidden">
                                <div class="bg-[#185D31] h-full" style="width: {{ $percent }}%"></div>
                            </div>
                            <span>{{ $percent }}%</span>
                        </div>
                    @endforeach
                    <p class="mt-5 mb-2 text-[#272727] text-[16px]">{{ __('messages.add_review_desc') }}</p>
                    <!-- Add Review Button -->
                    <div x-cloak x-data="{
                        open: false,
                        rating: 0,
                        comment: '',
                        errorMessage: ''
                    }">
                        <button
                            @click="
            @auth open = true;
                errorMessage = ''; // Clear error message when opening the modal
                rating = 0; // Reset rating when opening
                comment = ''; // Reset comment when opening
            @else 
                window.location.href = '{{ route('login') }}' @endauth
        "
                            class="mt-3 bg-[#185D31] w-full hover:bg-green-800 text-white py-2 px-4 rounded-lg text-sm">
                            {{ __('messages.add_review') }}
                        </button>

                        <div x-show="open" x-cloak
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                            <div @click.away="open = false; errorMessage = ''"
                                class="bg-white rounded-lg p-6 w-full max-w-md">
                                <h2 class="text-center mb-4 text-[20px] font-bold">
                                    {{ __('messages.how_was_experience') }}
                                </h2>

                                <div class="flex justify-center mb-4">
                                    <template x-for="star in 5" :key="star">
                                        <svg @click="rating = star"
                                            :class="{ 'fill-yellow-400': star <= rating, 'fill-gray-300': star > rating }"
                                            class="w-8 h-8 cursor-pointer" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.96a1 1 0 00.95.69h4.17c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.96c.3.92-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.96a1 1 0 00-.364-1.118L2.048 9.387c-.783-.57-.38-1.81.588-1.81h4.17a1 1 0 00.95-.69l1.286-3.96z" />
                                        </svg>
                                    </template>
                                </div>

                                <p class="font-bold text-[20px] mb-2">{{ __('messages.Do_you_have_comment') }}</p>

                                <textarea x-model="comment" placeholder="{{ __('messages.share_your_opinion') }}"
                                    class="w-full border rounded-lg p-2 mb-4" rows="4"></textarea>

                                <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 text-sm mb-4"></p>

                                <button
                                    @click="
                    errorMessage = ''; 

                    if (rating === 0) {
                        errorMessage = '{{ __('messages.please_select_a_rating') }}';
                        return;
                    }
        

                    fetch('{{ route('reviews.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: {{ $product->id }},
                            rating: rating,
                            comment: comment
                        })
                    }).then(res => res.json()).then(data => {
                        if(data.success){
                            open = false;
                            rating = 0;
                            comment = '';
                            errorMessage = ''; // Clear error on success
                            window.location.reload(); // or handle success
                        } else {
                            errorMessage = data.message || '{{ __('messages.error_submitting_review') }}';
                        }
                    }).catch(error => {
                        console.error('Fetch error:', error);
                        errorMessage = '{{ __('messages.network_error') }}'; // Generic network error
                    });
                "
                                    class="bg-[#185D31] w-full hover:bg-green-800 text-white py-2 px-4 rounded-lg text-sm">
                                    {{ __('messages.submit_review') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Reviews List --}}
                <div class="lg:w-2/3 relative" x-data="{
                    openFilter: false,
                    selectedRating: '{{ request('rating') }}',
                    sortBy: '{{ request('sortBy') }}',
                    visible: 5,
                    applyFilters() {
                        let params = new URLSearchParams(window.location.search);
                        if (this.selectedRating) {
                            params.set('rating', this.selectedRating);
                        } else {
                            params.delete('rating');
                        }
                        if (this.sortBy) {
                            params.set('sortBy', this.sortBy);
                        } else {
                            params.delete('sortBy');
                        }
                        window.location.search = params.toString();
                    }
                }">
                    @php
                        $filteredReviews = $product
                            ->reviews()
                            ->when(request('rating'), fn($q) => $q->where('rating', request('rating')))
                            ->when(request('sortBy') === 'highest', fn($q) => $q->orderByDesc('rating'))
                            ->when(request('sortBy') === 'lowest', fn($q) => $q->orderBy('rating'))
                            ->when(request('sortBy') === 'newest', fn($q) => $q->latest())
                            ->get()
                            ->filter(fn($review) => !empty(trim($review->comment)));
                        $totalReviewsCount = $filteredReviews->count();
                    @endphp
                    <div x-show="openFilter" x-cloak
                        class="absolute top-12 left-0 bg-white border rounded-lg shadow-lg p-4 w-72 z-50">
                        <h4 class="font-bold mb-2">{{ __('messages.filterd_by') }}</h4>
                        <div class="space-y-2 mb-4">
                            <label class="flex items-center">
                                <input type="radio" name="rating" value="" x-model="selectedRating"
                                    class="ml-2"> {{ __('messages.all_stars') }}
                            </label>
                            @for ($i = 5; $i >= 1; $i--)
                                <label class="flex items-center">
                                    <input type="radio" name="rating" value="{{ $i }}"
                                        x-model="selectedRating" class="ml-2">
                                    @for ($star = 1; $star <= $i; $star++)
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="gold" viewBox="0 0 16 16"
                                            class="w-4 h-4 inline-block">
                                            <path
                                                d="M8 .25a.7.7 0 0 1 .646.418L10.618 4l3.797.344a.7.7 0 0 1 .4 1.231L11.6 8.568l1.046 3.642a.7.7 0 0 1-1.045.77L8 10.25l-3.6 2.73a.7.7 0 0 1-1.045-.77l1.046-3.643L1.185 5.575a.7.7 0 0 1 .4-1.231L5.382 4 7.354.668A.7.7 0 0 1 8 .25Z" />
                                        </svg>
                                    @endfor
                                </label>
                            @endfor
                        </div>
                        <h4 class="font-bold mb-2">{{ __('messages.sort_by') }}</h4>
                        <div class="space-y-2 mb-4">
                            <label class="flex items-center">
                                <input type="radio" name="sortBy" value="featured" x-model="sortBy" class="ml-2">
                                {{ __('messages.low_reviews') }}
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="sortBy" value="newest" x-model="sortBy"
                                    class="ml-2">{{ __('messages.last_reviews') }}
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="sortBy" value="highest" x-model="sortBy" class="ml-2">
                                {{ __('messages.high_reviews') }}
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="sortBy" value="lowest" x-model="sortBy" class="ml-2">
                                {{ __('messages.low_reviews') }}
                            </label>
                        </div>
                        <div class="flex gap-2">
                            <button @click="selectedRating = null; sortBy = null"
                                class="bg-gray-200 px-4 py-2 rounded-lg">{{ __('messages.reset_filters') }}</button>
                            <button @click="applyFilters()"
                                class="bg-[#185D31] text-white px-4 py-2 rounded-lg">{{ __('messages.apply_filters') }}</button>
                        </div>
                    </div>
                    <div class="flex w-full mb-3  justify-between items-center">
                        <h3 class="text-xl font-bold pb-3">
                            <span>{{ $totalReviewsCount }} {{ __('messages.review') }}</span>
                        </h3>
                        <button @click="openFilter = !openFilter"
                            class="flex items-center text-sm rounded-[12px] border px-[16px] py-[10px] border-[#185D31] text-[#185D31] transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                            {{ __('messages.filter') }}
                        </button>
                    </div>
                    @forelse ($filteredReviews as $index => $review)
                        <div x-show="visible >= {{ $index + 1 }}"
                            class="border border-gray-100 py-3 px-4 rounded-lg shadow-sm mb-4">
                            <div class="flex items-center mb-2">
                                <img src="{{ $review->user->profile_picture ? asset('storage/' . $review->user->profile_picture) : asset('images/Unknown_person.jpg') }}"
                                    class="w-8 h-8 rounded-full mx-3" alt="user avatar">
                                <div>
                                    <p class="text-sm font-semibold">{{ $review->user->full_name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($review->review_date ?? $review->created_at)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center text-yellow-500 mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        fill="{{ $i <= $review->rating ? 'gold' : 'none' }}" viewBox="0 0 16 16"
                                        class="w-4 h-4">
                                        <path
                                            d="M8 .25a.7.7 0 0 1 .646.418L10.618 4l3.797.344a.7.7 0 0 1 .4 1.231L11.6 8.568l1.046 3.642a.7.7 0 0 1-1.045.77L8 10.25l-3.6 2.73a.7.7 0 0 1-1.045-.77l1.046-3.643L1.185 5.575a.7.7 0 0 1 .4-1.231L5.382 4 7.354.668A.7.7 0 0 1 8 .25Z" />
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-[16px] text-gray-700 mb-2">
                                {{ $review->comment }}
                            </p>
                            @if (Str::length($review->comment) > 100)
                                <button class="text-sm text-[#185D31] font-medium">عرض المزيد</button>
                            @endif
                            <div class="flex items-center gap-2 mt-2" x-data="{
                                liked: {{ auth()->check() && $review->likes->contains('user_id', auth()->id()) ? 'true' : 'false' }},
                                count: {{ $review->likes->count() }}
                            }">
                                @auth
                                    <form x-data
                                        @submit.prevent="
    fetch('{{ route('reviews.like', $review) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
    .then(data => {
        liked = data.liked;
        count = data.count;
    });
">

                                        <button type="submit"
                                            class="flex items-center text-sm rounded-[12px] border px-[16px] py-[10px] border-[#185D31] text-[#185D31] transition">
                                            <template x-if="liked">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-hand-thumbs-up-fill"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z" />
                                                </svg>
                                            </template>
                                            <template x-if="!liked">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-hand-thumbs-up" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" />
                                                </svg>
                                            </template>
                                            <span class="ml-1"
                                                x-text="liked ? '{{ __('messages.unuseful') }}' : '{{ __('messages.useful') }}'"></span>
                                            <span class="mx-3" x-text="count"></span>
                                        </button>
                                    </form>
                                @else
                                    <div
                                        class="flex items-center text-sm rounded-[12px] border px-[16px] py-[10px] border-gray-300 text-gray-500 cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                                            <path
                                                d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a10 10 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733q.086.18.138.363c.077.27.113.567.113.856s-.036.586-.113.856c-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.2 3.2 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.8 4.8 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z" />
                                        </svg>
                                        <span>{{ __('messages.useful') }}</span>
                                        <span class="mr-2" x-text="count"></span>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center text-center">
                            <img class="w-[280px] h-[227px] mb-4" src="{{ asset('images/Illustrations (4).svg') }}"
                                onerror="this.onerror=null;this.src='https://placehold.co/280x227/E0E0E0/ADADAD?text=No+Reviews+Illustration';"
                                alt="No Reviews Illustration">
                            <p class="text-[#696969] text-[24px] font-semibold">{{ __('messages.no_review') }}</p>
                        </div>
                    @endforelse
                    @if ($filteredReviews->count() > 3)
                        <button
                            class="mt-4 bg-[#185D31] px-[20px] py-[12px] rounded-[12px] font-semibold text-white flex items-center justify-center gap-2"
                            x-show="visible <= {{ $filteredReviews->count() }}"
                            @click="
        loading = true;
        setTimeout(() => {
            visible = visible + 3;
            loading = false;
        }, 500);
    "
                            x-data="{ loading: false }" :disabled="loading">

                            <svg x-show="loading" class="w-5 h-5 animate-spin text-white" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                                </path>
                            </svg>
                            <span>{{ __('messages.show_more') }}</span>

                        </button>
                    @endif

                </div>

            </div>
        </div>


        @if ($relatedProducts->count())
            <div class="mt-10">
                <h2 class="text-[40px] font-bold mb-4">{{ __('messages.may_like') }}</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @forelse ($relatedProducts as $product)
                        <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                            <div class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
                                <div class="swiper-wrapper">
                                    @php
                                        $images = is_string($product->images)
                                            ? json_decode($product->images, true)
                                            : $product->images ?? [];
                                    @endphp
                                    @if (!empty($images) && count($images) > 0)
                                        @foreach ($images as $image)
                                            <div class="swiper-slide">
                                                <img src="{{ asset($image) }}"
                                                    onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                                    class="w-full h-full object-contain">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="swiper-slide">
                                            <img src="{{ asset($product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                                onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                                class="w-full h-full object-contain">
                                        </div>
                                    @endif
                                </div>
                                @php
                                    $images = is_string($product->images)
                                        ? json_decode($product->images, true)
                                        : $product->images ?? [];
                                @endphp
                                <div class="swiper-pagination image-pagination"
                                    style="{{ count($images) <= 1 ? 'display:none;' : '' }}"></div>
                                @if ($product->is_offer && $product->discount_percent)
                                    <span
                                        class="absolute top-3 right-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                        {{ __('messages.discount_percentage', ['percent' => $product->discount_percent]) }}
                                    </span>
                                @endif
                                <button
                                    class="favorite-button absolute top-3 rtl:left-3 ltr:right-3 bg-white p-2 rounded-full shadow-md text-gray-500 hover:text-red-500 transition-colors duration-200 z-10"
                                    data-product-id="{{ $product->id }}" aria-label="Add to favorites">
                                    @if (Auth::check() && Auth::user()->hasFavorited($product->id))
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
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="flex w-full items-center text-sm mb-2 justify-between">
                                    <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $product->name }}</h3>
                                    <div class="flex items-center ">
                                        <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                        <span class="text-[18px]">{{ $product->rating ?? '4.5' }}</span>
                                    </div>
                                </div>
                                <span
                                    class="text-[#696969] text-[20px]">{{ $product->subCategory->category->name ?? 'غير مصنف' }}</span>
                                <div class="flex mt-2">
                                    @if ($product->supplier_confirmed)
                                        <span class="flex items-center text-[#185D31]">
                                            <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                                src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                            <p class="text-[20px] text-[#212121] ">{{ $product->supplier_name }}
                                            </p>
                                        </span>
                                    @else
                                        <p class="text-[20px] text-[#212121] ">{{ $product->supplier_name }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex items-center mb-2">
                                    <span class=" flex text-lg font-bold text-gray-800">
                                        {{ number_format($product->price * (1 - ($product->discount_percent ?? 0) / 100), 2) }}
                                        <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}"
                                            alt="">
                                    </span>
                                    @if ($product->is_offer && $product->discount_percent)
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
                    @empty
                        <div class="swiper-slide w-full text-center py-10 text-gray-600">
                            <p class="text-2xl font-bold mb-4">{{ __('messages.no_offers_available_title') }}</p>
                            <p>{{ __('messages.no_offers_available_description') }}</p>
                        </div>
                    @endforelse
                </div>
                <div class="swiper-pagination offer-swiper-pagination mt-8"></div>

                <div class="mt-8">
                    {{ $relatedProducts->links('pagination::tailwind') }}
                </div>
            </div>
        @endif




    </div>
@endsection



{{-- Login Popup HTML --}}
<div id="login-popup" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 max-w-sm mx-4">
        <h2 class="text-2xl font-bold mb-4 text-gray-900">{{ __('messages.login_important') }}</h2>
        <p class="mb-6 text-gray-700">{{ __('messages.login_important_for_fav') }}</p>
        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <button id="close-login-popup"
                class="bg-gray-200 text-gray-800 py-2 px-4 ml-2 rounded-md hover:bg-gray-300 transition-colors">
                {{ __('messages.cancel') }}
            </button>
            <a href="{{ route('login') }}"
                class="bg-[#185D31] text-white py-2 px-4 rounded-md hover:bg-[#154a2a] transition-colors">
                {{ __('messages.login') }}
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const priceItems = document.querySelectorAll('.price-item');
        const selectedImageSrc = "{{ asset('images/Saudi_Riyal_Symbol (2).svg') }}";
        const defaultImageSrc = "{{ asset('images/Saudi_Riyal_Symbol.svg') }}";
        const selectedColor = '#185D31'; // Green color for selected price
        const defaultColor = '#212121'; // Default price color

        priceItems.forEach(item => {
            item.addEventListener('click', function() {
                // First, reset all items to their default state
                priceItems.forEach(p => {
                    p.style.color = defaultColor;
                    p.querySelector('.currency-symbol').src = defaultImageSrc;
                });

                // Then, apply the selected state to the clicked item
                this.style.color = selectedColor;
                this.querySelector('.currency-symbol').src = selectedImageSrc;
            });
        });
    });
</script>

{{-- Include Swiper JS --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productMainSwiper = new Swiper('.product-main-swiper', {
            loop: true,
            spaceBetween: 10,
            slidesPerView: 1,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.product-main-pagination',
                clickable: true,
            },
        });

        const productThumbnailSwiper = new Swiper('.product-thumbnail-swiper', {
            spaceBetween: 8,
            slidesPerView: 'auto',
            freeMode: true,
            watchSlidesProgress: true,
            breakpoints: {
                640: {
                    slidesPerView: 5
                },
                768: {
                    slidesPerView: 5
                },
                1024: {
                    slidesPerView: 4
                },
            },
        });

        productMainSwiper.controller.control = productThumbnailSwiper;
        productThumbnailSwiper.controller.control = productMainSwiper;

        const colorSwatches = document.querySelectorAll('.color-swatch');
        const selectedColorNameSpan = document.getElementById('selectedColorName');

        const initialSelectedSwatch = document.querySelector('.color-swatch.selected');
        if (initialSelectedSwatch) {
            selectedColorNameSpan.textContent = initialSelectedSwatch.dataset.colorName;
        } else {
            selectedColorNameSpan.textContent = " ";
        }

        colorSwatches.forEach(swatch => {
            swatch.addEventListener('click', function() {
                colorSwatches.forEach(s => s.classList.remove('selected'));
                this.classList.add('selected');
                selectedColorNameSpan.textContent = this.dataset.colorName;
            });
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
                                disableOnInteraction: false
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

        const isUserLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const favoriteButtons = document.querySelectorAll('.favorite-button');
        const loginPopup = document.getElementById('login-popup');
        const closeLoginPopupBtn = document.getElementById('close-login-popup');

        favoriteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                if (!isUserLoggedIn) {
                    event.preventDefault();
                    loginPopup.classList.remove('hidden');
                } else {
                    const productId = this.dataset.productId;
                    fetch(`/products/${productId}/toggle-favorite`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                product_id: productId
                            })
                        })
                        .then(response => {
                            if (response.status === 401) {
                                window.location.href = '/login';
                                return Promise.reject('Unauthenticated');
                            }
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            const svg = this.querySelector('svg');
                            if (data.is_favorited) {
                                svg.setAttribute('fill', 'currentColor');
                                svg.classList.add('text-red-500');
                                svg.classList.remove('text-gray-500');
                            } else {
                                svg.setAttribute('fill', 'none');
                                svg.classList.remove('text-red-500');
                                svg.classList.add('text-gray-500');
                            }
                        })
                        .catch(error => console.error('Error toggling favorite:', error));
                }
            });
        });

        if (closeLoginPopupBtn) {
            closeLoginPopupBtn.addEventListener('click', function() {
                loginPopup.classList.add('hidden');
            });
        }

        if (loginPopup) {
            loginPopup.addEventListener('click', function(event) {
                if (event.target === loginPopup) {
                    loginPopup.classList.add('hidden');
                }
            });
        }
    });
</script>
<script src="//unpkg.com/alpinejs" defer></script>
