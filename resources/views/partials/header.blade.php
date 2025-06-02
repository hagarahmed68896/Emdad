<!-- Tailwind + Alpine.js -->
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUUMjGzzsoinenATBytoscF54qWQc_q0w&libraries=places">
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/alpinejs" defer></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
    .category-button {
        display: inline-block;
        padding: 8px 25px;
        background-color: #EDEDED;
        color: #767676;
        border-radius: 12px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        height: 40px;
        font-family: 'Cairo', sans-serif;
    }

    .category-button:hover {
        color: #212121;
    }

    .category-button.active {
        background-color: #185D31;
        color: white;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="//unpkg.com/alpinejs" defer></script>
<script>
    let map;
    let marker;
    let selectedLocation;

    document.addEventListener('DOMContentLoaded', function() {
        window.openMapModal = function() {
            document.getElementById('mapModal').classList.remove('hidden');
            initMap();
        };

        window.closeMapModal = function() {
            document.getElementById('mapModal').classList.add('hidden');
        };

        window.confirmLocation = function() {
            if (selectedLocation) {
                alert("{{ __('messages.selectedLocation') }}: " + selectedLocation.lat() + ", " +
                    selectedLocation.lng());
                closeMapModal();
            }
        };

        window.initMap = function() {
            if (map) return;
            const mapOptions = {
                center: {
                    lat: 24.7136,
                    lng: 46.6753
                },
                zoom: 10,
            };
            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            marker = new google.maps.Marker({
                position: mapOptions.center,
                map: map,
                draggable: true,
            });

            google.maps.event.addListener(marker, 'dragend', function(event) {
                selectedLocation = event.latLng;
            });

            const input = document.getElementById('searchInput');
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    map.setCenter(place.geometry.location);
                    marker.setPosition(place.geometry.location);
                    selectedLocation = place.geometry.location;
                }
            });
        };
    });
</script>
<!-- Alpine JS Logic -->
<script>
    function imageUploadComponent() {
        return {
            showUploadModal: false,
            imageFile: null,
            imagePreview: null,
            imageUrl: '',

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file && file.type.startsWith("image/")) {
                    this.imageFile = file;
                    this.imagePreview = URL.createObjectURL(file);
                } else {
                    alert("الرجاء اختيار صورة صحيحة.");
                }
            },

            handleDrop(event) {
                const files = event.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith("image/")) {
                        this.imageFile = file;
                        this.imagePreview = URL.createObjectURL(file);
                    } else {
                        alert("الرجاء اختيار صورة صحيحة.");
                    }
                }
            },

            submitImage() {
                if (!this.imageFile) {
                    alert("الرجاء تحميل صورة قبل البحث.");
                    return;
                }

                // You can upload or use imageFile here
                this.imageFile = null;
                this.imagePreview = null;
                this.showUploadModal = false;
            }
        }
    }
</script>



<header class=" bg-white flex items-center  nav justify-between px-[64px] py-2 shadow-md">
    <!-- Logo -->
    <div class="flex items-center space-x-4 h-[30px]" style="width: 72px; color: #212121;">
        <img src="{{ asset('images/Logo.png') }}" alt="...">
    </div>

    <div x-data="{ open: false }" class="relative inline-block w-[120px] h-[36px] text-[12px]  tracking-[0%]">
        <div @click="open = !open"
            class="flex items-center cursor-pointer p-1 hover:border font-normal rounded-[4px] space-x-1 h-full w-full">

            <img src="https://s.alicdn.com/@icon/flag/assets/sa.png" alt="SA" class="w-[24px] h-[24px] ml-2" />

            <span>{{ __('messages.deliver') }}</span>

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-[12px] h-[12px] shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>

        </div>

        <!-- Dropdown Popup -->
        <div x-show="open" @click.away="open = false" style="background-color: #FFFFFF; border-radius: 12px;"
            class="absolute z-10 mt-2 w-[350px] h-[299px] bg-white border border-gray-200 rounded shadow-lg p-[24px]"
            x-transition>
            <div class="w-[302px] h-[100px] flex flex-col">
                <p class="font-cairo font-bold text-[20px] leading-[150%] tracking-[0%] text-right align-middle mb-3">
                    {{ __('messages.deliverySite') }}</p>
                <p class="font-cairo text-[14px] leading-[150%] tracking-[0%] text-right align-middle"
                    style="color: #767676;">{{ __('messages.deliverySiteMSG') }}</p>
            </div>
            <div>
                <a
                    class="w-full h-[40px] bg-[#185D31] px-4 py-2 rounded-[12px] cursor-pointer  text-[14px] text-white flex items-center justify-center">
                    {{ __('messages.addLocation') }}
                </a>
            </div>
            <div class="flex items-center justify-center my-4" style="color:#EDEDED;">
                <hr class="flex-grow border-t border-gray-400">
                <span class="text-sm text-gray-500 font-medium mx-4"> {{ __('messages.or') }} </span>
                <hr class="flex-grow border-t border-gray-400">
            </div>


            <div
                class="dropdown w-full h-[40px] bg-white px-4 py-2 rounded-[12px] flex items-center justify-between border border-[#767676] text-[#767676] font-normal text-[16px] cursor-pointer">
                <a href="javascript:void(0)" onclick="openMapModal()" class="flex-1">{{ __('messages.chooseCity') }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 ml-2 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>

        </div>
    </div>

    <!-- Map Modal -->
    <div id="mapModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
        <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg relative">
            <div class="p-4 border-b">
                <input type="text" id="searchInput" placeholder="{{ __('messages.Search') }}"
                    class="w-full border border-gray-300 focus:ring-0 px-3 py-2  rounded">
            </div>
            <div id="map" class="w-full h-[500px]"> </div>
            <div class="flex justify-between p-4 border-t">
                <button onclick="closeMapModal()"
                    class="bg-white border px-4 py-2 rounded hover:bg-gray-100">{{ __('messages.return') }}</button>
                <button onclick="confirmLocation()"
                    class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">{{ __('messages.confirmLocation') }}</button>
            </div>
        </div>
    </div>



    <div class="flex-grow max-w-2xl mx-6" x-data="searchBoxComponent()">
        <form @submit.prevent="applySearch">
            <div class="flex border rounded-[12px] bg-[#F8F9FA] items-center py-1 px-2 relative">

                <!-- Dropdown -->
                <div class="relative" x-data="{ categoryOpen: false, selectedCategories: [] }">
                    <button type="button" @click="categoryOpen = !categoryOpen"
                        class="flex items-center px-1 h-full w-[163px] border-l text-[#767676] text-sm font-normal font-[Cairo]">
                        <div class="flex items-center justify-between px-2 h-full w-full">
                            <span class="text-sm">البحث حسب</span>
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                            </svg>
                        </div>
                    </button>

                    <!-- Checkbox Popup -->
                    <div x-show="categoryOpen" @click.outside="categoryOpen = false"
                        class="absolute z-10 bg-white border mt-1 rounded-2xl shadow-md p-[10px] text-[#212121] text-base font-[Cairo] leading-[50px] w-[163px] h-[100px]">
                        <label
                            class="flex items-center space-x-2 cursor-pointer py-1 text-sm text-gray-700 font-[Cairo] mb-3">
                            <input type="checkbox" value="suppliers" x-model="selectedCategories"
                                class="form-checkbox ml-4 w-[20px] h-[20px] border-[1.5px] border-gray-400 rounded">
                            <span class="text-sm font-[Cairo] text-gray-700">{{ __('messages.products') }}</span>

                        </label>

                        <label class="flex items-center space-x-2 cursor-pointer py-1">
                            <input type="checkbox" value="suppliers" x-model="selectedCategories"
                                class="form-checkbox w-[20px] h-[20px] border-[1.5px] border-gray-400 rounded ml-4">
                            <span class="text-sm font-[Cairo] text-gray-700">{{ __('messages.suppliers') }}</span>

                        </label>
                    </div>
                </div>


                <!-- Search Input -->
                <div class="relative w-full mx-2 " x-data="{ searchText: '', showHistory: false, recentSearches: ['تفاح', 'موز', 'عصير', 'كمبيوتر', 'هاتف'] }">
                    <input type="text" x-model="searchText" @focus="showHistory = true"
                        @input="showHistory = searchText.length > 0" @click="showHistory = true"
                        @click.outside="showHistory = false" placeholder="{{ __('messages.Search') }}"
                        class="w-full bg-[#F8F9FA] focus:ring-0 px-3 py-1.5 outline-none text-sm ">

                    <!-- Search History -->
                    <div x-show="showHistory" x-transition
                        class="absolute bg-white border rounded shadow w-full mt-1 z-20 max-h-48 overflow-auto">
                        <template x-for="item in recentSearches.filter(s => s.includes(searchText)).slice(0, 5)"
                            :key="item">
                            <div @click="searchText = item; showHistory = false"
                                class="px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm" x-text="item"></div>
                        </template>
                    </div>
                </div>

                {{-- camera icon --}}

                <!-- Header Blade Content -->
                <div x-data="imageUploadComponent()" class="relative flex items-center justify-center mx-2">
                    <label @click="showUploadModal = true" class="cursor-pointer hover:text-black  text-[#767676]">
                        <img src="{{ asset('images/Group (3).svg') }}" alt="" class="w-[30px] h-[30px] ">

                    </label>
                    <!-- Modal -->
                    <div x-show="showUploadModal" x-cloak class="fixed inset-0  flex items-center justify-center ">
                        <div @click.away="showUploadModal = false"
                            class="bg-white rounded-lg shadow-lg  p-6 relative w-[660px] h-[320px]">

                            <!-- Drag & Drop Box -->
                            <!-- Drag & Drop Box -->
                            <div class="border-2 border-dashed border-gray-300 p-6 rounded-md flex flex-col items-center justify-center text-gray-600 m-2 w-[600px] h-[210px]"
                                @drop.prevent="handleDrop" @dragover.prevent>
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" alt="Preview"
                                        class="w-32 h-32 object-contain mb-2" />
                                </template>

                                <template x-if="!imagePreview">
                                    <div class="flex flex-col items-center justify-center">
                                        <img src="{{ asset('images/Frame 3508.svg') }}" alt=""
                                            class="w-[48px] h-[48px] mb-2">
                                        <p class="text-center text-sm">
                                            {{ __('messages.dragPhoto') }}
                                            <label for="imageInput"
                                                class="text-green-700 underline cursor-pointer block">
                                                {{ __('messages.attachFile') }}
                                            </label>
                                        </p>
                                    </div>
                                </template>

                                <input type="file" id="imageInput" accept="image/*" class="hidden"
                                    @change="handleImageUpload">
                            </div>


                            <!-- Footer -->
                            <div class="flex mt-4 mb-4 items-center">
                                <div class="flex items-center justify-between w-full  mb-4">
                                    <input type="text" x-model="imageUrl"
                                        placeholder="{{ __('messages.imageURL') }}"
                                        class="border border-gray-300 px-3 py-2 rounded w-[400px] text-sm" />

                                    <button @click="submitImage"
                                        class="bg-green-800 text-white px-6 py-2 rounded text-sm">{{ __('messages.Search') }}
                                    </button>

                                </div>
                            </div>


                        </div>
                    </div>
                </div>




                <div>
                    <!-- Search Icon -->
                    <button type="submit"
                        class="bg-[#185D31] w-[61px] h-[32px] text-white rounded-[12px] pb-1 mx-1 text-sm ">
                        بحث</button>
                </div>
            </div>
        </form>
    </div>

    <!-- User Profile -->
    <script>
        document.getElementById('language-icon').addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default anchor behavior
            var dropdown = document.getElementById('language-dropdown');
            dropdown.classList.toggle('show'); // Toggle the dropdown visibility
        });
        // Optional: Close the dropdown if clicking outside of it
        window.onclick = function(event) {
            if (!event.target.matches('#language-icon') && !event.target.matches('#language-button')) {
                var dropdowns = document.getElementsByClassName("dropdown-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>

    @php
        $currentLang = app()->getLocale();
    @endphp
    <!-- Language -->
    <div class="btn-group flex items-center justify-between" style="color: #212121;  width:90px; height:24px; ">

        <button type="button" class="btn  w-[25px] h-[25px] border-none" data-bs-toggle="dropdown"
            aria-expanded="false">
            <img src="{{ asset('images/Vector (2).svg') }}" alt="">
        </button>

        <span>
            {{ $currentLang == 'ar' ? 'العربية' : 'English' }}
        </span>

        <div class="dropdown-menu w-[180px] h-[120px] rounded-[12px] bg-[#FFFFFF] pt-[8px] pb-[8px]">
            <div class="flex pt-[12px] pb-[12px] pl-[16px] pr-[16px] text-[16px] text-[#212121]">

                <input type="radio" name="language" value="arabic" {{ $currentLang == 'ar' ? 'checked' : '' }}
                    class="shrink-0 mt-0.5 border-[#185D31] focus:ring-[#185D31] 
           disabled:pointer-events-none dark:bg-[#185D31] dark:border-neutral-700 
           dark:checked:border-[#185D31] dark:focus:ring-offset-gray-800 
           w-[24px] h-[24px] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                    id="arabic">
                <label for="arabic" class="ms-3 dark:text-neutral-400">{{ __('messages.arabic') }}</label>
            </div>
            <div class="flex pt-[12px] pb-[12px] pl-[16px] pr-[16px] text-[16px] text-[#212121]">
                <input type="radio" name="language" value="english" {{ $currentLang == 'en' ? 'checked' : '' }}
                    class="shrink-0 mt-0.5 border-[#185D31] focus:ring-[#185D31] 
           disabled:pointer-events-none dark:bg-[#185D31] dark:border-neutral-700 
           dark:checked:border-[#185D31] dark:focus:ring-offset-gray-800 
           w-[24px] h-[24px] appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                    id="english">
                <label for="english" class="ms-3 dark:text-neutral-400">{{ __('messages.english') }}</label>
            </div>
        </div>
    </div>

    <!-- heart -->
    <div class="flex items-center space-x-4 w-[70px] justify-between">
        <a href="#" class="relative w-[18px] h-[18px]">
            <img src="{{ asset('images/Vector.svg') }}" alt="">
        </a>

        <!-- Cart -->
        <a href="#" class="relative w-[18px] h-[18px]">
            <img src="{{ asset('images/Group.svg') }}" alt="">
        </a>
    </div>



    <!-- Create Account -->
    @include('partials.register')





</header>







{{-- category-bar --}}
<div class="categories bg-white flex items-center justify-between px-[64px] pt-[20px] pb-[12px]">
    @if (isset($categories))
        @foreach ($categories as $category)
            <a href="{{ route('products.filterByCategory', $category->slug) }}"
                class="category-button {{ isset($selectedCategory) && $selectedCategory->slug === $category->slug ? 'active' : '' }}">
                {{ $category->name }}
            </a>
        @endforeach
    @else
        <p>Categories not loaded</p>
    @endif
</div>
