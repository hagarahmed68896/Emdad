<!-- Tailwind + Alpine.js -->
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/alpinejs" defer></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="//unpkg.com/alpinejs" defer></script>
{{-- <script>
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
                mapTypeId: google.maps.MapTypeId.ROADMAP // Example: ROADMAP, SATELLITE, HYBRID, TERRAIN
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
</script> --}}
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

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://unpkg.com/flowbite@1.7.0/dist/flowbite.min.js"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('searchBoxComponent', () => ({}));

        Alpine.data('imageUploadComponent', () => ({
            showUploadModal: false,
            imagePreview: null,
            uploadedFile: null,
            imageUrl: '',

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.uploadedFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.imagePreview = null;
                    this.uploadedFile = null;
                }
            },

            handleDrop(event) {
                const file = event.dataTransfer.files[0];
                if (file) {
                    this.uploadedFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            submitImage() {
                // This function is triggered by the "Search" button in the modal.
                // It needs to submit the image (either file or URL) to the backend.

                const form = document.querySelector(
                    '.main-search-form'); // Give your form a class for easy selection
                const formData = new FormData(form); // Get existing form data

                if (this.uploadedFile) {
                    formData.append('search_image', this.uploadedFile);
                } else if (this.imageUrl) {
                    formData.append('image_url', this.imageUrl);
                }

                // If you want to use the main search form's query and categories,
                // you need to append them as well.
                const textQueryInput = form.querySelector('input[name="query"]');
                if (textQueryInput && textQueryInput.value) {
                    formData.append('query', textQueryInput.value);
                }

                // Get selected categories from the dropdown
                const categoryCheckboxes = form.querySelectorAll(
                    'input[name="search_categories[]"]:checked');
                categoryCheckboxes.forEach(checkbox => {
                    formData.append('search_categories[]', checkbox.value);
                });


                // Submit the form using Fetch API because a file upload requires specific headers
                fetch(form.action, {
                        method: form.method,
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text(); // Or .json() if your backend returns JSON
                    })
                    .then(html => {
                        // Assuming the backend returns the HTML of the results page,
                        // you can replace the current content or redirect.
                        // For a full page reload to the search results:
                        window.location.href = form.action + '?' + new URLSearchParams(formData)
                            .toString();
                    })
                    .catch(error => {
                        console.error('Error during image search submission:', error);
                        alert('An error occurred during image search. Please try again.');
                    });

                this.showUploadModal = false; // Close the modal after submission
                this.imagePreview = null; // Clear preview
                this.uploadedFile = null; // Clear uploaded file
                this.imageUrl = ''; // Clear URL
            },
        }));
    });
</script>
<script>
    document.addEventListener('alpine:init', () => {
        // Main search box component (no significant changes here)
        Alpine.data('searchBoxComponent', () => ({
            // This component largely acts as a container; nested x-data handles specifics.
        }));

        // Image upload component (minor adjustment to trigger recent search add)
        Alpine.data('imageUploadComponent', () => ({
            showUploadModal: false,
            imagePreview: null,
            uploadedFile: null,
            imageUrl: '',

            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    this.uploadedFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.imagePreview = null;
                    this.uploadedFile = null;
                }
            },

            handleDrop(event) {
                const file = event.dataTransfer.files[0];
                if (file) {
                    this.uploadedFile = file;
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            },

            submitImage() {
                const form = document.querySelector('.main-search-form');
                const formData = new FormData(form);

                if (this.uploadedFile) {
                    formData.append('search_image', this.uploadedFile);
                } else if (this.imageUrl) {
                    formData.append('image_url', this.imageUrl);
                }

                const textQueryInput = form.querySelector('input[name="query"]');
                if (textQueryInput && textQueryInput.value) {
                    formData.append('query', textQueryInput.value);
                    // Crucial: Manually trigger addRecentSearch on the main search input's Alpine data
                    // Find the Alpine data scope for the search input
                    const searchInputXData = textQueryInput.closest('[x-data]')._x_dataStack[0];
                    if (searchInputXData && typeof searchInputXData.addRecentSearch ===
                        'function') {
                        searchInputXData.addRecentSearch(textQueryInput.value);
                    }
                }

                const categoryCheckboxes = form.querySelectorAll(
                    'input[name="search_categories[]"]:checked');
                categoryCheckboxes.forEach(checkbox => {
                    formData.append('search_categories[]', checkbox.value);
                });

                fetch(form.action, {
                        method: form.method,
                        body: formData,
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        window.location.href = form.action + '?' + new URLSearchParams(formData)
                            .toString();
                    })
                    .catch(error => {
                        console.error('Error during image search submission:', error);
                        alert('An error occurred during image search. Please try again.');
                    });

                this.showUploadModal = false;
                this.imagePreview = null;
                this.uploadedFile = null;
                this.imageUrl = '';
            },
        }));
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileImage = document.getElementById('profileImage');
        const dropdownMenu = document.getElementById('dropdownMenu');
        profileImage.addEventListener('click', function() {
            // Toggle the dropdown menu
            const dropdown = new bootstrap.Dropdown(dropdownMenu);
            dropdown.toggle();
        });
    });
</script>

<style>
    [x-cloak] {
        display: none !important;
    }

    .category-button,
    #mainDropdownButton {
        /* padding: 8px 21.5px; */
        background-color: #EDEDED;
        color: #212121;
        border-radius: 12px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        height: 40px;
    }

    .category-button:hover,
    #mainDropdownButton:hover {
        background-color: #185D31;
        color: white;
    }

    .category-button.active,
    #mainDropdownButton.active {
        background-color: #185D31;
        color: white;
    }

    .profile-menu {
        border: 1px solid #e0e0e0;
        border-radius: 0.75rem;
        background-color: #fff;
        text-align: right;
        position: absolute !important;
        transform: translate(0, 0) !important;
        top: 100% !important;
        left: 0 !important;
    }

    .dropdown-item {
        font-size: 14px;
        border-radius: 12PX;
        padding-top: 12px;
        padding-right: 10px;
        color: #696969;
        font-weight: 400;
        height: 49px;
    }

    .dropdown-item:hover {
        background-color: #185D31;
        color: #FFFFFF;
        font-weight: 500;
    }

    #all_Categories:hover {
        color: #FFFFFF;
    }


    /* @media(max-width: 1450px) {
        #dropdownMenuIconButton {
            display: inline;

        }

        .dropdown-text,
        .individualCategories {
            display: none;
        }

        .deliver,
        .language,
        .icons {
            display: none;
        }

        .user-profile-section {
            order: 2;
        }

        .search {
            margin-top: 20px;
        }
    } */
    @media(max-width:1450px) {

        .search,
        .deliver,
        .language,
        .categories {
            display: none;
        }

        #dropdownMenuIconButton,
        .search_menu_small {
            display: flex;
        }

    }
</style>
<style>
    .responsive-dropdown {
        position: absolute;
        top: 100%;
        width: 314px;
        z-index: 1002;
    }

    .left-align {
        left: 100%;
        margin-left: 0.25rem;
    }

    .right-align {
        right: 100%;
        margin-right: 0.25rem;
    }

    @media (max-width: 700px) {
        .responsive-dropdown {
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
            margin: 0 !important;
        }

        .left-align,
        .right-align {
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
            margin: 0 !important;
        }
    }
</style>

<header class="bg-white flex flex-wrap items-center justify-between py-3 h-auto w-full md:px-[64px] px-[20px]">

    <div class="flex items-center h-auto w-auto max-w-[72px] ">
        <a href="/">
            <img src="{{ asset('images/2.png') }}" alt="Logo">
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
<div x-data="deliveryDropdown()" class=" relative inline-block text-[12px] max-w-[150px]">

    <!-- Dropdown button -->
<div @click="toggleDropdown()"
     class="flex items-center cursor-pointer p-1 hover:border font-normal rounded-[4px] space-x-1 justify-center">

    <!-- Always show icon -->
    <img src="{{ asset('images/Flag Pack.svg') }}" alt="Location" class="w-[24px] h-[24px] ml-2">

    <!-- Only show text on medium screens and above -->
    <span x-text="selectedLocationText" class="hidden sm:inline"></span>

    <!-- Arrow icon, can also hide on very small screens if desired -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
         stroke="currentColor" class="w-[12px] h-[12px] shrink-0 hidden sm:inline">
        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
    </svg>
</div>

    <!-- Dropdown content -->
    <div x-show="open" @click.away="open = false" x-cloak
         class="absolute z-50 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg p-4
                w-[250px] sm:w-[350px] ltr:sm:left-0 rtl:sm:right-0 -right-25 md:translate-x-0">

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
        <a href="{{ route('login') }}"
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




   <div class="search w-full md:flex-grow md:max-w-2xl mx-8 order-4">
 <form x-data="searchForm()" action="{{ route('search') }}" method="POST" enctype="multipart/form-data" class="main-search-form" @submit="setMode">
        @csrf

        <!-- Hidden inputs -->
        <input type="hidden" name="mode" x-model="mode">
        <input type="hidden" name="image_url" x-model="imageUrl">
        <input type="file" name="image_file" x-ref="imageFile" class="hidden" @change="handleFileUpload">
        <div class="flex border rounded-[12px] bg-[#F8F9FA] items-center py-1 px-2 relative">
            {{-- Main Search Input and Suggestions Popup --}}
            <div class="relative w-full mx-2" x-cloak x-data="{
                searchText: '{{ request('query') }}',
                showPopup: false,
                recentSearches: JSON.parse(localStorage.getItem('recentSearches') || '[]'),
                productSuggestions: [],
                loadingSuggestions: false,
                debounceTimeout: null,
            
                init() {
                    // Submit form and add to recent searches when form is submitted
                    this.$el.closest('form').addEventListener('submit', () => {
                        this.addRecentSearch(this.searchText);
                    });
            
                    // Manage outside click listener for popup
                    // This handles closing the popup when clicking anywhere OUTSIDE the search input area.
                    document.addEventListener('click', (event) => {
                        const searchBoxElement = this.$el; // This div element
                        if (!searchBoxElement.contains(event.target)) {
                            this.showPopup = false;
                        }
                    });
            
                    // Initial fetch of default suggestions if searchText is empty on page load
                    this.$nextTick(() => {
                        if (this.searchText.length === 0) {
                            this.fetchSuggestions(true); // Fetch default suggestions on load if input is empty
                        } else {
                            this.fetchSuggestions(); // Fetch suggestions based on existing query
                        }
                    });
                },
            
                // fetchSuggestions now takes an optional 'isDefault' parameter
                // to distinguish between typing-based suggestions and default suggestions.
                fetchSuggestions(isDefault = false) {
                    if (this.debounceTimeout) {
                        clearTimeout(this.debounceTimeout);
                    }
                    this.debounceTimeout = setTimeout(async () => {
                        let queryParam = '';
                        if (isDefault || this.searchText.length === 0) {
                            // If isDefault is true, or searchText is empty, we request default products.
                            // The backend handles returning default products when 'query' is empty.
                            queryParam = ''; // An empty query parameter signals the backend to send defaults
                        } else if (this.searchText.length >= 1) { // Fetch if there's at least 1 character typed for dynamic suggestions
                            queryParam = `?query=${encodeURIComponent(this.searchText)}`;
                        } else {
                            this.productSuggestions = []; // Clear suggestions if searchText is too short for dynamic search
                            return;
                        }
            
                        this.loadingSuggestions = true;
                        this.productSuggestions = []; // Clear current suggestions before fetching
            
                        try {
                            const response = await fetch(`/products/suggestions${queryParam}`);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            const data = await response.json();
                            this.productSuggestions = data;
                        } catch (error) {
                            console.error('Error fetching product suggestions:', error);
                            this.productSuggestions = [];
                        } finally {
                            this.loadingSuggestions = false;
                        }
                    }, 300); // Debounce time
                },
            
                selectSuggestion(item) {
                    // item might be a string OR an object (e.g. { id, name })
                    const value = (item && typeof item === 'object') ? item.name : item;
            
                    this.searchText = value;
                    this.showPopup = false;
                    this.addRecentSearch(value);
            
                    // update the actual input (uses x-ref to avoid selector/quote issues)
                    if (this.$refs && this.$refs.queryInput) {
                        this.$refs.queryInput.value = value;
                    }
            
                    this.$el.closest('form').submit();
                },
            
                addRecentSearch(query) {
                    if (query && query.trim() !== '') {
                        let currentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                        // Remove if already exists (case-insensitive)
                        currentSearches = currentSearches.filter(s => s.toLowerCase() !== query.toLowerCase());
                        currentSearches.unshift(query.trim()); // Add to the beginning
                        currentSearches = currentSearches.slice(0, 5); // Keep max 5
                        localStorage.setItem('recentSearches', JSON.stringify(currentSearches));
                        this.recentSearches = currentSearches;
                    }
                },
            
                removeRecentSearch(searchToRemove) {
                    this.recentSearches = this.recentSearches.filter(s => s !== searchToRemove);
                    localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
                    // If no recent searches left and search text is empty, hide popup
                    if (this.recentSearches.length === 0 && this.searchText === '') {
                        this.showPopup = false;
                    }
                },
            
                clearAllRecentSearches() {
                    this.recentSearches = [];
                    localStorage.removeItem('recentSearches');
                    // If no recent searches and no product suggestions, hide popup
                    if (this.productSuggestions.length === 0 && this.searchText === '') {
                        this.showPopup = false;
                    }
                },
            
                refreshSuggestions() {
                    // This specifically requests a fresh set of default products from the API
                    this.fetchSuggestions(true);
                }
            }">
                <div class="flex items-center border-[1px] bg-white rounded-[12px] overflow-hidden">
                    <img src="{{ asset('images/interface-search--glass-search-magnifying--Streamline-Core.svg') }}"
                        alt="Search Icon" class="h-[16px] w-[16px] object-cover text-[#767676] ml-2 mr-0 md:mr-6">
                    <input
                        type="text"
                        name="query"
                        x-ref="queryInput"
                        x-model="searchText"
                        @focus="showPopup = true; if (searchText.length === 0) fetchSuggestions(true); else fetchSuggestions();"
                        @input="showPopup = true; fetchSuggestions()"
                        @click.stop
                        class="block w-full px-3 py-2 border-none focus:outline-none focus:ring-0 sm:text-sm"
                        placeholder="{{ __('messages.Search') }}">
                </div>
            
                {{-- Unified Popup for Suggestions and Recent Searches --}}
                <div x-show="showPopup" @click.away="showPopup = false" x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute bg-white border rounded shadow mt-1 z-20 max-h-64 overflow-auto 
                           w-[calc(100%-2rem)] left-1/2 -translate-x-1/2 
                           md:w-[660px] md:left-0 md:-translate-x-0">
            
                    <div x-show="loadingSuggestions" x-clock
                        class="px-3 py-2 text-gray-500 text-sm flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Loading suggestions...
                    </div>
            
                    {{-- Recent Searches Section --}}
                    <div x-show="recentSearches.length > 0 && searchText.length === 0" x-cloak>
                        <div class="px-3 py-3 uppercase border-b flex justify-between items-center">
                            <span
                                class="font-bold text-[#121212] text-[20px]">{{ __('messages.recent_Searches') }}</span>
                            <button type="button" @click="clearAllRecentSearches()"
                                class="flex justify-between text-gray-400 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="#696969" class="size-5 ml-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.924a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span class="text-[#696969] text-[12px]">{{ __('messages.delete') }}</span>
                            </button>
                        </div>
                        <template x-for="product in productSuggestions" :key="product.id">
                            <div class="flex justify-between items-center px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm mb-2"
                                @click="selectSuggestion(product.name)">
                                <div class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        strokeWidth={1.5} stroke="currentColor"
                                        class="size-6 text-[#212121] ml-2 bg-[#EDEDED] rounded-full p-1">
                                        <path strokeLinecap="round" strokeLinejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
            
                                    <span x-text="product.name" class="text-[16px]"></span>
                                </div>
                                <button type="button" @click.stop="removeRecentSearch(item)"
                                    class="text-gray-400 hover:text-gray-700 text-xs">
                                    &times;
                                </button>
                            </div>
                        </template>
                    </div>
            
                    {{-- Product Suggestions Section (Recommended for you) --}}
                    <div x-cloak
                        x-show="productSuggestions.length > 0 || searchText.length === 0 || loadingSuggestions">
                        <div class="px-3 py-3 text-gray-700 text-xs uppercase border-b flex justify-between items-center"
                            :class="{ 'mt-2': recentSearches.length > 0 && searchText.length === 0 }">
                            <span
                                class="text-[20px] sm:text-[14px] text-[#212121] font-bold">{{ __('messages.recommend') }}</span>
                            <button type="button" @click="refreshSuggestions()"
                                class="flex justify-between text-gray-400 hover:text-gray-700">
            
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="#696969" class="size-5 ml-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                <span class="text-[#696969] text-[12px]">{{ __('messages.refresh') }}</span>
                            </button>
                        </div>
                        <template x-for="item in productSuggestions" :key="'suggest-' + item">
                            <div @click="selectSuggestion(item)"
                                class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="size-6 text-[#212121] ml-2 bg-[#EDEDED] rounded-full p-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span x-text="item" class="text-[16px]"></span>
                            </div>
                        </template>
                        {{-- No Recommended Products Message --}}
                        <div x-cloak x-show="!loadingSuggestions && productSuggestions.length === 0"
                            class="px-3 py-2 text-gray-500 text-sm">
                            No recommended products at the moment.
                        </div>
                    </div>
            
                    {{-- No Search Results Message (only when typing and no suggestions) --}}
                    <div x-cloak
                        x-show="searchText.length > 0 && !loadingSuggestions && productSuggestions.length === 0">
                        <div class="px-3 py-2 text-gray-500 text-sm">No relevant search results found.</div>
                    </div>
                </div>
            </div>
            
            {{-- Image Upload Component (Unchanged) --}}
            <div  class="relative flex items-center justify-center mx-2 shrink-0">
                <label @click="showUploadModal = true" class="cursor-pointer hover:text-black text-[#767676]">
                    <img src="{{ asset('images/Group (3).svg') }}" alt="Upload Image" class="w-[20px] h-[20px]">
                </label>
            
                {{-- This is now the ONLY main container for the modal --}}
                <div x-show="showUploadModal" x-cloak @click.away="showUploadModal = false"
                    class="absolute top-full mt-2 bg-white shadow-lg rounded-lg py-2 z-30
                           w-[310px] sm:w-[660px] 
                           left-1/2 -translate-x-1/2">
                    {{-- The content div inside, responsible for its own padding --}}
                    <div class="p-6 relative w-full h-full">
                        <button @click="showUploadModal = false"
                            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="border-2 border-dashed border-gray-300 p-6 rounded-md flex flex-col items-center justify-center text-gray-600 m-2 h-[180px] sm:w-[600px] sm:h-[210px]"
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
                              x-ref="imageFile"  @change="handleImageUpload">
                        </div>
                        <div class="flex flex-col sm:flex-row mt-4 mb-4 items-center justify-between">
                            <input type="text" x-model="imageUrl" placeholder="{{ __('messages.imageURL') }}"
                                class="border border-gray-300 px-3 py-2 rounded w-full sm:w-[400px] text-sm mb-2 sm:mb-0" />
                            {{-- <button type="button" @click="submitImage"
                                class="bg-green-800 text-white px-6 py-2 rounded text-sm w-full sm:w-auto">
                                {{ __('messages.Search') }}
                            </button> --}}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="shrink-0">
                <button type="submit"
                    class="bg-[#185D31] w-[61px] h-[32px] text-white rounded-[12px] pb-1 mx-1 text-sm">
                    {{ __('messages.Search') }}
                </button>
            </div>
        </div>
    </form>
</div>







    @php
        $currentLang = app()->getLocale();
        // @dd($currentLang)
    @endphp
    <!-- Language -->
    <div class=" btn-group flex items-center  order-5" style="color: #212121;  width:90px; height:24px; ">

          <div class="dropdown flex items-center cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
    
    <!-- Always show icon -->
    <img src="{{ asset('images/Vector (2).svg') }}" alt="Language Icon"
         class="w-[16px] h-[16px] rtl:ml-2 ltr:mr-2">

    <!-- Show text only on medium+ screens -->
    <span class="text-[#212121] text-sm md:text-base hidden md:inline">
        {{ $currentLang == 'ar' ? 'العربية' : 'En' }}
    </span>

    <!-- Dropdown menu here -->
    <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="?lang=ar">العربية</a></li>
        <li><a class="dropdown-item" href="?lang=en">English</a></li>
    </ul>
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
    <a href="{{ route('supplier.dashboard') }}"
   class="inline-block px-3  rounded-lg  transition">
   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
</svg>

</a>


        {{-- Notification Icon and Popup (only if user is logged in) --}}
    @include('partials.notifications_popup')

    </div>







@include('supplier.supplier_userSection')
</header>


<div class="search_menu_small hidden flex-row justify-between px-4 sm:px-[64px] py-4">

    {{-- ********************************************drop menu for small screen******************************* --}}

    <button id="dropdownMenuIconButton" data-dropdown-toggle="mergedDropdownMenu"
        class="hidden order-3  items-center px-2 py-2 text-lg rtl:ml-2 ltr:ml-2 font-small text-center text-gray-900 bg-[#F8F9FA] rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-50 "
        type="button">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
            class="bi bi-list" viewBox="0 0 16 16">
            <path fill-rule="evenodd"
                d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
        </svg>
    </button>

    <!-- Dropdown menu -->
    {{-- <div id="mergedDropdownMenu"
        class="z-20 hidden p-4 bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-[200px] dark:bg-gray-700 dark:divide-gray-600">
        <ul class="py-2 text-sm text-gray-700 " aria-labelledby="dropdownMenuIconButton">

          
        </ul>
    </div> --}}

    {{-- ***************************************************************************************************** --}}
  <div class="w-full md:flex-grow md:max-w-2xl mx-8 order-4">
    <form x-data="searchForm()" action="{{ route('search') }}" method="POST" enctype="multipart/form-data" class="main-search-form" @submit="setMode">
        @csrf

        <!-- Hidden inputs -->
        <input type="hidden" name="mode" x-model="mode">
        <input type="hidden" name="image_url" x-model="imageUrl">
        <input type="file" name="image_file" x-ref="imageFile" class="hidden" @change="handleFileUpload">

        <div class="flex border rounded-[12px] bg-[#F8F9FA] items-center py-1 px-2 relative">
            {{-- Main Search Input and Suggestions Popup --}}
            <div class="relative w-full mx-2" x-cloak x-data="{
                searchText: '{{ request('query') }}',
                showPopup: false,
                recentSearches: JSON.parse(localStorage.getItem('recentSearches') || '[]'),
                productSuggestions: [],
                loadingSuggestions: false,
                debounceTimeout: null,
            
                init() {
                    // Submit form and add to recent searches when form is submitted
                    this.$el.closest('form').addEventListener('submit', () => {
                        this.addRecentSearch(this.searchText);
                    });
            
                    // Manage outside click listener for popup
                    // This handles closing the popup when clicking anywhere OUTSIDE the search input area.
                    document.addEventListener('click', (event) => {
                        const searchBoxElement = this.$el; // This div element
                        if (!searchBoxElement.contains(event.target)) {
                            this.showPopup = false;
                        }
                    });
            
                    // Initial fetch of default suggestions if searchText is empty on page load
                    this.$nextTick(() => {
                        if (this.searchText.length === 0) {
                            this.fetchSuggestions(true); // Fetch default suggestions on load if input is empty
                        } else {
                            this.fetchSuggestions(); // Fetch suggestions based on existing query
                        }
                    });
                },
            
                // fetchSuggestions now takes an optional 'isDefault' parameter
                // to distinguish between typing-based suggestions and default suggestions.
                fetchSuggestions(isDefault = false) {
                    if (this.debounceTimeout) {
                        clearTimeout(this.debounceTimeout);
                    }
                    this.debounceTimeout = setTimeout(async () => {
                        let queryParam = '';
                        if (isDefault || this.searchText.length === 0) {
                            // If isDefault is true, or searchText is empty, we request default products.
                            // The backend handles returning default products when 'query' is empty.
                            queryParam = ''; // An empty query parameter signals the backend to send defaults
                        } else if (this.searchText.length >= 1) { // Fetch if there's at least 1 character typed for dynamic suggestions
                            queryParam = `?query=${encodeURIComponent(this.searchText)}`;
                        } else {
                            this.productSuggestions = []; // Clear suggestions if searchText is too short for dynamic search
                            return;
                        }
            
                        this.loadingSuggestions = true;
                        this.productSuggestions = []; // Clear current suggestions before fetching
            
                        try {
                            const response = await fetch(`/products/suggestions${queryParam}`);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            const data = await response.json();
                            this.productSuggestions = data;
                        } catch (error) {
                            console.error('Error fetching product suggestions:', error);
                            this.productSuggestions = [];
                        } finally {
                            this.loadingSuggestions = false;
                        }
                    }, 300); // Debounce time
                },
            
                selectSuggestion(item) {
                    // item might be a string OR an object (e.g. { id, name })
                    const value = (item && typeof item === 'object') ? item.name : item;
            
                    this.searchText = value;
                    this.showPopup = false;
                    this.addRecentSearch(value);
            
                    // update the actual input (uses x-ref to avoid selector/quote issues)
                    if (this.$refs && this.$refs.queryInput) {
                        this.$refs.queryInput.value = value;
                    }
            
                    this.$el.closest('form').submit();
                },
            
                addRecentSearch(query) {
                    if (query && query.trim() !== '') {
                        let currentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                        // Remove if already exists (case-insensitive)
                        currentSearches = currentSearches.filter(s => s.toLowerCase() !== query.toLowerCase());
                        currentSearches.unshift(query.trim()); // Add to the beginning
                        currentSearches = currentSearches.slice(0, 5); // Keep max 5
                        localStorage.setItem('recentSearches', JSON.stringify(currentSearches));
                        this.recentSearches = currentSearches;
                    }
                },
            
                removeRecentSearch(searchToRemove) {
                    this.recentSearches = this.recentSearches.filter(s => s !== searchToRemove);
                    localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
                    // If no recent searches left and search text is empty, hide popup
                    if (this.recentSearches.length === 0 && this.searchText === '') {
                        this.showPopup = false;
                    }
                },
            
                clearAllRecentSearches() {
                    this.recentSearches = [];
                    localStorage.removeItem('recentSearches');
                    // If no recent searches and no product suggestions, hide popup
                    if (this.productSuggestions.length === 0 && this.searchText === '') {
                        this.showPopup = false;
                    }
                },
            
                refreshSuggestions() {
                    // This specifically requests a fresh set of default products from the API
                    this.fetchSuggestions(true);
                }
            }">
                <div class="flex items-center border-[1px] bg-white rounded-[12px] overflow-hidden">
                    <img src="{{ asset('images/interface-search--glass-search-magnifying--Streamline-Core.svg') }}"
                        alt="Search Icon" class="h-[16px] w-[16px] object-cover text-[#767676] ml-2 mr-0 md:mr-6">
                    <input
                        type="text"
                        name="query"
                        x-ref="queryInput"
                        x-model="searchText"
                        @focus="showPopup = true; if (searchText.length === 0) fetchSuggestions(true); else fetchSuggestions();"
                        @input="showPopup = true; fetchSuggestions()"
                        @click.stop
                        class="block w-full px-3 py-2 border-none focus:outline-none focus:ring-0 sm:text-sm"
                        placeholder="{{ __('messages.Search') }}">
                </div>
            
                {{-- Unified Popup for Suggestions and Recent Searches --}}
                <div x-show="showPopup" @click.away="showPopup = false" x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute bg-white border rounded shadow mt-1 z-20 max-h-64 overflow-auto 
                           w-[220px] left-1/2 -translate-x-1/2 "
                           >
            
                    <div x-show="loadingSuggestions" x-cloak
                        class="px-3 py-2 text-gray-500 text-sm flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
<span>{{ __('messages.loading_suggestions') }}</span>
                    </div>
            
                    {{-- Recent Searches Section --}}
                    <div x-show="recentSearches.length > 0 && searchText.length === 0" x-cloak>
                        <div class="px-3 py-3 uppercase border-b flex justify-between items-center">
                            <span
                                class="font-bold text-[#121212] text-[20px]">{{ __('messages.recent_Searches') }}</span>
                            <button type="button" @click="clearAllRecentSearches()"
                                class="flex justify-between text-gray-400 hover:text-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="#696969" class="size-5 ml-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.924a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span class="text-[#696969] text-[12px]">{{ __('messages.delete') }}</span>
                            </button>
                        </div>
                        <template x-for="product in productSuggestions" :key="product.id">
                            <div class="flex justify-between items-center px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm mb-2"
                                @click="selectSuggestion(product.name)">
                                <div class="flex items-center space-x-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        strokeWidth={1.5} stroke="currentColor"
                                        class="size-6 text-[#212121] ml-2 bg-[#EDEDED] rounded-full p-1">
                                        <path strokeLinecap="round" strokeLinejoin="round"
                                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
            
                                    <span x-text="product.name" class="text-[16px]"></span>
                                </div>
                                <button type="button" @click.stop="removeRecentSearch(item)"
                                    class="text-gray-400 hover:text-gray-700 text-xs">
                                    &times;
                                </button>
                            </div>
                        </template>
                    </div>
            
                    {{-- Product Suggestions Section (Recommended for you) --}}
                    <div x-cloak
                        x-show="productSuggestions.length > 0 || searchText.length === 0 || loadingSuggestions">
                        <div class="px-3 py-3 text-gray-700 text-xs uppercase border-b flex justify-between items-center"
                            :class="{ 'mt-2': recentSearches.length > 0 && searchText.length === 0 }">
                            <span
                                class="text-[20px] sm:text-[14px] text-[#212121] font-bold">{{ __('messages.recommend') }}</span>
                            <button type="button" @click="refreshSuggestions()"
                                class="flex justify-between text-gray-400 hover:text-gray-700">
            
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="#696969" class="size-5 ml-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                <span class="text-[#696969] text-[12px]">{{ __('messages.refresh') }}</span>
                            </button>
                        </div>
                        <template x-for="item in productSuggestions" :key="'suggest-' + item">
                            <div @click="selectSuggestion(item)"
                                class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm flex items-center space-x-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor"
                                    class="size-6 text-[#212121] ml-2 bg-[#EDEDED] rounded-full p-1">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                                <span x-text="item" class="text-[16px]"></span>
                            </div>
                        </template>
                        {{-- No Recommended Products Message --}}
                        <div x-cloak x-show="!loadingSuggestions && productSuggestions.length === 0"
                            class="px-3 py-2 text-gray-500 text-sm">
                            No recommended products at the moment.
                        </div>
                    </div>
            
                    {{-- No Search Results Message (only when typing and no suggestions) --}}
                    <div x-cloak
                        x-show="searchText.length > 0 && !loadingSuggestions && productSuggestions.length === 0">
                        <div class="px-3 py-2 text-gray-500 text-sm">No relevant search results found.</div>
                    </div>
                </div>
            </div>
            
            {{-- Image Upload Component (Unchanged) --}}
            <div x-data="imageUploadComponent()" class="relative flex items-center justify-center mx-2 shrink-0">
                <label @click="showUploadModal = true" class="cursor-pointer hover:text-black text-[#767676]">
                    <img src="{{ asset('images/Group (3).svg') }}" alt="Upload Image" class="w-[20px] h-[20px]">
                </label>
            
                {{-- This is now the ONLY main container for the modal --}}
                <div x-show="showUploadModal" x-cloak @click.away="showUploadModal = false"
                    class="absolute top-full mt-2 bg-white shadow-lg rounded-lg py-2 z-30
                           w-[310px] sm:w-[660px] 
                           left-1/2 -translate-x-1/2">
                    {{-- The content div inside, responsible for its own padding --}}
                    <div class="p-6 relative w-full h-full">
                        <button @click="showUploadModal = false"
                            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <div class="border-2 border-dashed border-gray-300 p-6 rounded-md flex flex-col items-center justify-center text-gray-600 m-2 h-[180px] sm:w-[600px] sm:h-[210px]"
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
                              x-ref="imageFile"   @change="handleImageUpload">
                        </div>
                        <div class="flex flex-col sm:flex-row mt-4 mb-4 items-center justify-between">
                            <input type="text" x-model="imageUrl" placeholder="{{ __('messages.imageURL') }}"
                                class="border border-gray-300 px-3 py-2 rounded w-full sm:w-[400px] text-sm mb-2 sm:mb-0" />
                            {{-- <button type="button" @click="submitImage"
                                class="bg-green-800 text-white px-6 py-2 rounded text-sm w-full sm:w-auto">
                                {{ __('messages.Search') }}
                            </button> --}}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="shrink-0">
                <button type="submit"
                    class="bg-[#185D31] w-[61px] h-[32px] text-white rounded-[12px] pb-1 mx-1 text-sm">
                    {{ __('messages.Search') }}
                </button>
            </div>
        </div>
    </form>
</div>
<script>
function searchForm() {
    return {
        mode: 'text',
        query: '',
        imageUrl: '',
        showUploadModal: false,
        imagePreview: null,

        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                this.mode = 'image';
                this.imageUrl = '';
                this.imagePreview = URL.createObjectURL(file);
            }
        },

        handleDrop(event) {
            const file = event.dataTransfer.files[0];
            if (file) {
                this.$refs.imageFile.files = event.dataTransfer.files;
                this.imagePreview = URL.createObjectURL(file);
                this.imageUrl = '';
                this.mode = 'image';
            }
        },

        setMode() {
            // This ensures the hidden input "mode" is correct before submitting
            if (this.$refs.imageFile.files.length > 0) {
                this.mode = 'image';
            } else if (this.imageUrl.trim() !== '') {
                this.mode = 'url';
            } else {
                this.mode = 'text';
            }
        },

        init() {
            this.$watch('imageUrl', (value) => {
                if (value.trim() !== '') {
                    this.mode = 'url';
                    this.imagePreview = null;
                    this.$refs.imageFile.value = '';
                } else if (!this.$refs.imageFile.files.length) {
                    this.mode = 'text';
                }
            });
        }
    }
}
</script>











{{-- <div id="mapModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg relative mx-4 md:mx-auto">
        <div class="p-4 border-b">
            <div class="flex items-center mt-2 border-[1px] border-[#767676] rounded-[12px] overflow-hidden">
                <img src="{{ asset('images/interface-search--glass-search-magnifying--Streamline-Core.svg') }}"
                    alt="Search Icon" class="h-[16px] w-[16px] object-cover text-[#767676] ml-2 mr-0 md:mr-6">
                <input type="text"
                    class="block w-full px-3 py-2 border-none h-[56px] focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder=" {{ __('messages.Search') }}">
            </div>
        </div>
        <div id="map" class="w-full h-[300px] md:h-[500px]"></div>
        <div class="flex justify-between p-4 border-t">
            <button onclick="closeMapModal()"
                class="bg-white border border-gray-300 px-4 py-2 rounded hover:bg-gray-100">{{ __('messages.return') }}</button>
            <button onclick="confirmLocation()"
                class="bg-green-700 text-white px-4 py-2 rounded hover:bg-green-800">{{ __('messages.confirmLocation') }}</button>
        </div>
    </div>
</div>--}}
</div> 
