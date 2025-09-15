@include('partials.header_scripts')



<header class="bg-white flex flex-wrap items-center justify-between py-3 h-auto w-full md:px-[64px] px-[20px]">

    <div class="flex items-center h-auto w-auto max-w-[72px] ">
        <a href="/">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo">
        </a>
    </div>

<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUUMjGzzsoinenATBytoscF54qWQc_q0w&libraries=places&callback=initMap" async defer></script>

<script>
let map, marker, selectedLocation;

// Pull saved location from backend (if any)
let savedLocation = @json(Auth::user()->address ?? null);

function initMap() {
    if (map) return;

    let center = { lat: 24.7136, lng: 46.6753 };

    if (savedLocation && savedLocation.includes("(") && savedLocation.includes(")")) {
        try {
            const coords = savedLocation.match(/\(([^)]+)\)/)[1].split(",");
            center = { lat: parseFloat(coords[0]), lng: parseFloat(coords[1]) };
        } catch (e) {}
    }

    map = new google.maps.Map(document.getElementById('map'), {
        center,
        zoom: 10
    });

    marker = new google.maps.Marker({
        position: center,
        map,
        draggable: true
    });

    selectedLocation = center;

    marker.addListener('dragend', e => {
        selectedLocation = e.latLng;
    });

    const input = document.getElementById('searchInput');
    if (input) {
        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
                selectedLocation = place.geometry.location;
            }
        });
    }
}

function deliveryDropdown() {
    return {
        open: false,
        selectedLocationText: savedLocation ?? '{{ __("messages.chooseLocation") }}',
        showMapModal: false,

        toggleDropdown() {
            this.open = !this.open;
        },

        openMapModal() {
            this.showMapModal = true;
            setTimeout(() => {
                google.maps.event.trigger(map, "resize");
                map.setCenter(marker.getPosition());
            }, 300);
        },

        closeMapModal() {
            this.showMapModal = false;
        },

        chooseCity(city) {
            this.selectedLocationText = city;
            this.open = false;

            fetch("{{ route('user.saveLocation') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                body: JSON.stringify({ city })
            })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert("Failed to save city.");
                }
            });
        },

        saveLocation() {
            if (!selectedLocation) {
                this.closeMapModal();
                return;
            }

            const lat = selectedLocation.lat ? selectedLocation.lat() : selectedLocation.lat;
            const lng = selectedLocation.lng ? selectedLocation.lng() : selectedLocation.lng;

            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ location: { lat, lng } }, (results, status) => {
                let address = null;

                if (status === "OK" && results[0]) {
                    address = results[0].formatted_address;
                }

                // Fallback: if no address, just use lat/lng
                if (!address) {
                    address = `Lat: ${lat}, Lng: ${lng}`;
                }

                fetch("{{ route('user.saveLocation') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ address, lat, lng })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.selectedLocationText = address;
                    } else {
                        alert("Failed to save location.");
                    }
                    this.closeMapModal();
                })
                .catch(() => this.closeMapModal());
            });
        }
    }
}
</script>



<!-- Dropdown + Modal -->
<div  x-data="deliveryDropdown()" class="deliver relative inline-block text-[12px] max-w-[150px]">

    <!-- Dropdown button -->
    <div @click="toggleDropdown()"
         class="flex items-center cursor-pointer p-1 hover:border font-normal rounded-[4px] space-x-1 justify-center">
        <img src="{{ asset('images/Flag Pack.svg') }}" alt="" class="w-[24px] h-[24px] ml-2">
        <span x-text="selectedLocationText"></span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor" class="w-[12px] h-[12px] shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </div>

    <!-- Dropdown content -->
    <div x-show="open" @click.away="open = false" x-cloak
         class="absolute z-50 mt-2 w-[calc(100vw-32px)] left-0 sm:right-0 bg-white border border-gray-200 rounded-lg shadow-lg p-4
                md:w-[350px] md:left-auto md:right-0 md:translate-x-0">

        <div class="flex flex-col mb-4">
            <p class="font-bold text-[20px] mb-2">{{ __('messages.deliverySite') }}</p>
            <p class="text-gray-500 text-[14px]">{{ __('messages.deliverySiteMSG') }}</p>
        </div>

        <!-- Riyadh Cities Dropdown -->
        <div x-data="{ cityOpen: false, selectedCity: '{{ __('messages.chooseCity') }}' }" class="relative mb-4">
            <div @click="cityOpen = !cityOpen"
                 class="w-full h-[40px] bg-white px-4 py-2 rounded-[12px] flex items-center justify-between border border-gray-400 text-gray-600 cursor-pointer">
               {{ __('messages.deliver_to') }} <span x-text="selectedCity" class="flex-1"></span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5 ml-2 shrink-0" :class="{ 'rotate-180': cityOpen }">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>

            <div x-show="cityOpen" @click.away="cityOpen = false" x-cloak
                 class="absolute z-10 w-full mt-1 bg-white rounded-[12px] shadow-lg border border-gray-300 overflow-hidden max-h-[200px] overflow-y-auto">
                <ul class="py-1">
                    <li><a href="#" @click.prevent="chooseCity('الرياض')" class="block px-4 py-2 hover:bg-gray-100">الرياض</a></li>
                    <li><a href="#" @click.prevent="chooseCity('الدرعية')" class="block px-4 py-2 hover:bg-gray-100">الدرعية</a></li>
                    <li><a href="#" @click.prevent="chooseCity('الخرج')" class="block px-4 py-2 hover:bg-gray-100">الخرج</a></li>
                    <li><a href="#" @click.prevent="chooseCity('المجمعة')" class="block px-4 py-2 hover:bg-gray-100">المجمعة</a></li>
                    <li><a href="#" @click.prevent="chooseCity('الزلفي')" class="block px-4 py-2 hover:bg-gray-100">الزلفي</a></li>
                    <li><a href="#" @click.prevent="chooseCity('شقراء')" class="block px-4 py-2 hover:bg-gray-100">شقراء</a></li>
                    <li><a href="#" @click.prevent="chooseCity('وادي الدواسر')" class="block px-4 py-2 hover:bg-gray-100">وادي الدواسر</a></li>
                    <li><a href="#" @click.prevent="chooseCity('حوطة بني تميم')" class="block px-4 py-2 hover:bg-gray-100">حوطة بني تميم</a></li>
                    <li><a href="#" @click.prevent="chooseCity('الأفلاج')" class="block px-4 py-2 hover:bg-gray-100">الأفلاج</a></li>
                    <li><a href="#" @click.prevent="chooseCity('عفيف')" class="block px-4 py-2 hover:bg-gray-100">عفيف</a></li>
                </ul>
            </div>
        </div>

        <!-- Map Option -->
        @auth
        <button @click="openMapModal()"
                class="w-full h-[40px] bg-[#185D31] text-white rounded-[12px] flex items-center justify-center">
            {{ __('messages.addLocationAuth') }}
        </button>
        @endauth

        @guest
       <a href="#" @click="toggleDropdown()"
            class="w-full h-[40px] bg-[#185D31] text-white rounded-[12px] flex items-center justify-center">
        {{ __('messages.addLocation') }}
    </a>
        @endguest
    </div>

    <!-- Map Modal -->
    <div id="mapModal" x-show="showMapModal" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg relative mx-4 md:mx-auto">
            <!-- Search + Close -->
            <div class="p-4 border-b flex items-center gap-2">
                <input id="searchInput" type="text" placeholder="{{ __('messages.Search') }}"
                       class="w-full p-2 border rounded">
                <button @click="closeMapModal()" class="px-4 py-2 border rounded">{{ __('messages.return') }}</button>
            </div>

            <!-- Map -->
            <div id="map" class="w-full h-[300px] md:h-[500px]"></div>

            <!-- Confirm Button -->
            <div class="flex justify-end p-4 border-t">
                <button @click="saveLocation()"
                        class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">
                    {{ __('messages.confirmLocation') }}
                </button>
            </div>
        </div>
    </div>
</div>

@include('search.search_bar')

    @php
        $currentLang = app()->getLocale();
        // @dd($currentLang)
    @endphp
    <!-- Language -->
    <div class="language btn-group flex items-center  order-5" style="color: #212121;  width:90px; height:24px; ">

        <div class="dropdown flex items-center cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
            {{-- The image is now inside the clickable area --}}
            <img src="{{ asset('images/Vector (2).svg') }}" alt="Language Icon"
                class="w-[16px] h-[16px] rtl:ml-2 ltr:mr-2">
            {{-- The span text is also inside the clickable area --}}
            <span class="text-[#212121] text-sm md:text-base">
                {{ $currentLang == 'ar' ? 'العربية' : 'English' }}
            </span>
            {{-- The actual dropdown menu would follow this div --}}
        </div>
        <div class="dropdown-menu w-[180px] h-auto rounded-[12px] bg-[#FFFFFF] py-2 shadow-lg">
            <div class="flex items-center cursor-pointer py-3 px-4 text-base text-[#212121]"
                onclick="window.location.href='{{ route('change.language', 'ar') }}'">
                <input type="radio" value="arabic" {{ $currentLang == 'ar' ? 'checked' : '' }}
                    class="shrink-0 rtl:ml-3 ltr:mr-3 w-6 h-6 border-[#185D31] focus:ring-[#185D31] disabled:pointer-events-none dark:bg-[#185D31] dark:border-neutral-700 dark:checked:border-[#185D31] dark:focus:ring-offset-gray-800 appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                    id="arabic">
                <label for="arabic" class="text-neutral-700">{{ __('messages.arabic') }}</label>
            </div>
            <div class="flex items-center cursor-pointer py-3 px-4 text-base text-[#212121]"
                onclick="window.location.href='{{ route('change.language', 'en') }}'">
                <input type="radio" value="english" {{ $currentLang == 'en' ? 'checked' : '' }}
                    class="shrink-0 rtl:ml-3 ltr:mr-3 w-6 h-6 border-[#185D31] focus:ring-[#185D31] disabled:pointer-events-none dark:bg-[#185D31] dark:border-neutral-700 dark:checked:border-[#185D31] dark:focus:ring-offset-gray-800 appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                    id="english">
                <label for="english" class="text-neutral-700">{{ __('messages.english') }}</label>
            </div>
        </div>
    </div>

    <div
        class="icons flex items-center w-auto justify-end gap-x-4 ml-4 shrink-0 md:w-[100px] md:justify-between md:ml-0 order-6">

        {{-- Favorites Icon and Popup (YOUR EXISTING CODE - UNCHANGED) --}}
        <div x-data="{ showPopup: false, buttonRect: null }" x-init="$watch('showPopup', value => {
            if (value) {
                // When popup is shown, get the button's position
                buttonRect = $el.querySelector('a').getBoundingClientRect();
            } else {
                buttonRect = null; // Clear when hidden
            }
        })" class="relative inline-block"> {{-- This 'relative' is for positioning the popup relative to the icon on larger screens --}}
            <a href="#" @click.prevent="showPopup = !showPopup" class="relative w-[17px] h-[17px] z-10">
                <img src="{{ asset('images/Vector.svg') }}" alt="Favorites Icon">
            </a>

            {{-- Favorites Popup --}}
            <div x-show="showPopup" x-cloak @click.away="showPopup = false"
                x-transition:enter="transition ease-out duration-300"
                class="bg-white shadow-lg rounded-lg p-4
fixed inset-x-0 top-[5%] w-[calc(100%-4rem)] max-w-[360px] mx-auto z-20 overflow-auto max-h-[90vh]
sm:absolute sm:top-full sm:mt-2 sm:w-[404px] sm:h-auto sm:max-h-none sm:mx-0
rtl:sm:left-0 rtl:sm:right-auto {{-- For RTL, position to the left --}}
ltr:sm:right-0 ltr:sm:left-auto {{-- For LTR, position to the right --}}
md:absolute md:top-full md:mt-2 md:w-[404px] md:h-auto md:max-h-none md:mx-0
rtl:md:left-0 rtl:md:right-auto {{-- For RTL, position to the left --}}
ltr:md:right-0 ltr:md:left-auto {{-- For LTR, position to the right --}}
lg:absolute lg:top-full lg:mt-2 lg:w-[404px] lg:h-auto lg:max-h-none lg:mx-0
rtl:lg:left-0 rtl:lg:right-auto {{-- For RTL, position to the left --}}
ltr:lg:right-0 ltr:lg:left-auto {{-- For LTR, position to the right --}}
">
                <h3 class="text-xl font-bold text-right text-gray-900 mb-4">{{ __('المفضلة') }}</h3>
                <div id="favorites-content-area" class="w-full flex flex-col items-center">
                    @if ($favorites->isEmpty())
                        <div class="flex flex-col justify-center items-center w-full py-10 text-gray-600">
                            <img src="{{ asset('images/Illustrations.svg') }}" alt="No favorites illustration"
                                class="w-[156px] h-[163px] mb-10 ">
                            <p class="text-[#696969] text-[20px] text-center">لم تقم باضافة أي منتج الي المفضلة بعد</p>
                            <a href="{{ route('products.index') }}" class="px-[20px] py-[12px] bg-[#185D31] text-[white] rounded-[12px] mt-3">تصفح المنتجات
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 w-full" id="favorites-grid">
                            {{-- Limit to the first two favorites --}}
                            @foreach ($favorites->take(2) as $favorite)
                                <div class="flex items-center justify-between bg-[#F8F9FA] rounded-lg shadow-md p-3">
                                    {{-- Product Image Container --}}
                                     <div class="w-16 h-16 bg-white rounded-md flex-shrink-0 overflow-hidden mx-2">
                                <img src="{{ Storage::url($favorite->product->image ?? '') }}"
     onerror="this.onerror=null;this.src='https://via.placeholder.com/80x80?text=Image+Error';"
     class="w-full h-full object-contain">

                            </div>
                                    {{-- Product Details (Text Content) --}}
                                    <div class="flex flex-col flex-grow rtl:ml-3 ltr:mr-3">
                                        {{-- Product Name --}}
                                        <p class="text-[16x] font-semibold text-[#212121] mb-1">
                                            {{ $favorite->product->name }}
                                        </p>
                                        <div class="flex items-center text-[16px] text-[#212121] mb-1">
                                            @if($favorite->product->supplier->is_confirmed)
                                            <img class="rtl:ml-2 ltr:mr-2 w-[20px] h-[20px]"
                                                src="{{ asset('images/Success.svg') }}" alt="Confirmed Supplier">
                                            @endif
                                            <span>{{ $favorite->product->supplier->company_name }}</span>

                                        </div>
                                        <p class=" text-[#212121] flex font-bold">
                                                   <span class="flex text-lg font-bold text-gray-800">
    {{ number_format($favorite->product->price_range['min'], 2) }}
    @if($favorite->product->price_range['min'] != $favorite->product->price_range['max'])
        - {{ number_format($favorite->product->price_range['max'], 2) }}
    @endif
    <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}" alt="">
</span>
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- "Go to Favorites" Button --}}
                        <div class="mt-6 text-center w-full"> {{-- Added w-full here to contain the button --}}
                            <a href="{{ route('profile.show', ['section' => 'favoritesSection']) }}#favoritesSection"
                                class="mt-2 w-full px-[20px] py-[11px] bg-[#185D31] text-white rounded-[12px] text-[16px] ">
                                {{ __('messages.go_to_fav') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        {{-- Cart Icon and Popup (MODIFIED TO MIRROR FAVORITES LOGIC) --}}
@include('partials.cart_popup')

        {{-- Notification Icon and Popup (only if user is logged in) --}}
@include('partials.notifications_popup')

    </div>





@include('partials.userSection')


</header>



{{-- category-bar --}}
<nav
    class="categories bg-white w-full sm:flex sm:items-center sm:justify-between sm:px-[64px] pt-4 pb-3 space-y-3 sm:space-y-0 flex-col sm:flex-row relative">
    <div class="relative inline-block ml-1 gap-1 w-full md:w-auto" x-data="{ mainDropdownOpen: false }"
        @click.outside="mainDropdownOpen = false">
        <a id="mainDropdownButton" @click="mainDropdownOpen = !mainDropdownOpen"
            class="justify-between px-[21.5px] py-[8px] flex items-center rtl:ml-1 ltr:mr-1 cursor-pointer">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
            <span class="dropdown-text ">{{ __('messages.All') }}</span>
        </a>

        <div id="mainDropdownMenu" x-show="mainDropdownOpen" x-transition.origin.top-left x-cloak
            class="origin-top-right mt-3 absolute w-[314px] rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-[1001]">
            <div class="py-1" role="none">
                @if (isset($categories))
                    @foreach ($categories as $index => $category)
                        <div class="relative" x-data="{ openIndex: null }" @mouseenter="openIndex = {{ $index }}"
                            @mouseleave="openIndex = null">
                            <div class="flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 rounded-lg mx-1 my-0.5 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50"
                                role="menuitem" aria-haspopup="true"
                                :aria-expanded="(openIndex === {{ $index }}).toString()">
                                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                    @php
                                        $icon = $category->iconUrl
                                            ? asset('storage/' . $category->iconUrl)
                                            : asset('images/default_avatar.png');
                                    @endphp
                                    <img src="{{ $icon }}" alt="{{ $category->name }}"
                                        class="rounded-[12px] w-[56px] h-[56px] object-cover">
                                    <span
                                        class="text-[#1F2B45] text-[16px] font-semibold">{{ $category->name }}</span>
                                </div>
                                <svg class="h-5 w-5 text-gray-400 transition-transform duration-200"
                                    :class="{ 'rotate-90': openIndex === {{ $index }} }"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>

                            <div x-show="openIndex === {{ $index }}" x-transition x-cloak
                                class="SideDropdown absolute top-0
              responsive-dropdown          {{ app()->getLocale() === 'ar' ? 'align-right ml-1' : 'align-left mr-1' }}
                        bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 w-[314px] z-[1002] overflow-hidden">
                                {{-- New Header for the Side Dropdown, styled like the image --}}
                                <div class=" text-black p-4 flex items-center space-x-3 rtl:space-x-reverse">
                                    <span class="text-[18px] font-semibold">{{ $category->name }}</span>
                                </div>

                                <div class="py-2 px-3 space-y-2 max-h-[300px] overflow-y-auto" role="none">
                                    {{-- Check if the category has products and it's not null --}}
                                    @if ($category->products && $category->products->isNotEmpty())
                                        @foreach ($category->products as $product)
                                            <a href=""
                                                class="flex items-center space-x-2 rtl:space-x-reverse px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                                @php
                                                    $productIcon = $product->image
                                                        ? asset('storage/' . $product->image)
                                                        : asset('images/default_product.png');
                                                @endphp
                                                <img src="{{ $productIcon }}" alt="{{ $product->name }}"
                                                    class="rounded-[8px] w-[60px] h-[60px] object-cover border border-gray-200">
                                                <span>{{ $product->name }}</span>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="px-2 py-1 text-sm text-gray-500">لا توجد منتجات لهذه الفئة.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>

<div class="individualCategories flex flex-wrap gap-2 w-full justify-start">
    @foreach ($categories as $category)
        @if ($loop->iteration <= 10)
            <a href="{{ route('products.filterByCategory', $category->slug) }}"
               class="category-button rtl:px-[21.5px] py-[8px] ltr:px-[17px] {{ isset($selectedCategory) && $selectedCategory->slug === $category->slug ? 'active' : '' }}">
                {{ $category->name }}
            </a>
        @endif
    @endforeach
</div>



</nav>

<div class="search_menu_small hidden flex-row justify-between px-4 sm:px-[64px] py-4">

    {{-- ********************************************drop menu for small screen******************************* --}}

    <button id="dropdownMenuIconButton" data-dropdown-toggle="mergedDropdownMenu"
        class="hidden order-3  items-center px-2 py-2 text-lg rtl:ml-2 ltr:ml-2 font-small h-full text-center text-gray-900 bg-[#F8F9FA] rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-50 "
        type="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
            class="bi bi-list" viewBox="0 0 16 16">
            <path fill-rule="evenodd"
                d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
        </svg>
    </button>

    <!-- Dropdown menu -->
    <div id="mergedDropdownMenu"
        class="z-20 hidden p-4 bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-[314px] dark:bg-gray-700 dark:divide-gray-600">
        <ul class="py-2 text-sm text-gray-700 " aria-labelledby="dropdownMenuIconButton">

            {{-- Delivery Location Section --}}
            <li class="mb-4">
             <!-- Dropdown + Modal -->
<div  x-data="deliveryDropdown()"
                    class="relative inline-block text-[12px] tracking-[0%] w-auto max-w-[150px] lg:mx-4 sm:mx-1 md:w-[120px] md:h-[36px] shrink-0">

    <!-- Dropdown button -->
    <div @click="toggleDropdown()"
         class="flex items-center cursor-pointer p-1 hover:border font-normal rounded-[4px] space-x-1 justify-center">
        <img src="{{ asset('images/Flag Pack.svg') }}" alt="" class="w-[24px] h-[24px] ml-2">
        <span x-text="selectedLocationText"></span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
             stroke="currentColor" class="w-[12px] h-[12px] shrink-0">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    </div>

    <!-- Dropdown content -->
    <div x-show="open" @click.away="open = false" x-cloak
         class="absolute z-50 mt-2 w-[calc(100vw-32px)] -left-25 sm:right-0 bg-white border border-gray-200 rounded-lg shadow-lg p-4
                md:w-[350px] md:left-auto md:right-0 md:translate-x-0">

        <div class="flex flex-col mb-4">
            <p class="font-bold text-[20px] mb-2">{{ __('messages.deliverySite') }}</p>
            <p class="text-gray-500 text-[14px]">{{ __('messages.deliverySiteMSG') }}</p>
        </div>

        <!-- Riyadh Cities Dropdown -->
        <div x-data="{ cityOpen: false, selectedCity: '{{ __('messages.chooseCity') }}' }" class="relative mb-4">
            <div @click="cityOpen = !cityOpen"
                 class="w-full h-[40px] bg-white px-4 py-2 rounded-[12px] flex items-center justify-between border border-gray-400 text-gray-600 cursor-pointer">
                <span x-text="selectedCity" class="flex-1"></span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5 ml-2 shrink-0" :class="{ 'rotate-180': cityOpen }">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>

            <div x-show="cityOpen" @click.away="cityOpen = false" x-cloak
                 class="absolute z-10 w-full mt-1 bg-white rounded-[12px] shadow-lg border border-gray-300 overflow-hidden max-h-[200px] overflow-y-auto">
                <ul class="py-1">
                    <li><a href="#" @click.prevent="chooseCity('الرياض')" class="block px-4 py-2 hover:bg-gray-100">الرياض</a></li>
                    <li><a href="#" @click.prevent="chooseCity('الدرعية')" class="block px-4 py-2 hover:bg-gray-100">الدرعية</a></li>
                    <li><a href="#" @click.prevent="chooseCity('الخرج')" class="block px-4 py-2 hover:bg-gray-100">الخرج</a></li>
                    <li><a href="#" @click.prevent="chooseCity('المجمعة')" class="block px-4 py-2 hover:bg-gray-100">المجمعة</a></li>
                    <li><a href="#" @click.prevent="chooseCity('الزلفي')" class="block px-4 py-2 hover:bg-gray-100">الزلفي</a></li>
                    <li><a href="#" @click.prevent="chooseCity('شقراء')" class="block px-4 py-2 hover:bg-gray-100">شقراء</a></li>
                    <li><a href="#" @click.prevent="chooseCity('وادي الدواسر')" class="block px-4 py-2 hover:bg-gray-100">وادي الدواسر</a></li>
                    <li><a href="#" @click.prevent="chooseCity('حوطة بني تميم')" class="block px-4 py-2 hover:bg-gray-100">حوطة بني تميم</a></li>
                    <li><a href="#" @click.prevent="chooseCity('الأفلاج')" class="block px-4 py-2 hover:bg-gray-100">الأفلاج</a></li>
                    <li><a href="#" @click.prevent="chooseCity('عفيف')" class="block px-4 py-2 hover:bg-gray-100">عفيف</a></li>
                </ul>
            </div>
        </div>

        <!-- Map Option -->
        @auth
        <button @click="openMapModal()"
                class="w-full h-[40px] bg-[#185D31] text-white rounded-[12px] flex items-center justify-center">
            {{ __('messages.addLocationAuth') }}
        </button>
        @endauth

         @guest
    <a href="#" @click="toggleDropdown()"
            class="w-full h-[40px] bg-[#185D31] text-white rounded-[12px] flex items-center justify-center">
        {{ __('messages.addLocation') }}
    </a>
    @endguest
    </div>

    <!-- Map Modal -->
    <div id="mapModal" x-show="showMapModal" x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg relative mx-4 md:mx-auto">
            <!-- Search + Close -->
            <div class="p-4 border-b flex items-center gap-2">
                <input id="searchInput" type="text" placeholder="{{ __('messages.Search') }}"
                       class="w-full p-2 border rounded">
                <button @click="closeMapModal()" class="px-4 py-2 border rounded">{{ __('messages.return') }}</button>
            </div>

            <!-- Map -->
            <div id="map" class="w-full h-[300px] md:h-[500px]"></div>

            <!-- Confirm Button -->
            <div class="flex justify-end p-4 border-t">
                <button @click="saveLocation()"
                        class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">
                    {{ __('messages.confirmLocation') }}
                </button>
            </div>
        </div>
    </div>
</div>
            </li>

            {{-- Language Selector Section --}}
            <li class="mb-4">
                @php
                    $currentLang = app()->getLocale();
                @endphp
                <div class="btn-group flex items-center" style="color: #212121; width:90px; height:24px;">
                    <div class="dropdown flex items-center cursor-pointer" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        {{-- The image is now inside the clickable area --}}
                        <img src="{{ asset('images/Vector (2).svg') }}" alt="Language Icon"
                            class="w-[16px] h-[16px] rtl:ml-2 ltr:mr-2">
                        {{-- The span text is also inside the clickable area --}}
                        <span class="text-[#212121] text-sm md:text-base">
                            {{ $currentLang == 'ar' ? 'العربية' : 'English' }}
                        </span>
                        {{-- The actual dropdown menu would follow this div --}}
                    </div>
                    <div class="dropdown-menu w-[180px] h-auto rounded-[12px] bg-[#FFFFFF] py-2 shadow-lg">
                        <div class="flex items-center cursor-pointer py-3 px-4 text-base text-[#212121]"
                            onclick="window.location.href='{{ route('change.language', 'ar') }}'">
                            <input type="radio" value="arabic" {{ $currentLang == 'ar' ? 'checked' : '' }}
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-6 h-6 border-[#185D31] focus:ring-[#185D31] disabled:pointer-events-none dark:bg-[#185D31] dark:border-neutral-700 dark:checked:border-[#185D31] dark:focus:ring-offset-gray-800 appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                id="arabic" readonly>
                            <label for="arabic" class="text-neutral-700">{{ __('messages.arabic') }}</label>
                        </div>
                        <div class="flex items-center cursor-pointer py-3 px-4 text-base text-[#212121]"
                            onclick="window.location.href='{{ route('change.language', 'en') }}'">
                            <input type="radio" value="english" {{ $currentLang == 'en' ? 'checked' : '' }}
                                class="shrink-0 rtl:ml-3 ltr:mr-3 w-6 h-6 border-[#185D31] focus:ring-[#185D31] disabled:pointer-events-none dark:bg-[#185D31] dark:border-neutral-700 dark:checked:border-[#185D31] dark:focus:ring-offset-gray-800 appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                                id="english" readonly>
                            <label for="english" class="text-neutral-700">{{ __('messages.english') }}</label>
                        </div>
                    </div>
                </div>
            </li>

            <hr class="my-2 border-gray-100 dark:border-gray-600">

            {{-- Categories and Products Section --}}
            @if (isset($categories))
                @foreach ($categories as $index => $category)
                    <li class="relative" x-data="{ openIndex: null }" @mouseenter="openIndex = {{ $index }}"
                        @mouseleave="openIndex = null">
                        <div class="flex items-center justify-between w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 rounded-lg mx-1 my-0.5 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-opacity-50"
                            role="menuitem" aria-haspopup="true"
                            :aria-expanded="(openIndex === {{ $index }}).toString()">
                            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                @php
                                    $icon = $category->iconUrl
                                        ? asset('storage/' . $category->iconUrl)
                                        : asset('images/default_avatar.png');
                                @endphp
                                <img src="{{ $icon }}" alt="{{ $category->name }}"
                                    class="rounded-[12px] w-[56px] h-[56px] object-cover">
                                <span class="text-[#1F2B45] text-[16px] font-semibold">{{ $category->name }}</span>
                            </div>
                            <svg class="h-5 w-5 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-90': openIndex === {{ $index }} }"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10l-3.293-3.293a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <div x-show="openIndex === {{ $index }}" x-transition x-cloak
                            class="SideDropdown absolute top-0
                        {{ app()->getLocale() === 'ar' ? 'align-right ml-1' : 'align-left mr-1' }}
                        bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 w-[314px] z-[1002] overflow-hidden">
                            <div class="text-black p-4 flex items-center space-x-3 rtl:space-x-reverse">
                                <span class="text-[18px] font-semibold">{{ $category->name }}</span>
                            </div>

                            <div class="py-2 px-3 space-y-2 max-h-[300px] overflow-y-auto" role="none">
                                @if ($category->products && $category->products->isNotEmpty())
                                    @foreach ($category->products as $product)
                                        <a href=""
                                            class="flex items-center space-x-2 rtl:space-x-reverse px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                            @php
                                                $productIcon = $product->image
                                                    ? asset('storage/' . $product->image)
                                                    : asset('images/default_product.png');
                                            @endphp
                                            <img src="{{ $productIcon }}" alt="{{ $product->name }}"
                                                class="rounded-[8px] w-[60px] h-[60px] object-cover border border-gray-200">
                                            <span>{{ $product->name }}</span>
                                        </a>
                                    @endforeach
                                @else
                                    <div class="px-2 py-1 text-sm text-gray-500">لا توجد منتجات لهذه الفئة.</div>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            @endif
        </ul>
    </div>

    {{-- ***************************************************************************************************** --}}
   @include('partials.responsive_search')
   {{-- @include('search.search_bar') --}}


</div>



