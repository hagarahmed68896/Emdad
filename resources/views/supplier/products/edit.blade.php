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
            wholesalePrices: @json($product->price_tiers ?? []),
            newWholesaleItem: { from: '', to: '', price: '' },
            sizes: @json($product->sizes ?? []),
            newSize: '',
            selectedSubCategory: @json($product->subCategory),
            selectedCategory: @json($product->subCategory->category),
            openCategory: false,
            openSubCategory: false,
            confirmingId: null,
            // ✅ Alpine methods
            handleFiles(e) {
                const newFiles = Array.from(e.target.files);
                newFiles.forEach(file => {
                    this.files.push(file);
                    this.previews.push(URL.createObjectURL(file));
                });
            },
            handleFileDrop(e) {
                const newFiles = Array.from(e.dataTransfer.files);
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
                // Add the removed image path to a hidden input for the backend to handle
                this.removedImages.push(this.existingImages[index]);
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
 

                    // ✅ New method to re-populate state from server errors
            repopulateState(errors) {
                // Repopulate dynamic wholesale prices
                if (errors.wholesale_from && errors.wholesale_from.length > 0) {
                    this.wholesalePrices = errors.wholesale_from.map((msg, index) => {
                        return { 
                            from: old('wholesale_from.' + index), 
                            to: old('wholesale_to.' + index), 
                            price: old('wholesale_price.' + index) 
                        };
                    });
                } else if (old('wholesale_from')) {
                    // Handle case where old data exists but no specific error
                    this.wholesalePrices = old('wholesale_from').map((from, index) => {
                        return {
                            from: from,
                            to: old('wholesale_to')[index],
                            price: old('wholesale_price')[index]
                        };
                    });
                }

                if (old('sizes')) {
                    this.sizes = old('sizes');
                }

                if (old('colors')) {
                    this.colors = old('colors');
                }
            },
            
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
                :class="{'border-gray-300': existingImages.length === 0 && previews.length === 0, 'border-gray-300': existingImages.length > 0 || previews.length > 0}"
            >
                <template x-if="existingImages.length >= 0 && previews.length >= 0">
                    <div class="flex flex-col items-center">
                        <img src="{{ asset('images/Frame 3508.svg') }}" class="w-8 h-8 mb-2" alt="">
                        <p class="text-sm text-gray-500">{{ __('messages.drag_or_click') }}</p>
                    </div>
                </template>
                <input type="file" multiple x-ref="imageInput" class="hidden" @change="handleFiles" accept="image/*" name="images[]">
            </div>

            {{-- ✅ PREVIEW GRID --}}
            <div class="flex flex-wrap gap-2 mt-4" x-show="existingImages.length > 0 || previews.length > 0">
                <template x-for="(img, index) in existingImages" :key="'existing-' + index">
                    <div class="relative w-24 h-24 border rounded overflow-hidden">
                        <img :src="'{{ asset('storage') }}' + '/' + img" class="w-full h-full object-cover">
                        <input type="hidden" name="existing_images[]" :value="img">
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
            @error('images.*') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- ✅ الاسم ورقم الموديل --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.product_name') }}</label>
                <input type="text" name="name" placeholder="{{ __('messages.product_name_placeholder') }}" class="border p-2 w-full rounded-xl" value="{{ old('name', $product->name) }}">
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-bold">{{ __('messages.product_name_en') }}</label>
                <input type="text" name="name_en" placeholder="{{ __('messages.product_name_en_placeholder') }}" class="border p-2 w-full rounded-xl" value="{{ old('name_en', $product->name_en ) }}">
                @error('name_en') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

         
        </div>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
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
                <ul x-show="openCategory" x-cloak class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl max-h-60 overflow-y-auto">
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
                <ul x-show="openSubCategory" x-cloak class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl max-h-60 overflow-y-auto">
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
            @error('price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
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
                        <input type="hidden" :name="'wholesale_from[]'" :value="item.from">
                        <input type="hidden" :name="'wholesale_to[]'" :value="item.to">
                        <input type="hidden" :name="'wholesale_price[]'" :value="item.price">
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
<script>
    window.productColors = @json($productColorsJson);
</script>

<div x-data="{
    newColor: '',
    newColorImage: null,
colors: Array.isArray(window.productColors) ? window.productColors : [],
    addColor() {
        if (!this.newColor) return;

        if (this.newColorImage) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.colors.push({
                    name: this.newColor,
                    image: e.target.result
                });
                this.resetFields();
            };
            reader.readAsDataURL(this.newColorImage);
        } else {
            // No image provided
            this.colors.push({
                name: this.newColor,
                image: null
            });
            this.resetFields();
        }
    },
    resetFields() {
        this.newColor = '';
        this.newColorImage = null;
        if (this.$refs.colorImageInput) {
            this.$refs.colorImageInput.value = null;
        }
    },
    removeColor(index) {
        this.colors.splice(index, 1);
    }
}">

    <label class="block mb-1 font-bold">{{ __('messages.available_colors') }}</label>

    <div class="flex flex-col md:flex-row md:items-center gap-2 mb-4">
        <input type="text" x-model="newColor" placeholder="{{ __('messages.color_name') }}" class="border p-2 rounded-xl w-full">

        <input type="file" @change="e => newColorImage = e.target.files[0]" x-ref="colorImageInput" accept="image/*" class="border p-2 rounded-xl w-full">

        <button type="button" @click="addColor" class="bg-[#185D31] text-white px-4 py-2 rounded-xl h-full">
            {{ __('messages.add') }}
        </button>
    </div>

    <div class="flex flex-wrap gap-2" x-show="colors.length > 0">
        <template x-for="(color, index) in colors" :key="index">
            <div class="bg-gray-100 rounded-xl px-4 py-2 flex items-center gap-4">
                <input type="hidden" name="colors[]" :value="JSON.stringify(color)">
                <template x-if="color.image">
                    
                    <img :src="color.image" class="w-8 h-8 rounded-full object-cover border">
                </template>
                <span x-text="color.name" class="font-bold"></span>
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

        {{-- ✅ حالة المنتج (Product Status) --}}
<div class="mb-4" x-data="{ 
    open: false, 
    selectedStatus: '{{ old('product_status', $product->product_status ?? '') }}', 
    statusOptions: {
        'ready_for_delivery': '{{ __("messages.ready_for_delivery") }}',
        'made_to_order': '{{ __("messages.made_to_order") }}'
    },
    get selectedStatusText() {
        return this.statusOptions[this.selectedStatus] || '{{ __("messages.select_product_status") }}';
    }
}">

<label class="block mb-1 font-bold">{{ __('messages.product_status') }}</label>
    
    <div class="relative">
        <input type="hidden" name="product_status" x-model="selectedStatus">

        <button type="button" @click="open = !open" class="flex justify-between items-center w-full px-2 py-2 border rounded-xl bg-white text-right">
            <span x-text="selectedStatusText"></span>
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" :class="{ 'transform rotate-180': open }">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <ul x-show="open" @click.away="open = false" class="absolute z-10 w-full mt-1 bg-white rounded-xl shadow-lg border">
            <template x-for="(text, value) in statusOptions" :key="value">
                <li @click="selectedStatus = value; open = false" class="px-2 py-2 cursor-pointer hover:bg-gray-100 rounded-xl">
                    <span x-text="text"></span>
                </li>
            </template>
        </ul>
    </div>
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
<input type="date"
       value="{{ old('offer_start', ($product->offer ? \Carbon\Carbon::parse($product->offer->offer_start)->format('Y-m-d') : null)) }}"
       name="offer_start"
               class="border p-2 w-full rounded-xl" 
>
</div>
<div>
    <label class="block mb-1 font-bold">{{ __('messages.offer_end') }}</label>
    <input 
        type="date" 
        name="offer_end" 
        class="border p-2 w-full rounded-xl" 
        value="{{ old('offer_end', $product->offer ? \Carbon\Carbon::parse($product->offer->offer_end)->format('Y-m-d'):null) }}"
    >
</div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.discount_percent') }}</label>
                <input type="number" min="0" step="1" name="discount_percent" placeholder="{{ __('messages.discount_percent') }}"
                 class="border p-2 w-full rounded-xl" value="{{ old('discount_percent', $product->offer ? $product->offer->discount_percent:null ) }}">
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
        <div>
            <button type="button" @click="confirmingId = {{ $product->id }}"
                class="flex-1 flex items-center justify-center gap-1 text-center text-red-500 py-2 rounded-xl transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
                <span class="underline text-black"> {{__('messages.trash_product')}}</span>
            </button>
        </div>
        <div class="flex gap-3">
    <button 
    type="submit" 
    x-bind:disabled="loading" 
    x-on:click="loading = true; window.scrollTo({ top: 0, behavior: 'smooth' });" 
    class="bg-[#185D31] text-white px-6 py-3 rounded-xl"
>                 {{ __('messages.save') }}
            </button>

            <a href="{{ url()->previous() }}" class="bg-gray-300 text-black px-6 py-3 rounded-xl">
                {{ __('messages.Cancel') }}
            </a>
        </div>
        <input type="hidden" name="image" id="main_image_input">
    </form>
    <div x-show="confirmingId" x-cloak class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg max-w-md w-full">
            <h2 class="text-xl font-bold mb-4">{{ __('messages.confirm_delete') }}</h2>
            <p class="mb-4 text-gray-600">{{ __('messages.are_you_sure_delete') }}</p>
            <div class="flex justify-end space-x-2">
                <button @click="confirmingId = null" class="px-4 py-2 mx-2 bg-gray-300 rounded hover:bg-gray-400">
                    {{ __('messages.cancel') }}
                </button>
                <form :action="'/products/' + confirmingId" method="POST">
                    @csrf
                    @method('DELETE')
         <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                {{ __('messages.delete') }}
                            </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
   document.getElementById('edit-product-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const statusMessageDiv = document.getElementById('status-message');
    statusMessageDiv.classList.add('hidden');
    statusMessageDiv.classList.remove('bg-red-100', 'text-red-700', 'bg-green-100', 'text-green-700');

    const formData = new FormData(this);

    // ✅ Fix 1: Manually append the _method field for PATCH request
    formData.append('_method', 'PATCH');
    
    // Append new files from Alpine.js state
    const alpine = Alpine.$data(document.querySelector('[x-data]'));
    if (alpine.files && alpine.files.length > 0) {
        alpine.files.forEach(file => {
            formData.append('images[]', file);
        });
    }

    fetch(this.action, {
        method: 'POST', // Use POST for form submission
        body: formData
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) {
            // Handle validation errors or other server-side errors
            let errorMessages = '';
            if (data.errors) {
                for (const [key, messages] of Object.entries(data.errors)) {
                    errorMessages += messages[0] + '<br>';
                }
            } else {
                errorMessages = data.message || 'An error occurred during the update.';
            }

            const statusMessageSpan = statusMessageDiv.querySelector('span');
            statusMessageSpan.innerHTML = errorMessages;
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

            if (data.redirect) {
                window.location.href = data.redirect;
            }
        }
    })
    .catch(error => {
        console.error('An unexpected error occurred:', error);
        const statusMessageSpan = statusMessageDiv.querySelector('span');
        if (statusMessageSpan.textContent === '') {
             statusMessageSpan.textContent = '{{ __('messages.error_message') }}';
        }
        statusMessageDiv.classList.add('bg-red-100', 'text-red-700');
        statusMessageDiv.classList.remove('hidden');
    });
});
</script>

<script>
    window.subCategories = @json($categories->mapWithKeys(fn($cat) => [$cat->id => $cat->subCategories]));
</script>

@endsection