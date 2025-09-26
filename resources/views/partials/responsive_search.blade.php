<div class="w-full md:flex-grow md:max-w-2xl px-2 md:mx-8 order-4">
<form action="{{ route('search') }}" method="POST" class="main-search-form" enctype="multipart/form-data">
    @csrf 
            <div class="flex flex-nowrap border rounded-[12px] bg-[#F8F9FA] items-center py-1 px-2 relative gap-2">

            {{-- Category Dropdown (UNCHANGED logic) --}}
            {{-- <div class="relative z-50" x-data="{
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
                    class="flex items-center px-1 h-full w-[86px] sm:w-[100px] md:w-[163px] border-l text-[#767676] text-sm font-normal font-[Cairo] shrink-0">
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
                    class="absolute left-0 top-full bg-white border mt-1 rounded-2xl shadow-md p-[10px] text-[#212121] text-base font-[Cairo] leading-[50px] w-full max-h-[100px] overflow-y-auto md:w-[163px]">
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
            </div> --}}
            {{-- Main Search Input and Suggestions Popup --}}
            <div class="flex-1 min-w-0 relative z-40">
                @include('search.suggestions_popup')
            </div>

            {{-- Image Upload Component (UNCHANGED logic) --}}
            <div x-data="imageUploadComponent()" class="relative flex items-center justify-center mx-2">
                <label @click="showUploadModal = true" class="cursor-pointer hover:text-black text-[#767676]">
                    <img src="{{ asset('images/Group (3).svg') }}" alt="Upload Image" class="w-[20px] h-[20px]">
                </label>

                <div x-show="showUploadModal" x-cloak @click.away="showUploadModal = false"
                    class="absolute top-full -left-20 mt-2 bg-white shadow-lg rounded-lg py-2
                             sm:w-[660px] sm:h-[320px] w-[90vw] max-w-[660px]">
                    <div class="p-6 relative w-full h-full">
                        <button @click="showUploadModal = false"
                            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Upload area -->
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

                        <!-- OR URL Input -->
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

            <!-- Unified Search Button -->
            <div class="shrink-0">
                <button type="submit"
                    class="bg-[#185D31] px-3 md:px-4 h-[32px] text-white rounded-[12px] pb-1 mx-1 text-sm">
                    {{ __('messages.Search') }}
                </button>
            </div>
        </div>
    </form>
</div>