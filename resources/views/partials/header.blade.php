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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainDropdownButton = document.getElementById('mainDropdownButton');
        const mainDropdownMenu = document.getElementById('mainDropdownMenu');

        if (!mainDropdownButton || !mainDropdownMenu) {
            console.warn("Dropdown button or menu is missing.");
            return;
        }

        // Toggle main dropdown
        mainDropdownButton.addEventListener('click', function(event) {
            event.stopPropagation();
            mainDropdownMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
            if (!mainDropdownMenu.contains(event.target) &&
                !mainDropdownButton.contains(event.target)) {
                mainDropdownMenu.classList.add('hidden');
            }
        });

        // Close dropdown when clicking any link inside it
        mainDropdownMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                mainDropdownMenu.classList.add('hidden');
            });
        });
    });
</script>
<style>
    .category-button,
    #mainDropdownButton {
        padding: 8px 30px;
        background-color: #EDEDED;
        color: #767676;
        border-radius: 12px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        height: 40px;
    }

    .category-button:hover,
    #mainDropdownButton:hover {
        color: #212121;
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

    @media (max-width: 770px) {

        /* Change 640px to your desired breakpoint */
        .dropdown-text,
        .individualCategories {
            display: none;
        }
    }
</style>

<header class="bg-white flex flex-wrap items-center justify-between py-2 shadow-md px-[64px]">
    <div class="flex items-center h-auto w-auto max-w-[72px] shrink-0">
        <img src="{{ asset('images/Logo.png') }}" alt="Logo">
    </div>

    <div x-data="{ open: false }"
        class="relative inline-block text-[12px] tracking-[0%] w-auto max-w-[150px] mx-4 md:w-[120px] md:h-[36px] shrink-0">
        <div @click="open = !open"
            class="flex items-center cursor-pointer p-1 hover:border font-normal rounded-[4px] space-x-1 h-full w-full justify-center">
            <img src="https://s.alicdn.com/@icon/flag/assets/sa.png" alt="SA" class="w-[24px] h-[24px] ml-2" />
            <span class="truncate">{{ __('messages.deliver') }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-[12px] h-[12px] shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </div>

        <div x-show="open" @click.away="open = false" x-transition
            class="absolute z-10 mt-2 w-[calc(100vw-32px)] left-1/2 -translate-x-1/2 bg-white border border-gray-200 rounded-lg shadow-lg p-4
                   md:w-[350px] md:left-auto md:right-0 md:translate-x-0">
            <div class="flex flex-col mb-4">
                <p class="font-cairo font-bold text-[20px] leading-[150%] tracking-[0%] text-right align-middle mb-3">
                    {{ __('messages.deliverySite') }}</p>
                <p class="font-cairo text-[14px] leading-[150%] tracking-[0%] text-right align-middle text-gray-500">
                    {{ __('messages.deliverySiteMSG') }}</p>
            </div>
            <div>
                <a href=""
                    class="w-full h-[40px] bg-[#185D31] px-4 py-2 rounded-[12px] cursor-pointer text-[14px] text-white flex items-center justify-center">
                    {{ __('messages.addLocation') }}
                </a>
            </div>
            <div class="flex items-center justify-center my-4 text-gray-300">
                <hr class="flex-grow border-t border-gray-300">
                <span class="text-sm text-gray-500 font-medium mx-4">{{ __('messages.or') }}</span>
                <hr class="flex-grow border-t border-gray-300">
            </div>
            <div
                class="dropdown w-full h-[40px] bg-white px-4 py-2 rounded-[12px] flex items-center justify-between border border-gray-400 text-gray-600 font-normal text-[16px] cursor-pointer">
                <a href="javascript:void(0)" onclick="openMapModal()"
                    class="flex-1 text-gray-600">{{ __('messages.chooseCity') }}</a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 ml-2 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>
        </div>
    </div>

    <div id="mapModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
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
    </div>

    <div class="w-full  mt-2 md:mt-0 md:flex-grow md:max-w-2xl mx-8">
        <form action="{{ route('search') }}" method="GET" class="main-search-form">
            <div class="flex border rounded-[12px] bg-[#F8F9FA] items-center py-1 px-2 relative">

                <div class="relative" x-data="{ categoryOpen: false, selectedCategories: @json(request('search_categories', [])) }">
                    <button type="button" @click="categoryOpen = !categoryOpen"
                        class="flex items-center px-1 h-full w-[100px] md:w-[163px] border-l text-[#767676] text-sm font-normal font-[Cairo] shrink-0">
                        <div class="flex items-center justify-between px-2 h-full w-full">
                            <span class="text-sm truncate">{{ __('messages.search_with') }}</span>
                            <svg class="size-4 transform" :class="{ 'rotate-180': categoryOpen }" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                            </svg>
                        </div>
                    </button>
                    <div x-show="categoryOpen" @click.outside="categoryOpen = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-10 bg-white border mt-1 rounded-2xl shadow-md p-[10px] text-[#212121] text-base font-[Cairo] leading-[50px] w-full max-h-[100px] overflow-y-auto
                               md:w-[163px]">
                        <label
                            class="flex items-center space-x-2 cursor-pointer py-1 text-sm text-gray-700 font-[Cairo] mb-3">
                            <input type="checkbox" value="products" name="search_categories[]"
                                x-model="selectedCategories"
                                class="form-checkbox ml-4 w-[20px] h-[20px] border-[1.5px] border-gray-400 rounded">
                            <span class="text-sm font-[Cairo] text-gray-700">{{ __('messages.products') }}</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer py-1">
                            <input type="checkbox" value="suppliers" name="search_categories[]"
                                x-model="selectedCategories"
                                class="form-checkbox w-[20px] h-[20px] border-[1.5px] border-gray-400 rounded ml-4">
                            <span class="text-sm font-[Cairo] text-gray-700">{{ __('messages.suppliers') }}</span>
                        </label>
                    </div>
                </div>

                <div class="relative w-full mx-2" x-data="{
                    searchText: '{{ request('query') }}',
                    showPopup: false,
                    recentSearches: JSON.parse(localStorage.getItem('recentSearches') || '[]'),
                    productSuggestions: [],
                    loadingSuggestions: false,
                    debounceTimeout: null,
                
                    init() {
                        this.$el.closest('form').addEventListener('submit', () => {
                            this.addRecentSearch(this.searchText);
                        });
                        this.$watch('showPopup', (value) => {
                            if (value) {
                                document.addEventListener('click', this.closePopupOnOutsideClick);
                            } else {
                                document.removeEventListener('click', this.closePopupOnOutsideClick);
                            }
                        });
                    },
                
                    closePopupOnOutsideClick(event) {
                        const searchBoxElement = this.$el;
                        if (!searchBoxElement.contains(event.target)) {
                            this.showPopup = false;
                        }
                    },
                
                    fetchSuggestions() {
                        if (this.debounceTimeout) {
                            clearTimeout(this.debounceTimeout);
                        }
                        this.debounceTimeout = setTimeout(async () => {
                            if (this.searchText.length > 1) {
                                this.loadingSuggestions = true;
                                try {
                                    const response = await fetch(`/api/products/suggestions?query=${encodeURIComponent(this.searchText)}`);
                                    const data = await response.json();
                                    this.productSuggestions = data;
                                } catch (error) {
                                    console.error('Error fetching product suggestions:', error);
                                    this.productSuggestions = [];
                                } finally {
                                    this.loadingSuggestions = false;
                                }
                            } else {
                                this.productSuggestions = [];
                            }
                        }, 300);
                    },
                
                    selectSuggestion(item) {
                        this.searchText = item;
                        this.showPopup = false;
                        this.addRecentSearch(item);
                        this.$el.closest('form').submit();
                    },
                
                    addRecentSearch(query) {
                        if (query && query.trim() !== '') {
                            let currentSearches = JSON.parse(localStorage.getItem('recentSearches') || '[]');
                            currentSearches = currentSearches.filter(s => s.toLowerCase() !== query.toLowerCase());
                            currentSearches.unshift(query.trim());
                            currentSearches = currentSearches.slice(0, 5);
                            localStorage.setItem('recentSearches', JSON.stringify(currentSearches));
                            this.recentSearches = currentSearches;
                        }
                    },
                    removeRecentSearch(searchToRemove) {
                        this.recentSearches = this.recentSearches.filter(s => s !== searchToRemove);
                        localStorage.setItem('recentSearches', JSON.stringify(this.recentSearches));
                    }
                }">
                    <div class="flex items-center border-[1px] bg-white rounded-[12px] overflow-hidden">
                        <img src="{{ asset('images/interface-search--glass-search-magnifying--Streamline-Core.svg') }}"
                            alt="Search Icon" class="h-[16px] w-[16px] object-cover text-[#767676] ml-2 mr-0 md:mr-6">
                        <input type="text" x-model="searchText" @focus="showPopup = true; fetchSuggestions()"
                            @input="showPopup = true; fetchSuggestions()"
                            class="block w-full px-3 py-2 border-none focus:outline-none focus:ring-0 px-3 py-1.5 outline-none sm:text-sm"
                            placeholder="{{ __('messages.Search') }}">
                    </div>

                    <div x-show="showPopup && (recentSearches.length > 0 || productSuggestions.length > 0 || loadingSuggestions)"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute bg-white border rounded shadow w-full mt-1 z-20 max-h-64 overflow-auto left-0 md:w-[660px]">

                        <div x-show="loadingSuggestions" class="px-3 py-2 text-gray-500 text-sm flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Loading suggestions...
                        </div>

                        <template x-if="recentSearches.length > 0">
                            <div>
                                <div class="px-3 py-2 text-[#212121] font-bold text-lg uppercase border-b">
                                    {{ __('messages.recent_Searches') }}</div>
                                <template x-for="item in recentSearches" :key="'recent-' + item">
                                    <div class="flex justify-between items-center px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm"
                                        @click="selectSuggestion(item)">
                                        <span x-text="item"></span>
                                        <button type="button" @click.stop="removeRecentSearch(item)"
                                            class="text-gray-400 hover:text-gray-700 text-xs">
                                            &times;
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="productSuggestions.length > 0">
                            <div>
                                <div class="px-3 py-2 text-gray-700 font-semibold text-xs uppercase border-b mt-2">
                                    Product Suggestions</div>
                                <template x-for="item in productSuggestions" :key="'suggest-' + item">
                                    <div @click="selectSuggestion(item)"
                                        class="px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm" x-text="item">
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template
                            x-if="searchText.length > 1 && !loadingSuggestions && recentSearches.length === 0 && productSuggestions.length === 0">
                            <div class="px-3 py-2 text-gray-500 text-sm">No relevant suggestions found.</div>
                        </template>
                    </div>
                </div>

                <div x-data="imageUploadComponent()" class="relative flex items-center justify-center mx-2 shrink-0">
                    <label @click="showUploadModal = true" class="cursor-pointer hover:text-black text-[#767676]">
                        <img src="{{ asset('images/Group (3).svg') }}" alt="Upload Image"
                            class="w-[20px] h-[20px]">
                    </label>
                    <div x-show="showUploadModal" x-cloak
                        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
                        <div @click.away="showUploadModal = false"
                            class="bg-white rounded-lg shadow-lg p-6 relative w-full max-w-md mx-4 sm:w-[660px] sm:h-[320px]">
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
                                    @change="handleImageUpload">
                            </div>
                            <div class="flex flex-col sm:flex-row mt-4 mb-4 items-center justify-between">
                                <input type="text" x-model="imageUrl"
                                    placeholder="{{ __('messages.imageURL') }}"
                                    class="border border-gray-300 px-3 py-2 rounded w-full sm:w-[400px] text-sm mb-2 sm:mb-0" />
                                <button type="button" @click="submitImage"
                                    class="bg-green-800 text-white px-6 py-2 rounded text-sm w-full sm:w-auto">
                                    {{ __('messages.Search') }}
                                </button>
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
    @endphp
    <!-- Language -->
    <div class="btn-group flex items-center " style="color: #212121;  width:90px; height:24px; ">

        <button type="button" class="btn  w-[16px]  border-none" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ asset('images/Vector (2).svg') }}" alt="">
        </button>
        <span class="text-[#212121] text-sm md:text-base ml-1">
            {{ $currentLang == 'ar' ? 'العربية' : 'English' }}
        </span>
        <div class="dropdown-menu w-[180px] h-auto rounded-[12px] bg-[#FFFFFF] py-2 shadow-lg">
            <div class="flex items-center cursor-pointer py-3 px-4 text-base text-[#212121]"
                onclick="window.location.href='{{ route('change.language', 'ar') }}'">
                <input type="radio" name="language" value="arabic" {{ $currentLang == 'ar' ? 'checked' : '' }}
                    class="shrink-0 mr-3 w-6 h-6 border-[#185D31] focus:ring-[#185D31] disabled:pointer-events-none dark:bg-[#185D31] dark:border-neutral-700 dark:checked:border-[#185D31] dark:focus:ring-offset-gray-800 appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                    id="arabic" readonly>
                <label for="arabic" class="text-neutral-700">{{ __('messages.arabic') }}</label>
            </div>
            <div class="flex items-center cursor-pointer py-3 px-4 text-base text-[#212121]"
                onclick="window.location.href='{{ route('change.language', 'en') }}'">
                <input type="radio" name="language" value="english" {{ $currentLang == 'en' ? 'checked' : '' }}
                    class="shrink-0 mr-3 w-6 h-6 border-[#185D31] focus:ring-[#185D31] disabled:pointer-events-none dark:bg-[#185D31] dark:border-neutral-700 dark:checked:border-[#185D31] dark:focus:ring-offset-gray-800 appearance-none rounded-full border-2 checked:bg-[#185D31] checked:border-[#185D31]"
                    id="english" readonly>
                <label for="english" class="text-neutral-700">{{ __('messages.english') }}</label>
            </div>
        </div>
    </div>

    <div class="flex items-center w-auto justify-end gap-x-4 ml-4 shrink-0 md:w-[100px] md:justify-between md:ml-0">
        <a href="#" class="relative w-[18px] h-[18px]">
            <img src="{{ asset('images/Vector.svg') }}" alt="Favorites Icon">
        </a>
        <a href="#" class="relative w-[18px] h-[18px]">
            <img src="{{ asset('images/Group.svg') }}" alt="Cart Icon">
        </a>
        <a href="#" class="relative w-[18px] h-[18px]">
            <img src="{{ asset('images/interface-alert-alarm-bell-2--alert-bell-ring-notification-alarm--Streamline-Core.svg') }}"
                alt="Notifications Icon">
        </a>
    </div>

    <div class="user-profile-section  shrink-0">
        @auth
            <div class="p-[24px]">
                <div class="dropdown" x-data="{ profile: false }">
                    <a class="btn p-0 border-0 bg-transparent" @click="profile = !profile" aria-expanded="false"
                        id="dropdownButton">
                        <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default_avatar.png') }}"
                            class="w-10 h-10 rounded-full object-cover" id="profileImage" style="cursor: pointer;">
                    </a>
                    <ul x-show="profile" @click.away="profile = false"
                        class="profile-menu shadow w-[296px] h-auto rounded-lg p-3 absolute right-0 mt-2 bg-white z-50"
                        style="min-width: 250px;">
                        <li class="flex items-center mb-2 border-b pb-3">
                            <img src="{{ Auth::user()->profile_picture ? asset('storage/' . Auth::user()->profile_picture) : asset('images/default_avatar.png') }}"
                                class="w-10 h-10 me-2 rounded-full object-cover">
                            <div>
                                <span class="text-base text-[#121212]">{{ Auth::user()->full_name }}</span><br>
                                <small class="text-sm text-gray-500">{{ Auth::user()->email }}</small>
                            </div>
                        </li>
                        <li class="pt-2"><a class="dropdown-item pb-4 block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded"
                                href="#">{{ __('messages.MyAccount') }}</a></li>
                        <li><a class="dropdown-item pb-4 block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded"
                                href="#">{{ __('messages.MyOrders') }}</a></li>
                        <li><a class="dropdown-item pb-4 block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded"
                                href="#">{{ __('messages.Fav') }}</a></li>
                        <li><a class="dropdown-item pb-4 block text-gray-700 hover:bg-gray-100 px-3 py-2 rounded"
                                href="#">{{ __('messages.settings_notifications') }}</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="dropdown-item pb-4 w-full text-left text-gray-700 hover:bg-gray-100 px-3 py-2 rounded">
                                    {{ __('messages.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @else
            @include('partials.login')
        @endauth
    </div>
</header>












{{-- category-bar --}}
<div
    class="categories bg-white w-full sm:flex sm:items-center sm:justify-between sm:px-[64px] pt-4 pb-3 space-y-3 sm:space-y-0  flex-col sm:flex-row  relative">

    <!-- Main Dropdown for "الجميع" -->
    <div class="relative  inline-block ml-6 cu w-full md:w-auto">
        <a id="mainDropdownButton"
            class="justify-between flex items-center space-x-2 px-4 py-2 cursor-pointer">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
            <span class="dropdown-text">الجميع</span>
        </a>


        <div class="relative text-left w-full max-w-xs mx-auto" x-data="{ openIndex: null }">
            <!-- Main Dropdown Menu -->
            <div id="mainDropdownMenu"
                class="origin-top-right mt-3 absolute w-[314px] rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden z-10"
                role="menu" aria-orientation="vertical" aria-labelledby="mainDropdownButton">

                <div class="py-1" role="none">
                    @if (isset($categories))
                        @foreach ($categories as $index => $category)
                            <div class="relative" @mouseenter="openIndex = {{ $index }}"
                                @mouseleave="openIndex = null">
                                <!-- Category Button -->
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

                                <!-- Side Dropdown for Products -->
                                <div x-show="openIndex === {{ $index }}" x-transition
                                    class="SideDropdown absolute top-0 right-full mr-1 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 w-[314px] z-20"
                                    role="menu" aria-orientation="vertical">
                                    <div class="py-2 px-3 space-y-2" role="none">
                                        @foreach (range(1, 3) as $i)
                                            <a href="#"
                                                class="flex items-center space-x-2 rtl:space-x-reverse px-2 py-1 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                                                <img src="https://placehold.co/30x30"
                                                    class="rounded-[12px] w-[56px] h-[56px] object-cover">
                                                <span>منتج {{ $i }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>



    </div>

    <div class="individualCategories flex flex-wrap gap-2 sm:gap-4 w-full justify-between">
        @foreach ($categories as $category)
            <a href="{{ route('products.filterByCategory', $category->slug) }}"
                class="category-button  {{ isset($selectedCategory) && $selectedCategory->slug === $category->slug ? 'active' : '' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>

</div>
