@extends('layouts.app')

@section('page_title', __('messages.add_new_product'))

@section('content')
<div class="mx-[64px] mb-4 bg-white rounded-xl mt-4"
     x-data="{
         previews: [],
         preview: '', 
         selectedCategoryId: null,
         selectedSubcategoryId: null,
         newWholesaleItem: { from: '', to: '', price: '' },
         wholesalePrices: [],
         newSize: '',
         sizes: [],
         handleFiles(e) {
             const files = e.target.files;
             this.previews = [];
             for (let i = 0; i < files.length; i++) {
                 this.previews.push(URL.createObjectURL(files[i]));
             }
         },
         handleFileDrop(e) {
             const files = e.dataTransfer.files;
             this.$refs.imageInput.files = files;
             this.handleFiles({ target: { files } });
         },
         addWholesale() {
             if (this.newWholesaleItem.from && this.newWholesaleItem.to && this.newWholesaleItem.price) {
                 this.wholesalePrices.push({...this.newWholesaleItem});
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

     }">


    <h1 class="text-3xl font-bold mb-6">{{ __('messages.add_new_product') }}</h1>

    {{-- ✅ Success Message Container --}}
    <div id="success-message" class="hidden p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
        <span class="font-medium"></span>
    </div>

    <form id="create-product-form" enctype="multipart/form-data"
        @submit.prevent="syncFilesToInput(); $el.submit()"  {{-- <== this is the key line --}}
 class="space-y-6">
        @csrf
        <p class="font-bold text-[24px]">{{ __('messages.product_details') }}</p>

        {{-- ✅ صورة المنتج --}}
<div
    x-data="{
        previews: [],
        files: [],
        handleFileDrop(e) {
            const droppedFiles = Array.from(e.dataTransfer.files);
            this.addFiles(droppedFiles);
        },
        handleFiles(e) {
            const selectedFiles = Array.from(e.target.files);
            this.addFiles(selectedFiles);
        },
        addFiles(newFiles) {
            newFiles.forEach(file => {
                this.files.push(file);
                this.previews.push(URL.createObjectURL(file));
            });
        },
        removeImage(index) {
            URL.revokeObjectURL(this.previews[index]);
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
        },
            syncFilesToInput() {
        const dataTransfer = new DataTransfer();
        this.files.forEach(file => dataTransfer.items.add(file));
        this.$refs.imageInput.files = dataTransfer.files;
    }
    }"
    class="space-y-2"
>
    <label class="block mb-2 font-bold">{{ __('messages.product_images') }}</label>
    <p class="text-sm text-gray-500 mb-2">PNG, JPG</p>

    {{-- ✅ Dropzone --}}
    <div
        @click="$refs.imageInput.click()"
        @dragover.prevent
        @drop.prevent="handleFileDrop($event)"
        class="w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer flex items-center justify-center bg-white"
    >
        <template x-if="previews.length >= 0">
            <div class="flex flex-col items-center">
                <img src="{{ asset('images/Frame 3508.svg') }}" alt="" class="w-8 h-8 mb-2">
                <p class="text-sm text-gray-500">{{ __('messages.drag_or_click') }}</p>
            </div>
        </template>
        <input 
            type="file" 
            multiple 
            name="images[]"
            x-ref="imageInput" 
            class="hidden" 
            @change="handleFiles" 
            accept="image/*"
        >
    </div>

    {{-- ✅ Preview Grid --}}
    <div class="flex flex-wrap gap-2 mt-4" x-show="previews.length > 0">
        <template x-for="(img, index) in previews" :key="index">
            <div class="relative w-24 h-24 border rounded overflow-hidden group">
                <img :src="img" alt="" class="w-full h-full object-cover">
<button
    @click.stop="removeImage(index)"
    type="button"
class="absolute top-1 left-1 bg-red-500 text-white rounded-full p-1 leading-none w-5 h-5 flex items-center justify-center"    title="{{ __('messages.remove') }}"
>✕</button>


            </div>
        </template>
    </div>

    @error('images') 
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
    @enderror
</div>





        {{-- ✅ الاسم ورقم الموديل --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.product_name') }}</label>
                <input type="text" name="name" placeholder="{{ __('messages.product_name_placeholder') }}" class="border p-2 w-full rounded-xl" value="{{ old('name') }}">
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block mb-1 font-bold">{{ __('messages.model_number') }}</label>
                <input type="text" name="model_number" placeholder="{{ __('messages.model_number_placeholder') }}" class="border p-2 w-full rounded-xl" value="{{ old('model_number') }}">
                @error('model_number') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

{{-- ✅ الفئة --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4"
    x-data="{ 
        openCategory: false, 
        openSubCategory: false, 
        selectedCategory: null, 
        selectedSubCategory: null 
    }" 
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
    <img src="{{ asset('storage/' . $category->iconUrl) }}"
                                class="w-10 h-10 mx-2 rounded-xl p-1 bg-gray-100 object-cover" />
                                            <span>{{ $category->name }}</span>
        </li>
    @endforeach
</ul>
        <input type="hidden" name="category_id" x-model="selectedCategory.id">
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
            <template x-for="sub in window.subCategories[selectedCategory.id]" :key="sub.id">
           <li @click="selectedSubCategory = {id: sub.id, name: sub.name}; openSubCategory = false" 
            class="p-2 cursor-pointer hover:bg-gray-100 flex items-center">
            <img :src="'{{ asset('storage/') }}' + '/' + sub.iconUrl"
                class="w-10 h-10 mx-2 rounded-xl p-1 bg-gray-100 object-cover" />
            <span x-text="sub.name"></span>
        </li>
            </template>
        </ul>
        <input type="hidden" name="sub_category_id" x-model="selectedSubCategory.id">
    </div>
</div>
{{--السعر الاساسي--}}

        <div>
        <label class="block mb-1 font-bold">{{ __('messages.base_price') }}</label>
  <div class="flex">
    <input 
        type="number" 
        min="0" 
        name="price" 
        placeholder="{{ __('messages.base_price_placeholder') }}" 
        class="border p-2 w-full rounded-r-xl h-[42px]" 
        value="{{ old('price') }}"
    >

    <div class="flex items-center justify-center border border-l-0 rounded-l-xl bg-gray-100 h-[42px] w-[42px]">
        <img src="{{ asset('/images/Saudi_Riyal_Symbol.svg') }}" alt="" class="w-5 h-5">
    </div>
</div>

</div>

{{-- ✅ التسعير بالجملة --}}
<div>
    <label class="block mb-1 font-bold">{{ __('messages.wholesale_pricing') }}</label>
<div class="flex flex-col md:flex-row md:items-center gap-2 mb-4">

  <!-- FROM + TO -->
  <div class="flex gap-1 w-full md:w-3/4">
    <input type="number" min="0" x-model="newWholesaleItem.from" placeholder="{{ __('messages.from_quantity') }}" class="border p-2 w-full rounded-xl">
    <input type="number" min="0" x-model="newWholesaleItem.to" placeholder="{{ __('messages.to_quantity') }}" class="border p-2 w-full rounded-xl">
  </div>

  <!-- PRICE + BUTTON -->
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
                <input type="hidden" :name="'wholesale_from[' + index + ']'" :value="item.from">
                <input type="hidden" :name="'wholesale_to[' + index + ']'" :value="item.to">
                <input type="hidden" :name="'wholesale_price[' + index + ']'" :value="item.price">
                <span x-text="item.from + ' - ' + item.to + ' ' + 'قطعة'"></span>
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
<div x-data="{
    newColorName: '',
    newColorImage: null,
    colors: [],
    addColor() {
        if (!this.newColorName) return; // ✅ require at least name

        if (this.newColorImage) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.colors.push({
                    name: this.newColorName,
                    image: e.target.result
                });
                this.resetInputs();
            };
            reader.readAsDataURL(this.newColorImage);
        } else {
            // ✅ Add without image
            this.colors.push({
                name: this.newColorName,
                image: null
            });
            this.resetInputs();
        }
    },
    resetInputs() {
        this.newColorName = '';
        this.newColorImage = null;
        if (this.$refs.imageInput) {
            this.$refs.imageInput.value = null;
        }
    },
    removeColor(index) {
        this.colors.splice(index, 1);
    }
}">
    {{-- ✅ Color Name --}}
    <label class="block mb-1 font-bold">{{ __('messages.available_colors') }}</label>

    {{-- Input group (responsive) --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
        <input type="text"
               x-model="newColorName"
               placeholder="{{ __('messages.color_name') }}"
               class="border p-2 w-full rounded-xl">

        {{-- ✅ Color Image --}}
        <input type="file"
               x-ref="imageInput"
               @change="newColorImage = $event.target.files[0]"
               accept="image/*"
               class="border p-2 rounded-xl w-full sm:w-auto">

        {{-- ✅ Add Button --}}
        <button type="button"
                @click="addColor"
                class="bg-[#185D31] text-white px-4 py-2 rounded-xl w-full sm:w-auto">
            {{ __('messages.add') }}
        </button>
    </div>

    {{-- ✅ Show colors --}}
    <div class="flex flex-wrap gap-2" x-show="colors.length > 0">
        <template x-for="(color, index) in colors" :key="index">
            <div class="bg-gray-100 rounded-xl px-4 py-2 flex items-center gap-3 w-full sm:w-auto">
                {{-- Hidden input --}}
                <input type="hidden" name="colors[]" :value="JSON.stringify(color)">

                {{-- Show color name --}}
                <span x-text="color.name" class="font-bold"></span>

                {{-- Show preview image if exists --}}
                <template x-if="color.image">
                    <img :src="color.image" alt="color" class="w-8 h-8 rounded-full object-cover border">
                </template>

                {{-- Remove button --}}
                <button type="button"
                        @click="removeColor(index)"
                        class="text-red-500 text-sm font-bold">
                    ✕
                </button>
            </div>
        </template>
    </div>
</div>



        {{-- ✅ باقي الحقول --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.material_type') }}</label>
                <input type="text" name="material_type" placeholder="{{ __('messages.material_type') }}" class="border p-2 w-full rounded-xl">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.available_quantity') }}</label>
                <input type="number" min="0" name="available_quantity" placeholder="{{ __('messages.available_quantity') }}" class="border p-2 w-full rounded-xl">
            </div>
        </div>

        <div>
            <label class="block mb-1 font-bold">{{ __('messages.min_order_quantity') }}</label>
            <input type="number" min="1" name="min_order_quantity" placeholder="{{ __('messages.min_order_quantity') }}" class="border p-2 w-full rounded-xl">
        </div>

        {{-- ✅ حالة المنتج (Product Status) --}}
<div class="mb-4" x-data="{ 
    open: false, 
    selectedStatus: '{{ old('product_status', $product->product_status ?? '') }}',
    statusOptions: {
        'ready_for_delivery': 'جاهز للتوصيل الفوري',
        'made_to_order': 'حسب الطلب'
    },
    get selectedStatusText() {
        return this.statusOptions[this.selectedStatus] || 'حدد حالة المنتج';
    }
}">
    <label class="block mb-1 font-bold ">حالة المنتج</label>
    
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
            <textarea name="description" placeholder="{{ __('messages.description') }}" rows="4" class="border p-2 w-full rounded-xl"></textarea>
        </div>

        <p class="font-bold text-[24px]">{{ __('messages.add_offers') }}</p>

        {{-- ✅ إضافة العروض --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.offer_start') }}</label>
                <input type="date" name="offer_start" class="border p-2 w-full rounded-xl">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.offer_end') }}</label>
                <input type="date" name="offer_end" class="border p-2 w-full rounded-xl">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.discount_percent') }}</label>
                <input type="number" min="0" name="discount_percent" placeholder="{{ __('messages.discount_percent') }}" class="border p-2 w-full rounded-xl">
            </div>
        </div>

        <p class="font-bold text-[24px]">{{ __('messages.manufacturing_delivery_time') }}</p>

{{-- ✅ مدة التصنيع والشحن --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.preparation_days') }}</label>
                <div class="relative">
                    <input type="number" min="0" name="preparation_days" placeholder="{{ __('messages.preparation_days') }}" class="border p-2 w-full rounded-xl pr-12">
                    <span class="absolute bg-gray-100 inset-y-0 rtl:left-0 rounded-l-xl flex items-center rtl:p-3 pointer-events-none">
                    {{ __('messages.days') }} 
                    </span>
                </div>
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.shipping_days') }}</label>
                <div class="relative">
                    <input type="number" min="0" name="shipping_days" placeholder="{{ __('messages.shipping_days') }}" class="border p-2 w-full rounded-xl pr-12">
                    <span class="absolute bg-gray-100 inset-y-0 rounded-l-xl rtl:left-0 flex items-center rtl:p-3 pointer-events-none">
                        {{ __('messages.days') }}
                    </span>
                </div> 
            </div>
        </div>

        {{-- ✅ القدرة الإنتاجية --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.production_capacity') }}</label>
            <input type="text" name="production_capacity" placeholder="{{ __('messages.production_capacity') }}" class="border p-2 w-full rounded-xl">
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
>
                    <span class="inline-flex items-center px-3 border border-l-0 rounded-l-xl bg-gray-100">{{ __('messages.kg') }}</span>
                </div>
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.package_dimensions') }}</label>
                <input type="text" name="package_dimensions" placeholder="{{ __('messages.package_dimensions') }}" class="border p-2 w-full rounded-xl">
            </div>
        </div>

        {{-- ✅ مرفقات --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.attachments') }}</label>
            <input type="file" name="attachments" accept=".pdf,image/*" class="border p-2 w-full rounded-xl">
            <p class="text-sm text-gray-500 mt-1">{{ __('messages.attachments_note') }}</p>
        </div>

      <button 
    type="submit" 
    x-bind:disabled="loading" 
    x-on:click="loading = true; window.scrollTo({ top: 0, behavior: 'smooth' });" 
    class="bg-[#185D31] text-white px-6 py-3 rounded-xl"
>
    {{ __('messages.add_product') }}
</button>

    </form>
</div>

<script>
document.getElementById('create-product-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear all previous errors and messages
    document.querySelectorAll('.text-red-600').forEach(el => el.textContent = '');
    const successMessageDiv = document.getElementById('success-message');
    successMessageDiv.classList.add('hidden');
    successMessageDiv.classList.remove('bg-red-100', 'text-red-700');
    successMessageDiv.classList.add('bg-green-100', 'text-green-700');

    const formData = new FormData(this);

    // ✅ Get images from Alpine multi-upload
    const alpine = Alpine.$data(document.querySelector('[x-data]'));
    if (alpine.files && alpine.files.length > 0) {
        alpine.files.forEach(file => {
            formData.append('images[]', file);
        });
    }

    // ✅ Add CSRF token
    const csrfToken = document.querySelector('input[name="_token"]').value;
    formData.append('_token', csrfToken);

    fetch("{{ route('products.store') }}", {
        method: 'POST',
        headers: {
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: formData
    })
    .then(async response => {
        const data = await response.json();

        if (!response.ok) {
            if (data.errors) {
                for (const [key, messages] of Object.entries(data.errors)) {
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
            } else if (data.message) {
                const successMessageSpan = successMessageDiv.querySelector('span');
                successMessageSpan.textContent = data.message;
                successMessageDiv.classList.remove('bg-green-100', 'text-green-700');
                successMessageDiv.classList.add('bg-red-100', 'text-red-700');
                successMessageDiv.classList.remove('hidden');
            }
            throw new Error('Request failed');
        }

        return data;
    })
    .then(data => {
        if (data.success) {
            const successMessageSpan = successMessageDiv.querySelector('span');
            successMessageSpan.textContent = data.success;
            successMessageDiv.classList.remove('hidden');

            setTimeout(() => {
                successMessageDiv.classList.add('hidden');
            }, 5000);

            if (data.redirect) {
                window.location.href = data.redirect;
            }
        }
    })
    .catch(error => {
        console.error('An unexpected error occurred:', error);
        const successMessageSpan = successMessageDiv.querySelector('span');
        successMessageSpan.textContent = {{__('message.error_message')}};
        successMessageDiv.classList.remove('bg-green-100', 'text-green-700');
        successMessageDiv.classList.add('bg-red-100', 'text-red-700');
        successMessageDiv.classList.remove('hidden');
    });
});
</script>


<script>
    window.subCategories = @json($categories->mapWithKeys(fn($cat) => [$cat->id => $cat->subCategories]));
</script>

@endsection
