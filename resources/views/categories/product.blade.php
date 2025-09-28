@extends('layouts.app')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<style>
    /* Custom styles for color swatches */
    .color-swatch {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        /* Light border */
        cursor: pointer;
        transition: transform 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }

    .color-swatch:hover {
        transform: scale(1.1);
    }

    .color-swatch.selected {
        border: 2px solid #3b82f6;
        /* Blue border for selected */
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
        /* Blue shadow for selected */
    }

    /* Custom styles for size buttons */
    .size-button {
        padding: 8px 12px;
        border: 1px solid #cbd5e0;
        /* Light gray border */
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }

    .size-button:hover {
        background-color: #f0f0f0;
    }

    .size-button.selected {
        background-color: #3b82f6;
        /* Blue background for selected */
        color: white;
        border-color: #3b82f6;
    }

    /* Hide scrollbar for filter sidebar */
    .sidebar-scroll {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    .sidebar-scroll::-webkit-scrollbar {
        display: none;
        /* Chrome, Safari, Opera */
    }
</style>

@section('content')
    <div class="py-8 mx-[64px]  flex flex-col lg:flex-row gap-6">

        <aside
            class="w-full lg:w-1/4 bg-white p-6 rounded-xl shadow-lg h-fit lg:sticky lg:top-8 sidebar-scroll overflow-y-auto max-h-[calc(100vh-64px)]">
            <div class="flex justify-between w-full mb-6 border-b pb-3">
                <h2 class="text-[24px] font-bold text-[#212121]">{{ __('messages.filters') }}</h2>
                <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                    alt="Filters Icon">
            </div>

            <form id="filterForm" method="GET" action="{{ route('products.index') }}">
                @if (request()->has('sub_category_id'))
                    <input type="hidden" name="sub_category_id" value="{{ request('sub_category_id') }}">
                @endif

                <div class="mb-6">
                    <input type="text" id="search" name="search" placeholder="{{ __('messages.search_products') }}"
                        value="{{ request('search') }}"
                        class="w-full p-3 border border-gray-300 rounded-lg transition duration-200">
                </div>

                {{-- Description filter (assuming it's still a separate top-level column) --}}
                <div class="mb-6 border-b pb-3">
                    <div class="flex flex-col space-y-2">
                        @php $selectedDescriptions = (array) request('description'); @endphp
                        @forelse($availableDescriptions as $descriptionOption)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="description[]" value="{{ $descriptionOption }}"
                                    class="form-checkbox h-[20px] w-[20px] rounded-[6px] border-2 border-black focus:ring-[#185D31] accent-[#185D31]"
                                    {{ in_array($descriptionOption, $selectedDescriptions) ? 'checked' : '' }}
                                    onchange="document.getElementById('filterForm').submit()">
                                <span class="rtl:mr-3 ltr:ml-3 text-[#212121] text-base">{{ $descriptionOption }}</span>
                            </label>
                        @empty
                            {{-- <p class="text-gray-500 text-sm">{{ __('messages.no_description_filters_available') }}</p> --}}
                        @endforelse
                    </div>
                </div>

                {{-- COLORS --}}
             @php
    $selectedColors = (array) request('colors');
@endphp

@if (!empty($availableSpecifications['colors']))
    <div class="mb-6 border-b pb-3">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('messages.colors') }}</h3>
        <div class="grid grid-cols-6 gap-2">
            @foreach ($availableSpecifications['colors'] as $color)
                @php
                    $colorName = $color['name'] ?? '';
                    $colorKey = mb_strtolower($colorName); // lowercase for matching
                    $bgColor = $colorHexMap[$colorKey] ?? '#ccc'; // get from colors.php
                    $isSelected = in_array($colorName, $selectedColors);
                @endphp

                <label class="flex items-center justify-center">
                    <input type="checkbox" name="colors[]" value="{{ $colorName }}" class="hidden"
                        {{ $isSelected ? 'checked' : '' }}
                        onchange="document.getElementById('filterForm').submit()">
                    <div class="color-swatch {{ $isSelected ? 'selected' : '' }} 
                        h-[37px] w-[37px] rounded-full border border-gray-300"
                        style="background-color: {{ $bgColor }}; background-size: cover; background-position: center;"
                        title="{{ $colorName }}">
                    </div>
                </label>
            @endforeach
        </div>
    </div>
@endif



                {{-- SIZES --}}
                @php
                    $selectedSizes = (array) request('sizes');
                @endphp

                @if (!empty($availableSpecifications['sizes']))
                    <div class="mb-6 border-b pb-3">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('messages.sizes') }}</h3>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach ($availableSpecifications['sizes'] as $size)
                                @php $isSelected = in_array($size, $selectedSizes); @endphp
                                <label class="flex items-center justify-center">
                                    <input type="checkbox" name="sizes[]" value="{{ $size }}" class="hidden"
                                        {{ $isSelected ? 'checked' : '' }}
                                        onchange="document.getElementById('filterForm').submit()">
                                    <div
                                        class="size-button w-[71px] bg-[#EDEDED] py-1 text-center px-2 rounded {{ $isSelected ? 'border border-[#185D31]' : '' }}">
                                        {{ $size }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif


                {{-- MATERIAL TYPE --}}
                @php
                    $selectedMaterial = request('material_type');
                @endphp

                @if (!empty($availableSpecifications['material_type']))
                    <div class="mb-6 border-b pb-3">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('messages.material_type') }}</h3>
                        @foreach ($availableSpecifications['material_type'] as $materialType)
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="material_type" value="{{ $materialType }}"
                                    class="form-checkbox h-[20px] w-[20px] rounded-[6px] border-2 border-black focus:ring-[#185D31] accent-[#185D31] rtl:ml-2"
                                    {{ $selectedMaterial == $materialType ? 'checked' : '' }}
                                    onchange="document.getElementById('filterForm').submit()">
                                <span class="text-[#212121] text-base">{{ $materialType }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif

                {{-- Rating --}}
                <div class="mb-6 border-b pb-3">
                    <h3 class="text-lg font-bold text-[20px] text-black mb-3">{{ __('messages.rating') }}</h3>
                    @php
                        $ratings = [
                            '5.0' => __('messages.5.0&Up'),
                            '4.5' => __('messages.4.5&Up'),
                            '4.0' => __('messages.4.0&Up'),
                        ];
                        $selectedRating = request('rating');
                    @endphp
                    @foreach ($ratings as $value => $label)
                        <label class="flex items-center mb-2 text-gray-700">
                            <input type="radio" name="rating" value="{{ $value }}"
                                class="form-radio h-[20px] w-[20px] rounded-full border-2 border-black focus:ring-[#185D31] accent-[#185D31]"
                                {{ $selectedRating == $value ? 'checked' : '' }}
                                onchange="document.getElementById('filterForm').submit()">
                            <span class="rtl:mr-3 ltr:ml-3">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                {{-- Trusted Factory --}}
                @if (!Auth::check() || Auth::user()->account_type !== 'supplier')
                    <div class="mb-6 border-b pb-3">
                        <h3 class="text-lg font-bold text-gray-800 mb-3"> {{ __('messages.suppliers_featured') }} </h3>
                        <label class="flex items-center mb-2 text-gray-700">
                            <input type="checkbox" id="supplier_confirmed" name="supplier_confirmed" value="1"
                                class="form-checkbox h-[20px] w-[20px] rounded-[6px] border-2 border-black focus:ring-[#185D31] accent-[#185D31]"
                                {{ request('supplier_confirmed') == '1' ? 'checked' : '' }}
                                onchange="document.getElementById('filterForm').submit()">
                            <img class="ltr:ml-3 rtl:mr-3 w-[20px] h-[20px]" src="{{ asset('images/Success.svg') }}"
                                alt="Confirmed Supplier">
                            <span class="rtl:mr-1 ltr:ml-1">{{ __('messages.supplier_confirmed') }}</span>
                        </label>
                    </div>
                @endif


                {{-- Delivery Options --}}
                @if (!Auth::check() || Auth::user()->account_type !== 'supplier')
                    <div class="mb-6 border-b pb-3">
                        <h3 class="text-lg font-bold text-gray-800 mb-3">{{ __('messages.delivery') }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ __('messages.unit_price_depends_on_delivery_date') }}</p>

                        @php $selectedDeliveryDate = request('delivery_date'); @endphp

                   @foreach ($deliveryOptions as $dateValue => $optionData)
    <label class="flex items-center mb-2 text-gray-700">
        <input type="radio" name="delivery_date" value="{{ $dateValue }}"
            class="form-radio h-[20px] w-[20px] rounded-full border-2 border-black focus:ring-[#185D31] accent-[#185D31]"
            {{ request('delivery_date') == $dateValue ? 'checked' : '' }}
            onchange="document.getElementById('filterForm').submit()">
        <span class="rtl:mr-3 ltr:ml-3">
            {{ __('messages.' . $optionData['label_key'], ['date' => $optionData['date_param']]) }}
        </span>
    </label>
@endforeach

                    </div>
                @endif
                {{-- Price Range Slider --}}
                <style>
                    input[type="range"]::-webkit-slider-thumb {
                        background-color: #185D31;
                        /* Blue-500 */
                        border: none;
                        border-radius: 50%;
                        width: 16px;
                        height: 16px;
                        cursor: pointer;
                        -webkit-appearance: none;
                        margin-top: -6px;
                    }

                    input[type="range"]::-moz-range-thumb {
                        background-color: #185D31;
                        /* Blue-500 */
                        border: none;
                        border-radius: 50%;
                        width: 16px;
                        height: 16px;
                        cursor: pointer;
                    }

                    input[type="range"]::-webkit-slider-runnable-track {
                        height: 4px;
                        background: #cbd5e1;
                        /* slate-300 */
                        border-radius: 2px;
                    }

                    input[type="range"]::-moz-range-track {
                        height: 4px;
                        background: #cbd5e1;
                        border-radius: 2px;
                    }
                </style>

                <div class="mb-6 border-b pb-3">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">{{ __('messages.price') }}</h3>

                    <div class="flex flex-col gap-2 mb-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span id="minPriceLabel" class="flex">{{ __('messages.from') }}:
                                {{ request('min_price', 0) }}
                                <img class="mx-1 w-[15px] h-[16px]" src="{{ asset('images/Vector (3).svg') }}"
                                    alt="">
                            </span>
                            <span id="maxPriceLabel" class="flex">{{ __('messages.to') }}:
                                {{ request('max_price', 100000) }}
                                <img class="mx-1 w-[15px] h-[16px]" src="{{ asset('images/Vector (3).svg') }}"
                                    alt="">
                            </span>
                        </div>
                        <div class="relative flex items-center gap-2">
                            <input type="range" min="0" max="1000" step="10" id="minRange"
                                name="min_price" value="{{ request('min_price', 0) }}"
                                class="w-full appearance-none bg-transparent focus:outline-none"
                                oninput="updateRangeLabels()" onchange="document.getElementById('filterForm').submit()">

                            <input type="range" min="0" max="1000" step="10" id="maxRange"
                                name="max_price" value="{{ request('max_price', 1000) }}"
                                class="w-full appearance-none bg-transparent focus:outline-none"
                                oninput="updateRangeLabels()" onchange="document.getElementById('filterForm').submit()">
                        </div>
                    </div>
                </div>

                <script>
                    function updateRangeLabels() {
                        const min = document.getElementById('minRange').value;
                        const max = document.getElementById('maxRange').value;
                        // Update your labels based on the selected range values
                        // document.getElementById('minPriceLabel').textContent = '{{ __('messages.from') }}: ' + min;
                        // document.getElementById('maxPriceLabel').textContent = '{{ __('messages.to') }}: ' + max;
                    }

                    function clearPriceFilter() {
                        document.getElementById('minRange').value = 0;
                        document.getElementById('maxRange').value = 100000;
                        updateRangeLabels();
                        document.getElementById('filterForm').submit();
                    }

                    document.addEventListener('DOMContentLoaded', updateRangeLabels);
                </script>

                {{-- Minimum Order Quantity --}}
                @if (!Auth::check() || Auth::user()->account_type !== 'supplier')
                    <div class="mb-6 border-b pb-3">
                        <h3 class="text-lg font-bold text-gray-800 mb-3">{{ __('messages.min_order_quantity') }}</h3>
                        <input type="number" name="min_order_quantity" value="{{ request('min_order_quantity') }}"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            onchange="document.getElementById('filterForm').submit()">
                    </div>
                @endif
                <div class="mb-6">
                    <button type="submit"
                        class="w-full bg-[#185D31] text-white py-3 rounded-[12px] font-semibold transition duration-300 shadow-md">
                        {{ __('messages.apply_filters') }}</button>
                </div>
                <div class="mb-6">
                    <button type="button" onclick="resetFilters()"
                        class="w-full bg-[#EDEDED] text-[#696969] py-3 rounded-[12px] font-semibold transition duration-300 shadow-md">
                        {{ __('messages.reset_filters') }}</button>
                </div>
            </form>
        </aside>


        <main class="w-full lg:w-3/4">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <p class="inline-flex flex-row text-[14px] px-[16px] py-[10px] rounded-[12px] text-white bg-[#185D31] mb-2 md:sm-0"
                    id="breadcrumbs">
                    <a href="{{ route('home') }}" class="hover:underline">{{ __('messages.home') }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 mx-1 mt-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                    </svg>
                    @if ($currentCategory)
                        <a href="#" class="hover:underline">{{ $currentCategory->name }}</a>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-4 mx-1 mt-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                        </svg>
                    @endif

                    @if ($currentSubCategory)
                        <a href="{{ route('products.index', ['sub_category_id' => $currentSubCategory->id]) }}"
                            class="hover:underline">{{ $currentSubCategory->name }}</a>
                    @endif

                </p>
                <!-- ✅ Alpine.js custom dropdown that submits form on select -->
                <div x-data="{
                    open: false,
                    selected: '{{ request('sort_by') ? __('messages.' . str_replace('_', '', request('sort_by'))) : __('messages.sort_by') }}',
                    value: '{{ request('sort_by') ?? '' }}'
                }" class="relative w-[200px]">

                    <!-- Display -->
                    <button type="button" @click="open = !open"
                        class="w-full border border-[#185D31] px-4 py-2 rounded-lg flex justify-between items-center text-sm md:text-base">
                        <span x-text="selected"></span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Options -->
                    <ul x-show="open" @click.away="open = false"
                        class="absolute left-0 w-full bg-white border border-[#185D31] mt-1 rounded-lg shadow z-50">

                        <template
                            x-for="option in [
        {label: '{{ __('messages.filter_by_price_low') }}', value: 'price_asc'},
        {label: '{{ __('messages.filter_by_price_high') }}', value: 'price_desc'},
        {label: '{{ __('messages.latest_products') }}', value: 'latest'},
        {label: '{{ __('messages.filter_by_rating') }}', value: 'rating_desc'}
      ]">
                            <li @click="
            selected = option.label;
            value = option.value;
            open = false;
            $nextTick(() => { 
              document.getElementById('sort_by_hidden').value = value;
              document.getElementById('filterForm').submit();
            });
          "
                                class="px-4 py-2 hover:bg-[#185D31] hover:text-white cursor-pointer">
                                <span x-text="option.label"></span>
                            </li>
                        </template>

                    </ul>

                    <!-- Hidden input that the form reads -->
                    <input type="hidden" name="sort_by" id="sort_by_hidden" :value="value" form="filterForm">
                </div>

            </div>

            @if ($products->isEmpty())
                <div class="bg-white p-8 rounded-xl shadow-lg text-center text-gray-600">
             <p class="text-xl font-semibold mb-4">{{ __('messages.no_products_found') }}</p>
             <p>{{ __('messages.try_adjusting_filters') }}</p>

                    <button onclick="resetFilters()"
                        class="mt-6 bg-[#185D31] text-white py-2 px-4 rounded-lg transition duration-300">
                        {{ __('messages.reset_filters') }}
                    </button>
                </div>
            @else
<!-- wrapper واحد يشمل الزر، المودال، والمنتجات -->
<div x-data="compareProducts()" x-cloak>

    <!-- زر المقارنة -->
    <button x-show="selectedProducts.length > 1"
            @click="openCompareModal = true"
            class="bg-green-600 text-white px-4 py-2 rounded mb-4">
        قارن المنتجات
    </button>

    <!-- المودال -->
    <div x-show="openCompareModal" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg w-11/12 max-w-5xl p-6">
            <h2 class="text-xl font-bold mb-4">جدول المقارنة</h2>

            <table class="w-full text-center border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th>الخاصية</th>
                        <template x-for="id in selectedProducts" :key="id">
                            <th x-text="getProductName(id)"></th>
                        </template>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>السعر</td>
                        <template x-for="id in selectedProducts" :key="id">
                            <td x-text="getProductPrice(id)"></td>
                        </template>
                    </tr>
                    <tr>
                        <td>الكمية</td>
                        <template x-for="id in selectedProducts" :key="id">
                            <td x-text="getProductQuantity(id)"></td>
                        </template>
                    </tr>
                          <tr>
                <td>مدة التوصيل</td>
                <template x-for="id in selectedProducts" :key="id">
                    <td x-text="getProductDelivery(id)"></td>
                </template>
            </tr>
            <tr>
                <td>الشركة</td>
                <template x-for="id in selectedProducts" :key="id">
                    <td x-text="getProductCompany(id)"></td>
                </template>
            </tr>
                    <tr>
                        <td>الشهادات</td>
                        <template x-for="id in selectedProducts" :key="id">
                            <td x-text="getProductCertifications(id)"></td>
                        </template>
                    </tr>
                </tbody>
            </table>

            <button @click="openCompareModal = false" class="mt-4 bg-red-500 text-white px-4 py-2 rounded">
                اغلق
            </button>
        </div>
    </div>

    <!-- بطاقات المنتجات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @forelse ($products as $product)
            <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col"
                 data-id="{{ $product->id }}"
                 data-name="{{ $product->name }}"
                 data-price="{{ number_format($product->price_range['min'], 2) }} {{ $product->price_range['min'] != $product->price_range['max'] ? ' - ' . number_format($product->price_range['max'], 2) : '' }}"
                 data-available_quantity="{{ $product->available_quantity ?? '1' }}"
                 data-attachments="{{ $product->attachments ?? 'لا يوجد' }}"
                 data-delivery="{{ $product->shipping_days }}" {{-- ✅ new logic --}}
                 data-company="{{ $product->supplier->company_name ?? '' }}"> {{-- ✅ supplier name --}}

                {{-- ✅ هنا الكارت الأصلي بتاعك بدون تغيير --}}
                            <div class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
                                <div class="swiper-wrapper">
                                    @php
                                        $images = collect(
                                            is_string($product->images)
                                                ? json_decode($product->images, true)
                                                : $product->images ?? [],
                                        );
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
                                @php
                                    $offer = $product->offer; // Relationship: Product hasOne Offer
                                @endphp

                                @if ($offer && $offer->discount_percent)
                                    <span
                                        class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                        {{ __('messages.discount_percentage', ['percent' => $product->offer->discount_percent]) }}
                                    </span>
                                @endif
                                @if (!Auth::check() || Auth::user()->account_type !== 'supplier')
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
                                @endif
                            </div>
                            <div class="p-4 flex flex-col flex-grow">
                                <div class="flex w-full items-center text-sm mb-2">
 <input type="checkbox"
                                   x-model="selectedProducts"
                                   :value="{{ $product->id }}"
                                   class="mx-2">


                                    <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $product->name }}</h3>
                                    <div class="flex items-center ">
                                        @if( $product->rating)
                                        <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                        @endif
                                        <span class="text-[18px]">{{ $product->rating }}</span>
                                    </div>
                                </div>
                                <span
                                    class="text-[#696969] text-[20px]">{{ $product->subCategory->category->name ?? 'غير مصنف' }}</span>
                                @if (!Auth::check() || Auth::user()->account_type !== 'supplier')
                                    <div class="flex mt-2">
                                        @if ($product->supplier->supplier_confirmed)
                                            <span class="flex items-center text-[#185D31]">
                                                <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                                    src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                              <a href="{{ route('suppliers.show', $product->supplier->id) }}"
   class="inline-block py-1 rounded-lg text-[#185D31] text-[18px] font-medium  transition">
    {{ $product->supplier->company_name }}
</a>
                                            </span>
                                        @else
                                          <a href="{{ route('suppliers.show', $product->supplier->id) }}"
   class="inline-block py-1 rounded-lg text-[#185D31] text-[18px] font-medium  transition">
    {{ $product->supplier->company_name }}
</a>
                                        @endif
                                    </div>
                                @endif
                                <div class="flex items-center mb-2">
                                   <span class="flex text-lg font-bold text-gray-800">
    {{ number_format($product->price_range['min'], 2) }}
    @if($product->price_range['min'] != $product->price_range['max'])
        - {{ number_format($product->price_range['max'], 2) }}
    @endif
    <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}" alt="">
</span>

                                    @php
                                        $offer = $product->offer; // Relationship: Product hasOne Offer
                                    @endphp

                                    @if ($offer && $offer->discount_percent)
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
                                @if (!Auth::check() || Auth::user()->account_type !== 'supplier')
                                    <div class="mt-auto">
                                        <a href="{{ route('products.show', $product->slug) }}"
                                            class="block w-full bg-[#185D31] text-white text-center py-[10px] px-[16px] rounded-[12px] font-medium transition-colors duration-200">
                                            {{ __('messages.view_details') }}
                                        </a>
                                    </div>
                                @else
                                    <div x-data="{ confirmingId: null }">
                                        {{-- Product actions --}}
                                        <div class="mt-auto flex justify-between">
                                            <div class="w-2/3 h-full">
                                                <a href="{{ route('products.show', $product->slug) }}"
                                                    class="block bg-[#185D31] text-white text-center py-[10px] px-[15px] rounded-[12px] font-medium transition-colors duration-200">
                                                    {{ __('messages.view_details') }}
                                                </a>
                                            </div>

                                            <div class="flex justify-between w-1/3 gap-1 h-full rtl:mr-2 ltr:ml-2">
                                                <a href="{{ route('products.edit', $product->id) }}"
                                                    class="flex-1 flex items-center justify-center gap-1 text-center text-[#185D31] py-2 bg-[#EDEDED] rounded-xl transition">
                                                    {{-- Edit icon --}}
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875
                              1.875 0 1 1 2.652 2.652L10.582
                              16.07a4.5 4.5 0 0 1-1.897
                              1.13L6 18l.8-2.685a4.5 4.5
                              0 0 1 1.13-1.897l8.932-8.931Z" />
                                                    </svg>
                                                </a>

                                                {{-- Delete button --}}
                                                <button type="button" @click="confirmingId = {{ $product->id }}"
                                                    class="flex-1 flex items-center justify-center bg-[#EDEDED] gap-1 text-center text-[#185D31] py-2 rounded-xl transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788
                              0L9.26 9m9.968-3.21c.342.052.682.107
                              1.022.166m-1.022-.165L18.16
                              19.673a2.25 2.25 0 0 1-2.244
                              2.077H8.084a2.25 2.25 0 0
                              1-2.244-2.077L4.772
                              5.79m14.456 0a48.108
                              48.108 0 0 0-3.478-.397m-12
                              .562c.34-.059.68-.114
                              1.022-.165m0 0a48.11
                              48.11 0 0 1 3.478-.397m7.5
                              0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964
                              51.964 0 0 0-3.32
                              0c-1.18.037-2.09 1.022-2.09
                              2.201v.916m7.5 0a48.667
                              48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Delete popup --}}
                                        <div x-show="confirmingId" x-cloak
                                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                                            <div class="bg-white p-6 rounded-lg max-w-md w-full">
                                                <h2 class="text-xl font-bold mb-4">{{ __('messages.confirm_delete') }}
                                                </h2>
                                                <p class="mb-4 text-gray-600">{{ __('messages.are_you_sure_delete') }}</p>
                                                <div class="flex justify-end space-x-2">
                                                    <button @click="confirmingId = null"
                                                        class="px-4 py-2 mx-2 bg-gray-300 rounded hover:bg-gray-400 h-10 flex items-center justify-center">
                                                        {{ __('messages.cancel') }}
                                                    </button>
                                                    <form :action="'/products/' + confirmingId" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 h-10 flex items-center justify-center">
                                                            {{ __('messages.delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif


                            </div>
                        
            </div>
        @empty
            <div class="swiper-slide w-full text-center py-10 text-gray-600">
                <p class="text-2xl font-bold mb-4">{{ __('messages.no_offers_available_title') }}</p>
                <p>{{ __('messages.no_offers_available_description') }}</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    function compareProducts() {
        return {
            selectedProducts: [],
            openCompareModal: false,
            getProductData(id) {
                const el = document.querySelector(`.product-card[data-id='${id}']`);
                return el ? el.dataset : {};
            },
            getProductName(id) { return this.getProductData(id).name || ''; },
            getProductPrice(id) { return this.getProductData(id).price || ''; },
            getProductQuantity(id) { return this.getProductData(id).available_quantity || ''; },
            getProductCertifications(id) { return this.getProductData(id).attachments || ''; },
             // ✅ improved delivery: "X days"
        getProductDelivery(id) { 
            const days = this.getProductData(id).delivery || 0;
            return days ? `${days} يوم` : 'غير متوفر'; 
        },

        // ✅ supplier name
        getProductCompany(id) { return this.getProductData(id).company || 'غير محدد'; },
        }
    }
</script>

                <div class="swiper-pagination offer-swiper-pagination mt-8"></div>

                <div class="mt-8">
                    {{ $products->links('pagination::tailwind') }}
                </div>
            @endif
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


<!-- تأكد من وجود هذه القاعدة في CSS -->
<style>
    [x-cloak] { display: none !important; }
</style>


    <script>
        // Function to clear a specific filter
        function clearFilter(filterName) {
            const form = document.getElementById('filterForm');
            if (!form) return;

            // Handle array names like 'color[]', 'size[]', 'gender[]', 'description[]', 'material[]', 'delivery_option[]'
            if (filterName.endsWith('[]')) {
                form.querySelectorAll(`input[name="${filterName}"]`).forEach(checkbox => {
                    checkbox.checked = false;
                    // For color/size swatches, remove the 'selected' class manually
                    if (checkbox.nextElementSibling && (checkbox.nextElementSibling.classList.contains(
                            'color-swatch') || checkbox.nextElementSibling.classList.contains('size-button'))) {
                        checkbox.nextElementSibling.classList.remove('selected');
                    }
                });
            } else {
                const input = form.elements[filterName];
                if (input) {
                    if (input.type === 'radio') {
                        form.querySelectorAll(`input[name="${filterName}"]`).forEach(radio => {
                            radio.checked = false;
                        });
                    } else if (input.type === 'text' || input.type === 'number' || input.tagName === 'SELECT') {
                        input.value = '';
                    }
                }
            }
            form.submit(); // Submit the form after clearing a filter
        }

        // Function to clear price range filters (can be called by resetFilters)
        function clearPriceRange() {
            const form = document.getElementById('filterForm');
            if (!form) return;
            form.elements['min_price'].value = '';
            form.elements['max_price'].value = '';
        }

        // Function to reset all filters while preserving sub_category_id
        function resetFilters() {
            const form = document.getElementById('filterForm');
            if (!form) return;

            let subCategoryIdValue = '';
            const subCategoryIdInput = form.querySelector('input[name="sub_category_id"][type="hidden"]');
            if (subCategoryIdInput) {
                subCategoryIdValue = subCategoryIdInput.value;
            }

            form.reset(); // This clears most inputs

            // Re-apply sub_category_id if it existed
            if (subCategoryIdInput && subCategoryIdValue) {
                subCategoryIdInput.value = subCategoryIdValue;
            }

            // Manually uncheck all checkboxes and radios (except the hidden sub_category_id)
            form.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(input => {
                if (!(input.type === 'hidden' && input.name === 'sub_category_id')) {
                    input.checked = false;
                    // Also remove 'selected' class from visual elements if they exist
                    if (input.nextElementSibling && (input.nextElementSibling.classList.contains('color-swatch') ||
                            input.nextElementSibling.classList.contains('size-button'))) {
                        input.nextElementSibling.classList.remove('selected');
                    }
                }
            });

            // Clear text and number inputs explicitly
            form.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
                input.value = '';
            });

            // Reset select dropdowns to their first option or an empty value
            form.querySelectorAll('select').forEach(select => {
                select.value = ''; // Assuming "" is your default/empty option value
            });

            form.submit(); // Submit the form after resetting filters
        }

        // Add event listeners for color and size clicks to simulate checkbox behavior
        document.querySelectorAll('.color-swatch').forEach(swatch => {
            swatch.addEventListener('click', function() {
                const checkbox = this.previousElementSibling; // The hidden checkbox input
                if (checkbox) {
                    checkbox.checked = !checkbox.checked; // Toggle check state
                    this.classList.toggle('selected', checkbox.checked); // Toggle visual 'selected' class
                    document.getElementById('filterForm').submit(); // Submit the form immediately
                }
            });
        });

        document.querySelectorAll('.size-button').forEach(button => {
            button.addEventListener('click', function() {
                const checkbox = this.previousElementSibling; // The hidden checkbox input
                if (checkbox) {
                    checkbox.checked = !checkbox.checked; // Toggle check state
                    this.classList.toggle('selected', checkbox.checked); // Toggle visual 'selected' class
                    document.getElementById('filterForm').submit(); // Submit the form immediately
                }
            });
        });

        // Ensure initial state of color/size swatches matches current filters on page load
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.color-swatch').forEach(swatch => {
                const checkbox = swatch.previousElementSibling;
                if (checkbox && checkbox.checked) {
                    swatch.classList.add('selected');
                } else {
                    swatch.classList.remove('selected');
                }
            });

            document.querySelectorAll('.size-button').forEach(button => {
                const checkbox = button.previousElementSibling;
                if (checkbox && checkbox.checked) {
                    button.classList.add('selected');
                } else {
                    button.classList.remove('selected');
                }
            });

            // Attach onchange listeners to price inputs only if you want them to auto-submit
            // If you prefer only the 'Apply Filters' button to submit prices, remove these.
            document.querySelector('input[name="min_price"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
            document.querySelector('input[name="max_price"]').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
            // Attach onchange for search input
            document.getElementById('search').addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper for each product card
            document.querySelectorAll('.product-image-swiper').forEach(function(swiperContainer) {
                // Check if there's more than one slide to enable navigation/pagination
                const slidesCount = swiperContainer.querySelectorAll('.swiper-slide').length;

                new Swiper(swiperContainer, {
                    // Optional parameters
                    direction: 'horizontal',
                    loop: slidesCount > 1, // Only loop if there's more than one image

                    // If you need pagination
                    pagination: {
                        el: swiperContainer.querySelector('.swiper-pagination'),
                        clickable: true,
                        // Dynamically enable/disable pagination based on slide count
                        enabled: slidesCount > 1,
                    },

                    // Optional: If you want navigation arrows, add them to your HTML
                    // For example, inside `.product-image-swiper`:
                    // <div class="swiper-button-prev"></div>
                    // <div class="swiper-button-next"></div>
                    // Then uncomment the following:
                    // navigation: {
                    //     nextEl: swiperContainer.querySelector('.swiper-button-next'),
                    //     prevEl: swiperContainer.querySelector('.swiper-button-prev'),
                    // },

                    // And if you want scrollbar
                    // scrollbar: {
                    //     el: '.swiper-scrollbar',
                    // },

                    // AutoPlay (optional)
                    // autoplay: {
                    //     delay: 2500,
                    //     disableOnInteraction: false,
                    // },
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            function initializeInnerSwipers() {
                document.querySelectorAll('.inner-swiper').forEach(swiperElement => {
                    // Prevent duplicate initialization
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
            initializeInnerSwipers(); // Initial call to set up inner swipers on page load

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
@endsection
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
