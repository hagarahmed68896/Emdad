<div class="search w-full md:flex-grow md:max-w-2xl px-2 md:mx-8 order-4">
    <form x-data="searchForm()" action="{{ route('search') }}" method="POST" enctype="multipart/form-data" class="main-search-form" @submit="setMode">
        @csrf

        <!-- Hidden inputs -->
        <input type="hidden" name="mode" x-model="mode">
        <input type="hidden" name="image_url" x-model="imageUrl">
        <input type="file" name="image_file" x-ref="imageFile" class="hidden" @change="handleFileUpload">

        <div class="flex flex-nowrap border rounded-[12px] bg-[#F8F9FA] items-center py-1 px-2 relative gap-2">

            {{-- Main Search Input --}}
            <div class="flex-1 min-w-0 relative z-40">
                @include('search.suggestions_popup')
            </div>

            {{-- Image Upload Component --}}
            <div class="relative flex items-center justify-center mx-2 shrink-0 z-50">
                <label @click="showUploadModal = true" class="cursor-pointer hover:text-black text-[#767676]">
                    <img src="{{ asset('images/Group (3).svg') }}" alt="Upload Image" class="w-[20px] h-[20px]">
                </label>

                <!-- Modal -->
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
                           <input type="file" id="imageInput" name="image_file" accept="image/*" class="hidden"
                                x-ref="imageFile" @change="handleFileUpload">
                        </div>

                        <!-- OR URL Input -->
                        <div class="flex flex-col sm:flex-row mt-4 mb-4 items-center justify-between">
                            <input type="text" x-model="imageUrl" placeholder="{{ __('messages.imageURL') }}"
                                class="border border-gray-300 px-3 py-2 rounded w-full sm:w-[400px] text-sm mb-2 sm:mb-0" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Button -->
            <div class="shrink-0">
                <button type="submit"
                    class="bg-[#185D31] px-3 md:px-4 h-[32px] text-white rounded-[12px] pb-1 mx-1 text-sm">
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
