@extends('layouts.app')

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
<div class="py-8 px-[64px] w-full lg:p-8 flex flex-col lg:flex-row gap-6">

    <aside class="w-full lg:w-1/4 bg-white p-6 rounded-xl shadow-lg h-fit lg:sticky lg:top-8 sidebar-scroll overflow-y-auto max-h-[calc(100vh-64px)]">
        <div class="flex justify-between w-full mb-6 border-b pb-3">
            <h2 class="text-[24px] font-bold text-[#212121]">{{__('messages.filters')}}</h2>
            <img src="{{asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg')}}" alt="Filters Icon">
        </div>

        <form id="filterForm" method="GET" action="{{ route('products.index') }}">
            {{-- This hidden input ensures sub_category_id is always passed, preserving context --}}
            @if(request()->has('sub_category_id'))
                <input type="hidden" name="sub_category_id" value="{{ request('sub_category_id') }}">
            @endif

            <div class="mb-6">
                <input type="text" id="search" name="search" placeholder="{{ __('messages.search_products') }}"
                    value="{{ request('search') }}"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
            </div>

            <div class="mb-6 border-b pb-3">
                <div class="flex flex-col space-y-2">
                    @php $selectedDescriptions = (array) request('description'); @endphp
                    @forelse($availableDescriptions as $descriptionOption)
                        <label class="inline-flex items-center">
                           <input type="checkbox" name="description[]" value="{{ $descriptionOption }}"
                                class="form-checkbox h-5 w-5 rounded-[12px] focus:ring-[#185D31] accent-[#185D31]"
                                {{ in_array($descriptionOption, $selectedDescriptions) ? 'checked' : '' }}
                                onchange="document.getElementById('filterForm').submit()">
                           <span class="rtl:mr-2 ltr:ml-2 text-[#212121] text-base">{{ $descriptionOption }}</span>
                        </label>
                    @empty
                        <p class="text-gray-500 text-sm">{{ __('messages.no_description_filters_available') }}</p>
                    @endforelse
                </div>
                @if(!empty($selectedDescriptions))
                    <button type="button" onclick="clearFilter('description[]')" class="text-sm text-blue-600 hover:underline mt-2">Clear Descriptions</button>
                @endif
            </div>

            <div class="mb-6 border-b pb-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{__('messages.colors')}}</h3>
                <div class="grid grid-cols-6 gap-2">
                    @php
                        $colorHexMap = [
                            'Red' => '#FF0000', 'Blue' => '#0000FF', 'Green' => '#008000',
                            'Yellow' => '#FFFF00', 'Orange' => '#FFA500', 'Purple' => '#800080',
                            'Black' => '#000000', 'White' => '#FFFFFF', 'Gray' => '#808080',
                            'Brown' => '#A52A2A',
                        ];
                        $selectedColors = (array) request('color');
                    @endphp
                    @forelse($availableColors as $colorName)
                        <label class="flex items-center justify-center">
                            <input type="checkbox" name="color[]" value="{{ $colorName }}" class="hidden"
                                {{ in_array($colorName, $selectedColors) ? 'checked' : '' }}>
                            <div class="color-swatch {{ in_array($colorName, $selectedColors) ? 'selected' : '' }}"
                                style="background-color: {{ $colorHexMap[$colorName] ?? '#ccc' }}"
                                title="{{ $colorName }}"></div>
                        </label>
                    @empty
                        <p class="text-gray-500 text-sm col-span-6">No colors available.</p>
                    @endforelse
                </div>
                @if(!empty($selectedColors))
                    <button type="button" onclick="clearFilter('color[]')" class="text-sm text-blue-600 hover:underline mt-2">Clear Colors</button>
                @endif
            </div>

            <div class="mb-6 border-b pb-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{__('messages.size')}}</h3>
                <div class="grid grid-cols-4 gap-2">
                    @php $selectedSizes = (array) request('size'); @endphp
                    @forelse($availableSizes as $size)
                        <label class="flex items-center justify-center">
                            <input type="checkbox" name="size[]" value="{{ $size }}" class="hidden"
                                {{ in_array($size, $selectedSizes) ? 'checked' : '' }}>
                            <div class="size-button text-center {{ in_array($size, $selectedSizes) ? 'selected' : '' }}">
                                {{ $size }}
                            </div>
                        </label>
                    @empty
                        <p class="text-gray-500 text-sm col-span-4">No sizes available.</p>
                    @endforelse
                </div>
                @if(!empty($selectedSizes))
                    <button type="button" onclick="clearFilter('size[]')" class="text-sm text-blue-600 hover:underline mt-2">Clear Sizes</button>
                @endif
            </div>

            <div class="mb-6 border-b pb-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{__('messages.gender')}}</h3>
                @php $selectedGenders = (array) request('gender'); @endphp
                @forelse($availableGenders as $gender)
                    <label class="flex items-center mb-2 text-gray-700">
                        <input type="checkbox" name="gender[]" value="{{ $gender }}"
                            class="form-checkbox h-5 w-5 rounded-[12px] focus:ring-[#185D31] accent-[#185D31]"
                            {{ in_array($gender, $selectedGenders) ? 'checked' : '' }}
                            onchange="document.getElementById('filterForm').submit()">
                        <span class="rtl:mr-2 ltr:ml-2 text-[#212121] text-base">{{ $gender }}</span>
                    </label>
                @empty
                    <p class="text-gray-500 text-sm">No genders available.</p>
                @endforelse
                @if (!empty($selectedGenders))
                    <button type="button" onclick="clearFilter('gender[]')"
                        class="text-sm text-blue-600 hover:underline mt-2">Clear Gender</button>
                @endif
            </div>
            <div class="mb-6 border-b pb-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{__('messages.material')}}</h3>
                @php $selectedMaterials = (array) request('material'); @endphp
                @forelse($availableMaterials as $material)
                    <label class="flex items-center mb-2 text-gray-700">
                        <input type="checkbox" name="material[]" value="{{ $material }}"
                            class="form-checkbox h-5 w-5 rounded-[12px] focus:ring-[#185D31] accent-[#185D31]"
                            {{ in_array($material, $selectedMaterials) ? 'checked' : '' }}
                            onchange="document.getElementById('filterForm').submit()">
                        <span class="rtl:mr-2 ltr:ml-2 text-[#212121] text-base">{{ $material }}</span>
                    </label>
                @empty
                    <p class="text-gray-500 text-sm">No materials available.</p>
                @endforelse
                @if (!empty($selectedMaterials))
                    <button type="button" onclick="clearFilter('material[]')"
                        class="text-sm text-blue-600 hover:underline mt-2">Clear Material</button>
                @endif
            </div>

            <div class="mb-6 border-b pb-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{__('messages.rating')}}</h3>
                @php
                    $ratings = ['4.5' => '4.5 & Up', '4.0' => '4.0 & Up'];
                    $selectedRating = request('rating');
                @endphp
                @foreach ($ratings as $value => $label)
                    <label class="flex items-center mb-2 text-gray-700">
                        <input type="radio" name="rating" value="{{ $value }}"
                            class="form-radio h-4 w-4 text-blue-600 rounded-full focus:ring-blue-500"
                            {{ $selectedRating == $value ? 'checked' : '' }}
                            onchange="document.getElementById('filterForm').submit()">
                        <span class="ml-2">{{ $label }}</span>
                    </label>
                @endforeach
                @if ($selectedRating)
                    <button type="button" onclick="clearFilter('rating')"
                        class="text-sm text-blue-600 hover:underline mt-2">Clear Rating</button>
                @endif
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{__('messages.delivery')}}</h3>
                <label class="flex items-center mb-2 text-gray-700">
                    <input type="checkbox" name="delivery_option[]" value="free_shipping"
                        class="form-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500"
                        {{ in_array('free_shipping', (array) request('delivery_option')) ? 'checked' : '' }}
                        onchange="document.getElementById('filterForm').submit()">
                    <span class="ml-2">Free Shipping</span>
                </label>
                @if(!empty(request('delivery_option')))
                    <button type="button" onclick="clearFilter('delivery_option[]')" class="text-sm text-blue-600 hover:underline mt-2">Clear Delivery Options</button>
                @endif
            </div>

            <div class="mb-6 border-b pb-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{__('messages.price')}}</h3>
                <div class="flex items-center gap-2 mb-2">
                    <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}"
                        class="w-1/2 p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <span>-</span>
                    <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}"
                        class="w-1/2 p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="mb-6 border-b pb-3">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">{{__('messages.min_order_quantity')}}</h3>
                <input type="number" name="min_order_quantity" placeholder="Min Qty"
                    value="{{ request('min_order_quantity') }}"
                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    onchange="document.getElementById('filterForm').submit()">
                @if (request('min_order_quantity'))
                    <button type="button" onclick="clearFilter('min_order_quantity')"
                        class="text-sm text-blue-600 hover:underline mt-2">Clear Min Qty</button>
                @endif
            </div>

            <div class="mb-6">
                <button type="submit"
                    class="w-full bg-[#185D31] text-white py-3 rounded-[12px] font-semibold transition duration-300 shadow-md">
                    {{__('messages.apply_filters')}}</button>
            </div>
            <div class="mb-6">
                <button type="button" onclick="resetFilters()"
                    class="w-full bg-[#EDEDED] text-[#696969] py-3 rounded-[12px] font-semibold transition duration-300 shadow-md">
                {{__('messages.reset_filters')}}</button>
            </div>
        </form>
    </aside>
    <main class="w-full lg:w-3/4">
        <div class="flex justify-between items-center mb-6">
            <p class="text-sm text-gray-500" id="breadcrumbs">
                <a href="{{ route('home') }}" class="hover:underline">{{ __('messages.home') }}</a>
                <span class="mx-1">&gt;</span>

                @if ($currentCategory)
                    <a href="#" class="hover:underline">{{ $currentCategory->name_ar }}</a>
                    <span class="mx-1">&gt;</span>
                @endif

                @if ($currentSubCategory)
                    <a href="{{ route('products.index', ['sub_category_id' => $currentSubCategory->id]) }}" class="hover:underline">{{ $currentSubCategory->name_ar }}</a>
                @endif

                {{-- **IMPORTANT FIX FOR HTMLSPECIALCHARS ERROR:** --}}
                {{-- If you had any dynamic text displaying selected filters like: --}}
                {{-- <span class="mx-1">&gt;</span>
                @if(request()->has('material') && !empty(request('material')))
                    <span class="mx-1">Materials: {{ request('material') }}</span> // THIS IS THE PROBLEM LINE
                @endif --}}
                {{-- The above line `{{ request('material') }}` would cause the error if material is an array. --}}
                {{-- To display selected materials (or any array filter), you need to implode them: --}}
                @if(!empty((array)request('material')))
                    <span class="mx-1">&gt;</span>
                    <span>{{ implode(', ', (array)request('material')) }}</span>
                @endif
                {{-- Apply the same logic for other array filters if you want them in breadcrumbs/summary --}}
                @if(!empty((array)request('description')))
                    <span class="mx-1">&gt;</span>
                    <span>{{ implode(', ', (array)request('description')) }}</span>
                @endif
                @if(!empty((array)request('color')))
                    <span class="mx-1">&gt;</span>
                    <span>{{ implode(', ', (array)request('color')) }}</span>
                @endif
                @if(!empty((array)request('size')))
                    <span class="mx-1">&gt;</span>
                    <span>{{ implode(', ', (array)request('size')) }}</span>
                @endif
                @if(!empty((array)request('gender')))
                    <span class="mx-1">&gt;</span>
                    <span>{{ implode(', ', (array)request('gender')) }}</span>
                @endif
                @if(!empty((array)request('delivery_option')))
                    <span class="mx-1">&gt;</span>
                    <span>{{ implode(', ', (array)request('delivery_option')) }}</span>
                @endif
            </p>
            <div class="relative">
                <select name="sort_by" id="sort_by" form="filterForm" onchange="document.getElementById('filterForm').submit()"
                        class="p-2 border border-[#185D31] rounded-lg focus:ring-2 focus:ring-[#185D31] focus:border-[#185D31] transition duration-200">
                    <option value="">{{__('messages.sort_by')}}</option>
                    <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>{{__('messages.filter_by_price_low')}}</option>
                    <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>{{__('messages.filter_by_price_high')}}</option>
                    <option value="latest" {{ request('sort_by') == 'latest' ? 'selected' : '' }}>{{__('messages.latest_products')}}</option>
                    <option value="rating_desc" {{ request('sort_by') == 'rating_desc' ? 'selected' : '' }}>{{__('messages.filter_by_rating')}}</option>
                </select>
            </div>
        </div>

        @if($products->isEmpty())
            <div class="bg-white p-8 rounded-xl shadow-lg text-center text-gray-600">
                <p class="text-xl font-semibold mb-4">No products found matching your criteria.</p>
                <p>Try adjusting your filters or resetting them.</p>
                <button onclick="resetFilters()" class="mt-6 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300">Reset Filters</button>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse ($products as $product)
                    <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                        <div
                            class="relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
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
                                    class="absolute top-3 rtl:right-3 ltr:left-3 bg-[#FAE1DF] text-[#C62525] text-xs font-bold px-[16px] py-[8px] rounded-full z-10">
                                    {{ __('messages.discount_percentage', ['percent' => $product->discount_percent]) }}
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
                                <h3 class="text-[24px] font-bold text-[#212121] mb-1">{{ $product->name }}</h3>
                                <div class="flex items-center ">
                                    <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}"
                                        alt="">
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
                                    <p class="text-[20px] text-[#212121] mb-3">{{ $product->supplier_name }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex items-center mb-2">
                                <span class=" flex text-lg font-bold text-gray-800">
                                    {{ number_format($product->price * (1 - ($product->discount_percent ?? 0) / 100), 2) }}
                                    <img class="mx-1 w-[20px] h-[21px]"
                                        src="{{ asset('images/Vector (3).svg') }}" alt="">
                                </span>
                                @if ($product->is_offer && $product->discount_percent)
                                    <span class="flex text-sm text-gray-400 line-through mr-2 mr-1">
                                        {{ number_format($product->price, 2) }}
                                        <img class="mx-1 w-[20px] h-[21px]"
                                            src="{{ asset('images/Vector (3).svg') }}" alt="">
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
                {{ $products->links('pagination::tailwind') }} {{-- Using Laravel's default Tailwind pagination view --}}
            </div>
        @endif
    </main>
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
                if (checkbox.nextElementSibling && (checkbox.nextElementSibling.classList.contains('color-swatch') || checkbox.nextElementSibling.classList.contains('size-button'))) {
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
                if (input.nextElementSibling && (input.nextElementSibling.classList.contains('color-swatch') || input.nextElementSibling.classList.contains('size-button'))) {
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
@endsection