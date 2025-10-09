@extends('layouts.app')

@section('page_title', __('messages.edit_offer'))

@section('content')
<div class="mx-[64px] mb-4 bg-white rounded-xl mt-4 p-6">

    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-100 border border-green-300 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <h1 class="text-3xl font-bold mb-6">{{ __('messages.edit_offer') }}</h1>

    {{-- ✅ Success Message Container --}}
    <div id="success-message" class="hidden p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
        <span class="font-medium"></span>
    </div>

    <form id="edit-offer-form" enctype="multipart/form-data" class="space-y-6"
        @submit.prevent="syncFilesToInput(); $el.submit()">

        @csrf
        @method('PUT')

        {{-- Alpine.js Image Upload and Preview Section --}}
        <div 
            x-data="{
               previews: {{ json_encode($offer->image ? [asset('storage/' . $offer->image)] : []) }},
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
                    this.$refs.offerImage.files = dataTransfer.files;
                }
            }"
            class="space-y-2"
        >
            <label class="block mb-2 font-bold">{{ __('messages.offer_image') }}</label>
            <p class="text-sm text-gray-500 mb-2">PNG, JPG</p>

            <div
                @click="$refs.offerImage.click()"
                @dragover.prevent
                @drop.prevent="handleFileDrop($event)"
                class="w-full h-40 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer flex items-center justify-center bg-white"
            >
                <div x-show="previews.length === 0" class="flex flex-col items-center">
                    <img src="{{ asset('images/Frame 3508.svg') }}" alt="" class="w-8 h-8 mb-2">
                    <p class="text-sm text-gray-500">{{ __('messages.drag_or_click') }}</p>
                </div>
                <input 
                    type="file" 
                    name="image"
                    x-ref="offerImage" 
                    class="hidden" 
                    @change="handleFiles" 
                    accept="image/*"
                >
            </div>

            {{-- Preview Grid --}}
            <div class="flex flex-wrap gap-2 mt-4" x-show="previews.length > 0">
                <template x-for="(img, index) in previews" :key="index">
                    <div class="relative w-24 h-24 border rounded overflow-hidden group">
                        <img :src="img" alt="" class="w-full h-full object-cover">
                        <button
                            @click.stop="removeImage(index)"
                            type="button"
                            class="absolute top-1 left-1 bg-red-500 text-white rounded-full p-1 leading-none w-5 h-5 flex items-center justify-center"
                            title="{{ __('messages.remove') }}"
                        >✕</button>
                    </div>
                </template>
            </div>

            @error('image') 
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
            @enderror
        </div>
        
        {{-- Offer Name --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold mb-1">{{ __('messages.offer_name') }}</label>
                <input type="text" name="name" value="{{ old('name', $offer->name ?? $offer->name) }}" placeholder="{{ __('messages.enter_offer_name') }}" class="border p-2 w-full rounded-xl">
                @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
            
            {{-- Product Selection Dropdown --}}
            <div class="relative" x-data="{ open: false, selectedProduct: {id: {{ $offer->product->id ?? 'null' }}, name: '{{ $offer->product->name ?? '' }}' } }" @click.away="open = false">
                <label class="block font-bold mb-1">{{ __('messages.product_name') }}</label>
                <div @click="open = !open"
                     class="border p-2 w-full rounded-xl cursor-pointer flex justify-between items-center bg-white">
                    <span x-text="selectedProduct ? selectedProduct.name : '{{ __('messages.select_product') }}'"></span>
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>

                <ul x-show="open" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-xl max-h-60 overflow-y-auto">
                   @foreach($products as $product)
    @php
        $productImages = is_string($product->images) ? json_decode($product->images, true) : ($product->images ?? []);
        $imageUrl = !empty($productImages)
            ? asset('storage/' . $productImages[0])
            : 'https://placehold.co/40x40/F0F0F0/ADADAD?text=No+Img';
    @endphp
    <li @click="selectedProduct = {id: {{ $product->id }}, name: '{{ $product->name }}'}; open = false;"
        class="p-2 cursor-pointer hover:bg-gray-100 flex items-center">
        <img src="{{ $imageUrl }}"
             onerror="this.onerror=null;this.src='https://placehold.co/40x40/F0F0F0/ADADAD?text=No+Img';"
             class="w-10 h-10 mx-2 rounded-xl p-1 bg-gray-100 object-cover" />
        <span>{{ $product->name }}</span>
    </li>
@endforeach

                </ul>

                <input type="hidden" name="product_id" x-model="selectedProduct.id">
            </div>
            @error('product_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Offer Period --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block font-bold mb-1">{{ __('messages.offer_start') }}</label>
<input 
    type="date" 
    name="offer_start" 
    value="{{ old('offer_start', optional($offer->offer_start)->format('Y-m-d')) }}" 
    class="border p-2 w-full rounded-xl">                @error('offer_start') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-bold mb-1">{{ __('messages.offer_end') }}</label>
<input 
    type="date" 
    name="offer_end" 
    value="{{ old('offer_end', optional($offer->offer_end)->format('Y-m-d')) }}" 
    class="border p-2 w-full rounded-xl">                @error('offer_end') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block font-bold mb-1">{{ __('messages.discount_percentage') }}</label>
<input 
    type="number" 
    name="discount_percent" 
    value="{{ old('discount_percent', (int) $offer->discount_percent) }}" 
    class="border p-2 w-full rounded-xl" 
    placeholder="{{ __('messages.enter_discount_percentage') }}"
    step="1" 
    min="0" 
    max="100">
                @error('discount_percent') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="block font-bold mb-1">{{ __('messages.offer_description') }}</label>
            <textarea name="description" rows="4" class="border p-2 w-full rounded-xl">{{ old('description', $offer->description) }}</textarea>
            @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Product Status --}}
        <div>
            <label class="block font-bold mb-1">{{ __('messages.product_status') }}</label>
            <select name="product_status" class="border p-2 w-full rounded-xl">
                <option value="">{{ __('messages.select_product_status') }}</option>
                <option value="ready_for_delivery" {{ old('product_status', $offer->product->product_status ?? '') == 'ready_for_delivery' ? 'selected' : '' }}>
                    {{ __('messages.ready_for_immediate_delivery') }}
                </option>
                <option value="made_to_order" {{ old('product_status', $offer->product->product_status ?? '') == 'made_to_order' ? 'selected' : '' }}>
                    {{ __('messages.made_to_order') }}
                </option>
            </select>
            @error('product_status') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

      <button 
    type="submit" 
    x-bind:disabled="loading" 
    x-on:click="loading = true; window.scrollTo({ top: 0, behavior: 'smooth' });" 
    class="bg-[#185D31] text-white px-6 py-3 rounded-xl"
>                  {{ __('messages.update_offer') }}
        </button>
    </form>
</div>

<script>
document.getElementById('edit-offer-form').addEventListener('submit', function(e) {
    e.preventDefault();

    document.querySelectorAll('.text-red-600').forEach(el => el.textContent = '');
    const successMessageDiv = document.getElementById('success-message');
    successMessageDiv.classList.add('hidden');
    successMessageDiv.classList.remove('bg-red-100', 'text-red-700');
    successMessageDiv.classList.add('bg-green-100', 'text-green-700');

    const formData = new FormData(this);
    formData.append('_method', 'PUT');

    fetch("{{ route('offers.update', $offer->id) }}", {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
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
            }
            throw new Error('Validation failed');
        }

        return data;
    })
    .then(data => {
        if (data.success) {
            const successMessageSpan = successMessageDiv.querySelector('span');
            successMessageSpan.textContent = data.success;
            successMessageDiv.classList.remove('hidden');

            setTimeout(() => successMessageDiv.classList.add('hidden'), 5000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const successMessageSpan = successMessageDiv.querySelector('span');
        successMessageSpan.textContent = "{{ __('messages.error_message') }}";
        successMessageDiv.classList.remove('bg-green-100', 'text-green-700');
        successMessageDiv.classList.add('bg-red-100', 'text-red-700');
        successMessageDiv.classList.remove('hidden');
    });
});
</script>

@endsection
