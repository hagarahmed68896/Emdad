<div class="relative w-full mx-2" x-cloak 
    x-data="{
        searchText: '{{ request('query') }}',
        showPopup: false,
        recentSearches: JSON.parse(localStorage.getItem('recentSearches') || '[]'),
        productSuggestions: [],
        loadingSuggestions: false,
        debounceTimeout: null,
        
        init() {
            // Save to recent searches when form is submitted
            this.$el.closest('form').addEventListener('submit', () => {
                this.addRecentSearch(this.searchText);
            });

            // Close popup on outside click
            document.addEventListener('click', (event) => {
                const searchBoxElement = this.$el;
                if (!searchBoxElement.contains(event.target)) {
                    this.showPopup = false;
                }
            });

            // Initial fetch
            this.$nextTick(() => {
                if (this.searchText.length === 0) {
                    this.fetchSuggestions(true);
                } else {
                    this.fetchSuggestions();
                }
            });
        },

        fetchSuggestions(isDefault = false) {
            if (this.debounceTimeout) clearTimeout(this.debounceTimeout);

            this.debounceTimeout = setTimeout(async () => {
                let queryParam = '';
                if (isDefault || this.searchText.length === 0) {
                    queryParam = ''; // default suggestions
                } else if (this.searchText.length >= 1) {
                    queryParam = `?query=${encodeURIComponent(this.searchText)}`;
                } else {
                    this.productSuggestions = [];
                    return;
                }

                this.loadingSuggestions = true;
                this.productSuggestions = [];

                try {
                    const response = await fetch(`/products/suggestions${queryParam}`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    const data = await response.json();
                    this.productSuggestions = data;
                } catch (error) {
                    console.error('Error fetching product suggestions:', error);
                    this.productSuggestions = [];
                } finally {
                    this.loadingSuggestions = false;
                }
            }, 300);
        },

        selectSuggestion(item) {
            // Handle both plain strings and objects
            const value = (item && typeof item === 'object') ? item.name : item;

            this.searchText = value;
            this.showPopup = false;
            this.addRecentSearch(value);

            if (this.$refs && this.$refs.queryInput) {
                this.$refs.queryInput.value = value;
            }

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
            if (this.recentSearches.length === 0 && this.searchText === '') {
                this.showPopup = false;
            }
        },

        clearAllRecentSearches() {
            this.recentSearches = [];
            localStorage.removeItem('recentSearches');
            if (this.productSuggestions.length === 0 && this.searchText === '') {
                this.showPopup = false;
            }
        },

        refreshSuggestions() {
            this.fetchSuggestions(true);
        }
    }"
>
    <div class="flex items-center border-[1px] bg-white rounded-[12px] overflow-hidden">
        <img src="{{ asset('images/interface-search--glass-search-magnifying--Streamline-Core.svg') }}"
            alt="Search Icon"
            class="h-[16px] w-[16px] object-cover text-[#767676] ml-2 md:mr-6 md:hidden">
        <input type="text" name="query" x-model="searchText" x-ref="queryInput"
            @focus="showPopup = true; if (searchText.length === 0) fetchSuggestions(true); else fetchSuggestions();"
            @input="showPopup = true; fetchSuggestions()" @click.stop
            class="block w-full px-3 py-2 border-none focus:outline-none focus:ring-0 sm:text-sm"
            placeholder="{{ __('messages.Search') }}">
    </div>

    {{-- Popup --}}
    <div x-show="showPopup" @click.away="showPopup = false" x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        class="absolute bg-white border rounded shadow w-[310px] mt-1 z-20 max-h-64 overflow-auto -left-28 md:w-[660px] ">

        {{-- Loader --}}
        <div x-show="loadingSuggestions" x-clock class="px-3 py-2 text-gray-500 text-sm flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                    stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 
                    5.291A7.962 7.962 0 014 12H0c0 
                    3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            Loading suggestions...
        </div>

        {{-- Recent Searches --}}
        <div x-show="recentSearches.length > 0 && searchText.length === 0" x-cloak>
            <div class="px-3 py-3 uppercase border-b flex justify-between items-center">
                <span class="font-bold text-[#121212] text-[20px]">{{ __('messages.recent_Searches') }}</span>
                <button type="button" @click="clearAllRecentSearches()"
                    class="flex justify-between text-gray-400 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="#696969" class="size-5 ml-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9.26 
                               9m9.968-3.21c.342.052.682.107 
                               1.022.166m-1.022-.165L18.16 
                               19.673a2.25 2.25 0 0 1-2.244 
                               2.077H8.924a2.25 2.25 0 0 
                               1-2.244-2.077L4.772 
                               5.79m14.456 0a48.108 48.108 
                               0 0 0-3.478-.397m-12 
                               .562c.34-.059.68-.114 
                               1.022-.165m0 0a48.11 
                               48.11 0 0 1 3.478-.397m7.5 
                               0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 
                               51.964 0 0 0-3.32 0c-1.18.037-2.09 
                               1.022-2.09 2.201v.916m7.5 
                               0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    <span class="text-[#696969] text-[12px]">{{ __('messages.delete') }}</span>
                </button>
            </div>
            <template x-for="item in recentSearches" :key="'recent-' + item">
                <div class="flex justify-between items-center px-3 py-1 hover:bg-gray-100 cursor-pointer text-sm mb-2"
                    @click="selectSuggestion(item)">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor"
                            class="size-6 text-[#212121] ml-2 bg-[#EDEDED] rounded-full p-1">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 
                                   1 1-18 0 9 9 0 0 1 18 0Z" />
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

        {{-- Suggestions --}}
        <div x-cloak
            x-show="productSuggestions.length > 0 || searchText.length === 0 || loadingSuggestions">
            <div class="px-3 py-3 text-gray-700 text-xs uppercase border-b flex justify-between items-center"
                :class="{ 'mt-2': recentSearches.length > 0 && searchText.length === 0 }">
                <span class="text-[20px] sm:text-[14px] text-[#212121] font-bold">{{ __('messages.recommend') }}</span>
                <button type="button" @click="refreshSuggestions()"
                    class="flex justify-between text-gray-400 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="#696969" class="size-5 ml-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.023 9.348h4.992v-.001M2.985 
                               19.644v-4.992m0 0h4.992m-4.993 
                               0 3.181 3.183a8.25 8.25 0 
                               0 0 13.803-3.7M4.031 
                               9.865a8.25 8.25 0 0 1 
                               13.803-3.7l3.181 
                               3.182m0-4.991v4.99" />
                    </svg>
                    <span class="text-[#696969] text-[12px]">{{ __('messages.refresh') }}</span>
                </button>
            </div>
            <template x-for="item in productSuggestions" :key="'suggest-' + (item.id || item)">
                <div @click="selectSuggestion(item)"
                    class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm flex items-center space-x-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor"
                        class="size-6 text-[#212121] ml-2 bg-[#EDEDED] rounded-full p-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 
                               0A7.5 7.5 0 1 0 
                               5.196 5.196a7.5 7.5 
                               0 0 0 10.607 10.607Z" />
                    </svg>
                    <span x-text="(item && typeof item === 'object') ? item.name : item" class="text-[16px]"></span>
                </div>
            </template>
            <div x-cloak x-show="!loadingSuggestions && productSuggestions.length === 0"
                class="px-3 py-2 text-gray-500 text-sm">
                No recommended products at the moment.
            </div>
        </div>

        {{-- No results --}}
        <div x-cloak x-show="searchText.length > 0 && !loadingSuggestions && productSuggestions.length === 0">
            <div class="px-3 py-2 text-gray-500 text-sm">No relevant search results found.</div>
        </div>
    </div>
</div>
