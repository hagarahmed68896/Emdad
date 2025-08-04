@extends('layouts.app')

@section('page_title', __('messages.edit_product'))

@section('content')
<div class="mx-[64px] mb-4 bg-white rounded-xl mt-4"
     x-data="{
         preview: '{{ $product->image_url ? asset('storage/' . $product->image_url) : '' }}',
         selectedCategory: { id: {{ $product->category_id }}, name: '{{ optional($product->category)->name }}' },
         selectedSubCategory: { id: {{ $product->sub_category_id ?? 'null' }}, name: '{{ optional($product->subCategory)->name }}' },
         newWholesaleItem: { from: '', to: '', price: '' },
         wholesalePrices: @json($product->wholesalePrices ?? []),
         newSize: '',
         sizes: @json($product->sizes ?? []),
         newColor: '',
         colors: @json($product->colors ?? []),
         handleFileDrop(e) {
             const file = e.dataTransfer.files[0];
             if (file) this.preview = URL.createObjectURL(file);
             this.$refs.imageInput.files = e.dataTransfer.files;
         },
         handleFile(e) {
             const file = e.target.files[0];
             if (file) this.preview = URL.createObjectURL(file);
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
         addColor() {
             if (this.newColor.trim() !== '') {
                 this.colors.push(this.newColor);
                 this.newColor = '';
             }
         },
         removeColor(index) {
             this.colors.splice(index, 1);
         }
     }">

    <h1 class="text-3xl font-bold mb-6">{{ __('messages.edit_product') }}</h1>

    <form method="POST" action="{{ route('products.update', $product->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- ✅ صورة المنتج --}}
        <div>
            <label class="block mb-2 font-bold">{{ __('messages.product_image') }}</label>
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
        </div>

        {{-- ✅ الاسم ورقم الموديل --}}
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

        {{-- ✅ الفئة والتصنيف --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4"
            x-data="{
                openCategory: false,
                openSubCategory: false,
                selectedCategory: { id: {{ $product->category_id }}, name: '{{ optional($product->category)->name }}' },
                selectedSubCategory: { id: {{ $product->sub_category_id ?? 'null' }}, name: '{{ optional($product->subCategory)->name }}' }
            }"
            @click.away="openCategory = false; openSubCategory = false"
        >
            <div class="relative">
                <label class="block mb-1 font-bold">{{ __('messages.select_category') }}</label>
                <div @click="openCategory = !openCategory" class="border p-2 w-full rounded-xl cursor-pointer flex justify-between items-center bg-white">
                    <span x-text="selectedCategory.name || '{{ __('messages.select_category') }}'"></span>
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <ul x-show="openCategory" class="absolute z-10 w-full mt-1 bg-white border rounded-xl max-h-60 overflow-y-auto">
                    @foreach ($categories as $category)
                        <li @click="selectedCategory = {id: {{ $category->id }}, name: '{{ $category->name }}'}; selectedSubCategory = null; openCategory = false;"
                            class="p-2 cursor-pointer hover:bg-gray-100 flex items-center">
                            <img src="{{ asset('storage/' . $category->iconUrl) }}" class="w-10 h-10 mx-2 rounded-xl p-1 bg-gray-100 object-cover" />
                            <span>{{ $category->name }}</span>
                        </li>
                    @endforeach
                </ul>
                <input type="hidden" name="category_id" :value="selectedCategory.id">
            </div>

            <div class="relative">
                <label class="block mb-1 font-bold">{{ __('messages.select_subcategory') }}</label>
                <div @click="if (selectedCategory) openSubCategory = !openSubCategory"
                    :class="{'opacity-50 cursor-not-allowed': !selectedCategory}"
                    class="border p-2 w-full rounded-xl cursor-pointer flex justify-between items-center bg-white">
                    <span x-text="selectedSubCategory.name || '{{ __('messages.select_subcategory') }}'"></span>
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <ul x-show="openSubCategory" class="absolute z-10 w-full mt-1 bg-white border rounded-xl max-h-60 overflow-y-auto">
                    <template x-for="sub in window.subCategories[selectedCategory.id]" :key="sub.id">
                        <li @click="selectedSubCategory = {id: sub.id, name: sub.name}; openSubCategory = false;"
                            class="p-2 cursor-pointer hover:bg-gray-100 flex items-center">
                            <img :src="'{{ asset('storage/') }}/' + sub.iconUrl" class="w-10 h-10 mx-2 rounded-xl p-1 bg-gray-100 object-cover" />
                            <span x-text="sub.name"></span>
                        </li>
                    </template>
                </ul>
                <input type="hidden" name="sub_category_id" :value="selectedSubCategory.id">
            </div>
        </div>

        {{-- ✅ السعر --}}
        <div>
            <label class="block mb-1 font-bold">{{ __('messages.base_price') }}</label>
            <div class="flex">
                <input type="number" min="0" name="price" class="border p-2 w-full rounded-r-xl" value="{{ old('price', $product->price) }}">
                <img class="inline-flex items-center h-full p-2 border border-l-0 rounded-l-xl bg-gray-100" src="{{asset('/images/Saudi_Riyal_Symbol.svg')}}" alt="">
            </div>
        </div>

        {{-- ✅ باقي الحقول: Wholesale, Sizes, Colors, Description, Shipping, etc --}}
        {{-- Just copy same logic from your create form and bind to $product fields --}}

        <button type="submit" class="bg-[#185D31] text-white px-6 py-3 rounded-xl">{{ __('messages.save_changes') }}</button>
    </form>
</div>

<script>
    window.subCategories = @json($categories->mapWithKeys(fn($cat) => [$cat->id => $cat->subCategories]));
</script>
@endsection
