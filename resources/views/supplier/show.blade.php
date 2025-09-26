@extends('layouts.app')

@section('title', $supplier->company_name . ' | ' . config('app.name'))

@section('content')
<div class="mx-[64px] py-8">

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        {{-- ✅ Sidebar (Supplier Header) --}}
        <aside class="lg:col-span-1 bg-white shadow-lg rounded-2xl p-6 border border-gray-100 sticky top-6">
            {{-- Avatar / Initials --}}
            <div class="w-24 h-24 mx-auto bg-gradient-to-r from-green-100 to-green-200 rounded-full flex items-center justify-center text-2xl font-bold text-green-800 shadow-inner">
                {{ strtoupper(substr($supplier->company_name, 0, 2)) }}
            </div>

            {{-- Supplier Info --}}
            <div class="mt-4 text-center">
                <h1 class="text-xl font-extrabold text-[#212121]">
                    {{ $supplier->company_name }}
                </h1>
                 @if($supplier->description)
                <p class="text-gray-600 text-sm mt-1">
                    {{ $supplier->description ?? __('messages.no_description') }}
                </p>
                @endif
            </div>

            {{-- Badges --}}
            <div class="mt-4 flex flex-col items-center gap-2">
                @if($supplier->supplier_confirmed)
                    <span class="flex items-center bg-green-50 text-green-700 px-3 py-1 rounded-full text-sm font-medium shadow-sm">
                        <img src="{{ asset('images/Success.svg') }}" class="w-4 h-4 mr-1" alt="">
                        {{ __('messages.confirmed_supplier') }}
                    </span>
                @endif
                 @if($supplier->experience_years)
                <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-full text-sm font-medium shadow-sm">
                    {{ __('messages.experience_years', ['years' => $supplier->experience_years ?? 0]) }}
                </span>
                @endif
            </div>

            {{-- Company Details --}}
            <div class="mt-6 text-sm text-gray-700 bg-gray-50 rounded-xl p-4 border border-gray-200">
                <p class="flex justify-between">
                    <span class="font-semibold text-gray-800">{{ __('messages.commercial_registration') }}:</span>
                    <span class="ml-2">{{ $supplier->commercial_registration }}</span>
                </p>
                <p class="flex justify-between mt-2">
                    <span class="font-semibold text-gray-800">{{ __('messages.tax_certificate') }}:</span>
                    <span class="ml-2">{{ $supplier->tax_certificate ?? __('messages.not_available') }}</span>
                </p>
            </div>
        </aside>

        {{-- ✅ Main Content (Products) --}}
        <main class="lg:col-span-3">
            <h2 class="text-2xl font-bold text-[#212121] mb-6">{{ __('messages.products_from_supplier') }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($products as $product)
                    {{-- Product Card --}}
                    <div class="product-card bg-white rounded-xl overflow-hidden shadow-md flex flex-col">
                        {{-- Inner Swiper --}}
                        <div class="swiper-container relative w-full h-48 sm:h-56 overflow-hidden product-image-swiper inner-swiper">
                            <div class="swiper-wrapper">
                                @php
                                    $images = collect(
                                        is_string($product->images)
                                            ? json_decode($product->images, true)
                                            : $product->images ?? [],
                                    );
                                @endphp

                                @forelse ($images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ Storage::url($image) }}"
                                            onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                            class="w-full h-full bg-[#F8F9FA] object-contain">
                                    </div>
                                @empty
                                    <div class="swiper-slide">
                                        <img src="{{ asset($product->image ?? 'https://placehold.co/300x200/F0F0F0/ADADAD?text=No+Image') }}"
                                            onerror="this.onerror=null;this.src='https://placehold.co/300x200/F0F0F0/ADADAD?text=Image+Error';"
                                            class="w-full h-full bg-[#F8F9FA] object-cover">
                                    </div>
                                @endforelse
                            </div>
                            <div class="swiper-pagination image-pagination"
                                style="{{ $images->count() <= 1 ? 'display:none;' : '' }}"></div>
                        </div>

                        {{-- Product Details --}}
                        <div class="p-4 flex flex-col flex-grow">
                            <div class="flex w-full items-center text-sm mb-2 justify-between">
                                <h3 class="text-[20px] font-bold text-[#212121] mb-1">{{ $product->name }}</h3>
                                <div class="flex items-center">
                                    @if($product->rating)
                                        <img class="mx-1" src="{{ asset('images/Vector (4).svg') }}" alt="">
                                    @endif
                                    <span class="text-[18px]">{{ $product->rating }}</span>
                                </div>
                            </div>

                            <span class="text-[#696969] text-[16px]">{{ $product->subCategory->category->name ?? __('messages.uncategorized') }}</span>

                            <div class="flex mt-2">
                                <p class="text-[16px] text-[#212121]">{{ $supplier->company_name }}</p>
                            </div>

                            {{-- Price --}}
                            <div class="flex items-center mb-2">
                                @php $offer = $product->offer; @endphp
                                @if ($offer && $offer->discount_percent)
                                    <span class="flex text-lg font-bold text-gray-800">
                                        {{ number_format($product->price_range['min'], 2) }}
                                        @if($product->price_range['min'] != $product->price_range['max'])
                                            - {{ number_format($product->price_range['max'], 2) }}
                                        @endif
                                        <img class="mx-1 w-[20px] h-[21px]" src="{{ asset('images/Vector (3).svg') }}" alt="">
                                    </span>
                                    <span class="flex text-sm text-gray-400 line-through mr-2">
                                        {{ number_format($product->price, 2) }}
                                        <img class="mx-1 w-[14px] h-[14px] mt-1 inline-block"
                                            src="{{ asset('images/Saudi_Riyal_Symbol.svg') }}" alt="currency">
                                    </span>
                                @else
                                    <span class="flex text-lg font-bold text-gray-800">
                                        {{ number_format($product->price, 2) }}
                                        <img class="mx-1 w-[20px] h-[21px]"
                                            src="{{ asset('images/Vector (3).svg') }}" alt="">
                                    </span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-600 mb-4">
                                {{ __('messages.minimum_order_quantity', ['quantity' => $product->min_order_quantity ?? '1']) }}
                            </p>

                            <div class="mt-auto flex justify-between">
                                <div class="w-full h-full">
                                    <a href="{{ route('products.show', $product->slug) }}"
                                        class="block bg-[#185D31] text-white text-center py-[10px] px-[15px] rounded-[12px] font-medium transition-colors duration-200">
                                        {{ __('messages.view_details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-3 text-center text-gray-500">{{ __('messages.no_products_found') }}</p>
                @endforelse
            </div>
        </main>

    </div>
</div>
@endsection
