    <div class="search w-full md:flex-grow md:max-w-2xl mx-8 order-4">
        <form action="{{ route('search') }}" method="GET" class="main-search-form">
            <div class="flex border rounded-[12px] bg-[#F8F9FA] items-center py-1 px-2 relative">
                {{-- Category Dropdown (Unchanged from previous versions) --}}
                <div class="relative" x-data="{
                    categoryOpen: false,
                    selectedCategories: @json(request('search_categories', [])),
                    get buttonText() {
                        if (this.selectedCategories.length === 0 || this.selectedCategories.length === 2) {
                            return '{{ __('messages.all') }}';
                        } else if (this.selectedCategories.includes('products')) {
                            return '{{ __('messages.products') }}';
                        } else if (this.selectedCategories.includes('suppliers')) {
                            return '{{ __('messages.suppliers') }}';
                        }
                        return '{{ __('messages.all') }}';
                    }
                }">
                    <button type="button" @click="categoryOpen = !categoryOpen"
                        class="flex items-center px-1 h-full w-[100px] md:w-[163px] border-l text-[#767676] text-sm font-normal font-[Cairo] shrink-0">
                        <div class="flex items-center justify-between px-2 h-full w-full">
                            <span class="text-sm truncate" x-text="buttonText"></span>
                            <svg class="size-4 transform" :class="{ 'rotate-180': categoryOpen }" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                            </svg>
                        </div>
                    </button>
                    <div x-show="categoryOpen" @click.outside="categoryOpen = false" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-10 bg-white border mt-1 rounded-2xl shadow-md p-[10px] text-[#212121] text-base font-[Cairo] leading-[50px] w-full max-h-[100px] overflow-y-auto md:w-[163px]">
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
                        this.searchText = item;
                        this.showPopup = false;
                        this.addRecentSearch(item);
                        this.$el.closest('form').submit(); // Submit the form
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
                        <input type="text" x-model="searchText" 
                            @focus="showPopup = true; if (searchText.length === 0) fetchSuggestions(true); else fetchSuggestions();"
                            @input="showPopup = true; fetchSuggestions()" @click.stop
                            class="block w-full px-3 py-2 border-none focus:outline-none focus:ring-0 sm:text-sm"
                            placeholder="{{ __('messages.Search') }}">
                    </div>

                    {{-- Unified Popup for Suggestions and Recent Searches --}}
                    <div x-show="showPopup" @click.away="showPopup = false" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute bg-white border rounded shadow w-[310px] mt-1 z-20 max-h-64 overflow-auto -left-28 md:w-[660px] ">

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
                            <div class="px-3 py-3  uppercase border-b flex justify-between items-center">
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
                            <template x-for="item in recentSearches" :key="'recent-' + item">
                                <div class="flex justify-between items-center px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm mb-2"
                                    @click="selectSuggestion(item)">
                                    <div class="flex items-center space-x-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            strokeWidth={1.5} stroke="currentColor"
                                            class="size-6 text-[#212121] ml-2 bg-[#EDEDED] rounded-full p-1">
                                            <path strokeLinecap="round" strokeLinejoin="round"
                                                d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>

                                        <span x-text="item" class="text-[16px]"></span>
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
                            <div class="px-3 py-3 text-gray-700  text-xs uppercase border-b flex justify-between items-center"
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
                                    class="px-3 py-2  hover:bg-gray-100 cursor-pointer text-sm flex items-center space-x-2">
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
                        class="absolute top-full -left-20   mt-2 bg-white shadow-lg rounded-lg py-2 z-30
               sm:w-[660px] sm:h-[320px] w-[310px]">
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
                                    @change="handleImageUpload">
                            </div>
                            <div class="flex flex-col sm:flex-row mt-4 mb-4 items-center justify-between">
                                <input type="text" x-model="imageUrl" placeholder="{{ __('messages.imageURL') }}"
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
