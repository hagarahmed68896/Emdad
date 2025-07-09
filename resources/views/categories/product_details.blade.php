@extends('layouts.app')

{{-- Include Swiper CSS --}}
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
{{-- Include Tailwind CSS (assuming it's already configured or loaded via CDN) --}}
<script src="https://cdn.tailwindcss.com"></script>

<style>
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
        <div class="py-8 flex flex-col lg:flex-row gap-8 items-stretch">

            {{-- Product Image Gallery Section --}}
<div class="w-full lg:w-1/2  rounded-[12px]  flex flex-col items-center">

    {{-- Main Product Image Swiper --}}
    <div class="relative w-full h-[960px] bg-[#EDEDED]  overflow-hidden rounded-[12px]">
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

            {{-- Discount Badge (Top Right) --}}
            @if ($product->is_offer && $product->discount_percent)
                <span
                    class="absolute top-3 right-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-4 py-2 rounded-full z-10">
                    {{ __('messages.discount_percentage', ['percent' => $product->discount_percent]) }}
                </span>
            @endif

            {{-- Favorite Button (Top Left) --}}
            <button
                class="favorite-button absolute top-3 left-3 bg-white p-2 rounded-full shadow-md text-gray-500 hover:text-red-500 transition-colors duration-200 z-10"
                data-product-id="{{ $product->id }}" aria-label="Add to favorites">
                @if (Auth::check() && Auth::user()->hasFavorited($product->id))
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
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
            class="swiper-slide w-[166px] h-[166px] bg-[#EDEDED] rounded-md overflow-hidden border-2 border-transparent hover:border-[#185D31] transition-all duration-200 cursor-pointer">
            <img src="{{ asset($imagePath) }}"
                onerror="this.onerror=null;this.src='https://placehold.co/120x120/F0F0F0/ADADAD?text=Thumb';"
                alt="Thumbnail"
                class="w-full h-full object-cover rounded-md">
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
                            $rating = $product->rating ?? 0;
                            $fullStars = floor($rating);
                            $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0;
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
                        <span
                            class="text-[16px] text-[#212121] rtl:mr-1 ltr:ml-1">({{ number_format($product->rating ?? 0, 1) }}
                            {{ __('messages.stars') }})</span>
                        <span class="text-[14px] text-bold mr-2">•</span>
                        <span class="text-[#212121] text-[16px] ltr:ml-2 rtl:mr-2">{{ $product->reviews_count ?? 0 }}
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

                {{-- price tiers --}}
                <div class="mb-2 w-full bg-[#F8F9FA] p-4 rounded-[12px]">
                    <p class="bg-white w-[129px] h-[40px] px-[16px] py-[8px] rounded-[40px]">
                        {{ __('messages.selectable') }}</p>
                    <div class="grid grid-cols-3 gap-3">
                        @forelse ($product->price_tiers as $tier)
                            <div class="p-3">
                                <p class="text-[16px] text-[#696969]">
                                    @if (isset($tier['max_qty']))
                                        {{ $tier['min_qty'] }}-{{ $tier['max_qty'] }} {{ __('messages.pieces') }}
                                    @else
                                        {{ $tier['min_qty'] }}+ {{ __('messages.pieces') }}
                                    @endif
                                </p>
                                <p class="text-[24px] text-[#212121] font-bold text-[#185D31]">
                                    {{ number_format($tier['price'], 2) }}
                                    <img class="inline-block mx-1 w-[24px] h-[27px]"
                                        src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="Currency">
                                </p>
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
                    @endphp
                    <div class="flex w-full justify-between">
                        <p class="text-[24px] font-bold mb-3">{{ __('messages.changes') }}</p>
                        <a href=""
                            class="underline text-[#696969] text-[14px] ">{{ __('messages.selectChanges') }}</a>
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
                                <img src="{{ asset('images/تمارا.svg') }}"
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
    </div>
@endsection




{{-- Login Popup HTML --}}
<div id="login-popup" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-8 rounded-lg shadow-lg w-96 max-w-sm mx-4">
        <h2 class="text-2xl font-bold mb-4 text-gray-900">{{ __('messages.login_important') }}</h2>
        <p class="mb-6 text-gray-700">{{ __('messages.login_important_for_fav') }}</p>
        <div class="flex justify-end space-x-2 rtl:space-x-reverse">
            <button id="close-login-popup"
                class="bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors">
                {{ __('messages.cancel') }}
            </button>
            <a href="{{ route('login') }}"
                class="bg-[#185D31] text-white py-2 px-4 rounded-md hover:bg-[#154a2a] transition-colors">
                {{ __('messages.login') }}
            </a>
        </div>
    </div>
</div>



{{-- Include Swiper JS --}}
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize main product image Swiper
        const productMainSwiper = new Swiper('.product-main-swiper', {
            loop: true, // Enable looping
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

        // Initialize thumbnail Swiper
        const productThumbnailSwiper = new Swiper('.product-thumbnail-swiper', {
            spaceBetween: 10,
            slidesPerView: auto, // Number of visible thumbnails
            freeMode: true,
            watchSlidesProgress: true,
            breakpoints: {
                640: {
                    slidesPerView: 5,
                },
                768: {
                    slidesPerView: 6,
                },
                1024: {
                    slidesPerView: 7,
                },
            }
        });

        // Link main Swiper to thumbnail Swiper
        productMainSwiper.controller.control = productThumbnailSwiper;
        productThumbnailSwiper.controller.control = productMainSwiper;


        // Handle color swatch selection
        const colorSwatches = document.querySelectorAll('.color-swatch');
        const selectedColorNameSpan = document.getElementById('selectedColorName');

        // Set initial selected color name
        const initialSelectedSwatch = document.querySelector('.color-swatch.selected');
        if (initialSelectedSwatch) {
            selectedColorNameSpan.textContent = initialSelectedSwatch.dataset.colorName;
        } else {
            selectedColorNameSpan.textContent = " ";
        }


        colorSwatches.forEach(swatch => {
            swatch.addEventListener('click', function() {
                // Remove 'selected' class from all swatches
                colorSwatches.forEach(s => s.classList.remove('selected'));

                // Add 'selected' class to the clicked swatch
                this.classList.add('selected');

                // Update the displayed color name
                selectedColorNameSpan.textContent = this.dataset.colorName;

            });
        });

        // Add to Favorites functionality (AJAX)
        const favoriteButton = document.querySelector('.favorite-button');
        if (favoriteButton) {
            favoriteButton.addEventListener('click', async function() {
                const productId = this.dataset.productId;
                const isFavorited = this.dataset.isFavorited === 'true';
                const url = isFavorited ? `/favorites/${productId}` : `/favorites`;
                const method = isFavorited ? 'DELETE' : 'POST';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')
                        .getAttribute('content');
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: method === 'POST' ? JSON.stringify({
                            product_id: productId
                        }) : null
                    });

                    const data = await response.json();

                    if (response.ok) {
                        // Toggle the heart icon
                        if (data.status === 'favorited') {
                            this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-6 text-red-500">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                </svg>`;
                            this.dataset.isFavorited = 'true';
                        } else if (data.status === 'unfavorited') {
                            this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                </svg>`;
                            this.dataset.isFavorited = 'false';
                        }
                        // Optionally show a success message to the user
                        console.log(data.message);
                    } else {
                        console.error('Error:', data.message || 'Something went wrong.');
                        // Optionally show an error message
                    }
                } catch (error) {
                    console.error('Network or parsing error:', error);
                    // Optionally show a network error message
                }
            });
        }

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
    const thumbnailSwiper = new Swiper('.product-thumbnail-swiper', {
    spaceBetween: 8,
    slidesPerView: 'auto',
    watchSlidesProgress: true,
});

const mainSwiper = new Swiper('.product-main-swiper', {
    spaceBetween: 10,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
    thumbs: {
        swiper: thumbnailSwiper,
    },
});

</script>
