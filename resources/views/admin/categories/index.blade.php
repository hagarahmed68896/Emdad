@extends('layouts.admin')

@section('page_title', __('messages.manage_categories'))

@section('content')

    <div class="p-6 overflow-y-auto">

        <p class="text-[32px] font-bold">{{ __('messages.manage_categories') }}</p>

        <div>
            @include('admin.total_categories')
        </div>

        <div x-data="{
            selectedCategories: [],
            selectAll: false,
            categoriesOnPage: {{ Js::from($items->map(fn($item) => ['id' => $item->id, 'type' => $item->type])) }},
        
            init() {
                this.$watch('selectedCategories', () => {
                    this.selectAll = this.selectedCategories.length === this.categoriesOnPage.length && this.categoriesOnPage.length > 0;
                });
            },
        
            toggleSelectAll() {
                if (this.selectAll) {
                    this.selectedCategories = [...this.categoriesOnPage];
                } else {
                    this.selectedCategories = [];
                }
            }
        }" class="bg-white p-4 rounded shadow">

            {{-- ✅ Action Bar --}}
            <div x-show="selectedCategories.length > 0" class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <!-- ✅ عدد المحدد -->
                    <span class="text-xl font-bold text-gray-800 rtl:mr-4 ltr:ml-4"
                        x-text="selectedCategories.length + ' {{ __('messages.selected') }}'"></span>

                    <!-- ✅ زر فتح المودال -->
                    <button type="button" @click="$dispatch('open-bulk-delete-modal')"
                        class="p-2 rounded-full bg-red-100 hover:bg-red-200 text-red-600">
                        <i class="fas fa-trash"></i>
                    </button>

                    <!-- ✅ مودال تأكيد الحذف -->
                    <div x-data="{ showDeleteModal: false }" @open-bulk-delete-modal.window="showDeleteModal = true">
                        <div x-show="showDeleteModal" x-cloak x-transition.opacity
                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">

                            <div @click.away="showDeleteModal = false"
                                class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm text-center">

                                <h2 class="text-lg font-bold mb-4">{{ __('messages.confirm_bulk_delete') }}</h2>
                                <p class="text-gray-600 mb-6">
                                    {{ __('messages.confirm_delete_message') }}
                                    <span x-text="selectedCategories.length"></span>
                                    {{ __('messages.selected_items') }}
                                </p>

                                <form method="POST" action="{{ route('admin.categories.bulkDelete') }}"
                                    class="flex flex-col sm:flex-row justify-center gap-4 flex-wrap">
                                    @csrf
                                    @method('DELETE')

                                    <!-- ✅ تمرير الـ IDs والأنواع -->
                                    <template x-for="cat in selectedCategories" :key="cat.id + '-' + cat.type">
                                        <div>
                                            <input type="hidden" name="ids[]" :value="cat.id">
                                            <input type="hidden" name="types[]" :value="cat.type">
                                        </div>
                                    </template>

                                    <button type="submit"
                                        class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">
                                        {{ __('messages.confirm') }}
                                    </button>

                                    <button type="button" @click="showDeleteModal = false"
                                        class="px-4 py-2 rounded-xl bg-gray-300 text-gray-800 hover:bg-gray-400">
                                        {{ __('messages.cancel') }}
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ Filter/Search Bar --}}
            <div x-show="selectedCategories.length === 0" x-cloak
                class="flex flex-col md:flex-row items-center justify-between mb-4 space-y-4 md:space-y-0">

                <form action="{{ route('admin.categories.index') }}" method="GET"
                    class="flex flex-col md:flex-row md:items-center gap-4 w-full max-w-xl">
                    <div x-data="{ open: false, selectedStatus: '{{ $selectedStatus ?? '' }}', selectedSort: '{{ $sort ?? '' }}' }" class="relative inline-block text-left">
                        <img src="{{ asset('images/interface-setting-slider-horizontal--adjustment-adjust-controls-fader-horizontal-settings-slider--Streamline-Core.svg') }}"
                            class="cursor-pointer w-6 h-6" @click="open = !open" alt="Filter Icon">

                        <div x-show="open" @click.away="open = false" x-transition.opacity x-cloak
                            class="absolute mt-2 w-64 bg-white border border-gray-200 rounded-xl shadow z-50 p-4 right-0 md:left-0">
                            <h3 class="font-bold text-gray-700 mb-2 rtl:text-right ltr:text-left">{{ __('messages.sort_by') }}</h3>
                            <ul class="space-y-1 mb-4">
                                <li><label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort_option" value="" x-model="selectedSort"
                                            class="shrink-0 w-5 h-5 ...">
                                        <span class="ml-2 text-gray-700">{{ __('messages.all') }}</span></label></li>
                                <li><label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort_option" value="name_asc" x-model="selectedSort"
                                            class="shrink-0 w-5 h-5 ...">
                                        <span class="ml-2 text-gray-700">{{ __('messages.name_asc') }}</span></label></li>
                                <li><label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort_option" value="name_desc" x-model="selectedSort"
                                            class="shrink-0 w-5 h-5 ...">
                                        <span class="ml-2 text-gray-700">{{ __('messages.name_desc') }}</span></label></li>
                                <li><label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort_option" value="latest" x-model="selectedSort"
                                            class="shrink-0 w-5 h-5 ...">
                                        <span class="ml-2 text-gray-700">{{ __('messages.latest') }}</span></label></li>
                                <li><label class="flex items-center cursor-pointer">
                                        <input type="radio" name="sort_option" value="oldest" x-model="selectedSort"
                                            class="shrink-0 w-5 h-5 ...">
                                        <span class="ml-2 text-gray-700">{{ __('messages.oldest') }}</span></label></li>
                            </ul>

                            <h3 class="font-bold text-gray-700 mb-2 rtl:text-right ltr:text-left">{{ __('messages.category_type') }}</h3>
                            <ul class="space-y-1 mb-4">
                                <li><label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status_option" value="" x-model="selectedStatus"
                                            class="shrink-0 w-5 h-5 ...">
                                        <span class="ml-2 text-gray-700">{{ __('messages.all') }}</span></label></li>
                                <li><label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status_option" value="category" x-model="selectedStatus"
                                            class="shrink-0 w-5 h-5 ...">
                                        <span class="ml-2 text-gray-700">{{ __('messages.general') }}</span></label></li>
                                <li><label class="flex items-center cursor-pointer">
                                        <input type="radio" name="status_option" value="sub_category"
                                            x-model="selectedStatus"
                                            class="shrink-0 w-5 h-5 ...">
                                        <span class="ml-2 text-gray-700">{{ __('messages.sub') }}</span></label></li>
                            </ul>

                            <div class="flex justify-center gap-2">
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
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ __('messages.search_categories') }}"
                            class="w-full pl-10 pr-12 py-2 border border-gray-300 rounded-xl focus:outline-none">

                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <!-- 🔍 -->
                        </div>

                        <button type="submit"
                            class="absolute inset-y-0 left-0 flex items-center px-3 text-white bg-[#185D31] rounded-l-xl">
                            {{ __('messages.search') }}
                        </button>
                    </div>
                </form>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.categories.export') }}"
                        class="bg-gray-100 mx-2 hover:bg-gray-300 text-[#185D31] py-2 px-4 rounded-xl flex items-center">
                        <i class="fas fa-download ml-2"></i>
                        <span>{{ __('messages.export') }}</span>
                    </a>

                    <a href="{{ route('admin.categories.create') }}"
                        class="bg-[#185D31] text-white py-2 px-4 rounded-xl flex items-center hover:bg-green-800">
                        <i class="fas fa-plus ml-2"></i>
                        <span>{{ __('messages.add_category') }}</span>
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200">
                {{-- ✅ هنا جدول الفئات --}}
                @include('admin.categories.categories_table')
            </div>

            <nav class="flex items-center justify-between p-3 bg-[#EDEDED]" aria-label="التنقل بين الصفحات">
                <div class="flex-1 flex flex-col md:flex-row justify-between items-center gap-4">
                    {{-- ✅ عبارة الإظهار --}}
                    <span class="text-sm text-gray-700">
                        {{ __('messages.showing_results', ['from' => $items->firstItem(), 'to' => $items->lastItem(), 'total' => $items->total()]) }}
                    </span>

                    <div class="flex items-center gap-4">
                        {{-- ✅ الصفوف لكل صفحة --}}
                        <span class="text-sm text-gray-700">
                            {{ __('messages.rows_per_page') }}:
                            <form action="{{ route('admin.categories.index') }}" method="GET" class="inline-block">
                                <input type="hidden" name="status" value="{{ $selectedStatus ?? '' }}">
                                <input type="hidden" name="search" value="{{ $search ?? '' }}">
                                <input type="hidden" name="sort" value="{{ $sort ?? '' }}">
                                <select name="per_page" onchange="this.form.submit()"
                                    class="form-select rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>١٠</option>
                                    <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>٢٥</option>
                                    <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>٥٠</option>
                                </select>
                            </form>
                        </span>

                        {{-- ✅ روابط الصفحات --}}
                        {!! $items->appends(request()->query())->links('pagination::tailwind') !!}
                    </div>
                </div>
            </nav>
        </div>
    </div>
@endsection
