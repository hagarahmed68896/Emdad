@extends('layouts.app')

@section('page_title', __('messages.edit_product', ['name' => $product->name]))

@section('content')
<div class="mx-[64px] mb-4 bg-white rounded-xl mt-4"
     x-data="{
         previews: @json($product->images ?? []),
         files: [],
         removedImages: [],
         handleFiles(e) {
             const newFiles = Array.from(e.target.files);
             newFiles.forEach(file => {
                 this.files.push(file);
                 this.previews.push(URL.createObjectURL(file));
             });
         },
         handleFileDrop(e) {
             e.preventDefault();
             const droppedFiles = Array.from(e.dataTransfer.files);
             droppedFiles.forEach(file => {
                 this.files.push(file);
                 this.previews.push(URL.createObjectURL(file));
             });
             this.$refs.imageInput.files = new DataTransfer().files;
         },
         removeImage(index) {
             const existingImage = this.previews[index];
             if (typeof existingImage === 'string' && !existingImage.startsWith('blob:')) {
                 this.removedImages.push(existingImage);
             } else {
                 this.files.splice(index - this.removedImages.length, 1);
             }
             this.previews.splice(index, 1);
         },

         selectedCategoryId: {{ $product->category_id ?? 'null' }},
         selectedSubcategoryId: {{ $product->sub_category_id ?? 'null' }},

         newWholesaleItem: { from: '', to: '', price: '' },
         wholesalePrices: @json($product->wholesale_prices ?? []),

         newSize: '',
         sizes: @json($product->sizes ?? []),

         newColor: '',
         colors: @json($product->colors ?? [])
     }">

    <h1 class="text-3xl font-bold mb-6">{{ __('messages.edit_product', ['name' => $product->name]) }}</h1>

    <form id="edit-product-form" method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ✅ Product Name --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.product_name') }}</label>
                <input type="text" name="name" class="border p-2 w-full rounded-xl" value="{{ old('name', $product->name) }}">
            </div>

            <div>
                <label class="block mb-1 font-bold">{{ __('messages.model_number') }}</label>
                <input type="text" name="model_number" class="border p-2 w-full rounded-xl" value="{{ old('model_number', $product->model_number) }}">
            </div>
        </div>

        {{-- ✅ Image Uploader --}}
        @include('products.partials.image-uploader-edit', ['product' => $product])

        {{-- ✅ Category/Subcategory --}}
        @include('products.partials.category-select', [
            'categories' => $categories,
            'selectedCategoryId' => $product->category_id,
            'selectedSubCategoryId' => $product->sub_category_id
        ])

        {{-- ✅ Base Price --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.base_price') }}</label>
            <div class="flex">
                <input type="number" min="0" name="price" value="{{ old('price', $product->price) }}" class="border p-2 w-full rounded-r-xl">
                <img class="inline-flex items-center h-full p-2 border border-l-0 rounded-l-xl bg-gray-100" src="{{asset('/images/Saudi_Riyal_Symbol.svg')}}" alt="">
            </div>
        </div>

        {{-- ✅ Wholesale --}}
        @include('products.partials.wholesale', ['wholesalePrices' => $product->wholesale_prices ?? []])

        {{-- ✅ Sizes --}}
        @include('products.partials.sizes', ['sizes' => $product->sizes ?? []])

        {{-- ✅ Colors --}}
        @include('products.partials.colors', ['colors' => $product->colors ?? []])

        {{-- ✅ Other Fields --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.material_type') }}</label>
                <input type="text" name="material_type" value="{{ old('material_type', $product->material_type) }}" class="border p-2 w-full rounded-xl">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.available_quantity') }}</label>
                <input type="number" name="available_quantity" value="{{ old('available_quantity', $product->available_quantity) }}" class="border p-2 w-full rounded-xl">
            </div>
        </div>

        <div>
            <label class="block mb-1 font-bold">{{ __('messages.min_order_quantity') }}</label>
            <input type="number" name="min_order_quantity" value="{{ old('min_order_quantity', $product->min_order_quantity) }}" class="border p-2 w-full rounded-xl">
        </div>

        <div>
            <label class="block mb-1 font-bold">{{ __('messages.description') }}</label>
            <textarea name="description" class="border p-2 w-full rounded-xl" rows="4">{{ old('description', $product->description) }}</textarea>
        </div>

        {{-- ✅ Offers --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.offer_start') }}</label>
                <input type="date" name="offer_start" value="{{ old('offer_start', optional($product->offer_start)->format('Y-m-d')) }}" class="border p-2 w-full rounded-xl">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.offer_end') }}</label>
                <input type="date" name="offer_end" value="{{ old('offer_end', optional($product->offer_end)->format('Y-m-d')) }}" class="border p-2 w-full rounded-xl">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.discount_percent') }}</label>
                <input type="number" name="discount_percent" value="{{ old('discount_percent', $product->discount_percent) }}" class="border p-2 w-full rounded-xl">
            </div>
        </div>

        {{-- ✅ Delivery Time --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.preparation_days') }}</label>
                <input type="number" name="preparation_days" value="{{ old('preparation_days', $product->preparation_days) }}" class="border p-2 w-full rounded-xl">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.shipping_days') }}</label>
                <input type="number" name="shipping_days" value="{{ old('shipping_days', $product->shipping_days) }}" class="border p-2 w-full rounded-xl">
            </div>
        </div>

        {{-- ✅ Production Capacity --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.production_capacity') }}</label>
            <input type="text" name="production_capacity" value="{{ old('production_capacity', $product->production_capacity) }}" class="border p-2 w-full rounded-xl">
        </div>

        {{-- ✅ Shipping Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.product_weight') }}</label>
                <input type="number" step="0.01" name="product_weight" value="{{ old('product_weight', $product->product_weight) }}" class="border p-2 w-full rounded-xl">
            </div>
            <div>
                <label class="block mb-1 font-bold">{{ __('messages.package_dimensions') }}</label>
                <input type="text" name="package_dimensions" value="{{ old('package_dimensions', $product->package_dimensions) }}" class="border p-2 w-full rounded-xl">
            </div>
        </div>

        {{-- ✅ Attachments --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.attachments') }}</label>
            <input type="file" name="attachments" accept=".pdf,image/*" class="border p-2 w-full rounded-xl">
            @if ($product->attachments)
                <p class="text-sm text-gray-500 mt-1">
                    <a href="{{ asset('storage/' . $product->attachments) }}" target="_blank" class="text-blue-600 underline">{{ __('messages.view_attachment') }}</a>
                </p>
            @endif
        </div>

        <button type="submit" class="bg-[#185D31] text-white px-6 py-3 rounded-xl">{{ __('messages.update_product') }}</button>
    </form>
</div>

<script>
    window.subCategories = @json($categories->mapWithKeys(fn($cat) => [$cat->id => $cat->subCategories]));
</script>
@endsection
