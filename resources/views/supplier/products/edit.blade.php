@extends('layouts.app')

@section('page_title', __('messages.edit_product'))

@section('content')
<script>
    function editProductComponent() {
        return {
            // ✅ Alpine component state
            previews: [],
            files: [],
            existingImages: @json($product->images ?? []),
            removedImages: [],
            wholesalePrices: @json($product->wholesale_prices ?? []),
            newWholesaleItem: { from: '', to: '', price: '' },
            sizes: @json($product->sizes ?? []),
            newSize: '',
            colors: @json($product->colors ?? []),
            newColor: '',
            selectedSubCategory: @json($product->subCategory),
            selectedCategory: @json($product->subCategory->category),


            // ✅ Alpine methods
            handleFiles(e) {
                const newFiles = Array.from(e.target.files);
                newFiles.forEach(file => {
                    this.files.push(file);
                    this.previews.push(URL.createObjectURL(file));
                });
            },
            removeNewImage(index) {
                this.previews.splice(index, 1);
                this.files.splice(index, 1);
            },
            removeExistingImage(index) {
                this.existingImages.splice(index, 1);
            },
            addWholesale() {
                if (this.newWholesaleItem.from && this.newWholesaleItem.to && this.newWholesaleItem.price) {
                    this.wholesalePrices.push({ ...this.newWholesaleItem });
                    this.newWholesaleItem = { from: '', to: '', price: '' };
                }
            },
            removeWholesale(index) {
                this.wholesalePrices.splice(index, 1);
            },
            addSize() {
                if (this.newSize.trim() !== '') {
                    this.sizes.push(this.newSize);
                    this.newSize = '';
                }
            },
            removeSize(index) {
                this.sizes.splice(index, 1);
            },
            addColor() {
                if (this.newColor.trim() !== '') {
                    this.colors.push(this.newColor);
                    this.newColor = '';
                }
            },
            removeColor(index) {
                this.colors.splice(index, 1);
            }
        };
    }
</script>
<div x-data="editProductComponent()" class="mx-[64px] mb-4 bg-white rounded-xl mt-4">

    <h1 class="text-3xl font-bold mb-6">{{ __('messages.edit_product') }}</h1>

    {{-- ✅ Success/Error Message Container --}}
    <div id="status-message" class="hidden p-4 mb-4 text-sm rounded-lg" role="alert">
        <span class="font-medium"></span>
    </div>

    <form id="edit-product-form" method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PATCH')
        <p class="font-bold text-[24px]">{{ __('messages.product_details') }}</p>

        {{-- ✅ MULTIPLE IMAGES UPLOAD --}}
        <div class="space-y-2">
            <label class="block mb-2 font-bold">{{ __('messages.product_images') }}</label>
            <p class="text-sm text-gray-500 mb-2">PNG/JPG</p>

            {{-- Drop Zone --}}
            <div
                @click="$refs.imageInput.click()"
                @dragover.prevent
                @drop.prevent="handleFileDrop($event)"
                class="w-full h-40 border-2 border-dashed rounded-xl cursor-pointer flex items-center justify-center bg-white"
                :class="{'border-gray-300': existingImages.length === 0 && previews.length === 0, 'border-[#185D31]': existingImages.length > 0 || previews.length > 0}"
            >
                <template x-if="existingImages.length === 0 && previews.length === 0">
                    <div class="flex flex-col items-center">
                        <img src="{{ asset('images/Frame 3508.svg') }}" class="w-8 h-8 mb-2" alt="">
                        <p class="text-sm text-gray-500">{{ __('messages.drag_or_click') }}</p>
                    </div>
                </template>
                <input type="file" multiple x-ref="imageInput" class="hidden" @change="handleFiles" accept="image/*">
            </div>

            {{-- ✅ PREVIEW GRID --}}
            <div class="flex flex-wrap gap-2 mt-4" x-show="existingImages.length > 0 || previews.length > 0">
                <template x-for="(img, index) in existingImages" :key="'existing-' + index">
                    <div class="relative w-24 h-24 border rounded overflow-hidden">
                        <img :src="'{{ asset('storage') }}' + '/' + img" class="w-full h-full object-cover">
                        <input type="hidden" name="existing_images[]" :value="img.id">
                        <button type="button" @click="removeExistingImage(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 leading-none w-5 h-5 flex items-center justify-center">x</button>
                    </div>
                </template>
                <template x-for="(img, index) in previews" :key="'new-' + index">
                    <div class="relative w-24 h-24 border rounded overflow-hidden">
                        <img :src="img" class="w-full h-full object-cover">
                        <button type="button" @click="removeNewImage(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 leading-none w-5 h-5 flex items-center justify-center">x</button>
                    </div>
                </template>
            </div>

            @error('images') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- ✅ الاسم ورقم الموديل --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.product_name') }}</label>
                <input type="text" name="name" placeholder="{{ __('messages.product_name_placeholder') }}" class="border p-2 w-full rounded-xl" value="{{ old('name', $product->name) }}">
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-bold">{{ __('messages.model_number') }}</label>
                <input type="text" name="model_number" placeholder="{{ __('messages.model_number_placeholder') }}" class="border p-2 w-full rounded-xl" value="{{ old('model_number', $product->model_number) }}">
                @error('model_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- ✅ الفئة --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4"
             @click.away="openCategory = false; openSubCategory = false"
        >
            <div class="relative">
                <label class="block mb-1 font-bold">{{ __('messages.select_category') }}</label>
                <div @click="openCategory = !openCategory" class="border p-2 w-full rounded-xl cursor-pointer flex justify-between items-center bg-white">
                    <span x-text="selectedCategory ? selectedCategory.name : '{{ __('messages.select_category') }}'"></span>
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                <ul x-show="openCategory" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl max-h-60 overflow-y-auto">
                    @foreach ($categories as $category)
                        <li @click="selectedCategory = {id: {{ $category->id }}, name: '{{ $category->name }}'}; selectedSubCategory = null; openCategory = false;"
                            class="p-2 cursor-pointer hover:bg-gray-100 flex items-center">
                            <img src="{{ asset('storage/' . $category->iconUrl) }}" class="w-10 h-10 mx-2 rounded-xl p-1 bg-gray-100 object-cover" />
                            <span>{{ $category->name }}</span>
                        </li>
                    @endforeach
                </ul>
                <input type="hidden" name="category_id" x-model="selectedCategory ? selectedCategory.id : ''">
            </div>

            <div class="relative">
                <label class="block mb-1 font-bold">{{ __('messages.select_subcategory') }}</label>
                <div @click="if (selectedCategory) openSubCategory = !openSubCategory"
                     :class="{'opacity-50 cursor-not-allowed': !selectedCategory}"
                     class="border p-2 w-full rounded-xl cursor-pointer flex justify-between items-center bg-white">
                    <span x-text="selectedSubCategory ? selectedSubCategory.name : '{{ __('messages.select_subcategory') }}'"></span>
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
                <ul x-show="openSubCategory" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl max-h-60 overflow-y-auto">
                    <template x-if="selectedCategory">
                        <template x-for="sub in window.subCategories[selectedCategory.id]" :key="sub.id">
                            <li @click="selectedSubCategory = {id: sub.id, name: sub.name}; openSubCategory = false"
                                class="p-2 cursor-pointer hover:bg-gray-100 flex items-center">
                                <img :src="'{{ asset('storage/') }}' + '/' + sub.iconUrl" class="w-10 h-10 mx-2 rounded-xl p-1 bg-gray-100 object-cover" />
                                <span x-text="sub.name"></span>
                            </li>
                        </template>
                    </template>
                </ul>
                <input type="hidden" name="sub_category_id" x-model="selectedSubCategory ? selectedSubCategory.id : ''">
            </div>
        </div>

        <div>
            <label class="block mb-1 font-bold">{{ __('messages.base_price') }}</label>
            <div class="flex">
                <input type="number" min="0" name="price" placeholder="{{ __('messages.base_price_placeholder') }}" class="border h-full p-2 w-full rounded-r-xl" value="{{ old('price', $product->price) }}">
                <img class="inline-flex items-center h-full p-2 border border-l-0 rounded-l-xl bg-gray-100" src="{{asset('/images/Saudi_Riyal_Symbol.svg')}}" alt="">
            </div>
        </div>

        {{-- ✅ التسعير بالجملة --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.wholesale_pricing') }}</label>
            <div class="flex flex-col md:flex-row md:items-center gap-2 mb-4">

                <div class="flex gap-1 w-full md:w-3/4">
                    <input type="number" min="0" x-model="newWholesaleItem.from" placeholder="{{ __('messages.from_quantity') }}" class="border p-2 w-full rounded-xl">
                    <input type="number" min="0" x-model="newWholesaleItem.to" placeholder="{{ __('messages.to_quantity') }}" class="border p-2 w-full rounded-xl">
                </div>

                <div class="flex flex-col md:flex-row gap-1 w-full md:w-1/4">
                    <div class="relative flex-grow flex items-center">
                        <input type="number" min="0" x-model="newWholesaleItem.price" placeholder="{{ __('messages.price') }}" class="border p-2 w-full rounded-xl pr-12">
                        <img class="absolute left-0 h-full p-2 text-gray-400 border rounded-l-xl bg-gray-100 pointer-events-none" src="{{ asset('/images/Saudi_Riyal_Symbol.svg') }}" alt="">
                    </div>
                    <button type="button" @click="addWholesale" class="bg-[#185D31] text-white px-4 py-2 rounded-xl h-full w-full md:w-auto">{{ __('messages.add') }}</button>
                </div>

            </div>


            <div class="bg-gray-100 p-4 rounded-xl" x-show="wholesalePrices.length > 0">
                <div class="grid grid-cols-3 font-bold text-sm text-gray-500 mb-2">
                    <span>{{ __('messages.quantity') }}</span>
                    <span class="col-span-2">{{ __('messages.price') }}</span>
                </div>
                <template x-for="(item, index) in wholesalePrices" :key="index">
                    <div class="grid grid-cols-3 gap-2 items-center mb-2">
                        <input type="hidden" :name="'wholesale_prices[' + index + '][from]'" :value="item.from">
                        <input type="hidden" :name="'wholesale_prices[' + index + '][to]'" :value="item.to">
                        <input type="hidden" :name="'wholesale_prices[' + index + '][price]'" :value="item.price">
                        <span x-text="item.from + ' - ' + item.to + ' ' + '{{ __('messages.pieces') }}'"></span>
                        <span class="font-bold flex items-center col-span-2">
                            <span x-text="item.price"></span>
                            <img class="w-5 h-5 mx-1" src="{{asset('/images/Saudi_Riyal_Symbol.svg')}}" alt="">
                            <button type="button" @click="removeWholesale(index)" class="text-red-500 hover:text-red-700 ml-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    </div>
                </template>
            </div>
        </div>

        {{-- ✅ الأحجام --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.available_sizes') }}</label>
            <div class="flex items-center gap-2 mb-4">
                <input type="text" x-model="newSize" placeholder="{{ __('messages.available_sizes') }}" class="border p-2 w-full rounded-xl">
                <button type="button" @click="addSize" class="bg-[#185D31] text-white px-4 py-2 rounded-xl h-full">{{ __('messages.add') }}</button>
            </div>
            <div class="flex flex-wrap gap-2" x-show="sizes.length > 0">
                <template x-for="(size, index) in sizes" :key="index">
                    <div class="bg-gray-100 rounded-full px-4 py-1 flex items-center gap-2">
                        <input type="hidden" name="sizes[]" :value="size">
                        <span x-text="size"></span>
                        <button type="button" @click="removeSize(index)" class="text-red-500 text-sm font-bold">x</button>
                    </div>
                </template>
            </div>
        </div>

        {{-- ✅ الألوان --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.available_colors') }}</label>
            <div class="flex items-center gap-2 mb-4">
                <input type="text" x-model="newColor" placeholder="{{ __('messages.available_colors') }}" class="border p-2 w-full rounded-xl">
                <button type="button" @click="addColor" class="bg-[#185D31] text-white px-4 py-2 rounded-xl h-full">{{ __('messages.add') }}</button>
            </div>
            <div class="flex flex-wrap gap-2" x-show="colors.length > 0">
                <template x-for="(color, index) in colors" :key="index">
                    <div class="bg-gray-100 rounded-full px-4 py-1 flex items-center gap-2">
                        <input type="hidden" name="colors[]" :value="color">
                        <span x-text="color"></span>
                        <button type="button" @click="removeColor(index)" class="text-red-500 text-sm font-bold">x</button>
                    </div>
                </template>
            </div>
        </div>
        
        {{-- ✅ باقي الحقول --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.material_type') }}</label>
                <input type="text" name="material_type" placeholder="{{ __('messages.material_type') }}" class="border p-2 w-full rounded-xl" value="{{ old('material_type', $product->material_type) }}">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.available_quantity') }}</label>
                <input type="number" min="0" name="available_quantity" placeholder="{{ __('messages.available_quantity') }}" class="border p-2 w-full rounded-xl" value="{{ old('available_quantity', $product->available_quantity) }}">
            </div>
        </div>

        <div>
            <label class="block mb-1 font-bold">{{ __('messages.min_order_quantity') }}</label>
            <input type="number" min="0" name="min_order_quantity" placeholder="{{ __('messages.min_order_quantity') }}" class="border p-2 w-full rounded-xl" value="{{ old('min_order_quantity', $product->min_order_quantity) }}">
        </div>

        <div>
            <label class="block mb-1 font-bold">{{ __('messages.description') }}</label>
            <textarea name="description" placeholder="{{ __('messages.description') }}" rows="4" class="border p-2 w-full rounded-xl">{{ old('description', $product->description) }}</textarea>
        </div>

        <p class="font-bold text-[24px]">{{ __('messages.add_offers') }}</p>

        {{-- ✅ إضافة العروض --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.offer_start') }}</label>
                <input type="date" name="offer_start" class="border p-2 w-full rounded-xl" value="{{ old('offer_start', $product->offer_start) }}">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.offer_end') }}</label>
                <input type="date" name="offer_end" class="border p-2 w-full rounded-xl" value="{{ old('offer_end', $product->offer_end) }}">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.discount_percent') }}</label>
                <input type="number" min="0" name="discount_percent" placeholder="{{ __('messages.discount_percent') }}" class="border p-2 w-full rounded-xl" value="{{ old('discount_percent', $product->discount_percent) }}">
            </div>
        </div>

        <p class="font-bold text-[24px]">{{ __('messages.manufacturing_delivery_time') }}</p>

        {{-- ✅ مدة التصنيع والشحن --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.preparation_days') }}</label>
                <input type="number" min="0" name="preparation_days" placeholder="{{ __('messages.preparation_days') }}" class="border p-2 w-full rounded-xl" value="{{ old('preparation_days', $product->preparation_days) }}">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.shipping_days') }}</label>
                <input type="number" min="0" name="shipping_days" placeholder="{{ __('messages.shipping_days') }}" class="border p-2 w-full rounded-xl" value="{{ old('shipping_days', $product->shipping_days) }}">
            </div>
        </div>

        {{-- ✅ القدرة الإنتاجية --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.production_capacity') }}</label>
            <input type="text" name="production_capacity" placeholder="{{ __('messages.production_capacity') }}" class="border p-2 w-full rounded-xl" value="{{ old('production_capacity', $product->production_capacity) }}">
        </div>

        {{-- ✅ تفاصيل الشحن --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.product_weight') }}</label>
                <div class="flex">
                    <input
                        type="number"
                        min="0"
                        step="0.01"
                        name="product_weight"
                        placeholder="{{ __('messages.product_weight') }}"
                        class="border p-2 w-full rounded-r-xl"
                        value="{{ old('product_weight', $product->product_weight) }}"
                    >
                    <span class="inline-flex items-center px-3 border border-l-0 rounded-l-xl bg-gray-100">{{ __('messages.kg') }}</span>
                </div>
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.package_dimensions') }}</label>
                <input type="text" name="package_dimensions" placeholder="{{ __('messages.package_dimensions') }}" class="border p-2 w-full rounded-xl" value="{{ old('package_dimensions', $product->package_dimensions) }}">
            </div>
        </div>

        {{-- ✅ مرفقات --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.attachments') }}</label>
            <input type="file" name="attachments" accept=".pdf,image/*" class="border p-2 w-full rounded-xl">
            @if($product->attachment_url)
                <p class="text-sm text-gray-500 mt-1">
                    {{ __('messages.current_attachment') }}: <a href="{{ asset('storage/' . $product->attachment_url) }}" target="_blank" class="text-blue-600 underline">{{ basename($product->attachment_url) }}</a>
                </p>
            @endif
            <p class="text-sm text-gray-500 mt-1">{{ __('messages.attachments_note') }}</p>
        </div>

        <button type="submit" class="bg-[#185D31] text-white px-6 py-3 rounded-xl">{{ __('messages.save_changes') }}</button>
    </form>
</div>

<script>
    document.getElementById('edit-product-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const statusMessageDiv = document.getElementById('status-message');
        statusMessageDiv.classList.add('hidden');
        statusMessageDiv.classList.remove('bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');

        const formData = new FormData(this);

        // Append new images from Alpine.js state
        const alpine = Alpine.$data(document.querySelector('[x-data]'));
        if (alpine.files && alpine.files.length > 0) {
            alpine.files.forEach(file => {
                formData.append('images[]', file);
            });
        }
        
        // Append IDs of existing images to keep
        const existingImageIds = alpine.existingImages.map(img => img.id);
        existingImageIds.forEach(id => {
            formData.append('existing_images[]', id);
        });

        // Add CSRF token
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('_token', csrfToken);

        fetch("{{ route('products.update', $product) }}", {
            method: 'POST', // Use POST with _method('PATCH')
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken // Ensure CSRF token is sent with headers
            },
            body: formData
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                // Handle validation errors or other server-side errors
                for (const [key, messages] of Object.entries(data.errors)) {
                    // This part will need to be more robust to handle nested fields like wholesale_prices
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        let errorContainer = input.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.className = 'text-red-600 text-sm mt-1';
                            input.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = messages[0];
                    }
                }
                const statusMessageSpan = statusMessageDiv.querySelector('span');
                statusMessageSpan.textContent = data.message || 'An error occurred during update.';
                statusMessageDiv.classList.add('bg-red-100', 'text-red-700');
                statusMessageDiv.classList.remove('hidden');
                throw new Error('Request failed');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                const statusMessageSpan = statusMessageDiv.querySelector('span');
                statusMessageSpan.textContent = data.success;
                statusMessageDiv.classList.add('bg-green-100', 'text-green-700');
                statusMessageDiv.classList.remove('hidden');

                setTimeout(() => {
                    statusMessageDiv.classList.add('hidden');
                }, 5000);

                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            }
        })
        .catch(error => {
            console.error('An unexpected error occurred:', error);
            const statusMessageSpan = statusMessageDiv.querySelector('span');
            statusMessageSpan.textContent = '{{ __('message.error_message') }}';
            statusMessageDiv.classList.add('bg-red-100', 'text-red-700');
            statusMessageDiv.classList.remove('hidden');
        });
    });
</script>

<script>
    window.subCategories = @json($categories->mapWithKeys(fn($cat) => [$cat->id => $cat->subCategories]));
</script>

@endsection