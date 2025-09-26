@extends('layouts.admin')

@section('page_title', __('messages.manage_products'))

@section('content')

<div class="p-6 overflow-y-auto">

    <p class="text-[32px] font-bold">{{ __('messages.manage_products') }}</p>

    {{-- 👇 إحصائيات المنتجات --}}
    <div>
        @include('admin.total_products')
    </div>

    {{-- 👇 النطاق الرئيسي لـ Alpine.js --}}
    <div x-data="{
        selectedProducts: [],
        selectAll: false,
        productsOnPage: JSON.parse('{{ $products->pluck('id')->toJson() }}'),
        init() {
            this.$watch('selectedProducts', () => {
                this.selectAll = this.selectedProducts.length === this.productsOnPage.length && this.productsOnPage.length > 0;
            });
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedProducts = [...this.productsOnPage];
            } else {
                this.selectedProducts = [];
            }
        }
    }" class="rounded-xl shadow mx-2 bg-white p-3">

        {{-- ✅ شريط الأكشن --}}
        <div x-show="selectedProducts.length > 0" class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-gray-800 rtl:mr-4 ltr:ml-4"
                      x-text="selectedProducts.length + ' {{ __('messages.selected') }}'"></span>
            </div>
        </div>

        {{-- ✅ شريط الفلترة والبحث --}}
        <div x-show="selectedProducts.length === 0" x-cloak
             class="flex flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0">

            <form action="{{ route('admin.products.index') }}" method="GET"
                  class="flex flex-col md:flex-row md:items-center gap-4 w-full max-w-xl">

                <div x-data="{ open: false, selectedStatus: '{{ $statusFilter ?? '' }}', selectedSort: '{{ $sortFilter ?? '' }}' }"
                     class="relative inline-block text-left">
                    <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                         class="cursor-pointer w-6 h-6" @click="open = !open" alt="Filter Icon">

                    <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                         class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0">
                        <h3 class="font-bold text-gray-700 mb-2">{{ __('messages.sort_by') }}</h3>
                        <ul class="space-y-1 mb-4">
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="sort_option" value="" x-model="selectedSort"
                                       class="w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31]">
                                <span class="ml-2 text-gray-700">{{ __('messages.all') }}</span></label></li>
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="sort_option" value="name_asc" x-model="selectedSort"
                                       class="w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31]">
                                <span class="ml-2 text-gray-700">{{ __('messages.name_asc') }}</span></label></li>
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="sort_option" value="name_desc" x-model="selectedSort"
                                       class="w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31]">
                                <span class="ml-2 text-gray-700">{{ __('messages.name_desc') }}</span></label></li>
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="sort_option" value="latest" x-model="selectedSort"
                                       class="w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31]">
                                <span class="ml-2 text-gray-700">{{ __('messages.latest') }}</span></label></li>
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="sort_option" value="oldest" x-model="selectedSort"
                                       class="w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31]">
                                <span class="ml-2 text-gray-700">{{ __('messages.oldest') }}</span></label></li>
                        </ul>

                        <h3 class="font-bold text-gray-700 mb-2">{{ __('messages.status') }}</h3>
                        <ul class="space-y-1 mb-4">
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="status_option" value="" x-model="selectedStatus"
                                       class="w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31]">
                                <span class="ml-2 text-gray-700">{{ __('messages.all') }}</span></label></li>
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="status_option" value="true" x-model="selectedStatus"
                                       class="w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31]">
                                <span class="ml-2 text-gray-700">{{ __('messages.available') }}</span></label></li>
                            <li><label class="flex items-center cursor-pointer">
                                <input type="radio" name="status_option" value="false" x-model="selectedStatus"
                                       class="w-5 h-5 border-[#185D31] rounded-full checked:bg-[#185D31]">
                                <span class="ml-2 text-gray-700">{{ __('messages.unavailable') }}</span></label></li>
                        </ul>

                        <div class="flex justify-end gap-2">
                            <button type="submit" @click="open = false;"
                                    class="px-4 py-2 rounded-xl bg-[#185D31] text-white hover:bg-green-800">
                                {{ __('messages.apply') }}
                            </button>
                            <button type="button"
                                    @click="selectedStatus = ''; selectedSort = ''; $el.closest('form').submit(); open = false;"
                                    class="px-4 py-2 rounded-xl bg-gray-200 text-gray-800 hover:bg-gray-300">
                                {{ __('messages.reset') }}
                            </button>
                        </div>
                    </div>

                    <input type="hidden" name="status" :value="selectedStatus">
                    <input type="hidden" name="sort" :value="selectedSort">
                </div>

                {{-- ✅ مربع البحث --}}
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('messages.search_product') }}"
                           class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none">

                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </div>

                    <button type="submit"
                            class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] rounded-l-xl">
                        {{ __('messages.search') }}
                    </button>
                </div>
            </form>

            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.products.export.csv', request()->query()) }}"
                   class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center">
                    <i class="fas fa-download ml-2"></i>
                    <span>{{ __('messages.download') }}</span>
                </a>
            </div>

        </div>

        {{-- ✅ هنا ضع جدول المنتجات --}}
        @include('admin.products.products_table')

    </div>
</div>

@endsection
