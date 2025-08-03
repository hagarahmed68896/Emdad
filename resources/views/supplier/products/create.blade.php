@extends('layouts.app')

@section('page_title', __('messages.add_new_product'))

@section('content')
<div class="mx-[64px] mb-4 bg-white rounded-xl mt-4"
     x-data="{
        preview: '',
        wholesalePrices: [{from: '', to: '', price: ''}],
        sizes: [''],
        handleFileDrop(e) {
            const file = e.dataTransfer.files[0];
            if (file) this.preview = URL.createObjectURL(file);
            this.$refs.imageInput.files = e.dataTransfer.files;
        },
        colors: [''],
        handleFile(e) {
            const file = e.target.files[0];
            if (file) this.preview = URL.createObjectURL(file);
        },
        addWholesale() { this.wholesalePrices.push({from: '', to: '', price: ''}); },
        addSize() { this.sizes.push(''); },
        addColor() { this.colors.push(''); }
     }">

    <h1 class="text-3xl font-bold mb-6">{{ __('messages.add_new_product') }}</h1>

    {{-- ✅ Success Message Container --}}
    <div id="success-message" class="hidden p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
        <span class="font-medium"></span>
    </div>

    <form id="create-product-form" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <p class="font-bold text-[24px]">{{ __('messages.product_details') }}</p>

        {{-- ✅ صورة المنتج --}}
        <div>
            <label class="block mb-2 font-bold">{{ __('messages.product_image') }}</label>
            <p class="text-sm text-gray-500 mb-2">PNG/JPG</p>
            <div 
                @click="$refs.imageInput.click()"
                @dragover.prevent
                @drop.prevent="handleFileDrop($event)"
                class="flex flex-col md:flex-row items-center justify-center w-full h-40 border-2 border-dashed rounded-xl cursor-pointer"
            >
                <template x-if="!preview">
                    <div class="flex flex-col md:flex-row items-center justify-center">
                        <img src="{{ asset('images/Frame 3508.svg') }}" class="w-8 h-8 mb-2 mx-2" alt="">
                        <p class="text-sm text-gray-500">{{ __('messages.drag_or_click') }}</p>
                    </div>
                </template>
                <template x-if="preview">
                    <img :src="preview" class="h-40 object-contain"/>
                </template>
                <input type="file" name="image" x-ref="imageInput" class="hidden" @change="handleFile" accept="image/*">
            </div>
            @error('image') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.select_category') }}</label>
                <select name="category_id" id="category_id" class="border p-2 w-full rounded-xl">
                    <option value="">{{ __('messages.select_category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

        <div>
            <label class="block mb-1 font-bold">{{ __('messages.select_subcategory') }}</label>
            <select name="sub_category_id" id="sub_category_id" class="border p-2 w-full rounded-xl">
                 <option value="">{{ __('messages.select_subcategory') }}</option>
            </select>
            </div>
        </div>


        <div>
        <label class="block mb-1 font-bold">{{ __('messages.base_price') }}</label>
        <div class="flex">
            <input type="number" min="0" name="price" placeholder="{{ __('messages.base_price_placeholder') }}" class="border h-full p-2 w-full rounded-r-xl" value="{{ old('price') }}">
            <img class="inline-flex items-center h-full p-2 border border-l-0 rounded-l-xl bg-gray-100" src="{{asset('/images/Saudi_Riyal_Symbol.svg')}}" alt="">
        </div>
</div>
        {{-- ✅ التسعير بالجملة --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.wholesale_pricing') }}</label>
            <div class="w-full flex flex-col md:flex-row md:items-center">
                <div class="flex-1">
                    <template x-for="(item, index) in wholesalePrices" :key="index">
                        <div class="grid grid-cols-3 gap-2 w-full">
                            <input type="number" min="0" :name="'wholesale_from[' + index + ']'" placeholder="{{ __('messages.from_quantity') }}" class="border p-2 rounded-xl" x-model="item.from">
                            <input type="number" min="0" :name="'wholesale_to[' + index + ']'" placeholder="{{ __('messages.to_quantity') }}" class="border p-2 rounded-xl" x-model="item.to">
                            <div class="flex items-center">
                            <input type="number" min="0" :name="'wholesale_price[' + index + ']'" placeholder="{{ __('messages.price') }}" class="border h-full p-2 w-full rounded-r-xl" x-model="item.price">
                            <img class="inline-flex items-center h-full p-2 border border-l-0 rounded-l-xl bg-gray-100" src="{{asset('/images/Saudi_Riyal_Symbol.svg')}}" alt="">
                        </div>
                        </div>
                    </template>
                </div>
                <div class="md:ml-2 md:rtl:mr-2 flex md:flex-col md:items-center">
                    <button type="button" @click="addWholesale" class="bg-[#185D31] text-white px-4 py-2 rounded-xl">{{ __('messages.add') }}</button>
                </div>
            </div>
        </div>

        {{-- ✅ الأحجام --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.available_sizes') }}</label>
            <div class="w-full flex flex-col md:flex-row md:items-center">
                <template x-for="(size, index) in sizes" :key="index">
                    <input type="text" name="sizes[]" placeholder="{{ __('messages.available_sizes') }}" class="border p-2 w-full rounded-xl">
                </template>
                <div class="md:ml-2 md:rtl:mr-2 flex md:flex-col md:items-center">
                    <button type="button" @click="addSize" class="bg-[#185D31] text-white px-4 py-2 rounded-xl">{{ __('messages.add') }}</button>
                </div>
            </div>
        </div>

        {{-- ✅ الألوان --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.available_colors') }}</label>
            <div class="w-full flex flex-col md:flex-row md:items-center">
                <template x-for="(color, index) in colors" :key="index">
                    <input type="text" name="colors[]" placeholder="{{ __('messages.available_colors') }}" class="border p-2 w-full rounded-xl">
                </template>
                <div class="md:ml-2 md:rtl:mr-2 flex md:flex-col md:items-center">
                    <button type="button" @click="addColor" class="bg-[#185D31] text-white px-4 py-2 rounded-xl">{{ __('messages.add') }}</button>
                </div>
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
            <input type="number" min="0" name="min_order_quantity" placeholder="{{ __('messages.min_order_quantity') }}" class="border p-2 w-full rounded-xl">
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
                <input type="number" min="0" name="preparation_days" placeholder="{{ __('messages.preparation_days') }}" class="border p-2 w-full rounded-xl">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.shipping_days') }}</label>
                <input type="number" min="0" name="shipping_days" placeholder="{{ __('messages.shipping_days') }}" class="border p-2 w-full rounded-xl">
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
                    <input type="number" min="0" name="product_weight" placeholder="{{ __('messages.product_weight') }}" class="border p-2 w-full rounded-r-xl">
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

        <button type="submit" class="bg-[#185D31] text-white px-6 py-3 rounded-xl">{{ __('messages.add_product') }}</button>
    </form>
</div>

<script>
document.getElementById('create-product-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch("{{ route('products.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json' // Add this header to ensure JSON response
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                // This is the key part: if there's a validation error, we will have access to the error messages
                throw new Error(JSON.stringify(errorData));
            });
        }
        return response.json();
    })
 .then(data => {
    // Clear previous errors
    document.querySelectorAll('.text-red-600').forEach(el => el.textContent = '');

    // Get the success message container
    const successMessageDiv = document.getElementById('success-message');
    const successMessageSpan = successMessageDiv.querySelector('span');

    if (data.errors) {
        // This handles validation errors from Laravel
        for (const [key, messages] of Object.entries(data.errors)) {
            const el = document.querySelector(`[name="${key}"]`);
            if (el) {
                let errorContainer = el.parentNode.querySelector('.text-red-600');
                if (!errorContainer) {
                    errorContainer = document.createElement('p');
                    errorContainer.className = 'text-red-600 text-sm mt-1';
                    el.parentNode.appendChild(errorContainer);
                }
                errorContainer.textContent = messages[0];
            }
        }
    } else if (data.success) {
        // Show the success message in the div
        successMessageSpan.textContent = data.success;
        successMessageDiv.classList.remove('hidden');

        // Optional: Hide the message after a few seconds
        setTimeout(() => {
            successMessageDiv.classList.add('hidden');
        }, 5000); // 5000 milliseconds = 5 seconds

        // Redirect after the message is shown (optional)
        if (data.redirect) {
            window.location.href = data.redirect;
        }
    }
})
    .catch(error => {
        try {
            // Attempt to parse the error as JSON
            const errorData = JSON.parse(error.message);
            if (errorData.errors) {
                document.querySelectorAll('.text-red-600').forEach(el => el.textContent = '');
                for (const [key, messages] of Object.entries(errorData.errors)) {
                    // This handles validation errors from Laravel
                    const el = document.querySelector(`[name="${key}"]`);
                    if (el) {
                        let errorContainer = el.parentNode.querySelector('.text-red-600');
                        if (!errorContainer) {
                            errorContainer = document.createElement('p');
                            errorContainer.className = 'text-red-600 text-sm mt-1';
                            el.parentNode.appendChild(errorContainer);
                        }
                        errorContainer.textContent = messages[0];
                    }
                }
            } else {
                console.error('An error occurred:', errorData);
            }
        } catch (e) {
            // Handle non-JSON errors
            console.error('An unexpected error occurred:', error);
            alert('An unexpected error occurred. Please try again.');
        }
    });
});
</script>

<!-- ✅ تحميل الفئات الفرعية -->
<script>
document.getElementById('category_id').addEventListener('change', function () {
    const categoryId = this.value;
    const subCategorySelect = document.getElementById('sub_category_id');
    subCategorySelect.innerHTML = '<option value="">اختر الفئة الفرعية</option>';

    if (categoryId && window.subCategories[categoryId]) {
        window.subCategories[categoryId].forEach(sub => {
            const option = document.createElement('option');
            option.value = sub.id;
            option.textContent = sub.name;
            subCategorySelect.appendChild(option);
        });
    }
});
window.subCategories = @json($categories->mapWithKeys(fn($cat) => [$cat->id => $cat->subCategories]));
</script>

@endsection
