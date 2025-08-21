<section id="myProductsSection" 
    class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-200 {{ request('section') === 'myProductsSection' ? 'hidden' : '' }}">

    <div x-data="{ tab: 'products' }" class="w-full bg-white py-6 rounded-xl">

        <div class="flex justify-between w-full">

            {{-- Tabs --}}
            <div class="flex mb-6">
                <button @click="tab = 'products'"
                    :class="tab === 'products' ? 'text-[#185D31] border-[#185D31]' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-lg font-bold border-b-2 focus:outline-none">
                    {{ __('messages.products') }}
                </button>
                <button @click="tab = 'offers'"
                    :class="tab === 'offers' ? 'text-[#185D31] border-[#185D31]' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-2 text-lg font-bold border-b-2 focus:outline-none">
                    {{ __('messages.offers') }}
                </button>
            </div>

            {{-- filter --}}
     <div x-data="{ open: false }" class="relative">
    <button @click="open = !open"
        class="w-full flex text-[#185D31] border border-[#185D31]  p-2 rounded-[12px] font-semibold transition duration-300 shadow-md mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6 mx-1">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
        </svg>
        {{ __('messages.filter') }}
    </button>

    <div x-show="open" x-transition @click.outside="open = false"
        class="absolute z-50 left-0 w-[300px] md:w-full lg:w-80 bg-white p-6 rounded-xl shadow-lg border border-gray-200 max-h-[500px] overflow-y-auto">
<form id="filterForm" method="GET" action="{{ route('profile.show') }}">
    <input type="hidden" name="section" value="{{ request('section', 'myProductsSection') }}">
    
            @if (request()->has('sub_category_id'))
                <input type="hidden" name="sub_category_id" value="{{ request('sub_category_id') }}">
            @endif

       {{-- CATEGORIES --}}
@if (!empty($userSelectedCategories))
    @php
        $selectedCategories = (array) request()->input('categories', []);
        $allSelected = request()->boolean('all_categories');
    @endphp

    <div class="mb-6 border-b pb-3">
        <h3 class="text-lg font-bold text-gray-800 mb-3">{{ __('messages.categories') }}</h3>
        <div class="flex flex-col gap-2">

            {{-- ✅ ALL OPTION --}}
            <label class="flex items-center text-gray-700">
                <input type="checkbox" name="all_categories" value="1"
                    class="form-checkbox h-[20px] w-[20px] rounded-[6px] border-2 border-black focus:ring-[#185D31] accent-[#185D31]"
                    {{ $allSelected ? 'checked' : '' }}
                    onchange="document.getElementById('filterForm').submit()">
                <span class="rtl:mr-3 ltr:ml-3">{{ __('messages.all') }}</span>
            </label>

            {{-- ✅ INDIVIDUAL CATEGORIES --}}
            @foreach ($categories as $id => $name)
                @if (in_array($id, $userSelectedCategories))
                    <label class="flex items-center text-gray-700">
                        <input type="checkbox" name="categories[]" value="{{ $id }}"
                            class="form-checkbox h-5 w-5 text-[#185D31] focus:ring-[#185D31] rounded"
                            {{ in_array($id, $selectedCategories) ? 'checked' : '' }}
                            onchange="document.getElementById('filterForm').submit()"
                            {{ $allSelected ? 'disabled' : '' }}>
                        <span class="rtl:mr-3 ltr:ml-3">{{ $name }}</span>
                    </label>
                @endif
            @endforeach
        </div>
    </div>
@endif


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
                                $colorKey = mb_strtolower($colorName);
                                $bgColor = $colorHexMap[$colorKey] ?? '#ccc';
                                $isSelected = in_array($colorName, $selectedColors);
                            @endphp

                            <label class="flex items-center justify-center">
                                <input type="checkbox" name="colors[]" value="{{ $colorName }}"
                                    class="hidden" {{ $isSelected ? 'checked' : '' }}
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

            {{-- Price --}}
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

            <div class="mb-6">
                <button type="submit"
                    class="w-full bg-[#185D31] text-white py-3 rounded-[12px] font-semibold transition duration-300 shadow-md">
                    {{ __('messages.apply_filters') }}
                </button>
            </div>
            <div class="mb-6">
                <button type="button" onclick="resetFilters()"
                    class="w-full bg-[#EDEDED] text-[#696969] py-3 rounded-[12px] font-semibold transition duration-300 shadow-md">
                    {{ __('messages.reset_filters') }}
                </button>
            </div>
        </form>
    </div>
</div>
        </div>






        {{-- Overview Tab --}}
        <div x-show="tab === 'products'" class="space-y-8 ">
            <style>
                [x-cloak] {
                    display: none;
                }

                /* Custom CSS to fix Swiper layout */
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

            <div class="bg-white">
                <div class="flex flex-col md:flex-row justify-between">
                    {{-- Supplier Products Section --}}

                    <div class="flex items-center space-x-4 mb-6">
                        <a href="{{ route('products.create') }}"
                            class="flex bg-[#185D31] text-white px-4 py-2 rounded-xl items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6 rtl:ml-2 ltr:mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            {{ __('messages.add_product') }}
                        </a>
                    </div>
                </div>

                @if ($products && $products->count() === 0)
                    <div class="flex flex-col items-center p-4">
                        <img src="{{ asset('/images/Chats illustration.svg') }}" alt="">
                        <p class="mt-4 text-[24px] text-[#696969]">{{ __('messages.no_products') }}</p>
                    </div>
                @elseif ($products && $products->count())
                    <div class="py-8" x-data="{ confirmingId: null }">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($products->take(8) as $product)
                                <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                                    {{-- Inner Swiper --}}
                                    <div
                                        class="swiper-container relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
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
                                                        class="w-full h-full bg-[#F8F9FA] object-contain">
                                                </div>
                                            @empty
                                                <div class="swiper-slide">
                                                    <img src="{{ asset($product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                                        onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                                        class="w-full h-full bg-[#F8F9FA] object-contain">
                                                </div>
                                            @endforelse
                                        </div>

                                        <div class="swiper-pagination image-pagination"
                                            style="{{ $images->count() <= 1 ? 'display:none;' : '' }}"></div>
                                    </div>

                                    {{-- Product Details --}}
                                    <div class="p-4 flex flex-col flex-grow">
                                                                  <div class="flex w-full items-center text-sm mb-2 justify-between">

                            <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $product->name }}</h3>
                                   <div class="flex items-center ">
                                    @if($product->rating)
                                        <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                    @endif
                                    <span class="text-[18px]">{{ $product->rating }}</span>
                                </div>
                                                        </div>
                                        <span
                                            class="text-[#696969] text-[20px]">{{ $product->subCategory->category->name ?? 'غير مصنف' }}</span>
                                        <div class="flex mt-2">
                                            @if ($product->supplier->supplier_confirmed)
                                                <span class="flex items-center text-[#185D31]">
                                                    <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                                        src="{{ asset('images/Success.svg') }}"
                                                        alt="Confirmed Supplier">
                                                    <p class="text-[20px] text-[#212121]">
                                                        {{ $product->supplier->company_name }}</p>
                                                </span>
                                            @else
                                                <p class="text-[20px] text-[#212121]">
                                                    {{ $product->supplier->company_name }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center mb-2">
                                            @php
                                                $offer = $product->offer; // Relationship: Product hasOne Offer
                                            @endphp

                                            @if ($offer && $offer->discount_percent)
                                                {{-- Discounted Price --}}
                                                <span class="flex text-lg font-bold text-gray-800">
                                                    {{ number_format($product->price * (1 - $offer->discount_percent / 100), 2) }}
                                                    <img class="mx-1 w-[20px] h-[21px]"
                                                        src="{{ asset('images/Vector (3).svg') }}" alt="">
                                                </span>

                                                {{-- Original Price (crossed out) --}}
                                                <span class="flex text-sm text-gray-400 line-through mr-2">
                                                    {{ number_format($product->price, 2) }}
                                                    <img class="mx-1 w-[14px] h-[14px] mt-1 inline-block"
                                                        src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}"
                                                        alt="currency">
                                                </span>
                                            @else
                                                {{-- Regular Price --}}
                                                <span class="flex text-lg font-bold text-gray-800">
                                                    {{ number_format($product->price, 2) }}
                                                    <img class="mx-1 w-[20px] h-[21px]"
                                                        src="{{ asset('images/Vector (3).svg') }}" alt="">
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-sm text-gray-600 mb-4">
                                            {{ __('messages.minimum_order_quantity', ['quantity' => $product->min_order_quantity ?? '1']) }}
                                        </p>
                                        <div class="mt-auto flex justify-between">
                                            <div class="w-2/3 h-full">
                                                <a href="{{ route('products.show', $product->slug) }}"
                                                    class="block bg-[#185D31] text-white text-center py-[10px] px-[15px] rounded-[12px] font-medium transition-colors duration-200">
                                                    {{-- Translated: View Details --}}
                                                    {{ __('messages.view_details') }}
                                                </a>
                                            </div>
                                            <div class="flex justify-between w-1/3 gap-1 h-full rtl:mr-2 ltr:ml-2">
                                                <a href="{{ route('products.edit', $product->id) }}"
                                                    class="flex-1 flex items-center justify-center gap-1 text-center text-[#185D31] py-2 bg-[#EDEDED] rounded-xl transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                </a>

                                                <button type="button" @click="confirmingId = {{ $product->id }}"
                                                    class="flex-1 flex items-center justify-center bg-[#EDEDED] gap-1 text-center text-[#185D31] py-2 rounded-xl transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                        class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{-- PAGINATION LINKS HERE --}}
                        <div class="mt-8 flex justify-center" id="favorites-pagination-links">
                            {{ $products->links() }}
                        </div>

                        <div x-show="confirmingId" x-cloak
                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                            <div class="bg-white p-6 rounded-lg max-w-md w-full">
                                <h2 class="text-xl font-bold mb-4">{{ __('messages.confirm_delete') }}</h2>
                                <p class="mb-4 text-gray-600">{{ __('messages.are_you_sure_delete') }}</p>
                                <div class="flex justify-end space-x-2">
                                    <button @click="confirmingId = null"
                                        class="px-4 py-2 mx-2 bg-gray-300 rounded hover:bg-gray-400">
                                        {{ __('messages.cancel') }}
                                    </button>
                                    <form :action="'/products/' + confirmingId" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                            {{ __('messages.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.querySelectorAll('.inner-swiper').forEach((el, index) => {
                        const paginationEl = el.querySelector('.swiper-pagination');
                        if (paginationEl) {
                            new Swiper(el, {
                                loop: true,
                                pagination: {
                                    el: paginationEl,
                                    clickable: true
                                },
                            });
                        }
                    });
                });
            </script>

            <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
            <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
        </div>

        <div x-show="tab === 'offers'" class="space-y-8 ">
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

<div class="bg-white">
    <div class="flex flex-col md:flex-row justify-between">

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

            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="offers-grid">
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
                                             class="w-full h-full bg-[#F8F9FA] object-contain">
                                    </div>
                                @empty
                                    <div class="swiper-slide">
                                        <img src="{{ asset($offer->product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                             onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                             class="w-full h-full bg-[#F8F9FA] object-contain">
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
                            @if ($offer->product->offer->is_offer && $offer->product->offer->discount_percent)
                                <span
                                    class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                    {{-- Translated: Discount X % --}}
                                    {{ __('messages.discount_percentage', ['percent' => $offer->product->discount_percent]) }}
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
                                <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $offer->product->name }}</h3>
                                <div class="flex items-center ">
                                    @if($offer->product->rating)
                                    <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                    @endif
                                    <span class="text-[18px]">{{ $offer->product->rating }}</span>
                                </div>
                            </div>
                            <span
                                class="text-[#696969] text-[20px]">{{ $offer->product->subCategory->category->name ?? 'غير مصنف' }}</span>
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
                                <span class=" flex text-lg font-bold text-gray-800">
                                    {{ number_format($offer->product->price * (1 - ($offer->discount_percent ?? 0) / 100), 2) }}
                                    <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}"
                                        alt="">
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

       </div> 
    </div>
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
</section>
